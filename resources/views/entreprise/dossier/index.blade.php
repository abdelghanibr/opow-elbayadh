@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction:rtl;text-align:right;max-width:1000px">

    <h3 class="fw-bold mb-4">📁 ملف المؤسسة
        @php $type = $enterprise->company_type ?? 'privee'; @endphp
        <span class="badge {{ $type === 'public' ? 'bg-primary' : 'bg-secondary' }}">
            {{ $type === 'public' ? 'مؤسسة عمومية' : 'مؤسسة خاصة' }}
        </span>
    </h3>

    {{-- رسالة نجاح --}}
    @if(session('success'))
        <div class="alert alert-success text-center fw-bold">
            {{ session('success') }}
        </div>
    @endif

    @php
        // المرفقات — Club::$attachments is already an array (cast)
        $files = $enterprise->attachments ?? [];

        // 🔁 ترجمة أسماء وثائق المؤسسة (عمومية + خاصة)
        $documentsLabels = [
            // مؤسسة عمومية
            'written_request'        => 'الطلب الخطي',
            'agreement'              => 'الاتفاقية',
            'trainees_insurance'     => 'تأمين المتدربين',
            'beneficiaries_list'     => 'قوائم المستفيدين',
            // مؤسسة خاصة
            'registre_commerce'      => 'السجل التجاري',
            'statut'                 => 'القانون الأساسي للمؤسسة',
            'tax_number'             => 'البطاقة الجبائية',
            'bank_rib'               => 'شهادة RIB بنكي',
            'insurance_certificate'  => 'شهادة التأمين',
            'rules_book'             => 'الإتفاقية',
            'minutes_meeting'        => 'محضر الجمعية (إن وجد)',
            'exploitation_request'   => 'طلب الاستغلال',
        ];
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

     

        {{-- زر التعديل --}}
        <a href="{{ route('entreprise.dossier.edit') }}"
           class="btn btn-primary">
           ✏️ تعديل الملف
        </a>
    </div>

</div>
@endsection
