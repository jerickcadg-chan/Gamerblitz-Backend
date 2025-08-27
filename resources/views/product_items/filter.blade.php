<div class="card mb-4">
    <div class="card-body">
        <form method="get">
            <div class="row">
                <div class="col-md-5">
                    <label for="product_input">Pilih Product</label>
                    <select class="form-control select2" name="product_id" id="product_input">
                        <option value="">All</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : null }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="product_item_name_input">Search Item Berdasarkan Kode</label>
                    <input type="text" class="form-control" name="name" value="{{ request('name') }}">
                </div>
                <div class="col-md-2">
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
