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
            <td>{!! $deposit->add_information_format !!}</td>
          </tr>
        </table>

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
