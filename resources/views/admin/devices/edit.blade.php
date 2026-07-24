@extends('layouts.app')

@section('content')
<div class="container">
    <h3>✏️ Modifier Device</h3>

    <form method="POST" action="{{ route('devices.update',$device) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="name" class="form-control"
                   value="{{ $device->name }}" required>
        </div>

        <div class="mb-3">
            <label>IP</label>
            <input type="text" name="ip" class="form-control"
                   value="{{ $device->ip }}" required>
        </div>

        <div class="mb-3">
            <label>Port</label>
            <input type="number" name="port" class="form-control"
                   value="{{ $device->port }}">
        </div>

        <div class="mb-3">
            <label>Location</label>
            <input type="text" name="location" class="form-control"
                   value="{{ $device->location }}">
        </div>

        <button class="btn btn-primary">💾 Mettre à jour</button>
        <a href="{{ route('devices.index') }}" class="btn btn-secondary">↩️ Retour</a>
    </form>
</div>
@endsection
