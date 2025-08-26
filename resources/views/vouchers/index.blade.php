@extends('layouts.app', [
    'activePage' => 'voucher',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} {{ !is_null($productItem) ? $productItem->name : null }}</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('voucher.index', ['product_item_id' => request('product_item_id')]) }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Data List</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="row mb-2">
                    <div class="col-md-5">
                        <form method="get">
                          <input type="text" class="form-control" name="serial_number" placeholder="Search serial number" value="{{ request('serial_number') }}">
                          <input type="hidden" name="product_item_id" value="{{ request('product_item_id') }}">
                        </form>
                    </div>
                    <div class="col-7 text-lg-end">
                        <a href="{{ route('voucher.import', ['product_item_id' => request('product_item_id')]) }}" class="btn btn-success">Import data</a>
                        <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Serial Number </th>
                            <th> Status </th>
                            <th> Modal </th>
                            <th> Vendor </th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vouchers as $index => $voucher)
                        <tr>
                            <td>{{ $vouchers->firstItem() + $index }}</td>
                            <td>{{ $voucher->serial_number }}</td>
                            <td>{!! $voucher->status_label !!}</td>
                            <td>{{ rp_format($voucher->capital) }}</td>
                            <td>{{ $voucher->vendor }}</td>
                            <td>
                                @include('master.action', [
                                    'view_url' => route('voucher.show', $voucher). '?product_item_id='. request('product_item_id'),
                                    'edit_url' => route('voucher.edit', $voucher). '?product_item_id='. request('product_item_id'),
                                    'delete_url' => route('voucher.destroy', $voucher)
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
                    {!! $vouchers->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
