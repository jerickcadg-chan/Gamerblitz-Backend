@extends('layouts.app', [
    'activePage' => 'deposit',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
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
            <th width="200">Date</th>
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
            <th width="200">Amount</th>
            <td>{{ currency_format($deposit->amount) }}</td>
          </tr>
          <tr>
            <th width="200">Unique Code</th>
            <td>{{ $deposit->unique_code }}</td>
          </tr>
          <tr>
            <th width="200">Total Deposit</th>
            <td>{{ currency_format($deposit->total_amount) }}</td>
          </tr>
          <tr>
            <th width="200">Payment Method</th>
            <td>{{ $deposit->paymentMethod->name }}</td>
          </tr>
          @if($deposit->status === \App\Constants\StatusConst::EXPIRED)
            <tr>
              <th width="200">Expired At</th>
              <td>{{ parse_date_time($deposit->expired_at) }}</td>
            </tr>
          @endif
          @if($deposit->status === \App\Constants\StatusConst::PAID)
            <tr>
              <th width="200">Paid At</th>
              <td>{{ parse_date_time($deposit->paid_at) }}</td>
            </tr>
          @endif
          <tr>
            <th>Additional Information</th>
            <td>{!! $deposit->additional_information_html !!}</td>
          </tr>
        </table>

        {{-- Fee Breakdown (only shown for paid deposits with a payment gateway) --}}
        @if($deposit->status === \App\Constants\StatusConst::PAID)
        <div class="mt-4">
          <h5 class="mb-3" style="color: #aaa; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em;">Fee Breakdown</h5>
          <table class="table table-nospace">
            <tr>
              <th width="200">Total Deposited</th>
              <td>{{ currency_format($deposit->total_amount) }}</td>
            </tr>
            @if($feeBreakdown['has_fee'])
            <tr style="background: rgba(220,53,69,0.08);">
              <th width="200">Gateway Fee</th>
              <td class="text-danger">-{{ currency_format($feeBreakdown['gateway_fee']) }}</td>
            </tr>
            <tr style="background: rgba(220,53,69,0.05);">
              <th width="200">VAT on Fee (12%)</th>
              <td class="text-danger">-{{ currency_format($feeBreakdown['vat_on_fee']) }}</td>
            </tr>
            <tr style="background: rgba(40,167,69,0.08);">
              <th width="200"><strong>Net Amount Received</strong></th>
              <td class="text-success"><strong>{{ currency_format($feeBreakdown['net_received']) }}</strong></td>
            </tr>
            @else
            <tr>
              <th width="200">Gateway Fee</th>
              <td class="text-muted">₱0.00 <small>(no gateway fee)</small></td>
            </tr>
            <tr style="background: rgba(40,167,69,0.08);">
              <th width="200"><strong>Net Amount Received</strong></th>
              <td class="text-success"><strong>{{ currency_format($deposit->total_amount) }}</strong></td>
            </tr>
            @endif
          </table>
        </div>
        @endif

        @if($deposit->status === \App\Constants\StatusConst::PENDING)
          <form method="POST" action="{{ route('deposit.update-status', $deposit) }}">
            @csrf @method('PUT')
            <div class="row mt-4">
              <div class="col-md-2 mb-2 mb-md-0">
                <select class="form-control" name="status">
                  <option value="paid">Paid</option>
                </select>
              </div>
              <div class="col-md-4 mb-2 mb-md-0">
                <input type="number" step="0.01" name="amount" class="form-control" placeholder="Change amount">
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
