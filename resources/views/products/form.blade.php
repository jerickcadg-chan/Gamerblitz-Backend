@extends('layouts.app', [
    'activePage' => 'product',
])

@php
  $isEdit = isset($product);
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? 'Edit Data' : 'Create Data' }}</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
          @csrf
          @if ($isEdit)
            @method('PUT')
          @endif

          <div class="form-group">
            <label for="name_input" class="required">Name</label>
            <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
              id="name_input" placeholder="e.g. Mobile Legends" value="{{ old('name', $product->name ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            <label for="code_input" class="required">Code</label>
            <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code"
              id="code_input" placeholder="e.g. ML, VAL, FF, etc" value="{{ old('code', $product->code ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'code'])
          </div>

          <div class="form-group">
            <label for="provider_input" class="required">Provider</label>
            <select id="provider_input" class="form-control" name="provider">
              @php $v = old('provider', $product->provider ?? ''); @endphp
              @foreach ($providers as $providerName => $providerDisplayName)
                <option value="{{ $providerName }}" {{ $v === $providerName ? 'selected' : '' }}>
                  {{ $providerDisplayName }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'provider'])
          </div>

          <div class="form-group">
            <label for="provider_code_input" class="required">Provider Product Code</label>
            <input type="text" class="form-control {{ $errors->has('provider_code') ? ' is-invalid' : '' }}"
              name="provider_code" id="provider_code_input" placeholder="e.g. ML, VAL, FF, etc"
              value="{{ old('provider_code', $product->provider_code ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'provider_code'])
          </div>

          <div class="form-group">
            <label for="provider_country_input" class="required">Country</label>
            <select id="provider_country_input" class="form-control" name="provider_country">
              @php $v = old('provider_country', $product->provider_country ?? ''); @endphp
              @foreach ($countries as $countryCode => $countryName)
                <option value="{{ $countryCode }}" {{ strtoupper($v) === strtoupper($countryCode) ? 'selected' : '' }}>
                  {{ $countryName }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'provider_country'])
          </div>

          <div class="form-group">
            <label for="company_input" class="required">Company</label>
            <input type="text" class="form-control {{ $errors->has('company') ? ' is-invalid' : '' }}" name="company"
              id="company_input" placeholder="e.g. Moonton" value="{{ old('company', $product->company ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'company'])
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_user_input" class="required">Markup User (%)</label>
                <input type="number" min="0"
                  class="form-control {{ $errors->has('markup_user') ? ' is-invalid' : '' }}" name="markup_user"
                  id="markup_user_input" placeholder="0" value="{{ old('markup_user', $product->markup_user ?? '') }}"
                  required>
                @include('alerts.feedback', ['field' => 'markup_user'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_reseller_silver_input" class="required">Markup Reseller Silver (%)</label>
                <input type="number" min="0"
                  class="form-control {{ $errors->has('markup_reseller_silver') ? ' is-invalid' : '' }}"
                  name="markup_reseller_silver" id="markup_reseller_silver_input" placeholder="0"
                  value="{{ old('markup_reseller_silver', $product->markup_reseller_silver ?? '') }}" required>
                @include('alerts.feedback', ['field' => 'markup_reseller_silver'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_reseller_gold_input" class="required">Markup Reseller Gold (%)</label>
                <input type="number" min="0"
                  class="form-control {{ $errors->has('markup_reseller_gold') ? ' is-invalid' : '' }}"
                  name="markup_reseller_gold" id="markup_reseller_gold_input" placeholder="0"
                  value="{{ old('markup_reseller_gold', $product->markup_reseller_gold ?? '') }}" required>
                @include('alerts.feedback', ['field' => 'markup_reseller_gold'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_reseller_vip_input" class="required">Markup Reseller VIP (%)</label>
                <input type="number" min="0"
                  class="form-control {{ $errors->has('markup_reseller_vip') ? ' is-invalid' : '' }}"
                  name="markup_reseller_vip" id="markup_reseller_vip_input" placeholder="0"
                  value="{{ old('markup_reseller_vip', $product->markup_reseller_vip ?? '') }}" required>
                @include('alerts.feedback', ['field' => 'markup_reseller_vip'])
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="category_input" class="required">Category</label>
            <select class="form-control {{ $errors->has('product_category_id') ? ' is-invalid' : '' }}"
              name="product_category_id" id="category_input" required>
              <option value="">Select category</option>
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
            <label for="description_input" class="required">Description</label>
            <textarea class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }} tinymce" name="description"
              id="description_input" placeholder="Enter Description">{{ old('description', $product->description ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'description'])
          </div>

          <div class="form-group">
            <label for="input_format_input" class="required">Format Input</label>
            <textarea class="form-control {{ $errors->has('input_format') ? ' is-invalid' : '' }}" name="input_format"
              id="input_format_input" placeholder="Enter Format Input" required>{{ old('input_format', $product->input_format ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'input_format'])
          </div>

          <div class="form-group">
            <label for="how_to_order_input" class="required">How to Order</label>
            <textarea class="form-control {{ $errors->has('how_to_order') ? ' is-invalid' : '' }} tinymce" name="how_to_order"
              id="how_to_order_input" placeholder="Enter Cara Order">{{ old('how_to_order', $product->how_to_order ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'how_to_order'])
          </div>

          <div class="form-group">
            <label for="cover">Cover</label>
            <input type="file" name="cover" class="form-control {{ $errors->has('cover') ? ' is-invalid' : '' }}"
              accept="image/*">
            @include('alerts.feedback', ['field' => 'cover'])
            @if ($isEdit && !empty($product->default_cover))
              <p class="d-block text-small mt-2">Current</p>
              <a href="{{ asset($product->default_cover) }}" target="_blank">
                <img src="{{ asset($product->default_cover) }}" height="100" alt="image preview" />
              </a>
            @endif
            @include('alerts.feedback', ['field' => 'cover'])
          </div>

          <div class="form-group">
            <label for="picture" class="required">Picture</label>
            <input type="file" name="picture"
              class="form-control {{ $errors->has('picture') ? ' is-invalid' : '' }}" accept="image/*">
            @include('alerts.feedback', ['field' => 'picture'])
            @if ($isEdit && !empty($product->default_picture))
              <p class="d-block text-small mt-2">Current</p>
              <a href="{{ asset($product->default_picture) }}" target="_blank">
                <img src="{{ asset($product->default_picture) }}" height="100" alt="image preview" />
              </a>
            @endif
            @include('alerts.feedback', ['field' => 'picture'])
          </div>

          @if ($isEdit)
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
