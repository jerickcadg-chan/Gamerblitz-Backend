@extends('layouts.app', [
    'activePage' => 'lapakgaming.products',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('flash_sale.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data List</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                @if ($error)
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                @endif
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Variant</th>
                            <th>Check ID</th>
                            <th>Country</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product['code'] }}</td>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['variant'] }}</td>
                            <td>{{ $product['check_id'] }}</td>
                            <td>{{ $product['country_code'] }}</td>
                            <td></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="100%" class="text-center">No Data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
