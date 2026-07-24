@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; text-align:right">

    <div class="card shadow p-4 text-center">

        <h3 class="text-danger mb-3">
            ⚠️ لا يمكن متابعة عملية الحجز
        </h3>

        <p class="lead" style="font-size: 18px;">
            {{ $message }}
        </p>

        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3" style="width:200px;">
            الرجوع للخلف
        </a>
    </div>

</div>
@endsection
