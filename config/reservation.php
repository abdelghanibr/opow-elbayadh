<?php

return [

    /*
    | Seuil (DA) de règlement par remboursement : si, après déduction des crédits réservés,
    | le reste à payer est STRICTEMENT inférieur à ce seuil, la réservation peut être réglée
    | par remboursement (sans passerelle) et le petit reste est abandonné (trace : credit_waived).
    | Utilisé partout (vue, PaymentController, ReservationCreditService, tests).
    */
    'credit_waive_threshold' => (float) env('CREDIT_WAIVE_THRESHOLD', 100),

];
