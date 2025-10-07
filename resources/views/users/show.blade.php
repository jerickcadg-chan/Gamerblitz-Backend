@extends('layouts.app', [
    'activePage' => 'user',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <table class="table table-nospace">
          <tr>
            <th>Name</th>
            <td>{{ $user->name }}</td>
          </tr>
          <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
          </tr>
          <tr>
            <th>Phone Number</th>
            <td>{{ $user->phone_number }}</td>
          </tr>
          <tr>
            <th>Role</th>
            <td>{{ $user->role }}</td>
          </tr>
          <tr>
            <th>Balance</th>
            <td>{{ $user->balance->amount ?? 0 }}</td>
          </tr>
          <tr>
            <th>Affiliate</th>
            @isset($user->affiliate)
              <td>{{ $user->affiliate?->code }} - {{ $user->affiliate->status }}</td>
            @else
              <td>-</td>
            @endisset
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ parse_date_time($user->created_at) }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ parse_date_time($user->updated_at) }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <h3 class="page-title mb-3"> Balance History </h3>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Balance From</th>
            <th>Nominal</th>
            <th>Description</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($balanceHistories as $index => $history)
            <tr>
              <td>{{ $balanceHistories->firstItem() + $index }}</td>
              <td>{{ parse_date_time($history->created_at) }}</td>
              <td>
                @if(strpos($history->balanceable_type, 'Deposit'))
                  By Deposit {{ $history->balanceable->code }}
                @else
                  By Admin
                @endif
              </td>
              <td>{{ currency_format($history->amount) }}</td>
              <td>{{ $history->description }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="100%" class="text-center">No Data</td>
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

  @if($user->affiliate)
    <div class="card mt-4">
      <div class="card-body table-responsive">
        <h3 class="page-title mb-3">Affiliate History</h3>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Description</th>
            <th>Balance From</th>
            <th>Balance After</th>
            <th>Nominal</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($affiliateHistories as $index => $history)
            <tr>
              <td>{{ $affiliateHistories->firstItem() + $index }}</td>
              <td>{{ parse_date_time($history->created_at) }}</td>
              <td>
                @if(strpos($history->affiliateable_type, 'AffiliateWithdraw'))
                  Withdraw #{{ $history->affiliateable_id }}
                @elseif(strpos($history->affiliateable_type, 'Order'))
                  Order #{{ $history->affiliateable->code }}
                @else
                  By Admin
                @endif
              </td>
              <td>{{ currency_format($history->amount_before) }}</td>
              <td>{{ currency_format($history->amount_before + $history->amount) }}</td>
              <td>{{ currency_format($history->amount) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center">No Data</td>
            </tr>
          @endforelse
          </tbody>
        </table>

        <div class="mt-2">
          {!! $histories->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  @endif
@endsection
