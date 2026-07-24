@extends('layouts.app')

@section('content')
<div class="container-fluid" style="direction: rtl">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="fw-bold mb-0">📂 أنواع الرياضات</h>

        <a href="{{ route('activity-categories.create') }}"
           class="btn btn-primary">
        إضافة نوع جديد
        </a>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success fw-bold">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <table id="capacityTable"
                   class="table table-bordered table-hover align-middle text-center w-100">

                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                    
                          <th>اسم النوع</th>
                       
                        <th>اللون</th>
                        <th>تاريخ الإنشاء</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>

                        <td class="fw-bold">
                            {{ $cat->name }}
                        </td>

                   

                        <td>
                            @if($cat->color)
                                <span class="badge px-3"
                                      style="background: {{ $cat->color }}">
                                    {{ $cat->color }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            {{ $cat->created_at
                                ? $cat->created_at->format('Y-m-d')
                                : '—' }}
                        </td>

                        <td class="text-nowrap">
                            <a href="{{ route('activity-categories.edit', $cat) }}"
                               class="btn btn-sm btn-warning"
                               title="تعديل">
                                ✏️
                            </a>

                            <form method="POST"
                                  action="{{ route('activity-categories.destroy', $cat) }}"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('هل أنت متأكد من الحذف؟')"
                                        class="btn btn-sm btn-danger"
                                        title="حذف">
                                    🗑
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection

{{-- ================= DATATABLE ================= --}}
@push('js')
@include('admin.partials.datatable-script', ['tableId' => '#capacityTable'])
@endpush
