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
use App\Models\Setting;
use Carbon\Carbon;

class TicketPaymentController extends Controller
{
    public function showTicketPay()
    {
        $ticketData = session('ticket_payment');

        if (!$ticketData) {
            return redirect()->route('matches.public');
        }

        $match    = MatchModel::with(['homeTeam', 'awayTeam', 'complex'])->find($ticketData['match_id']);
        $seatType = SeatType::find($ticketData['seat_type_id']);

        if (!$match || !$seatType) {
            return redirect()->route('matches.public');
        }

        return view('payments.ticket-pay', [
            'match'      => $match,
            'seatType'   => $seatType,
            'ticketData' => $ticketData,
        ]);
    }

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

        return redirect()->route('ticket.pay.show');
    }

    public function initiatePayment(Request $request)
    {
        $ticketData = session('ticket_payment');

        if (!$ticketData) {
            return redirect()->route('ticket.pay.show')->withErrors(['payment' => 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة']);
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
                return redirect()->route('ticket.pay.show')->withErrors(['g-recaptcha-response' => 'فشل التحقق الأمني']);
            }
        }

        $match    = MatchModel::with(['homeTeam', 'awayTeam', 'complex'])->findOrFail($ticketData['match_id']);
        $seatType = SeatType::findOrFail($ticketData['seat_type_id']);

        $ticket = Ticket::create([
            'match_id'       => $ticketData['match_id'],
            'seat_type_id'   => $ticketData['seat_type_id'],
            'buyer_name'     => $ticketData['full_name'],
            'buyer_phone'    => $ticketData['phone'],
            'email'          => $ticketData['email'] ?? null,
            'identity_number'=> $ticketData['identity_number'] ?? null,
            'age'            => $ticketData['age'] ?? null,
            'status'         => 'reserved',
        ]);

        $amount = (int) round($seatType->price);

        $payment = Payment::create([
            'order_id' => 'ORD-' . \Str::uuid(),
            'amount'   => $amount,
            'status'   => 'pending',
        ]);

        $ticket->update(['payment_id' => $payment->id]);

        $response = Http::withHeaders([
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'x-app-key'    => config('services.guiddini.app_key'),
            'x-app-secret' => config('services.guiddini.secret_key'),
        ])->post('https://epay.guiddini.dz/api/payment/initiate', [
            'amount'       => $amount,
            'return_url'   => route('ticket.payment.verify'),
            'callback_url' => route('ticket.payment.verify'),
            'language'     => 'AR',
        ]);

        $isSandboxBypass = false;

        if (!$response->successful()) {
            $body = $response->json();
            $errorCode    = data_get($body, 'errors.0.code', 'UNKNOWN');

            if (app()->environment('local') && $response->status() === 403 && $errorCode === 'ACCESS_DENIED') {
                $isSandboxBypass = true;
            } else {
                $errorTitle   = data_get($body, 'errors.0.title', '');
                $errorDetail  = data_get($body, 'errors.0.detail', '');
                $errorMessage = data_get($body, 'errors.0.meta.satim_response.errorMessage', '');

                $ticket->update(['status' => 'cancelled']);
                $payment->update(['status' => 'failed', 'payload' => $body]);

                $errorInfo = 'HTTP ' . $response->status()
                    . ' | Code: ' . $errorCode
                    . ' | Title: ' . $errorTitle
                    . ($errorDetail ? ' | Detail: ' . $errorDetail : '')
                    . ($errorMessage ? ' | SATIM: ' . $errorMessage : '');

                return redirect()->route('ticket.pay.show')->withErrors(['payment' => $errorInfo]);
            }
        }

        if ($isSandboxBypass) {
            $qrPayload = json_encode([
                'id'   => $ticket->id,
                'm'    => $match->homeTeam->name . ' vs ' . $match->awayTeam->name,
                's'    => $seatType->name,
                'b'    => $ticketData['full_name'],
                'p'    => $ticketData['phone'],
                'c'    => $ticketData['identity_number'],
                'a'    => $amount,
                'o'    => $payment->order_id,
                'd'    => $match->match_date,
                't'    => $match->match_time,
                'w'    => Setting::get('office_short', 'OPOW EL BAYADH'),
            ], JSON_UNESCAPED_UNICODE);

            $ticket->update([
                'status'  => 'paid',
                'qr_code' => $qrPayload,
            ]);

            $payment->update([
                'status'  => 'success',
                'payload' => ['sandbox_bypass' => true, 'amount' => $amount],
            ]);

            session()->forget('ticket_payment');

            return redirect()->route('ticket.show-result', $ticket->id);
        }

        $data = $response->json();
        $formUrl = data_get($data, 'data.attributes.form_url');

        if (!$formUrl) {
            $errors = data_get($data, 'errors', []);
            $errorInfo = 'No form_url — Errors: ' . json_encode($errors, JSON_UNESCAPED_UNICODE);

            $ticket->update(['status' => 'cancelled']);
            $payment->update(['status' => 'failed', 'payload' => $data]);
            return redirect()->route('ticket.pay.show')->withErrors(['payment' => $errorInfo]);
        }

        $payment->update([
            'status'  => 'processing',
            'payload' => $data,
            'order_id'=> data_get($data, 'data.id'),
        ]);

        session()->forget('ticket_payment');

        return redirect()->away($formUrl);
    }

    public function printTicket($id)
    {
        $ticket = Ticket::with(['match.homeTeam', 'match.awayTeam', 'match.complex', 'seatType'])->findOrFail($id);
        $payment = $ticket->payment ?? null;

        $settings = \App\Models\Setting::allArray();

        $ticketData = [
            'full_name'      => $ticket->buyer_name,
            'phone'          => $ticket->buyer_phone,
            'identity_number' => '—',
            'age'            => '—',
        ];

        return view('payments.ticket-print', [
            'ticket'     => $ticket,
            'payment'    => $payment,
            'settings'   => $settings,
            'ticketData' => $ticketData,
        ]);
    }

    public function showResult($id)
    {
        $ticket = Ticket::with(['match.homeTeam', 'match.awayTeam', 'seatType', 'payment'])->findOrFail($id);

        $ticketData = [
            'full_name' => $ticket->buyer_name,
            'phone'     => $ticket->buyer_phone,
            'email'     => $ticket->email,
        ];

        return view('payments.ticket-result', [
            'payment'    => $ticket->payment,
            'ticket'     => $ticket,
            'status'     => $ticket->status === 'paid' ? 'success' : $ticket->status,
            'action'     => 'تم الدفع بنجاح',
            'ticketData' => $ticketData,
        ]);
    }

    public function showVerifyQR()
    {
        return view('payments.verify-qr');
    }

    public function checkQR(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $raw = $request->input('qr_data');

        $data = json_decode($raw, true);

        if (!$data || !isset($data['id'])) {
            return view('payments.verify-qr', [
                'result' => 'invalid',
                'message' => 'الرمز غير صالح — لا يمكن قراءة بيانات التذكرة',
                'qrData' => null,
            ]);
        }

        $ticket = Ticket::with(['match.homeTeam', 'match.awayTeam', 'seatType', 'payment'])
            ->where('id', $data['id'])
            ->first();

        if (!$ticket) {
            return view('payments.verify-qr', [
                'result' => 'invalid',
                'message' => 'التذكرة غير موجودة في المنصة',
                'qrData' => $data,
            ]);
        }

        if ($ticket->status !== 'paid') {
            return view('payments.verify-qr', [
                'result' => 'invalid',
                'message' => 'التذكرة غير مفعّلة — الحالة: ' . $ticket->status,
                'qrData' => $data,
                'ticket' => $ticket,
            ]);
        }

        $checks = [
            'buyer'  => $ticket->buyer_name === ($data['b'] ?? ''),
            'seat'   => $ticket->seatType->name === ($data['s'] ?? ''),
            'amount' => (int)$ticket->seatType->price === (int)($data['a'] ?? 0),
            'date'   => $ticket->match->match_date === ($data['d'] ?? ''),
            'time'   => $ticket->match->match_time === ($data['t'] ?? ''),
        ];

        $isValid = !in_array(false, $checks);

        return view('payments.verify-qr', [
            'result'   => $isValid ? 'valid' : 'tampered',
            'message'  => $isValid ? 'تذكرة صالحة — تم التحقق بنجاح' : 'التذكرة غير متطابقة — بيانات مشبوهة',
            'qrData'   => $data,
            'ticket'   => $ticket,
            'checks'   => $checks,
        ]);
    }

    public function sendTicketReceiptEmail(Request $request, $ticketId)
    {
        $ticket = Ticket::with('payment')->findOrFail($ticketId);

        if ($ticket->status !== 'paid') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'التذكرة غير مدفوعة']);
            }
            return back()->with('error', 'التذكرة غير مدفوعة');
        }

        $email = $request->input('email');

        if (!$email) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'البريد غير متوفر']);
            }
            return back()->with('error', 'البريد غير متوفر');
        }

        $payment = $ticket->payment;

        $response = Http::withHeaders([
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'x-app-key'    => config('services.guiddini.app_key'),
            'x-app-secret' => config('services.guiddini.secret_key'),
        ])->post('https://epay.guiddini.dz/api/payment/email', [
            'order_number' => $payment->order_id,
            'email'        => $email,
        ]);

        if (!$response->successful()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'فشل إرسال الوصل بالبريد']);
            }
            return back()->with('error', 'فشل إرسال الوصل بالبريد');
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم إرسال الوصل إلى البريد: ' . $email]);
        }

        return back()->with('success', 'تم إرسال الوصل إلى البريد: ' . $email);
    }

    public function verify(Request $request)
    {
        $orderNumber = $request->query('order_number');

        abort_if(!$orderNumber, 400, 'Missing order number');

        $payment = Payment::where('order_id', $orderNumber)->firstOrFail();
        $ticket  = Ticket::where('payment_id', $payment->id)->first();

        $response = Http::withHeaders([
            'Accept'       => 'application/json',
            'x-app-key'    => config('services.guiddini.app_key'),
            'x-app-secret' => config('services.guiddini.secret_key'),
        ])->get('https://epay.guiddini.dz/api/payment/show', [
            'order_number' => $orderNumber,
        ]);

        if (!$response->successful()) {
            return view('payments.ticket-result', [
                'payment'    => $payment,
                'ticket'     => $ticket,
                'status'     => 'pending',
                'action'     => 'Échec de vérification',
                'ticketData' => ['full_name' => $ticket->buyer_name ?? '', 'phone' => $ticket->buyer_phone ?? '', 'email' => null],
                'orderNumber' => $orderNumber,
            ]);
        }
//dd($response);
        $data   = $response->json();
        $status = data_get($data, 'data.attributes.status');
        $paidAt = data_get($data, 'data.attributes.updated_at');

        $respDesc = data_get($data, 'data.attributes.params.respCode_desc');
        $action   = $respDesc ?: data_get($data, 'data.attributes.action_code_description');

        if (preg_match('/error\s*code\s*[:=]/i', (string) $action)) {
            $action = null;
        }

        $isSuccess = in_array($status, ['succeeded', 'paid']);

        $updatedAt = now();
        if (!empty($paidAt)) {
            try {
                $updatedAt = Carbon::parse($paidAt)->setTimezone(config('app.timezone'));
            } catch (\Exception $e) {
                logger()->warning('Invalid paidAt', ['paidAt' => $paidAt]);
            }
        }

        $payment->update([
            'status'        => $isSuccess ? 'success' : 'failed',
            'payload'       => $data,
            'updated_at'    => $paidAt,
            'datetimesatim' => $updatedAt,
        ]);

        if ($ticket) {
            if ($isSuccess) {
                $ticket->load(['match.homeTeam', 'match.awayTeam', 'seatType']);

                $qrPayload = json_encode([
                    'id' => $ticket->id,
                    'm'  => $ticket->match->homeTeam->name . ' vs ' . $ticket->match->awayTeam->name,
                    's'  => $ticket->seatType->name,
                    'b'  => $ticket->buyer_name,
                    'p'  => $ticket->buyer_phone,
                    'a'  => $ticket->seatType->price,
                    'o'  => $payment->order_id,
                    'd'  => $ticket->match->match_date,
                    't'  => $ticket->match->match_time,
                ], JSON_UNESCAPED_UNICODE);

                $ticket->update([
                    'status'  => 'paid',
                    'qr_code' => $qrPayload,
                ]);
            } else {
                $ticket->update(['status' => 'cancelled']);
            }
        }

        return view('payments.ticket-result', [
            'payment'    => $payment,
            'ticket'     => $ticket,
            'status'     => $isSuccess ? 'success' : 'failed',
            'action'     => $action,
            'ticketData' => ['full_name' => $ticket->buyer_name ?? '', 'phone' => $ticket->buyer_phone ?? '', 'email' => null],
            'orderNumber' => $orderNumber,
        ]);
    }
}
