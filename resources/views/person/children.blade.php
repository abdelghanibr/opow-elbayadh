@extends('layouts.app')

@section('content')
<style>
    body { font-family: "Cairo", sans-serif !important; }
    .dash-box { background: #ffffff; border-radius: 16px; padding: 25px; box-shadow: 0 4px 16px rgba(0,0,0,0.08);}
    .dash-card { border-radius: 14px; padding: 20px; background: #f8fdf9; border: 1px solid #d6f5e1;
                 text-align: center; transition:.25s; height:100%;}
    .dash-card:hover { transform: translateY(-4px); box-shadow:0 4px 14px rgba(0,0,0,0.1);}
    .btn-main { background:#1b5e20!important; color:#fff; border-radius:10px; padding:8px 18px; font-weight:700;}
    .badge-soft { font-size:0.85rem; padding:.4em .8em; border-radius:50rem; font-weight:700; }
</style>

<div class="container py-4" style="direction: rtl; text-align:right">

    <div class="dash-box mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="mb-1">👶 أبنائي</h3>
            <p class="text-muted mb-0">
                سجّل أبناءك ثم أكمل ملف كل واحد (4 خطوات) لتمكينهم من حجز المقاعد.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('profile.step', 1) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-user"></i> ملفي الشخصي
            </a>
            <form action="{{ route('children.store') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-main">
                    <i class="fa-solid fa-child-reaching"></i> إضافة طفل جديد
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info rounded-4">{{ session('info') }}</div>
    @endif

    @if($children->isEmpty())
        <div class="dash-box text-center py-5">
            <i class="fa-solid fa-baby" style="font-size:3rem; color:#cbd5e1;"></i>
            <h5 class="mt-3 text-muted">لا يوجد أطفال مسجلون بعد</h5>
            <p class="text-muted">انقر على «إضافة طفل جديد» ثم أكمل معلوماته في الخطوات 1 إلى 4.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($children as $child)
                <div class="col-md-6 col-lg-4">
                    <div class="dash-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($child->photo)
                                <img src="{{ asset($child->photo) }}" alt="{{ $child->firstname }}"
                                     style="width:56px; height:56px; border-radius:50%; object-fit:cover; border:3px solid #d6f5e1;">
                            @else
                                <div style="width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,#d6f5e1,#a7f3d0); display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:900; color:#1b5e20; border:3px solid #d6f5e1;">
                                    {{ mb_substr($child->firstname ?? 'ط', 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h5 class="mb-0">{{ $child->firstname ?? '—' }} {{ $child->lastname ?? '' }}</h5>
                                <small class="text-muted">
                                    {{ $child->birth_date ? \Carbon\Carbon::parse($child->birth_date)->age . ' سنة' : '—' }}
                                    @if($child->ageCategory)
                                        • {{ $child->ageCategory->name ?? '' }}
                                    @endif
                                </small>
                            </div>
                            <span class="badge badge-soft
                                {{ ($child->dossier && $child->dossier->etat === 'approved') ? 'bg-success text-white'
                                   : (($child->dossier && $child->dossier->etat === 'rejected') ? 'bg-danger text-white'
                                      : 'bg-warning text-dark') }}">
                                {{ ($child->dossier && $child->dossier->etat === 'approved') ? '✔ مقبول'
                                   : (($child->dossier && $child->dossier->etat === 'rejected') ? '✖ مرفوض'
                                      : '⏳ قيد التحقق') }}
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            @if($child->firstname)
                                <a href="{{ route('children.edit', $child->id) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fa-solid fa-pen"></i> تعديل المعلومات
                                </a>
                            @else
                                <a href="{{ route('children.edit', $child->id) }}" class="btn btn-main btn-sm">
                                    <i class="fa-solid fa-list-check"></i> إكمال التسجيل (الخطوات)
                                </a>
                            @endif

                            @if($child->dossier && $child->dossier->etat === 'approved')
                                <a href="{{ route('children.reserve', $child->id) }}" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-calendar-check"></i> حجز مقعد 🎟️
                                </a>
                            @else
                                <span class="btn btn-secondary btn-sm disabled opacity-50">
                                    <i class="fa-solid fa-lock"></i> الحجز بعد مصادقة الملف
                                </span>
                            @endif

                            @if($child->dossier && $child->dossier->attachments)
                                <a href="{{ route('dossier.print', $child->dossier->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-folder-open"></i> عرض الملف
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
