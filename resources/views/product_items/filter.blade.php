<div class="card mb-4">
    <div class="card-body">
        <form method="get">
            <div class="row">
                <div class="col-md-5">
                    <label for="product_input">Pilih Produk</label>
                    <select class="form-control" name="product_id" id="product_input">
                        <option value=""></option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : null }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="product_item_name_input">Cari Item Berdasarkan Kode</label>
                    <input type="text" class="form-control" name="name" value="{{ request('name') }}">
                </div>
                <div class="col-md-2">
                    <div class="pt-4">
                        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                        <a href="{{ route('product_item.index') }}" class="btn btn-danger btn-sm">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
