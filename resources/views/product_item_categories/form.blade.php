@extends('layouts.app', [
    'activePage' => 'product_item_category',
])

@php
  $page = isset($productItemCategory) ? 'Create' : 'Edit';
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_item_category.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $page }} Data</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ $actionLink }}">
            @csrf @isset($productItemCategory)
              @method('PUT')
            @endisset
            <div class="form-group">
              <label for="input_product_id" class="required">Product</label>
              <select
                name="product_id"
                class="form-control select2 {{ $errors->has('product_id') ? ' is-invalid' : '' }}"
                id="input_product_id"
                required
              >
                @foreach(\App\Models\Product::all() as $product)
                  <option value="{{ $product->id }}"
                          @if (@$productItemCategory->product_id === $product->id) selected @endif>{{ $product->name }}</option>
                @endforeach
              </select>
              @include('alerts.feedback', ['field' => 'product_id'])
            </div>
            <div class="form-group">
              <label for="input_name" class="required">Name</label>
              <input
                type="text"
                name="name"
                class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                id="input_name"
                placeholder="Enter name"
                value="{{ old('name', @$productItemCategory->name) }}"
                required
              />
              @include('alerts.feedback', ['field' => 'name'])
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
