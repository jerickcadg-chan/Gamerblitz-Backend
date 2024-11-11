@extends('layouts.app', [
    'activePage' => 'dashboard',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Dashboard {{ get_month_name(now()->format('m')) }}</h3>
    </div>

    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-gradient-danger card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Total Semua Pesanan <i class="mdi mdi-chart-line mdi-24px float-right"></i>
                        </h4>
                        <h2>{{ $orderSum['total'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-gradient-info card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Total Pesanan Kadaluarsa <i class="mdi mdi-bookmark-outline mdi-24px float-right"></i>
                        </h4>
                        <h2>{{ $orderSum['expired'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-gradient-success card-img-holder text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-3">Total Pesanan Selesai <i class="mdi mdi-diamond mdi-24px float-right"></i>
                        </h4>
                        <h2>{{ $orderSum['done'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mt-4">
        <div class="card">
            <div class="card-header">
                <b>Progres Harian</b>
            </div>
            <div class="card-body">
                <table class="table table-hover table-bordered">
                    <thead class="bg-gradient-danger text-white">
                        <tr>
                            <th>Tanggal</th>
                            <th>Est. Laba</th>
                            <th>Jumlah Pesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($progress as $p)
                            <tr>
                                <td>{{ parse_date($p->date) }}</td>
                                <td align="right">{{ rp_format($p->total_income) }}</td>
                                <td>{{ currency_format($p->count) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" align="right">{{ rp_format($progress->sum('total_income')) }}</td>
                            <td>{{ $progress->sum('count') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
