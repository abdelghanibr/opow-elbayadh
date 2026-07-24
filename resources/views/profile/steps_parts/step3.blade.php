<form action="{{ route('profile.step.save', 3) }}" method="POST">
    @csrf

    <h4 class="mb-4 fw-bold">معلومات إضافية</h4>

    <div class="row">

        <!-- رقم الهاتف -->
        <div class="col-md-6 mb-3">
            <label class="form-label">رقم الهاتف</label>
            <input type="text" name="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone', $person->phone) }}">
            @error('phone')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <!-- الولاية -->
        
@if ($user->type == 'club' || $user->type == 'company')
    <div class="mb-3">
        <label class="form-label fw-bold">الفئة داخل النادي / المؤسسة</label>
        <select name="education" class="form-select" required>
            <option value="">-- اختر --</option>
            <option value="مسير" {{ old('education', $person->education ?? '') == 'مسير' ? 'selected' : '' }}>مسير</option>
            <option value="مدرب" {{ old('education', $person->education ?? '') == 'مدرب' ? 'selected' : '' }}>مدرب</option>
            <option value="لاعب" {{ old('education', $person->education ?? '') == 'لاعب' ? 'selected' : '' }}>لاعب</option>
            <option value="آخر" {{ old('education', $person->education ?? '') == 'آخر' ? 'selected' : '' }}>آخر</option>
        </select>
        @error('education')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
    </div>
@endif
          

        <!-- العنوان -->
        <div class="col-12 mb-3">
            <label class="form-label">العنوان</label>
            <input type="text" name="address"
                class="form-control @error('address') is-invalid @enderror"
                value="{{ old('address', $person->address) }}">
            @error('address')
                <div class="form-error text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <!-- النشاط المفضل -->
   


        <!-- المستوى الدراسي -->
       

    </div>


    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('profile.step', 2) }}" class="btn btn-secondary px-4">السابق</a>
        <button class="btn btn-success px-4">التالي</button>
    </div>

</form>
