@extends('layouts.app', [
    'activePage' => 'affiliate_withdraw',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <div class="row mb-2">
          <div class="col-md-4 mb-2">
            <form method="get">
              <input type="text" class="form-control" name="name" placeholder="Search user name"
                value="{{ request('name') }}">
            </form>
          </div>
        </div>
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Amount</th>
              <th>Payout Account</th>
              <th>Requested At</th>
              <th>Processed At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($affiliateWithdraws as $index => $withdraw)
              <tr>
                <td>{{ $affiliateWithdraws->firstItem() + $index }}</td>
                <td>
                  {{ $withdraw->user->name }}
                  <br>
                  <br>
                  <span class="fw-bold">
                    {{ $withdraw->affiliate->code }}
                  </span>
                </td>
                <td>
                  {{ currency_format($withdraw->amount) }}
                  <br>
                  <br>
                  {!! $withdraw->status_raw !!}
                </td>
                <td>
                  <ul>
                    <li>Bank Name: {{ $withdraw->payoutAccount?->bank_name ?? 'None' }}</li>
                    <li>Account Name: {{ $withdraw->payoutAccount?->account_name ?? 'None' }}</li>
                    <li>Account Number: {{ $withdraw->payoutAccount?->account_number ?? 'None' }}</li>
                  </ul>
                </td>
                <td>{{ $withdraw->requested_at }}</td>
                <td>{{ $withdraw->processed_at }}</td>
                <td>
                  @if ($withdraw->status === 'pending')
                    <div class="d-flex">
                      {{-- Paid --}}
                      <form action="{{ route('user.affiliate-withdraw.process', $withdraw->id) }}" method="POST"
                        onsubmit="return confirm('Mark this as paid?')">
                        @csrf
                        <input type="hidden" name="status" value="paid">
                        <button type="submit" class="btn btn-gradient-primary btn-sm" data-bs-toggle="tooltip"
                          title="Paid" data-bs-placement="top">
                          <i class="mdi mdi-check-bold menu-icon"></i>
                        </button>
                      </form>

                      {{-- Reject --}}
                      <form action="{{ route('user.affiliate-withdraw.process', $withdraw->id) }}" method="POST"
                        onsubmit="return confirm('Reject this withdrawal?')">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-gradient-danger btn-sm ms-2" data-bs-toggle="tooltip"
                          title="Reject" data-bs-placement="top">
                          <i class="mdi mdi-close menu-icon"></i>
                        </button>
                      </form>
                    </div>
                  @else
                    <span class="text-sm text-gray-500">Processed</span>
                  @endif
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
          {!! $affiliateWithdraws->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
