@extends('layouts.app', [
    'activePage' => 'exchange_rate',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> {{ $title }} Page </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('exchange_rate.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $exchangeRate->currency_code }}</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th> Rate </th>
                            <th> Effective Date </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($exchangeRates as $index => $exchangeRate)
                        <tr>
                            <td>{{ 1 + $index }}</td>
                            <td>{{ currency_format($exchangeRate->rate, 8) }}</td>
                            <td>{{ parse_date_time_full($exchangeRate->effective_at) }}</td>
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

