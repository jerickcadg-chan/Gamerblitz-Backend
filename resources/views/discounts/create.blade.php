@extends('layouts.app', [
    'activePage' => 'discount',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('discount.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $storeLink }}">
                    @csrf
                    <div class="form-group">
                        <label for="input_name" class="required">Nama Diskon</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="input_name" placeholder="Masukkan nama" value="{{ old('name') }}" required>
                        @include('alerts.feedback', ['field' => 'name'])
                    </div>
                    <div class="form-group">
                        <label for="input_code">Kode Diskon</label>
                        <input type="text" name="code" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" id="input_code" placeholder="Kosongi apabila tidak ada kode" value="{{ old('code') }}">
                        @include('alerts.feedback', ['field' => 'code'])
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="input_disc_type" class="required">Jenis Diskon</label>
                            <select class="form-control {{ $errors->has('disc_type') ? ' is-invalid' : '' }}" id="input_disc_type" name="disc_type" required>
                                @foreach (config('array.discount.disc_type') as $disc)
                                    <option value="{{ $disc['value'] }}" {{ old('disc_type') == $disc['value'] ? 'selected' : null }}>{{ $disc['desc'] }}</option>
                                @endforeach
                            </select>
                            @include('alerts.feedback', ['field' => 'disc_type'])
                        </div>
                        <div class="form-group col-md-5">
                            <label for="input_nominal" class="required">Nominal Diskon</label>
                            <input type="number" name="nominal" class="form-control {{ $errors->has('nominal') ? ' is-invalid' : '' }}" id="input_nominal" placeholder="Masukkan nominal sesuai jenis diskon" value="{{ old('nominal') }}" required>
                            @include('alerts.feedback', ['field' => 'nominal'])
                        </div>
                        <div class="form-group col-md-4">
                            <label for="input_maximum" class="required">Maksimal Penggunaan</label>
                            <input type="number" min="1" name="maximum" class="form-control {{ $errors->has('maximum') ? ' is-invalid' : '' }}" id="input_maximum" placeholder="Masukkan maksimal penggunaan diskon" value="{{ old('maximum') }}" required>
                            @include('alerts.feedback', ['field' => 'maximum'])
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description_input">Deskripsi</label>
                        <textarea class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }} tinymce" name="description" id="description_input" placeholder="Masukkan Deskripsi">{{ old('description') }}</textarea>
                        @include('alerts.feedback', ['field' => 'description'])
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="input_start_date" class="required">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control {{ $errors->has('start_date') ? ' is-invalid' : '' }}" id="input_start_date" placeholder="Masukkan tanggal mulai diskon" value="{{ old('start_date') }}" required>
                            @include('alerts.feedback', ['field' => 'start_date'])
                        </div>
                        <div class="form-group col-md-6">
                            <label for="input_end_date" class="required">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control {{ $errors->has('end_date') ? ' is-invalid' : '' }}" id="input_end_date" placeholder="Masukkan tanggal berakhir diskon" value="{{ old('end_date') }}" required>
                            @include('alerts.feedback', ['field' => 'end_date'])
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_product_type" class="required">Jenis Produk</label>
                        <select class="form-control {{ $errors->has('product_type') ? ' is-invalid' : '' }}" id="input_product_type" name="product_type" onchange="checkProductType()" required>
                            @foreach (config('array.discount.product_type') as $disc)
                                <option value="{{ $disc['value'] }}" {{ old('product_type') == $disc['value'] ? 'selected' : null }}>{{ $disc['desc'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="product_type_div" style="display: none">
                        <h4>Pilih Produk</h4>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <input type="text" id="product_search" placeholder="Cari produk" class="form-control" oninput="search(this)">
                            </div>
                        </div>
                        @foreach (\App\Models\Product::all() as $product)
                            <div class="item">
                                <input type="checkbox" name="product_id[]" value="{{ $product->id }}" class="my-2" {{ (is_array(old('product_id')) && in_array($product->id, old('product_id'))) ? ' checked' : '' }}>
                                <span>{{ $product->name }}</span>
                            <br></div>
                        @endforeach
                        <hr>
                    </div>

                    <div id="product_item_div" style="display: none">
                        <h4>Pilih Item Produk</h4>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <input type="text" id="product_item_search" placeholder="Cari produk" class="form-control" oninput="search(this)">
                            </div>
                        </div>
                        @foreach (\App\Models\ProductItem::all() as $product_item)
                            <div class="item">
                                <input type="checkbox" name="product_item_id[]" value="{{ $product_item->id }}" class="my-2" {{ (is_array(old('product_item_id')) && in_array($product->id, old('product_item_id'))) ? ' checked' : '' }}>
                                <span>{{ $product_item->product->name }} - {{ $product_item->name }}</span>
                            <br></div>
                        @endforeach
                        <hr>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.tiny.cloud/1/wejmk4ubc4t2ncovd3risw07yelp0dwzbvdxjq1ilyoizq6p/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector:'textarea.tinymce',
        height: 300
    });

    checkProductType();

    function checkProductType() {
        let input = $('#input_product_type').val();
        let product_type = $('#product_type_div');
        let product_item = $('#product_item_div');

        if (input == 'all') {
            product_type.hide();
            product_item.hide();
        }

        if (input == 'product_type') {
            product_type.show();
            product_item.hide();
        }

        if (input == 'product_item') {
            product_type.hide();
            product_item.show();
        }
    }

    function search(el) {
        var input, filter, item, span, i, txtValue;
        filter = el.value.toUpperCase();
        item = $(el).parent().parent().parent().find('.item');
        for (i = 0; i < item.length; i++) {
            span = item[i].getElementsByTagName("span")[0];
            console.log(span)
            txtValue = span.textContent || span.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                item[i].style.display = "";
            } else {
                item[i].style.display = "none";
            }
        }
    }
</script>
@endpush
