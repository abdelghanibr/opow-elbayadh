@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction: rtl; text-align:right">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">📋 إدارة الأنشطة</h3>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            ➕ إضافة نشاط
        </button>
    </div>

    {{-- جدول الأنشطة --}}
    <table class="table table-striped text-center align-middle" id="activitiesTable">
        <thead class="table-dark">
        <tr>
            <th>الأيقونة</th>
            <th>العنوان</th>
            <th>الفئة</th>
            <th>اللون</th>
            <th>التاريخ</th>
            <th>إجراءات</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($activities as $a)
        <tr>
            <td>
                @if($a->icon)
                    <img src="{{ $a->icon }}" width="50" height="50" class="rounded">
                @else
                    <span>—</span>
                @endif
            </td>
            <td>{{ $a->title }}</td>
            <td>{{ $a->category ?? '—' }}</td>
            <td>
                <span class="rounded-circle d-inline-block" style="width:22px;height:22px;background:{{ $a->color }}"></span>
            </td>
            <td>{{ $a->created_at ? $a->created_at->format('Y-m-d') : '—' }}</td>

            <td>
                <!-- Edit -->
                <button class="btn btn-warning btn-sm editBtn"
                        data-id="{{ $a->id }}"
                        data-title="{{ $a->title }}"
                        data-description="{{ $a->description }}"
                        data-category="{{ $a->category }}"
                        data-color="{{ $a->color }}"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal">
                    ✏ تعديل
                </button>

                <!-- Delete -->
                <form action="{{ route('activities.destroy', $a->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('حذف النشاط؟')">🗑 حذف</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

</div>


<!-- Modal إضافة نشاط -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">➕ إضافة نشاط جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <label class="fw-bold">عنوان النشاط</label>
                    <input type="text" name="title" class="form-control" required>

                    <label class="fw-bold mt-2">الوصف</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>

                    <label class="fw-bold mt-2">الفئة</label>
                    <input type="text" name="category" class="form-control">

                    <label class="fw-bold mt-2">اللون</label>
                    <input type="color" name="color" class="form-control form-control-color">

                    <label class="fw-bold mt-2">أيقونة النشاط</label>
                    <input type="file" name="icon" class="form-control" accept="image/*" required>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">💾 حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal تعديل نشاط -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">✏ تعديل النشاط</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <label class="fw-bold">عنوان النشاط</label>
                    <input type="text" id="edit_title" name="title" class="form-control" required>

                    <label class="fw-bold mt-2">الوصف</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="2"></textarea>

                    <label class="fw-bold mt-2">الفئة</label>
                    <input type="text" id="edit_category" name="category" class="form-control">

                    <label class="fw-bold mt-2">اللون</label>
                    <input type="color" id="edit_color" name="color" class="form-control form-control-color">

                    <label class="fw-bold mt-2">تغيير الأيقونة</label>
                    <input type="file" id="edit_icon" name="icon" class="form-control" accept="image/*">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">✏ تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script>
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        let id = this.dataset.id;

        document.getElementById('edit_title').value = this.dataset.title;
        document.getElementById('edit_description').value = this.dataset.description;
        document.getElementById('edit_category').value = this.dataset.category;
        document.getElementById('edit_color').value = this.dataset.color;

        document.getElementById('editForm').action = "/activities/" + id;
    });
});
</script>
@endsection
