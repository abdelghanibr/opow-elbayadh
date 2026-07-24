<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>المركبات المتوفرة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { direction: rtl; text-align: right; font-family: "Cairo", sans-serif; background:#f7f7f7; }
        .card { transition:.3s; }
        .card:hover { transform: scale(1.03); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
    </style>
</head>
<body>

<div class="container py-4">

    <h3 class="mb-3">المركبات المتوفرة لنوع: {{ $type }}</h3>

    <div class="row g-3">

        @foreach ($complexes as $c)
        <div class="col-md-4">
            <div class="card p-3">
                <h4>{{ $c->nom }}</h4>
                <p>السعة: {{ $c->capacite }} شخص</p>
                <p>السعر: {{ $c->prix }} دج</p>

                <a href="{{ route('reservation.form', $c->id) }}"
                   class="btn btn-primary w-100 mt-3">
                    حجز الآن
                </a>
            </div>
        </div>
        @endforeach

    </div>

</div>

</body>
</html>
