@extends('layouts.app', [
    'activePage' => 'streamer',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('streamer.index') }}">Streamers</a></li>
        <li class="breadcrumb-item active" aria-current="page">Details</li>
      </ol>
    </nav>
  </div>

  <div class="row">
    <div class="col-lg-4 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Streamer Info</h4>
          <table class="table">
            <tr>
              <th>Code</th>
              <td><strong>{{ $streamer->code }}</strong></td>
            </tr>
            <tr>
              <th>Channel Name</th>
              <td>{{ $streamer->channel_name }}</td>
            </tr>
            <tr>
              <th>Platform</th>
              <td>{{ $streamer->platform }}</td>
            </tr>
            <tr>
              <th>Channel URL</th>
              <td>
                @if($streamer->channel_url)
                  <a href="{{ $streamer->channel_url }}" target="_blank">{{ $streamer->channel_url }}</a>
                @else
                  -
                @endif
              </td>
            </tr>
            <tr>
              <th>Status</th>
              <td>
                @if($streamer->status === 'active')
                  <span class="badge badge-success">Active</span>
                @else
                  <span class="badge badge-secondary">Inactive</span>
                @endif
              </td>
            </tr>
            <tr>
              <th>Balance</th>
              <td><strong class="text-success">{{ currency_format($streamer->balance) }}</strong></td>
            </tr>
            <tr>
              <th>Total Earnings</th>
              <td>{{ currency_format($streamer->total_earnings) }}</td>
            </tr>
            <tr>
              <th>Created</th>
              <td>{{ $streamer->created_at->format('Y-m-d H:i') }}</td>
            </tr>
          </table>
          <div class="mt-3">
            <a href="{{ route('streamer.edit', $streamer->id) }}" class="btn btn-gradient-warning btn-sm">Edit</a>
            <a href="{{ route('streamer.index') }}" class="btn btn-light btn-sm">Back</a>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-lg-8 grid-margin stretch-card">
      <div class="card">
        <div class="card-body table-responsive">
          <h4 class="card-title">Commission History</h4>
          <table class="table-bordered table-hover table">
            <thead>
              <tr>
                <th>#</th>
                <th>Type</th>
                <th>Order Amount</th>
                <th>Commission</th>
                <th>Rate</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($histories as $index => $history)
                <tr>
                  <td>{{ $histories->firstItem() + $index }}</td>
                  <td>
                    @if($history->order_id)
                      <span class="badge badge-success">Commission</span>
                    @elseif($history->status === 'withdraw')
                      <span class="badge badge-warning">Withdraw</span>
                    @elseif($history->status === 'withdraw_approved')
                      <span class="badge badge-info">Withdraw Approved</span>
                    @elseif($history->status === 'withdraw_rejected')
                      <span class="badge badge-danger">Withdraw Rejected</span>
                    @else
                      <span class="badge badge-secondary">{{ $history->status }}</span>
                    @endif
                  </td>
                  <td>{{ currency_format($history->order_amount ?? 0) }}</td>
                  <td class="text-success">
                    +{{ currency_format($history->commission_amount ?? 0) }}
                  </td>
                  <td>{{ $history->commission_rate ?? 0 }}%</td>
                  <td>
                    @if($history->status === 'credited')
                      <span class="badge badge-success">Credited</span>
                    @elseif($history->status === 'pending')
                      <span class="badge badge-warning">Pending</span>
                    @else
                      <span class="badge badge-secondary">{{ $history->status }}</span>
                    @endif
                  </td>
                  <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="100%" class="text-center">No History</td>
                </tr>
              @endforelse
            </tbody>
          </table>
          <div class="mt-2">
            {!! $histories->appends(request()->query())->links() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection