@extends('layouts.app')

@section('content')
<div class="container py-5" style="direction: rtl; text-align:right;">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">👑 إدارة المسؤولين</h3>
            <p class="text-muted mb-0 small">إدارة حسابات المسؤولين ومدققي QR</p>
        </div>
        <a href="{{ route('admins.create') }}" class="btn btn-primary btn-glow px-4">
            ➕ إضافة مسؤول جديد
        </a>
    </div>

    <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد</th>
                        <th>النوع</th>
                        <th>المجمع الرياضي</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($admins as $a)
                    <tr>
                        <td data-label="#" class="text-muted fw-semibold">{{ $a->id }}</td>
                        <td data-label="الاسم" class="fw-semibold">{{ $a->name }}</td>
                        <td data-label="البريد">{{ $a->email }}</td>

                        {{-- نوع المسؤول --}}
                        <td data-label="النوع">
                            @if($a->type === 'verify_qr')
                                <span class="badge rounded-pill type-badge verify-badge">📷 مدقق QR</span>
                            @else
                                <span class="badge rounded-pill type-badge admin-badge">👮 مسؤول كامل</span>
                            @endif
                        </td>

                        {{-- عرض اسم المجمع أو لا يوجد --}}
                        <td data-label="المجمع">
                            @if($a->complex_id && $a->complex)
                                <span class="badge rounded-pill complex-badge">{{ $a->complex->nom }}</span>
                            @else
                                <span class="text-muted">— بدون مجمع —</span>
                            @endif
                        </td>

                        <td data-label="إجراءات">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admins.edit', $a->id) }}"
                                   class="btn btn-sm btn-warning btn-action">✏ تعديل</a>

                                <form action="{{ route('admins.delete', $a->id) }}"
                                      method="POST"
                                      class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger btn-action"
                                            onclick="return confirm('هل تريد حذف هذا المسؤول؟')">
                                        🗑 حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            لا يوجد أي مسؤول بعد.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

{{-- ======================== STYLES MODERN CLAIR RESPONSIVE ======================== --}}
<style>
    body {
        background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .card-modern {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.7);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .btn-glow {
        background: linear-gradient(135deg, #4361ee, #4cc9f0);
        color: white;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 6px 18px rgba(67, 97, 238, 0.35);
    }

    .btn-glow:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(67, 97, 238, 0.45);
        color: white;
    }

    /* --- Tableau --- */
    .admin-table thead th {
        background: linear-gradient(135deg, #1e293b, #334155);
        color: #fff;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
        padding: 0.9rem 1rem;
        white-space: nowrap;
        border: none;
    }

    .admin-table tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-color: #eef2f7;
    }

    .admin-table tbody tr {
        transition: background 0.2s ease;
    }

    .admin-table tbody tr:hover {
        background: #f8fafc;
    }

    /* --- Badges --- */
    .type-badge {
        font-size: 0.78rem;
        padding: 0.45rem 0.8rem;
        font-weight: 600;
    }

    .admin-badge {
        background: rgba(67, 97, 238, 0.12);
        color: #4361ee;
        border: 1px solid rgba(67, 97, 238, 0.35);
    }

    .verify-badge {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .complex-badge {
        background: rgba(245, 158, 11, 0.12);
        color: #b45309;
        border: 1px solid rgba(245, 158, 11, 0.4);
        font-size: 0.78rem;
        padding: 0.45rem 0.8rem;
        font-weight: 600;
    }

    /* --- Boutons actions --- */
    .btn-action {
        border: none;
        border-radius: 0.6rem;
        font-size: 0.82rem;
        white-space: nowrap;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
    }

    /* --- Responsive cards on small screens --- */
    @media (max-width: 767.98px) {
        .table-responsive {
            border: 0;
        }
        .admin-table thead {
            display: none;
        }
        .admin-table,
        .admin-table tbody,
        .admin-table tr,
        .admin-table td {
            display: block;
            width: 100%;
        }
        .admin-table tbody tr {
            margin-bottom: 1rem;
            border: 1px solid #eef2f7;
            border-radius: 1rem;
            padding: 0.5rem;
        }
        .admin-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            border: none;
            border-bottom: 1px dashed #eef2f7;
            padding: 0.6rem 0.8rem;
        }
        .admin-table tbody td:last-child {
            border-bottom: none;
        }
        .admin-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #64748b;
            font-size: 0.75rem;
        }
        .admin-table tbody td .btn-action {
            padding: 0.4rem 0.9rem;
        }
    }
</style>
