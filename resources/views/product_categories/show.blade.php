@extends('layouts.app', [
    'activePage' => 'product_category',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('product_category.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <table class="table table-nospace">
                  <tr>
                    <th>Nama</th>
                    <td>{{ $productCategory->name }}</td>
                  </tr>
                  <tr>
                    <th>Slug</th>
                    <td>{{ $productCategory->slug }}</td>
                  </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
