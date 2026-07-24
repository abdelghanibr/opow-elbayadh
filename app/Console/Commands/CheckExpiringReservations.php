<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationExpiringMail;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiringReservations extends Command
{
    protected $signature = 'reservations:check-expiring';
    protected $description = 'Check reservations expiring soon and notify users by email';

public function handle()
{
    Log::info('🔔 reservations:check-expiring STARTED');

    $daysBefore = 3;
    $today = now();
    $limitDate = now()->addDays($daysBefore);

    $reservations = Reservation::with('user')
        ->where('payment_status', 'paid')
        ->whereDate('end_date', '>=', $today)
        ->whereDate('end_date', '<=', $limitDate)
        ->get();

    Log::info('📊 Reservations found', [
        'count' => $reservations->count()
    ]);

    foreach ($reservations as $reservation) {

        if (!$reservation->user || empty($reservation->user->email)) {
            Log::warning('⚠️ Reservation without email', [
                'reservation_id' => $reservation->id
            ]);
            continue;
        }

        Log::info('📧 Sending email', [
            'to' => $reservation->user->email,
            'reservation_id' => $reservation->id
        ]);

        Mail::to($reservation->user->email)
            ->send(new ReservationExpiringMail($reservation));
    }

    Log::info('✅ reservations:check-expiring FINISHED');
}
}
