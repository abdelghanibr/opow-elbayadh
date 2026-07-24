<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Vérification Email - RCDistribution</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
body {
    margin:0;
    background: linear-gradient(135deg,#43cea2,#ffb347);
    font-family:'Inter',sans-serif;
    display:flex; justify-content:center; align-items:center;
    height:100vh;
}
.card {
    width:380px;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(12px);
    padding:30px;
    border-radius:20px;
    text-align:center;
    color:white;
    box-shadow:0 8px 32px rgba(0,0,0,0.25);
}
.btn {
    margin-top:15px;
    padding:12px;
    width:100%;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#43cea2,#ffb347);
    color:white;
    cursor:pointer;
}
</style>
</head>

<body>

<div class="card">
    <h2>Vérification Email</h2>

    @if (session('resent'))
        <p style="color:#fff;">Un nouveau lien a été envoyé.</p>
    @endif

    <p>
        Avant de continuer, veuillez vérifier votre email.
        <br>Si vous n'avez rien reçu :
    </p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button class="btn">Renvoyer le lien</button>
    </form>

    <a href="{{ route('logout') }}" style="color:white;text-decoration:underline;">Déconnexion</a>
</div>

</body>
</html>
