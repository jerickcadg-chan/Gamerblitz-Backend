@extends('layouts.app', [
    'activePage' => 'flash_sale',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('flash_sale.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $updateLink }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name_input">Nama</label>
                        <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" id="name_input" placeholder="Masukkan Nama" value="{{ old('name', $flash_sale->name) }}">
                        @include('alerts.feedback', ['field' => 'name'])
                    </div>
                    <div class="form-group">
                        <label for="start_date">Tanggal mulai</label>
                        <input type="datetime-local" class="form-control {{ $errors->has('start_date') ? ' is-invalid' : '' }}" name="start_date" id="start_date" placeholder="Masukkan Nama" value="{{ old('start_date', $flash_sale->start_date) }}">
                        @include('alerts.feedback', ['field' => 'start_date'])
                    </div>
                    <div class="form-group">
                        <label for="end_date">Tanggal berakhir</label>
                        <input type="datetime-local" class="form-control {{ $errors->has('end_date') ? ' is-invalid' : '' }}" name="end_date" id="end_date" placeholder="Masukkan Nama" value="{{ old('end_date', $flash_sale->end_date) }}">
                        @include('alerts.feedback', ['field' => 'end_date'])
                    </div>
                    <div id="product_item_div">
                        <h4>Pilih Item Produk</h4>
                        <hr>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <input type="text" id="product_item_search" placeholder="Cari produk" class="form-control {{ $errors->has('product_item_ids') ? ' is-invalid' : '' }}" oninput="search(this)">
                                @include('alerts.feedback', ['field' => 'product_item_ids'])
                            </div>
                        </div>
                        <div style="max-height: 200px; overflow-y: scroll;">
                          <x-search-product-item-flash-sale :flash-sale-product-items="$flash_sale->items"/>
                        </div>
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
