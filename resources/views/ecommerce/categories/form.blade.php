@extends('layouts.app', [
    'activePage' => 'ecommerce_category',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ $indexLink }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ isset($category) ? 'Edit' : 'Create' }}</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form action="{{ $formAction }}" method="POST">
          @csrf
          @if(isset($category))
            @method('PUT')
          @endif

          <div class="form-group">
            <label for="name">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
              value="{{ old('name', $category->name ?? '') }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <div class="form-check">
              <label class="form-check-label">
                <input type="checkbox" class="form-check-input" name="is_active" value="1"
                  {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
                Active
              </label>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Update' : 'Create' }}</button>
            <a href="{{ $indexLink }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
