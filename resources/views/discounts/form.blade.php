@extends('layouts.app', [
    'activePage' => 'discount',
])

@php
  $isEdit = isset($discount);

  $selectedProductIds = collect($discount?->products ?? [])
      ->where('productable_type', \App\Models\Product::class)
      ->pluck('productable_id')
      ->all();

  $selectedProductItemIds = collect($discount?->products ?? [])
      ->where('productable_type', \App\Models\ProductItem::class)
      ->pluck('productable_id')
      ->all();

  $allProducts = $products ?? \App\Models\Product::all();
  $allProductItems = $productItems ?? \App\Models\ProductItem::with('product')->get();
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Halaman {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('discount.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? 'Edit Data' : 'Tambah Data' }}</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $formAction }}">
          @csrf
          @if($isEdit) @method('PUT') @endif

          <div class="form-group">
            <label for="input_name" class="required">Nama Diskon</label>
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                   id="input_name" placeholder="Masukkan nama"
                   value="{{ old('name', $discount->name ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            <label for="input_code" class="required">Kode Diskon</label>
            <input type="text" name="code" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}"
                   id="input_code" placeholder="Kosongi apabila tidak ada kode"
                   value="{{ old('code', $discount->code ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'code'])
          </div>

          <div class="row">
            <div class="form-group col-md-3">
              <label for="input_disc_type" class="required">Jenis Diskon</label>
              <select class="form-control {{ $errors->has('disc_type') ? ' is-invalid' : '' }}"
                      id="input_disc_type" name="disc_type" required>
                @foreach (config('array.discount.disc_type') as $disc)
                  <option value="{{ $disc['value'] }}"
                    {{ old('disc_type', $discount->disc_type ?? null) == $disc['value'] ? 'selected' : null }}>
                    {{ $disc['desc'] }}
                  </option>
                @endforeach
              </select>
              @include('alerts.feedback', ['field' => 'disc_type'])
            </div>

            <div class="form-group col-md-5">
              <label for="input_nominal" class="required">Nominal Diskon</label>
              <input type="number" name="nominal" class="form-control {{ $errors->has('nominal') ? ' is-invalid' : '' }}"
                     id="input_nominal" placeholder="Masukkan nominal sesuai jenis diskon"
                     value="{{ old('nominal', $discount->nominal ?? '') }}" required>
              @include('alerts.feedback', ['field' => 'nominal'])
            </div>

            <div class="form-group col-md-4">
              <label for="input_maximum" class="required">Maksimal Penggunaan</label>
              <input type="number" min="1" name="maximum" class="form-control {{ $errors->has('maximum') ? ' is-invalid' : '' }}"
                     id="input_maximum" placeholder="Masukkan maksimal penggunaan diskon"
                     value="{{ old('maximum', $discount->maximum ?? '') }}" required>
              @include('alerts.feedback', ['field' => 'maximum'])
            </div>
          </div>

          <div class="form-group">
            <label for="description_input">Deskripsi</label>
            <textarea class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }} tinymce"
                      name="description" id="description_input" placeholder="Masukkan Deskripsi">{{ old('description', $discount->description ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'description'])
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label for="input_start_date" class="required">Tanggal Mulai</label>
              <input type="date" name="start_date" class="form-control {{ $errors->has('start_date') ? ' is-invalid' : '' }}"
                     id="input_start_date" placeholder="Masukkan tanggal mulai diskon"
                     value="{{ old('start_date', isset($discount?->start_date) ? parse_date_format($discount->start_date) : '') }}" required>
              @include('alerts.feedback', ['field' => 'start_date'])
            </div>
            <div class="form-group col-md-6">
              <label for="input_end_date" class="required">Tanggal Selesai</label>
              <input type="date" name="end_date" class="form-control {{ $errors->has('end_date') ? ' is-invalid' : '' }}"
                     id="input_end_date" placeholder="Masukkan tanggal berakhir diskon"
                     value="{{ old('end_date', isset($discount?->end_date) ? parse_date_format($discount->end_date) : '') }}" required>
              @include('alerts.feedback', ['field' => 'end_date'])
            </div>
          </div>

          <div class="form-group">
            <label for="input_product_type" class="required">Jenis Produk</label>
            <select class="form-control {{ $errors->has('product_type') ? ' is-invalid' : '' }}"
                    id="input_product_type" name="product_type" onchange="checkProductType()" required>
              @foreach (config('array.discount.product_type') as $disc)
                <option value="{{ $disc['value'] }}"
                  {{ old('product_type', $discount->product_type ?? null) == $disc['value'] ? 'selected' : null }}>
                  {{ $disc['desc'] }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'product_type'])
          </div>

          {{-- PRODUCT (by product) --}}
          <div id="product_type_div" style="display: none">
            <h4>Pilih Produk</h4>
            <hr>
            <div class="row mb-2">
              <div class="col-md-4">
                <input type="text" id="product_search" placeholder="Cari produk" class="form-control" oninput="search(this)">
              </div>
            </div>

            @foreach ($allProducts as $product)
              <div class="item">
                <input type="checkbox" name="product_id[]"
                       value="{{ $product->id }}" class="my-2"
                  {{ in_array($product->id, old('product_id', $selectedProductIds)) ? 'checked' : '' }}>
                <span>{{ $product->name }}</span>
                <br>
              </div>
            @endforeach
            <hr>
          </div>

          {{-- PRODUCT ITEM (by item) --}}
          <div id="product_item_div" style="display: none">
            <h4>Pilih Item Produk</h4>
            <hr>
            <div class="row mb-2">
              <div class="col-md-4">
                <input type="text" id="product_item_search" placeholder="Cari produk" class="form-control" oninput="search(this)">
              </div>
            </div>

            @foreach ($allProductItems as $product_item)
              <div class="item">
                <input type="checkbox" name="product_item_id[]"
                       value="{{ $product_item->id }}" class="my-2"
                  {{ in_array($product_item->id, old('product_item_id', $selectedProductItemIds)) ? 'checked' : '' }}>
                <span>{{ $product_item?->product?->name }} - {{ $product_item?->name }}</span>
                <br>
              </div>
            @endforeach
            <hr>
          </div>

          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <x-tinymce-script />
  <script>
    document.addEventListener('DOMContentLoaded', checkProductType);

    function checkProductType() {
      let input = document.getElementById('input_product_type').value;
      let product_type = $('#product_type_div');
      let product_item = $('#product_item_div');

      if (input === 'all') {
        product_type.hide();
        product_item.hide();
      } else if (input === 'product_type') {
        product_type.show();
        product_item.hide();
      } else if (input === 'product_item') {
        product_type.hide();
        product_item.show();
      }
    }

    function search(el) {
      const filter = el.value.toUpperCase();
      const items = $(el).closest('.card-body').find('.item');
      for (let i = 0; i < items.length; i++) {
        const span = items[i].getElementsByTagName("span")[0];
        const txtValue = span.textContent || span.innerText;
        items[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1) ? "" : "none";
      }
    }
  </script>
@endpush
