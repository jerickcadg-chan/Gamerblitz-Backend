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
          <label for="input_min_price" class="required">Price Minimal</label>
          <input
            type="number"
            name="min_price"
            class="form-control {{ $errors->has('min_price') ? ' is-invalid' : '' }}"
            id="input_min_price"
            placeholder="Enter minimal price"
            value="{{ old('min_price', @$meta->min_price) }}"
            required
          />
          @include('alerts.feedback', ['field' => 'min_price'])
        </div>
        <div class="form-group">
          <label for="input_picture" @if(!isset($meta)) class="required" @endif>Icon</label>
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
          <input type="hidden" name="product_item_category_id" value="{{ $productItemCategory->id }}"/>
          <button class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
@endsection
