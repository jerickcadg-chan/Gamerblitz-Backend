@extends('layouts.app', [
    'activePage' => 'product',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <div class="row mb-2">
          <div class="col-md-4 mb-2">
            <form method="get">
              <input type="text" class="form-control" name="name" placeholder="Search name produk" value="{{ request('name') }}">
            </form>
          </div>
          <div class="col-md-8 text-lg-end">
            <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
          </div>
        </div>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th> # </th>
            <th> Name </th>
            <th> Code </th>
            <th> Category </th>
            <th> Status </th>
            <th> Action </th>
          </tr>
          </thead>
          <tbody>
          @forelse ($products as $index => $product)
            <tr>
              <td>{{ $products->firstItem() + $index }}</td>
              <td>{{ $product->name }}</td>
              <td>{{ $product->code }}</td>
              <td>{{ $product->productCategory?->name }}</td>
              <td>{!! $product->statusView !!}</td>
              <td>
                @include('master.action', [
                    'view_url' => route('product.show', $product),
                    'edit_url' => route('product.edit', $product),
                ])
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="100%" class="text-center">No Data</td>
            </tr>
          @endforelse
          </tbody>
        </table>
        <div class="mt-2">
          {!! $products->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
