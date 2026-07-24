@extends('layouts.app')
@include('admin.partials.theme-admin')

@section('content')
<div class="container py-5" style="direction: rtl;">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card-modern rounded-4 shadow-lg">
                <div class="card-header text-center py-4">
                    <h3 class="fw-bold">✏️ تعديل المباراة</h3>
                </div>

                <form action="{{ route('matches.update', $match->id) }}" method="POST" class="card-body p-4">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        <!-- الفريق المستضيف -->
                        <div class="col-md-6">
                            <label class="form-label">الفريق المستضيف *</label>
                            <select name="team_home_id"
                                    class="form-select form-control-modern @error('team_home_id') is-invalid @enderror">
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}"
                                        {{ old('team_home_id', $match->team_home_id) == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('team_home_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الفريق الضيف -->
                        <div class="col-md-6">
                            <label class="form-label">الفريق الضيف *</label>
                            <select name="team_away_id"
                                    class="form-select form-control-modern @error('team_away_id') is-invalid @enderror">
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}"
                                        {{ old('team_away_id', $match->team_away_id) == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('team_away_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- المركب -->
                        <div class="col-md-6">
                            <label class="form-label">المركب *</label>
                            <select name="complex_id"
                                    class="form-select form-control-modern @error('complex_id') is-invalid @enderror">
                                @foreach($complexes as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('complex_id', $match->complex_id) == $c->id ? 'selected' : '' }}>
                                        {{ $c->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('complex_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- التاريخ -->
                        <div class="col-md-6">
                            <label class="form-label">تاريخ المباراة *</label>
                            <input type="text"
                                   name="match_date"
                                   class="form-control js-date-fr form-control-modern @error('match_date') is-invalid @enderror"
                                   value="{{ old('match_date', $match->match_date) }}"
                                   required>
                            @error('match_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الوقت -->
                        <div class="col-md-6">
                            <label class="form-label">الوقت *</label>
                            <input type="time"
                                   name="match_time"
                                   class="form-control form-control-modern @error('match_time') is-invalid @enderror"
                                   value="{{ old('match_time', $match->match_time) }}"
                                   required>
                            @error('match_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- المنافسة -->
                        <div class="col-md-6">
                            <label class="form-label">نوع المنافسة</label>
                            <input type="text"
                                   name="competition"
                                   class="form-control form-control-modern @error('competition') is-invalid @enderror"
                                   value="{{ old('competition', $match->competition) }}"
                                   placeholder="مثال: دوري - كأس">
                            @error('competition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- حالة المباراة -->
                        <div class="col-md-6">
                            <label class="form-label">حالة المباراة *</label>
                            <select name="status"
                                    class="form-select form-control-modern @error('status') is-invalid @enderror"
                                    required>
                                <option value="scheduled" {{ old('status', $match->status) == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                <option value="active" {{ old('status', $match->status) == 'active' ? 'selected' : '' }}>نشطة</option>
                                <option value="pending" {{ old('status', $match->status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="sold_out" {{ old('status', $match->status) == 'sold_out' ? 'selected' : '' }}>مباعة بالكامل</option>
                                <option value="finished" {{ old('status', $match->status) == 'finished' ? 'selected' : '' }}>مكتملة</option>
                                <option value="cancelled" {{ old('status', $match->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <a href="{{ route('matches.index') }}" class="btn btn-outline-secondary rounded-pill px-5">
                            رجوع
                        </a>
                        <button class="btn btn-primary btn-glow rounded-pill px-5">
                            💾 حفظ التعديلات
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>
@endsection
