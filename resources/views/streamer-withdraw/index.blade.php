@extends('layouts.app', [
    'activePage' => 'streamer_withdraw',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('streamer-withdraw.index') }}">Streamer Withdrawals</a></li>
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
              <input type="text" class="form-control" name="search" placeholder="Search streamer code or channel"
                value="{{ request('search') }}">
            </form>
          </div>
          <div class="col-md-2 mb-2">
            <form method="get">
              <select class="form-control" name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </form>
          </div>
        </div>
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th>#</th>
              <th>Streamer</th>
              <th>Amount</th>
              <th>Payout Account</th>
              <th>Status</th>
              <th>Requested At</th>
              <th>Processed At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($withdraws as $index => $withdraw)
              <tr>
                <td>{{ $withdraws->firstItem() + $index }}</td>
                <td>
                  <strong>{{ $withdraw->streamer->code }}</strong>
                  <br>
                  <small>{{ $withdraw->streamer->channel_name }}</small>
                </td>
                <td>{{ currency_format($withdraw->amount) }}</td>
                <td>
                  <ul class="mb-0 ps-3">
                    <li>Bank: {{ $withdraw->payment_method ?? '-' }}</li>
                    <li>Account: {{ $withdraw->account_name ?? '-' }}</li>
                    <li>Number: {{ $withdraw->account_number ?? '-' }}</li>
                  </ul>
                </td>
                <td>
                  @if($withdraw->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($withdraw->status === 'approved')
                    <span class="badge badge-success">Approved</span>
                  @else
                    <span class="badge badge-danger">Rejected</span>
                  @endif
                </td>
                <td>{{ $withdraw->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $withdraw->processed_at ? $withdraw->processed_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                  @if ($withdraw->status === 'pending')
                    <div class="d-flex">
                      <form action="{{ route('streamer-withdraw.approve', $withdraw->id) }}" method="POST"
                        onsubmit="return confirm('Approve this withdrawal?')">
                        @csrf
                        <button type="submit" class="btn btn-gradient-primary btn-sm" title="Approve">
                          <i class="mdi mdi-check-bold"></i>
                        </button>
                      </form>

                      <form action="{{ route('streamer-withdraw.reject', $withdraw->id) }}" method="POST"
                        onsubmit="return confirm('Reject this withdrawal?')">
                        @csrf
                        <button type="submit" class="btn btn-gradient-danger btn-sm ms-2" title="Reject">
                          <i class="mdi mdi-close"></i>
                        </button>
                      </form>
                    </div>
                  @else
                    <span class="text-muted small">Processed</span>
                    @if($withdraw->reject_reason)
                      <br><small class="text-danger">{{ $withdraw->reject_reason }}</small>
                    @endif
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
          {!! $withdraws->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection