
@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction:rtl;text-align:right;max-width:1000px">

    <h3 class="fw-bold mb-4">📁 ملف النادي</h3>

    @if(session('success'))
        <div class="alert alert-success text-center fw-bold">
            {{ session('success') }}
        </div>
    @endif

    @php
        // المرفقات — مصفوفة مفهرسة باسم الوثيقة (Club::$attachments)
        $files = $club->attachments;

        // 🔁 ترجمة أسماء الوثائق
        $documentsLabels = App\Models\Club::DOSSIER_FILES;
    @endphp

    <table class="table table-bordered text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th>الوثيقة</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($files as $key => $path)
                <tr>
                    <td class="fw-bold">
                        {{ $documentsLabels[$key] ?? $key }}
                    </td>
                    <td>
                        <a href="{{ asset($path) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary">
                            👁 عرض
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-muted">
                        لا توجد وثائق مرفوعة بعد
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 d-flex justify-content-between align-items-center">
     

        <a href="{{ route('club.dossier.edit') }}"
           class="btn btn-primary">
           ✏️ تعديل الملف
        </a>
    </div>

</div>
@endsection
