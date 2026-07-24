<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Paiement Espaces de Jeux</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f3f4f6;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .card-kpi {
            border-radius: 16px;
            border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .card-kpi h5 {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6b7280;
        }
        .card-kpi .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
        }
        .badge-espace {
            font-size: 0.75rem;
            border-radius: 999px;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <h2 class="mb-4">Dashboard – Paiement en ligne des espaces de jeux</h2>

    {{-- Ligne 1 : KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-kpi p-3">
                <h5>CA du jour</h5>
                <div class="value">
                    {{-- Exemple statique, tu remplaces par variable --}}
                    85 000 DA
                </div>
                <small class="text-success">+12% vs hier</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-kpi p-3">
                <h5>Réservations du jour</h5>
                <div class="value">24</div>
                <small class="text-muted">Stade, piscine, salle</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-kpi p-3">
                <h5>Taux d’occupation</h5>
                <div class="value">76%</div>
                <small class="text-muted">Tous espaces confondus</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-kpi p-3">
                <h5>Cautions en cours</h5>
                <div class="value">210 000 DA</div>
                <small class="text-danger">10 cautions à rembourser</small>
            </div>
        </div>
    </div>

    {{-- Ligne 2 : Répartition par espace + Abonnements --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    Répartition des revenus par type d’espace (aujourd’hui)
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><span class="badge bg-primary badge-espace">Stade</span> Réservations : 10</span>
                            <strong>45 000 DA</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><span class="badge bg-info text-dark badge-espace">Piscine</span> Réservations : 8</span>
                            <strong>25 000 DA</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><span class="badge bg-success badge-espace">Salle de sport</span> Abonnements : 6</span>
                            <strong>15 000 DA</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Bloc abonnements & cautions --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    Abonnements & Cautions
                </div>
                <div class="card-body">
                    <p><strong>Abonnés actifs :</strong> 120</p>
                    <p><strong>Abonnements qui expirent sous 7 jours :</strong> 9</p>
                    <hr>
                    <p><strong>Cautions encaissées aujourd’hui :</strong> 5 (50 000 DA)</p>
                    <p><strong>Cautions à rembourser :</strong> 3</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Ligne 3 : Réservations du jour / en attente de paiement --}}
    <div class="card mb-4">
        <div class="card-header">
            Réservations du jour / en attente de paiement
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Client</th>
                            <th>Espace</th>
                            <th>Date & Heure</th>
                            <th>Montant</th>
                            <th>Statut paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Exemples statiques, à remplacer par @foreach --}}
                        <tr>
                            <td>Ali B.</td>
                            <td><span class="badge bg-primary badge-espace">Stade</span></td>
                            <td>01/12/2025 – 18:00</td>
                            <td>5 000 DA</td>
                            <td><span class="badge bg-success">Payé en ligne</span></td>
                        </tr>
                        <tr>
                            <td>Samir K.</td>
                            <td><span class="badge bg-info text-dark badge-espace">Piscine</span></td>
                            <td>01/12/2025 – 15:00</td>
                            <td>3 000 DA</td>
                            <td><span class="badge bg-warning text-dark">En attente</span></td>
                        </tr>
                        <tr>
                            <td>Sarah L.</td>
                            <td><span class="badge bg-success badge-espace">Salle de sport</span></td>
                            <td>Abonnement mensuel</td>
                            <td>6 000 DA</td>
                            <td><span class="badge bg-success">Payé</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>
