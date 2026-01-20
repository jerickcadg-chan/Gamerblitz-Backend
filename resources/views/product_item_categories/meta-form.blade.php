@extends('layouts.app', [
    'activePage' => 'product_item_category'
])

@php
  $page = isset($meta) ? 'Create' : 'Edit';
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_item_category.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
      </ol>
    </nav>
  </div>

  <div class="card">
    <div class="card-body">
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
          <label for="input_picture">Icon</label>
          <input
            type="file"
            name="picture"
            class="form-control {{ $errors->has('picture') ? ' is-invalid' : '' }}"
            id="input_picture"
            value="{{ old('picture') }}"
            accept="image/*"
          />
          @include('alerts.feedback', ['field' => 'picture'])
        </div>

        <div class="form-group">
          <div class="form-group">
            <input type="text" class="form-control mb-3" id="search-input" placeholder="Search product item...">
          </div>

          <table class="table table-bordered table-hover" id="product-table">
            <thead>
            <tr>
              <th width="50">
                <input type="checkbox" id="select-all">
              </th>
              <th>Product Name</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($productItems as $item)
              <tr>
                <td>
                  <input
                    type="checkbox"
                    name="product_item_ids[]"
                    value="{{ $item->id }}"
                    class="product-checkbox"
                    {{ isset($meta) && $meta->productItems->pluck('id')->contains($item->id) ? 'checked' : '' }}
                  >
                </td>
                <td>{{ $item->product->name }} - {{ $item->name }}</td>
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

