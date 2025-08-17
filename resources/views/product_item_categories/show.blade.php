@extends('layouts.admin', [
    'activePage' => 'product_item_category',
])

@section('content')
  <div class="row">
    <div class="col-12 col-sm-6">
      <h3 class="page-title font-weight-bold"> Halaman Detail {{ $title }} </h3>
    </div>
    <div class="col-12 col-sm-6">
      <nav aria-label="breadcrumb" class="float-md-right float-sm-none">
        <ol class="breadcrumb pl-0">
          <li class="breadcrumb-item">
            <a href="{{ route('home') }}">Home</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <a href="{{ route('product_item_category.index') }}">{{ $title }}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <table class="table table-nospace">
          <tr>
            <th>Produk</th>
            <td>{{ $productItemCategory->product->name }}</td>
          </tr>
          <tr>
            <th>Nama</th>
            <td>{{ $productItemCategory->name }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="form-group">
        <a
          class="btn btn-primary"
          href="{{ route('product_item_categories.metas.create', ['product_item_category' => $productItemCategory]) }}"
        >Tambah Data</a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
          <tr>
            <th>Harga Minimal</th>
            <th>Ikon</th>
            <th>Aksi</th>
          </tr>
          </thead>
          <tbody>

          @forelse ($productItemCategory->metas as $meta)
            <tr>
              <td>{{ rp_format($meta->min_price) }}</td>
              <td>
                <a href="{{ $meta->picture->url }}" target="_blank">
                  <img src="{{ $meta->picture->url }}" alt="{{ $productItemCategory->name }}" height="100" width="100">
                </a>
              </td>
              <td>
                @canany(['Edit Product Item', 'Delete Product Item'])
                  @include('master.action', [
                      'edit_url' => route('product_item_categories.metas.edit', [
                          'product_item_category' => $productItemCategory->id,
                          'meta' => $meta->id,
                      ]),
                      'delete_url' => route('product_item_categories.metas.destroy', [
                          'product_item_category' => $productItemCategory->id,
                          'meta' => $meta->id,
                      ])
                  ])
                @endcanany
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="100%" class="text-center">Tidak ada data ditemukan</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

@endsection
