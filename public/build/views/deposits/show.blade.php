@extends('layouts.app', [
    'activePage' => 'deposit',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Halaman {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('deposit.index') }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <table class="table table-nospace">
                    <tr>
                        <th width="200">Tanggal Deposit</th>
                        <td>{{ parse_date_time($deposit->created_at) }}</td>
                    </tr>
                    <tr>
                        <th width="200">Kode</th>
                        <td>{{ $deposit->code }}</td>
                    </tr>
                    <tr>
                        <th width="200">User</th>
                        <td>{{ $deposit->user->name }}</td>
                    </tr>
                    <tr>
                        <th width="200">Status</th>
                        <td>{!! $deposit->status_raw !!}</td>
                    </tr>
                    <tr>
                        <th width="200">Jumlah</th>
                        <td>{{ rp_format($deposit->amount) }}</td>
                    </tr>
                    <tr>
                        <th width="200">Kode Unik</th>
                        <td>{{ $deposit->unique_code }}</td>
                    </tr>
                    <tr>
                        <th width="200">Total Deposit</th>
                        <td>{{ rp_format($deposit->total_amount) }}</td>
                    </tr>
                    <tr>
                        <th width="200">Metode Pembayaran</th>
                        <td>{{ $deposit->paymentMethod->name }}</td>
                    </tr>
                    @if($deposit->status === \App\Constants\StatusConst::EXPIRED)
                        <tr>
                            <th width="200">Tanggal Kadaluarsa</th>
                            <td>{{ parse_date_time($deposit->expired_at) }}</td>
                        </tr>
                    @endif
                    @if($deposit->status === \App\Constants\StatusConst::PAID)
                        <tr>
                            <th width="200">Tanggal Kadaluarsa</th>
                            <td>{{ parse_date_time($deposit->paid_at) }}</td>
                        </tr>
                    @endif
                </table>

                @if($deposit->status === \App\Constants\StatusConst::PENDING)
                    <form method="POST" action="{{ route('deposit.update-status', $deposit) }}">
                        @csrf @method('PUT')
                        <div class="row mt-4">
                            <div class="col-md-2 mb-2 mb-md-0">
                                <select class="form-control" name="status">
                                    <option value="paid">Lunas</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <input type="number" name="amount" class="form-control" placeholder="Revisi Jumlah | Kosongi Jika Tidak Direvisi">
                            </div>
                            <div class="col mb-2 mb-md-0">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
