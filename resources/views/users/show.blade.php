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
            <td>{{ $user->balance->amount ? currency_format($user->balance->amount) : 0 }}</td>
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
           <tr>
             <th>Banned At</th>
             <td>{{ $user->banned_at ? parse_date_time($user->banned_at) : '-' }}</td>
           </tr>
           <tr>
             <th>Ban Reason</th>
             <td>{{ $user->ban_reason ?: '-' }}</td>
           </tr>
         </table>
         @if ($user->id != 1)
         @if (!$user->banned_at)
           @can('Ban User')
           <div class="mt-3">
             <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#banModal">
               <i class="mdi mdi-account-off"></i> Ban User
             </button>
           </div>
           @endcan
         @else
           @can('Unban User')
           <div class="mt-3">
             <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#unbanModal">
               <i class="mdi mdi-account-check"></i> Unban User
             </button>
           </div>
           @endcan
         @endif
         @endif
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
             <th>Updated By</th>
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
               <td>
                 @if($history->updater)
                   <a href="{{ route('user.show', $history->updater) }}">{{ $history->updater->name }}</a>
                 @else
                   -
                 @endif
               </td>
             </tr>
          @empty
             <tr>
               <td colspan="6" class="text-center">No Data</td>
             </tr>
          @endforelse
          </tbody>
        </table>
        <div class="mt-2">
          {!! $balanceHistories->appends(request()->query())->links() !!}
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
                  Order #{{ $history->affiliateable?->code }}
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
          {!! $affiliateHistories->appends(request()->query())->links() !!}
        </div>
      </div>
     </div>
    @endif

    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body table-responsive">
           <h3 class="page-title mb-3"> User Activity Log </h3>
          <table class="table table-bordered table-hover">
            <thead>
            <tr>
              <th>#</th>
              <th>IP Address</th>
              <th>Action</th>
              <th>Date</th>
            </tr>
            </thead>
            <tbody>
             @forelse ($activityLogs as $index => $log)
              <tr>
                 <td>{{ ($activityLogs->currentPage() - 1) * $activityLogs->perPage() + $index + 1 }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ parse_date_time($log->created_at) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="100%" class="text-center">No Data</td>
              </tr>
            @endforelse
            </tbody>
           </table>
           <div class="mt-2">
             {!! $activityLogs->links() !!}
           </div>
        </div>
      </div>
    </div>

    <!-- Ban Modal -->
   <div class="modal fade" id="banModal" tabindex="-1" aria-labelledby="banModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="banModalLabel">Ban User</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
        <form method="POST" action="{{ route('user.ban', $user) }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="reason" class="form-label">Ban Reason</label>
              <textarea class="form-control" id="reason" name="reason" placeholder="Ban Reason" required></textarea>
            </div>
            <div class="mb-3">
               <h6>User's Recent IPs:</h6>
              <div style="max-height: 200px; overflow-y: auto;">
                <ul class="list-group mb-3">
                   @forelse($activityLogs->take(5) as $log)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      {{ $log->ip_address }}
                      <small class="text-muted">{{ $log->action }} - {{ parse_date_time($log->created_at) }}</small>
                    </li>
                  @empty
                    <li class="list-group-item">No IP logs available</li>
                  @endforelse
                </ul>
              </div>
              <div class="form-check mb-3">
                <label class="form-check-label" for="ban_ip">
                  <input type="checkbox" class="form-check-input" id="ban_ip" name="ban_ip" value="1" checked>
                  Also ban all user's IPs
                </label>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-warning">Ban User</button>
          </div>
        </form>
       </div>
     </div>
   </div>

   <!-- Unban Modal -->
   <div class="modal fade" id="unbanModal" tabindex="-1" aria-labelledby="unbanModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="unbanModalLabel">Unban User</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
           Are you sure you want to unban this user?
         </div>
         <div class="modal-footer">
           <form method="POST" action="{{ route('user.unban', $user) }}" style="display: inline;">
             @csrf
             @method('POST')
             <button type="submit" class="btn btn-success">Yes, Unban</button>
           </form>
           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         </div>
       </div>
     </div>
   </div>

   <!-- Unban Modal -->
   <div class="modal fade" id="unbanModal" tabindex="-1" aria-labelledby="unbanModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="unbanModalLabel">Unban User</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
           Are you sure you want to unban this user: {{ $user->name }}?
         </div>
         <div class="modal-footer">
           <form method="POST" action="{{ route('user.unban', $user) }}" style="display: inline;">
             @csrf
             @method('POST')
             <button type="submit" class="btn btn-success">Yes, Unban</button>
           </form>
           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         </div>
       </div>
     </div>
   </div>
@endsection
