<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Ticket;
use App\Models\Payment;
use App\Models\MatchModel;
use App\Models\SeatType;
use Carbon\Carbon;

class TicketPaymentController extends Controller
{
    public function confirmAndPay(Request $request)
    {
        $request->validate([
            'match_id'       => 'required|exists:matches,id',
            'seat_type_id'   => 'required|exists:seat_types,id',
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email',
            'phone'          => 'required|string|max:20',
            'identity_number'=> 'required|string|max:20',
            'age'            => 'required|integer|min:5|max:120',
        ]);

        $match    = MatchModel::with(['homeTeam', 'awayTeam', 'complex'])->findOrFail($request->match_id);
        $seatType = SeatType::findOrFail($request->seat_type_id);

        $ticketData = [
            'match_id'       => $match->id,
            'seat_type_id'   => $seatType->id,
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'identity_number'=> $request->identity_number,
            'age'            => $request->age,
            'amount'         => $seatType->price,
        ];

        session(['ticket_payment' => $ticketData]);

        return view('payments.ticket-pay', [
            'match'    => $match,
            'seatType' => $seatType,
            'ticketData' => $ticketData,
        ]);
    }

    public function initiatePayment(Request $request)
    {
        $ticketData = session('ticket_payment');

        if (!$ticketData) {
            return back()->withErrors(['payment' => 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة']);
        }

        $rules = [
            'accept_terms' => 'accepted',
        ];

        if (!app()->environment('local')) {
            $rules['g-recaptcha-response'] = 'required';
        }

        $request->validate($rules);

        if (!app()->environment('local')) {
            $captcha = Http::asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret'   => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ]
            );

            if (!data_get($captcha->json(), 'success')) {
                return back()->withErrors(['g-recaptcha-response' => 'فشل التحقق الأمني']);
            }
        }

        $match    = MatchModel::with(['homeTeam', 'awayTeam', 'complex'])->findOrFail($ticketData['match_id']);
        $seatType = SeatType::findOrFail($ticketData['seat_type_id']);

        $ticket = Ticket::create([
            'match_id'    => $ticketData['match_id'],
            'seat_type_id'=> $ticketData['seat_type_id'],
            'buyer_name'  => $ticketData['full_name'],
            'buyer_phone' => $ticketData['phone'],
            'status'      => 'pending',
        ]);

        $orderNumber = 'TK' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT) . substr((string) time(), -6);

        $amountInCentimes = (int) round($seatType->price * 100);

        $payment = Payment::create([
            'order_id' => $orderNumber,
            'amount'   => $amountInCentimes,
            'status'   => 'pending',
        ]);

        $ticket->update(['payment_id' => $payment->id]);

        $payload = [
            'userName'    => config('services.satim.username'),
            'password'    => config('services.satim.password'),
            'orderNumber' => $payment->order_id,
            'amount'      => $amountInCentimes,
            'currency'    => '012',
            'returnUrl'   => route('ticket.payment.verify', ['order_number' => $payment->order_id]),
            'failUrl'     => route('ticket.payment.verify', ['order_number' => $payment->order_id]),
            'description' => 'Tiquet match ' . $match->homeTeam->name . ' vs ' . $match->awayTeam->name . ' #' . $ticket->id,
            'language'    => 'fr',
            'jsonParams'  => json_encode([
                'force_terminal_id' => config('services.satim.terminal_id'),
                'ticket_id'         => (string) $ticket->id,
            ], JSON_UNESCAPED_UNICODE),
        ];

        $response = Http::asForm()
            ->timeout(30)
            ->post(config('services.satim.register_url'), $payload);

        if (!$response->successful()) {
            $ticket->update(['status' => 'failed']);
            return back()->withErrors(['payment' => 'خطأ في بوابة الدفع SATIM']);
        }

        $data = $response->json();
        $errorCode = (string) data_get($data, 'errorCode', '');
        $formUrl = data_get($data, 'formUrl');

        if ($errorCode !== '0') {
            $payment->update(['status' => 'failed', 'payload' => $data]);
            $ticket->update(['status' => 'failed']);
            return back()->withErrors(['payment' => data_get($data, 'errorMessage', 'خطأ SATIM') . ' (code ' . $errorCode . ')']);
        }

        $payment->update(['status' => 'processing', 'payload' => $data]);

        if (!$formUrl) {
            return back()->withErrors(['payment' => 'رابط الدفع SATIM غير متوفر']);
        }

        session()->forget('ticket_payment');

        return redirect()->away($formUrl);
    }

    public function verify(Request $request)
    {
        $orderNumber  = $request->query('order_number') ?: $request->query('orderNumber');
        $satimOrderId = $request->query('orderId') ?: $request->query('mdOrder');

        abort_if(!$orderNumber, 400, 'Missing order number');

        $payment = Payment::where('order_id', $orderNumber)->firstOrFail();
        $ticket = Ticket::where('payment_id', $payment->id)->first();

        $savedPayload = is_array($payment->payload)
            ? $payment->payload
            : (json_decode($payment->payload ?? '[]', true) ?: []);

        if (!$satimOrderId) {
            $satimOrderId = data_get($savedPayload, 'orderId');
        }

        $ackPayload = [
            'userName' => config('services.satim.username'),
            'password' => config('services.satim.password'),
        ];

        if ($satimOrderId) {
            $ackPayload['orderId'] = $satimOrderId;
        } else {
            $ackPayload['orderNumber'] = $payment->order_id;
        }

        $response = Http::asForm()
            ->timeout(30)
            ->post(config('services.satim.ack_url'), $ackPayload);

        Log::info('SATIM ticket verify', [
            'order_number' => $orderNumber,
            'ack_status'   => $response->status(),
        ]);

        if (!$response->successful()) {
            return view('payments.ticket-result', [
                'payment'  => $payment,
                'ticket'   => $ticket,
                'status'   => 'pending',
                'action'   => 'Échec de vérification SATIM',
            ]);
        }

        $data = $response->json();

        if (!is_array($data)) {
            return view('payments.ticket-result', [
                'payment'  => $payment,
                'ticket'   => $ticket,
                'status'   => 'pending',
                'action'   => 'Réponse SATIM invalide',
            ]);
        }

        $errorCode    = (string) data_get($data, 'ErrorCode', '');
        $actionCode   = (string) data_get($data, 'actionCode', '');
        $orderStatus  = (string) data_get($data, 'OrderStatus', '');
        $action       = data_get($data, 'actionCodeDescription', data_get($data, 'ErrorMessage', ''));
        $isSuccess    = $errorCode === '0' && $actionCode === '0' && $orderStatus === '2';

        $headerDate = $response->header('Date');
        $operationDate = $headerDate ? Carbon::parse($headerDate)->setTimezone(config('app.timezone')) : now();

        $payment->update([
            'status'        => $isSuccess ? 'success' : 'failed',
            'payload'       => $data,
            'datetimesatim' => $operationDate,
        ]);

        if ($ticket) {
            $ticket->update([
                'status' => $isSuccess ? 'confirmed' : 'failed',
            ]);
        }

        return view('payments.ticket-result', [
            'payment' => $payment,
            'ticket'  => $ticket,
            'status'  => $isSuccess ? 'success' : 'failed',
            'action'  => $action,
        ]);
    }
}
