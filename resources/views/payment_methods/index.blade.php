@extends('layouts.app', [
    'activePage' => 'payment_method',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('payment_method.index') }}">{{ $title }}</a></li>
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
                            <th>Name</th>
                            <th>Currency</th>
                            <th>Vendor</th>
                            <th>Category</th>
                            <th> Action </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentMethods as $index => $paymentMethod)
                        <tr>
                            <td>{{ $paymentMethods->firstItem() + $index }}</td>
                            <td>{{ $paymentMethod->name }}</td>
                            <td>{{ $paymentMethod->currency_code }}</td>
                            <td>{{ $paymentMethod->vendor }}</td>
                            <td>{{ $paymentMethod->category }}</td>
                            <td>
                                @include('master.action', [
                                    'view_url' => route('payment_method.show', $paymentMethod),
                                    'edit_url' => route('payment_method.edit', $paymentMethod),
                                    'delete_url' => route('payment_method.destroy', $paymentMethod)
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
                    {!! $paymentMethods->appends(request()->query())->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

