@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right">

    <h3 class="mb-4">🛂 إدارة ملفات الدوسيي</h3>

    <div class="card p-4 shadow">

        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                     <th>الاسم</th>
                      <th>اللقب</th>
                    <th>الملف</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>

            <tbody>
                @foreach($dossiers as $d)
                <tr>
                    <td>{{ $d->id }}</td>

                    <td>{{ $d->user->name }}</td>
                       <td>{{ $d->user->firstname }}</td>
                          <td>{{ $d->user->lastname }}</td>

<td>
    {{-- صورة المستخدم --}}
    @if($d->user->photo)
        <a href="{{ $d->user->photo }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-info w-100 mb-1">
            📷 عرض الصورة
        </a>
    @else
        <span class="text-muted d-block">لا توجد صورة</span>
    @endif

    {{-- شهادة الميلاد --}}
    @if($d->user->birth_certificate)
        <a href="{{ $d->user->birth_certificate }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-primary w-100">
            📄 شهادة الميلاد
        </a>
    @else
        <span class="text-muted d-block">لا توجد شهادة</span>
    @endif
</td>



                 <td>
    {{-- حالة ملف غير مكتمل --}}
    @if(!$d->user->photo || !$d->user->birth_certificate)
        <span class="badge bg-info text-dark">ملف غير مكتمل</span>

    @elseif($d->etat == 'valid')
        <span class="badge bg-success">مقبول</span>

    @elseif($d->etat == 'refused')
        <span class="badge bg-danger">مرفوض</span>

    @elseif($d->etat == 'pending')
        <span class="badge bg-warning text-dark">في الانتظار</span>

    @else
        <span class="badge bg-secondary">{{ $d->etat }}</span>
    @endif
</td>

                    <td>

                        <form action="{{ route('dossier.valider', $d->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">✔ قبول</button>
                        </form>

                        <form action="{{ route('dossier.refuser', $d->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-danger btn-sm">✘ رفض</button>
                        </form>
                        
                       

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>
@endsection
