@extends('layouts.app', [
    'activePage' => 'flash_sale',
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
                <div class="row mb-2">
                    <div class="col-md-4 mb-2">
                    </div>
                    <div class="col-md-8 text-lg-end">
                        <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th> Product Item </th>
                            <th> Capital </th>
                            <th> Normal Price </th>
                            <th> Price </th>
                            <th> Stock </th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($flash_sales as $index => $flash_sale)
                        <tr>
                            <td>{{ $flash_sales->firstItem() + $index }}</td>
                            <td>{{ $flash_sale->productItem->full_name }}</td>
                            <td>{{ currency_format($flash_sale->productItem->capital, $flash_sale->productItem->currencyCode) }}</td>
                            <td>{{ currency_format($flash_sale->productItem->margin_price_public, $flash_sale->productItem->currencyCode) }}</td>
                            <td>{{ currency_format($flash_sale->price) }}</td>
                            <td>{{ $flash_sale->stock }}</td>
                            <td>
                                @include('master.action', [
                                    'edit_url' => route('flash_sale.edit', $flash_sale),
                                    'delete_url' => route('flash_sale.destroy', $flash_sale)
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
                    {!! $flash_sales->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

