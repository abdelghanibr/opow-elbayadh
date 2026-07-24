@extends('layouts.app')

@section('content')
<div class="container py-4" style="direction:rtl;text-align:right">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">🏟️ إدارة المركبات</h3>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            ➕ إضافة مركب
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <table class="table table-hover text-center align-middle" id="complexesTable">
        <thead class="table-dark">
            <tr>
                <th>الاسم</th>
                <th>الوصف</th>
                <th>السعة</th>
            
                <th>النوع</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
        @foreach($complexes as $c)
            <tr>
                <td>{{ $c->nom }}</td>
                <td>{{ $c->description ?? '—' }}</td>
                <td>{{ $c->capacite }}</td>
             
                <td>{{ $c->type ?? '—' }}</td>
                <td>

                    <button class="btn btn-warning btn-sm editBtn"
                        data-id="{{ $c->id }}"
                        data-nom="{{ $c->nom }}"
                        data-description="{{ $c->description }}"
                        data-type="{{ $c->type }}"
                    
                        data-capacite="{{ $c->capacite }}"
                        data-bs-toggle="modal"
                        data-bs-target="#editModal">
                        ✏ تعديل
                    </button>

                    <form action="{{ route('complexes.destroy',$c->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('هل تريد حذف المركب؟')">
                            🗑 حذف
                        </button>
                    </form>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>



<!-- Modal add -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">➕ إضافة مركب جديد</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('complexes.store') }}" method="POST">
        @csrf
        <div class="modal-body">

            <label>الاسم</label>
            <input type="text" name="nom" class="form-control" required>

            <label class="mt-2">الوصف</label>
            <textarea name="description" class="form-control"></textarea>

            <label class="mt-2">السعة</label>
            <input type="number" name="capacite" class="form-control" required>

            <label class="mt-2">السعر (دج)</label>
            <input type="number" step="0.01" name="prix" class="form-control" required>

            <label class="mt-2">النوع</label>
            <input type="text" name="type" class="form-control">

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-success">💾 حفظ</button>
        </div>
      </form>

    </div>
  </div>
</div>


<!-- Modal edit -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">✏ تعديل المركب</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="editForm" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-body">

            <label>الاسم</label>
            <input type="text" name="nom" id="edit_nom" class="form-control" required>

            <label class="mt-2">الوصف</label>
            <textarea name="description" id="edit_description" class="form-control"></textarea>

            <label class="mt-2">السعة</label>
            <input type="number" name="capacite" id="edit_capacite" class="form-control" required>

            <label class="mt-2">السعر (دج)</label>
            <input type="number" step="0.01" name="prix" id="edit_prix" class="form-control" required>

            <label class="mt-2">النوع</label>
            <input type="text" name="type" id="edit_type" class="form-control">

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-warning">تحديث ✏</button>
        </div>

      </form>

    </div>
  </div>
</div>

@endsection


@section('scripts')
<script>
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function () {

        document.getElementById('edit_nom').value = this.dataset.nom;
        document.getElementById('edit_description').value = this.dataset.description;
        document.getElementById('edit_type').value = this.dataset.type;
        document.getElementById('edit_prix').value = this.dataset.prix;
        document.getElementById('edit_capacite').value = this.dataset.capacite;

        document.getElementById('editForm').action = "/complexes/" + this.dataset.id;
    });
});
</script>
@endsection
