@extends('layouts.app', [
    'activePage' => 'voucher',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('voucher.index', ['product_item_id' => request('product_item_id')]) }}">{{ $title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <table class="table table-nospace">
                    <tr>
                        <th>Serial Number</th>
                        <td>{{ $voucher->serial_number }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <p>{!! $voucher->status_label !!}</p>
                            @if ($voucher->order)
                                <p><a href="{{ route('order.show', $voucher->order->id) }}" target="_blank">{{ $voucher->order->code }}</a></p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Modal</th>
                        <td>{{ rp_format($voucher->capital) }}</td>
                    </tr>
                    <tr>
                        <th>Vendor</th>
                        <td>{{ $voucher->vendor }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ parse_date_time($voucher->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Vendor</th>
                        <td>{{ parse_date_time($voucher->updated_at) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <form>
                                <input type="text" class="form-control" placeholder="Enter PIN untuk melihat Password" name="pin" value="{{ request('pin') }}" required>
                                <input type="hidden" name="product_item_id" value="{{ $voucher->product_item_id }}">
                            </form>
                        </td>
                    </tr>
                    @if (\Hash::check(request('pin'), config('array.setting.pin')))
                        <tr>
                            <th>Password</th>
                            <td>{{ \Crypt::decryptString($voucher->password) }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
