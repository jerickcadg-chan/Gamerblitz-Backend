@extends('layouts.app', [
'activePage' => 'account',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('account.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $storeLink }}" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label for="title_input" class="required">Name</label>
            <input type="text" class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" id="title_input" placeholder="Enter Name Akun" value="{{ old('title') }}">
            @include('alerts.feedback', ['field' => 'title'])
          </div>
          <div class="form-group">
            <label for="code_input" class="required">Code</label>
            <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" id="code_input" placeholder="Enter Code Akun" value="{{ old('code') }}">
            @include('alerts.feedback', ['field' => 'code'])
          </div>
          <div class="form-group">
            <label for="description_input" class="required">Description</label>
            <textarea class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }} tinymce" name="description" id="description_input" placeholder="Enter Description Akun">{{ old('description') }}</textarea>
            @include('alerts.feedback', ['field' => 'description'])
          </div>
          <div class="form-group">
            <label for="winrate_input" class="required">Winrate</label>
            <input type="number" step="0.1" class="form-control {{ $errors->has('winrate') ? ' is-invalid' : '' }}" name="winrate" id="winrate_input" placeholder="Enter Winrate Akun" value="{{ old('winrate') }}">
            @include('alerts.feedback', ['field' => 'winrate'])
          </div>
          <div class="form-group">
            <label for="skin_input" class="required">Skin</label>
            <input type="number" class="form-control {{ $errors->has('skin') ? ' is-invalid' : '' }}" name="skin" id="skin_input" placeholder="Enter Jumlah Skin Akun" value="{{ old('skin') }}">
            @include('alerts.feedback', ['field' => 'skin'])
          </div>
          <div class="form-group">
            <label for="heroes_input" class="required">Heroes</label>
            <input type="number" class="form-control {{ $errors->has('heroes') ? ' is-invalid' : '' }}" name="heroes" id="heroes_input" placeholder="Enter Jumlah Heroes Akun" value="{{ old('heroes') }}">
            @include('alerts.feedback', ['field' => 'heroes'])
          </div>
          <div class="form-group">
            <label for="price_input" class="required">Price</label>
            <input type="number" step="0.1" class="form-control {{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" id="price_input" placeholder="Enter Harga Akun" value="{{ old('price') }}">
            @include('alerts.feedback', ['field' => 'price'])
          </div>
          <div class="form-group">
            <label for="information_input" class="required">Information</label>
            <textarea class="form-control {{ $errors->has('information') ? ' is-invalid' : '' }}" name="information" id="information_input" placeholder="Enter Informasi Akun">{{ old('information') }}</textarea>
            @include('alerts.feedback', ['field' => 'information'])
          </div>
          <div class="form-group">
            <label for="picture" class="required">Gambar kover</label>
            <input type="file" name="cover_picture[]" class="form-control" accept="image/*" value="{{ old('cover_picture') }}" multiple>
            @include('alerts.feedback', ['field' => 'cover_picture'])
          </div>
          <div class="form-group">
            <label class="form-check-label" for="discount_checkbox">
            <input class="form-check-input" type="checkbox" id="discount_checkbox" name="discount" value="1" {{ old('discount') ? 'checked' : '' }}>
              Createkan harga coret (harga ini akan muncul di halaman produk akun)
            </label>
          </div>
          <div id="discount_form" style="display: none;">
            <div class="form-group">
              <label for="discount_type" class="required">Tipe diskon</label>
              <select class="form-control {{ $errors->has('discount_type') ? ' is-invalid' : '' }}" name="discount_type" id="discount_type">
                <option value="">Pilih tipe diskon</option>
                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                <option value="nominal" {{ old('discount_type') == 'nominal' ? 'selected' : '' }}>Nominal</option>
              </select>
              @include('alerts.feedback', ['field' => 'discount_type'])
            </div>
            <div class="form-group">
              <label for="discount_amount" class="required">Jumlah diskon</label>
              <input type="number" step="0.1" class="form-control {{ $errors->has('discount_amount') ? ' is-invalid' : '' }}" name="discount_amount" id="discount_amount" placeholder="Enter Discount Amount" value="{{ old('discount_amount') }}">
              @include('alerts.feedback', ['field' => 'discount_amount'])
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <script src="https://cdn.tiny.cloud/1/vs7rit0kmvgxaum7jh7z3nsu8rytp668mu1agxyhwvspvk6p/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
  <script>
    tinymce.init({
      selector:'textarea.tinymce',
      height: 300
    });
    document.addEventListener('DOMContentLoaded', function() {
      const discountCheckbox = document.getElementById('discount_checkbox');
      const discountForm = document.getElementById('discount_form');

      function toggleDiscountForm() {
        if (discountCheckbox.checked) {
          discountForm.style.display = 'block';
        } else {
          discountForm.style.display = 'none';
        }
      }

      discountCheckbox.addEventListener('change', toggleDiscountForm);
      toggleDiscountForm(); // Initial check
    });
  </script>
@endpush

