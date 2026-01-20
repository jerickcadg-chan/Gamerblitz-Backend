@extends('layouts.app', [
    'activePage' => 'banned-ip',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Banned IPs </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('banned-ip.index') }}">Banned IPs</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="get" class="row g-3">
          <div class="col-md-4">
            <label for="ip" class="form-label">IP Address</label>
            <input type="text" class="form-control" id="ip" name="ip" placeholder="Search IP Address" value="{{ request('ip') }}">
          </div>
          <div class="col-md-4">
            <label for="reason" class="form-label">Ban Reason</label>
            <input type="text" class="form-control" id="reason" name="reason" placeholder="Search Ban Reason" value="{{ request('reason') }}">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Search</button>
            @if(request('ip') || request('reason'))
              <a href="{{ route('banned-ip.index') }}" class="btn btn-secondary">Clear</a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

   <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="row mb-2">
          <div class="col-md-12 text-lg-end">
            @can('Create Banned IP')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannedIpModal">Add Banned IP</button>
            @endcan
          </div>
        </div>
        <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>#</th>
            <th>IP Address</th>
            <th>Reason</th>
            <th>Banned At</th>
            <th>Action</th>
          </tr>
          </thead>
          <tbody>
          @forelse ($bannedIps as $index => $bannedIp)
            <tr>
              <td>{{ $bannedIps->firstItem() + $index }}</td>
              <td>{{ $bannedIp->ip_address }}</td>
              <td>{{ $bannedIp->ban_reason }}</td>
              <td>{{ parse_date_time($bannedIp->banned_at) }}</td>
               <td>
                 @can('Delete Banned IP')
                 <button type="button" class="btn btn-gradient-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $bannedIp->id }}" data-bs-toggle="tooltip" title="Delete" data-bs-placement="top">
                   <i class="mdi mdi-delete-forever menu-icon"></i>
                 </button>
                 @endcan
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
          {!! $bannedIps->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>

  @foreach ($bannedIps as $bannedIp)
  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal-{{ $bannedIp->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $bannedIp->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel-{{ $bannedIp->id }}">Delete Banned IP</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to unban this IP: {{ $bannedIp->ip_address }}?
        </div>
        <div class="modal-footer">
          <form method="POST" action="{{ route('banned-ip.destroy', $bannedIp) }}" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Yes, Unban</button>
          </form>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  @endforeach

  <!-- Modal -->
  <div class="modal fade" id="addBannedIpModal" tabindex="-1" aria-labelledby="addBannedIpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addBannedIpModalLabel">Add Banned IP</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('banned-ip.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="ip_address" class="form-label">IP Address</label>
              <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="IP Address" required>
            </div>
            <div class="mb-3">
              <label for="reason" class="form-label">Ban Reason</label>
              <textarea class="form-control" id="reason" name="reason" placeholder="Ban Reason" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Ban IP</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection