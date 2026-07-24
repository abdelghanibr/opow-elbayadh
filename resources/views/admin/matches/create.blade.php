@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card-modern rounded-4 shadow-lg">
                <div class="card-header text-center py-4">
                    <h3 class="fw-bold">➕ إضافة مباراة جديدة</h3>
                    <p class="small">قم بتحديد الفرق والمركب والتاريخ والوقت</p>
                </div>

                <div class="card-body p-4">

                    <form action="{{ route('matches.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">

                            <!-- Home team -->
                            <div class="col-md-6">
                                <label class="form-label">الفريق المستضيف *</label>
                                <select name="team_home_id"
                                        class="form-select form-control-modern @error('team_home_id') is-invalid @enderror">
                                    <option value="">-- اختر الفريق --</option>
                                    @foreach($teams as $t)
                                        <option value="{{ $t->id }}" {{ old('team_home_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                @error('team_home_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Away team -->
                            <div class="col-md-6">
                                <label class="form-label">الفريق الضيف *</label>
                                <select name="team_away_id"
                                        class="form-select form-control-modern @error('team_away_id') is-invalid @enderror">
                                    <option value="">-- اختر الفريق --</option>
                                    @foreach($teams as $t)
                                        <option value="{{ $t->id }}" {{ old('team_away_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                @error('team_away_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Complex -->
                            <div class="col-md-6">
                                <label class="form-label">المركب *</label>
                                <select name="complex_id"
                                        class="form-select form-control-modern @error('complex_id') is-invalid @enderror">
                                    <option value="">-- اختر المركب --</option>
                                    @foreach($complexes as $c)
                                        <option value="{{ $c->id }}" {{ old('complex_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                                    @endforeach
                                </select>
                                @error('complex_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date -->
                            <div class="col-md-6">
                                <label class="form-label">تاريخ المباراة *</label>
                                <input type="text"
                                       name="match_date"
                                       class="form-control js-date-fr form-control-modern @error('match_date') is-invalid @enderror"
                                       value="{{ old('match_date') }}"
                                       required>
                                @error('match_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Time -->
                            <div class="col-md-6">
                                <label class="form-label">الوقت *</label>
                                <input type="time"
                                       name="match_time"
                                       class="form-control form-control-modern @error('match_time') is-invalid @enderror"
                                       value="{{ old('match_time') }}"
                                       required>
                                @error('match_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Competition -->
                            <div class="col-md-6">
                                <label class="form-label">نوع المنافسة</label>
                                <input name="competition"
                                       class="form-control form-control-modern @error('competition') is-invalid @enderror"
                                       placeholder="مثال: دوري - كأس"
                                       value="{{ old('competition') }}">
                                @error('competition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Match Status -->
                            <div class="col-md-6">
                                <label class="form-label">حالة المباراة *</label>
                                <select name="status"
                                        class="form-select form-control-modern @error('status') is-invalid @enderror"
                                        required>
                                    <option value="">-- اختر الحالة --</option>
                                    <option value="scheduled"  {{ old('status')=='scheduled'  ? 'selected':'' }}>مجدولة</option>
                                    <option value="active"     {{ old('status')=='active'     ? 'selected':'' }}>نشطة</option>
                                    <option value="pending"    {{ old('status')=='pending'    ? 'selected':'' }}>قيد الانتظار</option>
                                    <option value="sold_out"   {{ old('status')=='sold_out'   ? 'selected':'' }}>مباعة بالكامل</option>
                                    <option value="finished"   {{ old('status')=='finished'   ? 'selected':'' }}>مكتملة</option>
                                    <option value="cancelled"  {{ old('status')=='cancelled'  ? 'selected':'' }}>ملغاة</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-4">
                            <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary rounded-pill px-5">رجوع</a>
                            <button class="btn btn-primary btn-glow rounded-pill px-5">💾 حفظ المباراة</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
