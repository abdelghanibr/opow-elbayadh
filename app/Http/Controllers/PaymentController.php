<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Ticket;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Http;
use App\Models\Payment;
use Illuminate\Support\Str;

use Carbon\Carbon;
use App\Models\Person;
use App\Models\ReservationCredit;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{


public function initiate(Request $request)
{
    $rules = [
        'reservation_id' => 'required|exists:reservations,id',
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
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );

        if (!data_get($captcha->json(), 'success')) {
            return back()->withErrors([
                'g-recaptcha-response' => 'فشل التحقق الأمني'
            ]);
        }
    }

    $reservation = Reservation::findOrFail($request->reservation_id);
    $amount = (int) round($reservation->total_price);

    $payment = Payment::create([
        'order_id' => 'ORD-' . \Str::uuid(),
        'amount'   => $amount,
        'status'   => 'pending',
    ]);

    $reservation->update([
        'payment_id'     => $payment->id,
        'payment_status' => 'pending',
    ]);

    $response = Http::withHeaders([
        'Accept'        => 'application/json',
        'Content-Type'  => 'application/json',
        'x-app-key'     => config('services.guiddini.app_key'),
        'x-app-secret'  => config('services.guiddini.secret_key'),
    ])->post('https://epay.guiddini.dz/api/payment/initiate', [
        'amount'       => $amount,
        'return_url'   => config('services.guiddini.return_url'),
        'callback_url' => config('services.guiddini.callback'),
        'language'     => 'AR',
    ]);

    if (! $response->successful()) {
        return back()->withErrors(['payment' => 'خطأ في بوابة الدفع']);
    }

    $data = $response->json();
    $formUrl = data_get($data, 'data.attributes.form_url');

    if (! $formUrl) {
        return back()->withErrors(['payment' => 'رابط الدفع غير متوفر']);
    }

    $payment->update([
        'status'  => 'processing',
        'payload' => $data,
        'order_id'=> data_get($data, 'data.id')
    ]);

    return redirect()->away($formUrl);
}


public function verify(Request $request)
{
    $orderNumber = $request->query('order_number');

    abort_if(! $orderNumber, 400, 'Missing order number');

    $payment = Payment::where('order_id', $orderNumber)->firstOrFail();

    $response = Http::withHeaders([
        'Accept'       => 'application/json',
        'x-app-key'    => config('services.guiddini.app_key'),
        'x-app-secret' => config('services.guiddini.secret_key'),
    ])->get('https://epay.guiddini.dz/api/payment/show', [
        'order_number' => $orderNumber,
    ]);

    $reservation = Reservation::where('payment_id', $payment->id)->first();
    $ticket = $reservation ? null : Ticket::where('payment_id', $payment->id)->with(['match.homeTeam', 'match.awayTeam', 'seatType'])->first();
    $user = optional($reservation)->user ?? optional($ticket)->buyer_name ?? auth()->user();

    if (! $response->successful()) {
        $view = $ticket ? 'payments.ticket-result' : 'payments.result';
        $data = $ticket
            ? ['payment' => $payment, 'ticket' => $ticket, 'status' => 'pending', 'action' => 'Échec de vérification', 'ticketData' => ['full_name' => $ticket->buyer_name ?? '', 'phone' => $ticket->buyer_phone ?? '', 'email' => null], 'orderNumber' => $orderNumber]
            : ['payment' => $payment, 'reservation' => $reservation, 'user' => $user, 'status' => 'pending', 'orderNumber' => $orderNumber];
        return view($view, $data);
    }

    $status = data_get($response->json(), 'data.attributes.status');
    $paidAt = data_get($response->json(), 'data.attributes.updated_at');

    $respDesc = data_get($response->json(), 'data.attributes.params.respCode_desc');
    $action = data_get(
        $response->json(),
        'data.attributes.action_code_description'
    );
 $action = $respDesc ?: $action;
   if (preg_match('/your\s+payment\s+was\s+accepted/i', $action)) {
    $action = 'Your payment was accepted';}
    
//dd($action,$respDesc );
    $order_id = data_get($response->json(), 'data.attributes.order_id');
    $approval_code = data_get($response->json(), 'data.attributes.approval_code')
        ?: data_get($response->json(), 'data.attributes.auth_code');

    $updatedAt = now();
    if (!empty($paidAt)) {
        try {
            $updatedAt = Carbon::parse($paidAt)
                ->setTimezone(config('app.timezone'));
        } catch (\Exception $e) {
            logger()->warning('Invalid paidAt', ['paidAt'=>$paidAt]);
        }
    }

    $payment->update([
        'payload' => $response->json(),
        'updated_at'   => $paidAt,
        'datetimesatim'  => $updatedAt ,
    ]);

    $isSuccess = in_array($status, ['succeeded', 'paid']);

    $payment->update([
        'status' => $isSuccess ? 'success' : 'failed',
        'updated_at' => $paidAt,
        'datetimesatim' => $updatedAt,
    ]);

    if ($reservation) {
        $reservation->update([
            'payment_id' => $payment->id,
            'payment_status' => $isSuccess ? 'paid' : 'failed',
            'statut' => 'confirmee',
            'updated_at' => $paidAt,
        ]);

        if ($isSuccess && !empty($reservation->user_id)) {
            Person::where('user_id', $reservation->user_id)
                ->update([
                    'etat_ass'   => 0,
                    'updated_at' => now(),
                    'assured_expires_on'=> now(),
                ]);

            $this->applyPendingCreditsAfterSuccessfulPayment($reservation);
        }
    } elseif ($ticket) {
        if ($isSuccess) {
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

    $view = $ticket ? 'payments.ticket-result' : 'payments.result';
    $viewData = $ticket
        ? [
            'payment'    => $payment,
            'ticket'     => $ticket,
            'status'     => $isSuccess ? 'success' : 'failed',
            'action'     => $action,
            'ticketData' => ['full_name' => $ticket->buyer_name ?? '', 'phone' => $ticket->buyer_phone ?? '', 'email' => null],
            'orderNumber'=> $orderNumber,
          ]
        : [
            'payment'      => $payment,
            'reservation'  => $reservation,
            'user'         => $user,
            'status'       => $isSuccess ? 'success' : 'failed',
            'action'       => $action,
            'order_id'     => $order_id,
            'approval_code'=> $approval_code,
            'orderNumber'  => $orderNumber,
          ];

    return view($view, $viewData);
}


public function downloadReceipt($orderId)
{
    $payment = Payment::where('order_id', $orderId)->firstOrFail();

    if ($payment->receipt_url) {
        return redirect()->away($payment->receipt_url);
    }

    $response = Http::withHeaders([
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
        'x-app-key'    => config('services.guiddini.app_key'),
        'x-app-secret' => config('services.guiddini.secret_key'),
    ])->get('https://epay.guiddini.dz/api/payment/receipt', [
        'order_number' => $orderId
    ]);

    if (! $response->successful()) {
        return back()->with('error', 'تعذر تحميل الوصل');
    }

    $contentType = $response->header('Content-Type', '');

    if (str_contains($contentType, 'application/pdf')) {
        return response($response->body())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="recu-satim-'.$orderId.'.pdf"');
    }

    $data = $response->json() ?: [];

    $pdfUrl = data_get($data, 'links.href')
        ?: data_get($data, 'data.receipt_url')
        ?: data_get($data, 'receipt_url')
        ?: data_get($data, 'data.url');

    if (! $pdfUrl) {
        return back()->with('error', 'رابط الوصل غير متوفر');
    }

    $payment->update([
        'receipt_url' => $pdfUrl
    ]);

    return redirect()->away($pdfUrl);
}


public function sendReceiptEmail(Request $request, $orderId)
{

    $email = $request->input('email');

    if (! $email) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'البريد غير متوفر']);
        }
        return back()->with('error', 'البريد غير متوفر');
    }

    $response = Http::withHeaders([
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
        'x-app-key'    => config('services.guiddini.app_key'),
        'x-app-secret' => config('services.guiddini.secret_key'),
    ])->post('https://epay.guiddini.dz/api/payment/email', [
        'order_number' => $orderId,
        'email'        => $email
    ]);

    if (! $response->successful()) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'فشل إرسال الوصل بالبريد']);
        }
        return back()->with('error', 'فشل إرسال الوصل بالبريد');
    }

    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'تم إرسال الوصل إلى البريد: '.$email]);
    }

    return back()->with('success', 'تم إرسال الوصل إلى البريد: '.$email);
}

public function pay(Reservation $reservation)
    {
        if ((int)$reservation->user_id !== (int)Auth::id()) {
            abort(403, 'غير مصرح لك بالدفع لهذا الحجز');
        }

        if ($reservation->payment_status === 'paid') {
            return back()->with('info', 'هذا الحجز مدفوع بالفعل');
        }

        return view('payments.pay', [
            'reservation' => $reservation
        ]);
    }

 private function applyPendingCreditsAfterSuccessfulPayment(Reservation $reservation): void
{
    DB::transaction(function () use ($reservation) {

        $alreadyUsedForThisReservation = ReservationCredit::where('used_in_reservation_id', $reservation->id)
            ->where('status', 'used')
            ->exists();

        if ($alreadyUsedForThisReservation) {
            return;
        }

        $availableCredit = ReservationCredit::where('user_id', $reservation->user_id)
            ->where('status', 'pending')
            ->sum('credited_amount');

        if ($availableCredit <= 0) {
            return;
        }

        ReservationCredit::where('user_id', $reservation->user_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'used',
                'used_in_reservation_id' => $reservation->id,
                'updated_at' => now(),
            ]);
    });
}

}
