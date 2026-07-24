@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right">

    <h3 class="mb-4">📄 رفع ملف الدوسيي</h3>

    <div class="card p-4">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('dossier.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">اختر الملف (PDF أو صورة)</label>
                <input type="file" name="document" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">📤 رفع الملف</button>
        </form>
    </div>

</div>
@endsection
