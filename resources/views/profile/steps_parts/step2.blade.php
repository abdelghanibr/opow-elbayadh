<form action="{{ route('profile.step.save', 2) }}" method="POST">
    @csrf

    <h4 class="mb-4 fw-bold">معلومات الولي</h4>

    <div class="row">

        <!-- اسم الولي -->
        <div class="col-md-6 mb-3">
            <label class="form-label">اسم الولي</label>
            <input type="text" name="parent_firstname"
                class="form-control @error('parent_firstname') is-invalid @enderror"
                value="{{ old('parent_firstname', $person->parent_firstname) }}">
            @error('parent_firstname')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- لقب الولي -->
        <div class="col-md-6 mb-3">
            <label class="form-label">لقب الولي</label>
            <input type="text" name="parent_lastname"
                class="form-control @error('parent_lastname') is-invalid @enderror"
                value="{{ old('parent_lastname', $person->parent_lastname) }}">
            @error('parent_lastname')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- تاريخ ميلاد الولي -->
       

        <!-- مكان ازدياد الولي -->
        

        <!-- رقم هاتف الولي -->
        <div class="col-md-6 mb-3">
            <label class="form-label">رقم هاتف الولي</label>
            <input type="text" name="parent_phone"
                class="form-control @error('parent_phone') is-invalid @enderror"
                value="{{ old('parent_phone', $person->parent_phone) }}">
            @error('parent_phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- جنس الولي -->
       

    </div>

    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('profile.step', 1) }}" class="btn btn-secondary px-4">السابق</a>
        <button class="btn btn-success px-4">التالي</button>
    </div>

</form>
