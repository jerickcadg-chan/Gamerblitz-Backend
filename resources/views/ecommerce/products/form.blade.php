@extends('layouts.app', [
    'activePage' => 'ecommerce_product',
])

@push('css')
<style>
  .cke_chrome {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
  }
</style>
@endpush

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ $indexLink }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ isset($product) ? 'Edit' : 'Create' }}</li>
      </ol>
    </nav>
  </div>

  <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($product))
      @method('PUT')
    @endif

    <div class="row">
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Product Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <div class="form-group">
                  <label for="name">Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name', $product->name ?? '') }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="category_id">Category <span class="text-danger">*</span></label>
                  <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                      <option value="{{ $category->id }}"
                        {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="10">{{ old('description', $product->description ?? '') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-group">
              <label for="image">Product Image</label>
              @if(isset($product) && $product->image)
                <div class="mb-2">
                  <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                    style="max-width: 150px; max-height: 150px;">
                </div>
              @endif
              <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
              @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">SEO Settings</h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="meta_title">Meta Title</label>
              <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title"
                value="{{ old('meta_title', $product->meta_title ?? '') }}" maxlength="255">
              <small class="form-text text-muted">Recommended: 50-60 characters. Leave blank to use product name.</small>
              @error('meta_title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <label for="meta_description">Meta Description</label>
              <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description"
                rows="3" maxlength="1000">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
              <small class="form-text text-muted">Recommended: 150-160 characters for optimal SEO.</small>
              @error('meta_description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <label for="meta_keywords">Meta Keywords</label>
              <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" id="meta_keywords" name="meta_keywords"
                value="{{ old('meta_keywords', $product->meta_keywords ?? '') }}" maxlength="500">
              <small class="form-text text-muted">Comma-separated keywords (e.g., iphone, apple, smartphone)</small>
              @error('meta_keywords')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Product Variants</h5>
            <button type="button" class="btn btn-sm btn-primary" id="add-variant-option">
              <i class="mdi mdi-plus"></i> Add Variant Option
            </button>
          </div>
          <div class="card-body">
            <p class="text-muted mb-3">Add variant options like Color, Size, Storage, etc.</p>
            <div id="variant-options-container"></div>
          </div>
        </div>

        @if(isset($product) && $product->logs->count() > 0)
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Product Change History</h5>
          </div>
          <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-striped">
              <thead>
                <tr>
                  <th style="width: 150px;">Date</th>
                  <th style="width: 120px;">User</th>
                  <th>Change</th>
                </tr>
              </thead>
              <tbody>
                @foreach($product->logs->take(50) as $log)
                <tr>
                  <td class="text-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                  <td>{{ $log->user->name ?? 'System' }}</td>
                  <td>{{ $log->description }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @endif
      </div>

      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Pricing</h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="price">Base Price <span class="text-danger">*</span> <span class="text-muted" title="Selling price to customers">(?)</span></label>
              <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price"
                value="{{ old('price', $product->price ?? '') }}" required>
              @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <label for="sale_price">Sale Price <span class="text-muted" title="Discounted selling price">(?)</span></label>
              <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price"
                value="{{ old('sale_price', $product->sale_price ?? '') }}">
              @error('sale_price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <label for="capital_price">Capital Price <span class="text-muted" title="Your cost/purchase price">(?)</span></label>
              <input type="number" step="0.01" class="form-control @error('capital_price') is-invalid @enderror" id="capital_price" name="capital_price"
                value="{{ old('capital_price', $product->capital_price ?? '') }}">
              <small class="form-text text-muted">Your cost for this product (used for profit calculation)</small>
              @error('capital_price')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Inventory</h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label for="stock">Stock</label>
              <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock"
                value="{{ old('stock', $product->stock ?? 0) }}">
              @error('stock')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <div class="form-check">
                <label class="form-check-label">
                  <input type="checkbox" class="form-check-input" name="track_stock" value="1"
                    {{ old('track_stock', $product->track_stock ?? false) ? 'checked' : '' }}>
                  Track Stock
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Status</h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <div class="form-check">
                <label class="form-check-label">
                  <input type="checkbox" class="form-check-input" name="is_active" value="1"
                    {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                  Active
                </label>
              </div>
            </div>
            <div class="form-group">
              <div class="form-check">
                <label class="form-check-label">
                  <input type="checkbox" class="form-check-input" name="is_featured" value="1"
                    {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                  Featured
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg">{{ isset($product) ? 'Update Product' : 'Create Product' }}</button>
          <a href="{{ $indexLink }}" class="btn btn-secondary">Cancel</a>
        </div>
      </div>
    </div>
  </form>

  <template id="variant-option-template">
    <div class="variant-option card mb-3">
      <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <div class="d-flex align-items-center flex-grow-1">
          <input type="hidden" class="option-id" value="">
          <input type="text" class="form-control form-control-sm option-name" style="max-width: 200px;"
            placeholder="Option name (e.g., Color, Size)">
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger remove-option">
          <i class="mdi mdi-delete"></i>
        </button>
      </div>
      <div class="card-body">
        <div class="variant-values"></div>
        <button type="button" class="btn btn-sm btn-outline-primary add-variant-value mt-2">
          <i class="mdi mdi-plus"></i> Add Value
        </button>
      </div>
    </div>
  </template>

  <template id="variant-value-template">
    <div class="variant-value row align-items-end mb-3 pb-3 border-bottom">
      <input type="hidden" class="value-id" value="">
      <div class="col-md-2">
        <label class="form-label small">Value Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control form-control-sm value-name" placeholder="e.g., Red, XL">
      </div>
      <div class="col-md-2">
        <label class="form-label small">Price <span class="text-muted">(?)</span></label>
        <input type="number" step="0.01" class="form-control form-control-sm value-price" placeholder="0.00">
      </div>
      <div class="col-md-2">
        <label class="form-label small">Sale Price</label>
        <input type="number" step="0.01" class="form-control form-control-sm value-sale-price" placeholder="0.00">
      </div>
      <div class="col-md-2">
        <label class="form-label small">Capital</label>
        <input type="number" step="0.01" class="form-control form-control-sm value-capital-price" placeholder="0.00">
      </div>
      <div class="col-md-1">
        <label class="form-label small">Stock</label>
        <input type="number" class="form-control form-control-sm value-stock" placeholder="0" value="0">
      </div>
      <div class="col-md-1">
        <label class="form-label small">Image</label>
        <input type="file" class="form-control form-control-sm value-image" accept="image/*">
      </div>
      <div class="col-md-1">
        <label class="form-label small">Active</label>
        <div class="form-check">
          <input type="checkbox" class="form-check-input value-active" value="1" checked>
        </div>
      </div>
      <div class="col-md-1">
        <button type="button" class="btn btn-sm btn-outline-danger remove-value">
          <i class="mdi mdi-close"></i>
        </button>
      </div>
    </div>
  </template>

@endsection

@push('js')
<script src="https://cdn.ckeditor.com/4.25.1/standard/ckeditor.js"></script>
<script>
$(document).ready(function() {
  var descEl = document.getElementById('description');
  var content = descEl.value;
  
  if (content && (content.indexOf('<!doctype') !== -1 || content.indexOf('<!DOCTYPE') !== -1)) {
    var bodyMatch = content.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
    if (bodyMatch && bodyMatch[1]) {
      descEl.value = bodyMatch[1].trim();
    }
  }

  CKEDITOR.replace('description', {
    height: 350
  });
});
</script>
<script>
(function() {
  var existingVariants = @json($variantData);
  var container = document.getElementById('variant-options-container');
  var addOptionBtn = document.getElementById('add-variant-option');
  var optionTemplate = document.getElementById('variant-option-template');
  var valueTemplate = document.getElementById('variant-value-template');

  function updateNames() {
    var options = container.querySelectorAll('.variant-option');
    options.forEach(function(optionEl, oIdx) {
      optionEl.querySelector('.option-id').name = 'variant_options[' + oIdx + '][id]';
      optionEl.querySelector('.option-name').name = 'variant_options[' + oIdx + '][name]';

      var values = optionEl.querySelectorAll('.variant-value');
      values.forEach(function(valueEl, vIdx) {
        valueEl.querySelector('.value-id').name = 'variant_options[' + oIdx + '][values][' + vIdx + '][id]';
        valueEl.querySelector('.value-name').name = 'variant_options[' + oIdx + '][values][' + vIdx + '][name]';
        valueEl.querySelector('.value-price').name = 'variant_options[' + oIdx + '][values][' + vIdx + '][price]';
        valueEl.querySelector('.value-sale-price').name = 'variant_options[' + oIdx + '][values][' + vIdx + '][sale_price]';
        valueEl.querySelector('.value-capital-price').name = 'variant_options[' + oIdx + '][values][' + vIdx + '][capital_price]';
        valueEl.querySelector('.value-stock').name = 'variant_options[' + oIdx + '][values][' + vIdx + '][stock]';
        valueEl.querySelector('.value-image').name = 'variant_images[' + oIdx + '][' + vIdx + ']';
        valueEl.querySelector('.value-active').name = 'variant_options[' + oIdx + '][values][' + vIdx + '][is_active]';
      });
    });
  }

  function addVariantOption(data) {
    var clone = optionTemplate.content.cloneNode(true);
    var optionEl = clone.querySelector('.variant-option');

    if (data) {
      optionEl.querySelector('.option-id').value = data.id || '';
      optionEl.querySelector('.option-name').value = data.name || '';
    }

    container.appendChild(optionEl);

    optionEl.querySelector('.remove-option').addEventListener('click', function() {
      optionEl.remove();
      updateNames();
    });

    optionEl.querySelector('.add-variant-value').addEventListener('click', function() {
      addVariantValue(optionEl, null);
    });

    if (data && data.values && data.values.length > 0) {
      data.values.forEach(function(value) {
        addVariantValue(optionEl, value);
      });
    } else {
      addVariantValue(optionEl, null);
    }

    updateNames();
  }

  function addVariantValue(optionEl, data) {
    var valuesContainer = optionEl.querySelector('.variant-values');
    var clone = valueTemplate.content.cloneNode(true);
    var valueEl = clone.querySelector('.variant-value');

    if (data) {
      valueEl.querySelector('.value-id').value = data.id || '';
      valueEl.querySelector('.value-name').value = data.name || '';
      if (data.price) valueEl.querySelector('.value-price').value = data.price;
      if (data.sale_price) valueEl.querySelector('.value-sale-price').value = data.sale_price;
      if (data.capital_price) valueEl.querySelector('.value-capital-price').value = data.capital_price;
      valueEl.querySelector('.value-stock').value = data.stock || 0;
      valueEl.querySelector('.value-active').checked = data.is_active !== false;
    }

    valuesContainer.appendChild(valueEl);

    valueEl.querySelector('.remove-value').addEventListener('click', function() {
      valueEl.remove();
      updateNames();
    });

    updateNames();
  }

  document.addEventListener('DOMContentLoaded', function() {
    addOptionBtn.addEventListener('click', function() {
      addVariantOption(null);
    });

    if (existingVariants && existingVariants.length > 0) {
      existingVariants.forEach(function(option) {
        addVariantOption(option);
      });
    }
  });
})();
</script>
@endpush