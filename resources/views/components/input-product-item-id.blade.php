<div>
  <div class="row my-2">
    <div class="col-md-3">
      <input
        type="checkbox" name="product_item_ids[{{ $index }}][product_item_id]"
        data-index="{{ $index }}"
        value="{{ $productItem->id }}"
        @class([
          'mt-3 product_item_ids',
        ])
        {{ $isChecked() ? ' checked' : '' }}
        >
        @include('alerts.feedback', ['field' => "product_item_ids.{$index}.product_item_id"])
      <span>{{ $productItem?->product?->name }} - {{ $productItem?->name }}</span>
    </div>
    <div class="col-md-3">
      <input
        type="number" min="0"
        step="0.01"
        @class([
          'form-control',
          'is-invalid' => $errors->has("product_item_ids.{$index}.stock"),
        ])
        value="{{ $getFlashSaleProductItem()->price }}"
        name="{{ $isChecked() ? "product_item_ids[$index][price]" : '' }}"
        placeholder="Harga flash sale"
      >
      @include('alerts.feedback', ['field' => "product_item_ids.{$index}.price"])
    </div>
    <div class="col-md-3">
      <input
        type="number" min="0"
        step="0.01"
        @class([
          'form-control',
          'is-invalid' => $errors->has("product_item_ids.{$index}.stock"),
        ])
        value="{{ $getFlashSaleProductItem()->stock }}"
        name="{{ $isChecked() ? "product_item_ids[$index][stock]" : '' }}"
        placeholder="Stok"
      >
      @include('alerts.feedback', ['field' => "product_item_ids.{$index}.price"])
    </div>
  </div>
</div>
