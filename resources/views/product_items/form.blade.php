@php use App\Models\ProductItem; @endphp
@extends('layouts.app', [
    'activePage' => 'product_item',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title">Page {{ $title }}</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product_item.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ isset($productItem) ? 'Edit Data' : 'Create Data' }}</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $actionLink }}" enctype="multipart/form-data">
          @csrf
          @isset($productItem)
            @method('PUT')
          @endisset

          <div class="form-group">
            <label for="product_id_input" class="required">Product</label>
            <select name="product_id" id="product_id_input"
              class="form-control {{ $errors->has('product_id') ? 'is-invalid' : '' }}" required>
              <option value="">-- Choose Product --</option>
              @foreach ($products as $product)
                <option value="{{ $product->id }}"
                  {{ old('product_id', $productItem->product_id ?? '') == $product->id ? 'selected' : '' }}>
                  {{ $product->name }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'product_id'])
          </div>

          <div class="form-group">
            <label for="code_input" class="required">Code</label>
            <input type="text" name="code" id="code_input"
              class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}"
              value="{{ old('code', $productItem->code ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'code'])
          </div>

          <div class="form-group">
            <label for="name_input" class="required">Name</label>
            <input type="text" name="name" id="name_input"
              class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
              value="{{ old('name', $productItem->name ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            <label for="capital_input" class="required">Capital Price (Raw)</label>
            <input type="number" step="0.01" name="capital" id="capital_input"
              class="form-control {{ $errors->has('capital') ? 'is-invalid' : '' }}"
              value="{{ old('capital', $productItem->capital ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'capital'])
          </div>

          <div class="form-group">
            <label for="stock_input">Stock</label>
            <input type="number" name="stock" id="stock_input"
              class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}"
              value="{{ old('stock', $productItem->stock ?? '') }}">
            @include('alerts.feedback', ['field' => 'stock'])
          </div>

          @if (isset($productItem))
            <div class="form-group">
              <label for="provider_input">Provider</label>
              <input type="text" name="provider" id="provider_input"
                class="form-control {{ $errors->has('provider') ? 'is-invalid' : '' }}"
                value="{{ old('provider', $productItem->provider ?? '') }}" readonly>
              @include('alerts.feedback', ['field' => 'provider'])
            </div>
          @endif

          <div class="row">
            <div class="form-group col-md-3">
              <label for="margin_input">Margin Public (%)</label>
              <input type="number" step="0.01" name="margin" id="margin_input"
                class="form-control {{ $errors->has('margin') ? 'is-invalid' : '' }}"
                value="{{ old('margin', $productItem->margin ?? '') }}">
              @include('alerts.feedback', ['field' => 'margin'])
            </div>
            <div class="form-group col-md-3">
              <label for="margin_silver_input">Margin Silver (%)</label>
              <input type="number" step="0.01" name="margin_silver" id="margin_silver_input"
                class="form-control {{ $errors->has('margin_silver') ? 'is-invalid' : '' }}"
                value="{{ old('margin_silver', $productItem->margin_silver ?? '') }}">
              @include('alerts.feedback', ['field' => 'margin_silver'])
            </div>
            <div class="form-group col-md-3">
              <label for="margin_gold_input">Margin Gold (%)</label>
              <input type="number" step="0.01" name="margin_gold" id="margin_gold_input"
                class="form-control {{ $errors->has('margin_gold') ? 'is-invalid' : '' }}"
                value="{{ old('margin_gold', $productItem->margin_gold ?? '') }}">
              @include('alerts.feedback', ['field' => 'margin_gold'])
            </div>
            <div class="form-group col-md-3">
              <label for="margin_vip_input">Margin VIP (%)</label>
              <input type="number" step="0.01" name="margin_vip" id="margin_vip_input"
                class="form-control {{ $errors->has('margin_vip') ? 'is-invalid' : '' }}"
                value="{{ old('margin_vip', $productItem->margin_vip ?? '') }}">
              @include('alerts.feedback', ['field' => 'margin_vip'])
            </div>
            <div class="form-group col-md-12">
              <label for="provider_input" class="required">Provider</label>
              <select id="provider_input" class="form-control" name="provider">
                @php $v = old('provider', $productItem->provider ?? ''); @endphp
                @foreach ($providers as $providerName => $providerDisplayName)
                  <option value="{{ $providerName }}" {{ $v === $providerName ? 'selected' : '' }}>
                    {{ $providerDisplayName }}
                  </option>
                @endforeach
              </select>
              @include('alerts.feedback', ['field' => 'provider'])
            </div>
            <div class="form-group col-md-12">
              <label>
                Status
                (
                <label class="form-check-label" for="is_locked_input">Is Locked?</label>
                <input type="checkbox" class="form-check-input mt-0" id="is_locked_input" name="is_locked"
                  value="1" {{ old('is_locked', $product->is_locked ?? false) ? 'checked' : '' }}>
                )
              </label>
              <select class="form-control" name="status">
                @php $v = old('status', $productItem->status ?? ProductItem::STATUS_ACTIVE); @endphp
                <option value="{{ ProductItem::STATUS_ACTIVE }}"
                  {{ $v === ProductItem::STATUS_ACTIVE ? 'selected' : '' }}>
                  {{ ProductItem::STATUS_ACTIVE }}
                </option>
                <option value="{{ ProductItem::STATUS_EMPTY }}"
                  {{ $v === ProductItem::STATUS_EMPTY ? 'selected' : '' }}>
                  {{ ProductItem::STATUS_EMPTY }}
                </option>
                <option value="{{ ProductItem::STATUS_NON_ACTIVE }}"
                  {{ $v === ProductItem::STATUS_NON_ACTIVE ? 'selected' : '' }}>
                  {{ ProductItem::STATUS_NON_ACTIVE }}
                </option>
                <option value="{{ ProductItem::STATUS_TROUBLE }}"
                  {{ $v === ProductItem::STATUS_TROUBLE ? 'selected' : '' }}>
                  {{ ProductItem::STATUS_TROUBLE }}
                </option>
              </select>
              <label for="">
                <small>
                  is locked to ensure this product item remains active when you change the product’s provider.
                </small>
              </label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ route('product_item.index') }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection
