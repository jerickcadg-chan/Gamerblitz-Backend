@extends('layouts.app', [
  'activePage' => 'account',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Halaman {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('account.index') }}">{{ $title }}</a></li>
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
            <label for="title_input">Nama</label>
            <input type="text" class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" id="title_input" placeholder="Masukkan Nama" value="{{ old('title', $account->title) }}">
            @include('alerts.feedback', ['field' => 'title'])
          </div>
          <div class="form-group">
            <label for="code_input">Kode</label>
            <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" id="code_input" placeholder="Masukkan Kode" value="{{ old('code', $account->code) }}">
            @include('alerts.feedback', ['field' => 'code'])
          </div>
          <div class="form-group">
            <label for="description_input">Deskripsi</label>
            <textarea class="form-control tinymce {{ $errors->has('description') ? ' is-invalid' : '' }}" name="description" id="description_input" placeholder="Masukkan Deskripsi">{{ old('description', $account->description) }}</textarea>
            @include('alerts.feedback', ['field' => 'description'])
          </div>
          <div class="form-group">
            <label for="winrate_input">Winrate</label>
            <input type="number" step="0.1" class="form-control {{ $errors->has('winrate') ? ' is-invalid' : '' }}" name="winrate" id="winrate_input" placeholder="Masukkan Winrate" value="{{ old('winrate', $account->winrate) }}">
            @include('alerts.feedback', ['field' => 'winrate'])
          </div>
          <div class="form-group">
            <label for="skin_input">Jumlah Skin</label>
            <input type="number" class="form-control {{ $errors->has('skin') ? ' is-invalid' : '' }}" name="skin" id="skin_input" placeholder="Masukkan Jumlah Skin" value="{{ old('skin', $account->skin) }}">
            @include('alerts.feedback', ['field' => 'skin'])
          </div>
          <div class="form-group">
            <label for="heroes_input">Jumlah Heroes</label>
            <input type="number" class="form-control {{ $errors->has('heroes') ? ' is-invalid' : '' }}" name="heroes" id="heroes_input" placeholder="Masukkan Jumlah Heroes" value="{{ old('heroes', $account->heroes) }}">
            @include('alerts.feedback', ['field' => 'heroes'])
          </div>
          <div class="form-group">
            <label for="stock_input">Stok</label>
            <input type="number" class="form-control {{ $errors->has('stock') ? ' is-invalid' : '' }}" name="stock" id="stock_input" placeholder="Masukkan Stok" value="{{ old('stock', $account->productItem->stock) }}">
            @include('alerts.feedback', ['field' => 'stock'])
          </div>
          <div class="form-group">
            <label for="price_input">Harga</label>
            <input type="number" step="0.1" class="form-control {{ $errors->has('price') ? ' is-invalid' : '' }}" name="price" id="price_input" placeholder="Masukkan Harga" value="{{ old('price', $account->productItem->price) }}">
            @include('alerts.feedback', ['field' => 'price'])
          </div>
          <div class="form-group" id="information-group" style="display: none;">
            <label for="information_input">Informasi</label>
            <textarea class="form-control tinymce {{ $errors->has('information') ? ' is-invalid' : '' }}" name="information" id="information_input" placeholder="Masukkan Informasi">{{ old('information', 'xxxxxxxxxxxxxxxxxxxxxxx') }}</textarea>
            @include('alerts.feedback', ['field' => 'information'])
          </div>
          <div class="form-group">
            <label for="picture" class="required">Gambar kover</label>
            <input type="file" name="cover_picture" class="form-control" accept="image/*" value="{{ old('cover_picture') }}">
            <small><i>Kosongi apabila tidak merubah cover</i></small>
            @include('alerts.feedback', ['field' => 'cover_picture'])
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
  <script>
    tinymce.init({
      selector:'textarea.tinymce',
      height: 300
    });
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const editInformation = urlParams.get('edit-information');

      if (editInformation) {
        fetch('{{ route('account.show-information', $account) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ pin: editInformation })
        })
          .then(response => response.json())
          .then(data => {
            document.getElementById('information-group').style.display = 'block';
            document.getElementById('information_input').value = data.data;
          })
          .catch(error => {
            Swal.fire({
              title: "Pin Salah",
              icon: 'error',
            });
          });
      }
    });
  </script>
@endpush
