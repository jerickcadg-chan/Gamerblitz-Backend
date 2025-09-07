<div class="card mb-4">
  <div class="card-body">
    <form method="get">
      <div class="row">
        <div class="col-md-4">
          <label class="mb-2" for="product_input">Select Product</label>
          <select class="form-control select2" name="product_id" id="product_input">
            <option value="">--- All Products ---</option>
            @foreach ($products as $product)
              <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : null }}>{{ $product->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="mb-2" for="product_item_name_input">Search by code</label>
          <input type="text" class="form-control" name="code" value="{{ request('code') }}" placeholder="e.g. 100 Diamonds">
        </div>
        <div class="col-md-4">
          <label class="mb-2" for="product_item_name_input">Search by name</label>
          <input type="text" class="form-control" name="name" value="{{ request('name') }}" placeholder="e.g. ML28-S1">
        </div>
        <div class="col-md-4">
          <div class="pt-4">
            <button type="submit" class="btn btn-primary btn-sm">Search</button>
            <a href="{{ route('product_item.index') }}" class="btn btn-danger btn-sm">Reset</a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

@push('js')
  <script>
    $('#product_input').on('change', function() {
      $(this).closest('form').submit()
    })
  </script>
@endpush
