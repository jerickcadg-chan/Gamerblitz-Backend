@extends('layouts.app', [
    'activePage' => 'product',
])

@php
    $isEdit = isset($product);
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Halaman {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? 'Edit Data' : 'Tambah Data' }}</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
          @csrf
          @if($isEdit) @method('PUT') @endif

          <div class="form-group">
            <label for="name_input" class="required">Nama</label>
            <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                   name="name" id="name_input" placeholder="Masukkan Nama"
                   value="{{ old('name', $product->name ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            <label for="code_input" class="required">Kode</label>
            <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}"
                   name="code" id="code_input" placeholder="Masukkan Kode"
                   value="{{ old('code', $product->code ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'code'])
          </div>

          <div class="form-group">
            <label for="company_input" class="required">Perusahaan</label>
            <input type="text" class="form-control {{ $errors->has('company') ? ' is-invalid' : '' }}"
                   name="company" id="company_input" placeholder="Montoon"
                   value="{{ old('company', $product->company ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'company'])
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_user_input" class="required">Markup User (%)</label>
                <input type="number" min="0" class="form-control {{ $errors->has('markup_user') ? ' is-invalid' : '' }}"
                       name="markup_user" id="markup_user_input" placeholder="0"
                       value="{{ old('markup_user', $product->markup_user ?? '') }}" required>
                @include('alerts.feedback', ['field' => 'markup_user'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_reseller_input" class="required">Markup Reseller (%)</label>
                <input type="number" min="0" class="form-control {{ $errors->has('markup_reseller') ? ' is-invalid' : '' }}"
                       name="markup_reseller" id="markup_reseller_input" placeholder="0"
                       value="{{ old('markup_reseller', $product->markup_reseller ?? '') }}" required>
                @include('alerts.feedback', ['field' => 'markup_reseller'])
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="category_input" class="required">Kategori</label>
            <select class="form-control {{ $errors->has('product_category_id') ? ' is-invalid' : '' }}"
                    name="product_category_id" id="category_input" required>
              <option value="">Pilih kategori</option>
              @foreach (\App\Models\ProductCategory::all() as $category)
                <option value="{{ $category->id }}"
                  {{ (string) old('product_category_id', $product->product_category_id ?? '') === (string) $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'product_category_id'])
          </div>

          <div class="form-group">
            <label for="description_input" class="required">Deskripsi</label>
            <textarea class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }} tinymce"
                      name="description" id="description_input" placeholder="Masukkan Deskripsi">{{ old('description', $product->description ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'description'])
          </div>

          <div class="form-group">
            <label for="input_format_input" class="required">Format Input</label>
            <textarea class="form-control {{ $errors->has('input_format') ? ' is-invalid' : '' }}"
                      name="input_format" id="input_format_input" placeholder="Masukkan Format Input" required>{{ old('input_format', $product->input_format ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'input_format'])
          </div>

          <div class="form-group">
            <label for="how_to_order_input" class="required">Cara Order</label>
            <textarea class="form-control {{ $errors->has('how_to_order') ? ' is-invalid' : '' }} tinymce"
                      name="how_to_order" id="how_to_order_input" placeholder="Masukkan Cara Order">{{ old('how_to_order', $product->how_to_order ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'how_to_order'])
          </div>

          <div class="form-group">
            <label for="cover">Cover</label>
            <input type="file" name="cover" class="form-control {{ $errors->has('cover') ? ' is-invalid' : '' }}" accept="image/*">
            @include('alerts.feedback', ['field' => 'cover'])
            @if($isEdit && !empty($product->default_cover))
              <p class="d-block mt-2 text-small">Current</p>
              <a href="{{ asset($product->default_cover) }}" target="_blank">
                <img src="{{ asset($product->default_cover) }}" height="100" alt="image preview" />
              </a>
            @endif
            @include('alerts.feedback', ['field' => 'cover'])
          </div>

          <div class="form-group">
            <label for="picture" class="required">Gambar</label>
            <input type="file" name="picture" class="form-control {{ $errors->has('picture') ? ' is-invalid' : '' }}" accept="image/*">
            @include('alerts.feedback', ['field' => 'picture'])
            @if($isEdit && !empty($product->default_picture))
              <p class="d-block mt-2 text-small">Current</p>
              <a href="{{ asset($product->default_picture) }}" target="_blank">
                <img src="{{ asset($product->default_picture) }}" height="100" alt="image preview" />
              </a>
            @endif
            @include('alerts.feedback', ['field' => 'picture'])
          </div>

          @if($isEdit)
            <div class="form-group">
              <label class="required d-block">Status</label>
              <label class="me-3">
                <input type="radio" name="status" value="active"
                  {{ old('status', $product->status ?? 'active') === 'active' ? 'checked' : '' }}>
                Active
              </label>
              <label>
                <input type="radio" name="status" value="inactive"
                  {{ old('status', $product->status ?? 'active') === 'inactive' ? 'checked' : '' }}>
                Inactive
              </label>
              @include('alerts.feedback', ['field' => 'status'])
            </div>
          @else
            {{-- Saat create, paksa default active --}}
            <input type="hidden" name="status" value="active">
          @endif

          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ route('product.index') }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <x-tinymce-script />
@endpush
