@extends('layouts.app')

@section('content')
<div class="container">
    <h3>➕ Ajouter Device</h3>

    <form method="POST" action="{{ route('devices.store') }}">
        @csrf

        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>IP</label>
            <input type="text" name="ip" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Port</label>
            <input type="number" name="port" class="form-control" value="4370">
        </div>

        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control">
        </div>

        <button class="btn btn-success">💾 Enregistrer</button>
        <a href="{{ route('devices.index') }}" class="btn btn-secondary">↩️ Retour</a>
    </form>
</div>
@endsection
