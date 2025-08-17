@extends('layouts.admin', [
    'activePage' => 'product_item_category'
])

@php
  $page = isset($meta) ? 'Tambah' : 'Edit';
@endphp

@section('content')
  <div class="row">
    <div class="col-12">
      <nav aria-label="breadcrumb" class="float-md-right float-sm-none">
        <ol class="breadcrumb pl-0">
          <li class="breadcrumb-item">
            <a href="{{ route('product_item_category.index') }}">Home</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="{{ route('product_item_category.index') }}">Kategori Item</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a
              href="{{ route('product_item_category.show', ['product_item_category' => $productItemCategory]) }}">{{ $title }}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Atur Ikon</li>
        </ol>
      </nav>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <h4 class="font-weight-bold">{{ $title }}</h4>
      <hr/>
      <form
        method="POST"
        action="{{ $actionLink }}"
        enctype="multipart/form-data"
      >
        @isset($meta)
          @method('PUT')
        @endisset
        @csrf
        <div class="form-group">
          <label for="input_min_price" class="required">Harga Minimal</label>
          <input
            type="number"
            name="min_price"
            class="form-control {{ $errors->has('min_price') ? ' is-invalid' : '' }}"
            id="input_min_price"
            placeholder="Masukkan harga minimal"
            value="{{ old('min_price', @$meta->min_price) }}"
            required
          />
          @include('alerts.feedback', ['field' => 'min_price'])
        </div>
        <div class="form-group">
          <label for="input_picture" @if(!isset($meta)) class="required" @endif>Ikon</label>
          <input
            type="file"
            name="picture"
            class="form-control {{ $errors->has('picture') ? ' is-invalid' : '' }}"
            id="input_picture"
            value="{{ old('picture') }}"
            accept="image/*"
            @if(!isset($meta)) required @endif
          />
          @include('alerts.feedback', ['field' => 'picture'])
        </div>

        <div class="form-group">
          <div class="form-group">
            <input type="text" class="form-control mb-3" id="search-input" placeholder="Cari produk item...">
          </div>

          <table class="table table-bordered table-hover" id="product-table">
            <thead>
            <tr>
              <th width="50">
                <input type="checkbox" id="select-all">
              </th>
              <th>Nama Produk</th>
              <th>Harga</th>
              <th>Kategori</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($productItems as $item)
              <tr>
                <td>
                  <input type="checkbox" name="product_item_ids[]" value="{{ $item->id }}" class="product-checkbox">
                </td>
                <td>{{ $item->product->name }} - {{ $item->name }}</td>
                <td>{{ rp_format($item->price_public) }}</td>
                <td>{{ $item->itemCategory?->name }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>

        <div class="form-group">
          <input type="hidden" name="product_item_category_id" value="{{ $productItemCategory->id }}"/>
          <button class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('js')
  <script>
    document.getElementById("search-input").addEventListener("input", function () {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll("#product-table tbody tr");

      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    });

    document.getElementById("select-all").addEventListener("change", function () {
      const checkboxes = document.querySelectorAll(".product-checkbox");
      checkboxes.forEach(cb => cb.checked = this.checked);
    });
  </script>
@endpush
