@extends('layouts.app', [
    'activePage' => 'account',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('account.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $storeLink }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title_input" class="required">Nama</label>
                        <input type="text" class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" id="title_input" placeholder="Masukkan Nama Akun" value="{{ old('title') }}">
                        @include('alerts.feedback', ['field' => 'title'])
                    </div>
                    <div class="form-group">
                        <label for="code_input" class="required">Code</label>
                        <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" id="code_input" placeholder="Masukkan Code Akun" value="{{ old('code') }}">
                        @include('alerts.feedback', ['field' => 'code'])
                    </div>
                    <div class="form-group">
                        <label for="description_input" class="required">Description</label>
                        <textarea class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}" name="description" id="description_input" placeholder="Masukkan Deskripsi Akun">{{ old('description') }}</textarea>
                        @include('alerts.feedback', ['field' => 'description'])
                    </div>
                    <div class="form-group">
                        <label for="winrate_input" class="required">Winrate</label>
                        <input type="number" step="0.1" class="form-control {{ $errors->has('winrate') ? ' is-invalid' : '' }}" name="winrate" id="winrate_input" placeholder="Masukkan Winrate Akun" value="{{ old('winrate') }}">
                        @include('alerts.feedback', ['field' => 'winrate'])
                    </div>
                    <div class="form-group">
                        <label for="skin_input" class="required">Skin</label>
                        <input type="number" class="form-control {{ $errors->has('skin') ? ' is-invalid' : '' }}" name="skin" id="skin_input" placeholder="Masukkan Jumlah Skin Akun" value="{{ old('skin') }}">
                        @include('alerts.feedback', ['field' => 'skin'])
                    </div>
                    <div class="form-group">
                        <label for="heroes_input" class="required">Heroes</label>
                        <input type="number" class="form-control {{ $errors->has('heroes') ? ' is-invalid' : '' }}" name="heroes" id="heroes_input" placeholder="Masukkan Jumlah Heroes Akun" value="{{ old('heroes') }}">
                        @include('alerts.feedback', ['field' => 'heroes'])
                    </div>
                    <div class="form-group">
                        <label for="stock_input" class="required">Stock</label>
                        <input type="number" class="form-control {{ $errors->has('stock') ? ' is-invalid' : '' }}" name="stock" id="stock_input" placeholder="Masukkan Jumlah Stock Akun" value="{{ old('stock') }}">
                        @include('alerts.feedback', ['field' => 'stock'])
                    </div>
                    <div class="form-group">
                        <label for="price_input" class="required">Price</label>
                        <input type="number" step="0.1" class="form-control {{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" id="price_input" placeholder="Masukkan Harga Akun" value="{{ old('price') }}">
                        @include('alerts.feedback', ['field' => 'price'])
                    </div>
                    <div class="form-group">
                        <label for="information_input" class="required">Information</label>
                        <textarea class="form-control {{ $errors->has('information') ? ' is-invalid' : '' }}" name="information" id="information_input" placeholder="Masukkan Informasi Akun">{{ old('information') }}</textarea>
                        @include('alerts.feedback', ['field' => 'information'])
                    </div>
                    <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.tiny.cloud/1/wejmk4ubc4t2ncovd3risw07yelp0dwzbvdxjq1ilyoizq6p/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@endpush

