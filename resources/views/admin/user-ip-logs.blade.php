@extends('layouts.app', [
    'activePage' => 'user-ip-logs',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> User IP Logs </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user-ip-logs.index') }}">User IP Logs</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

   <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <form method="GET" action="{{ route('user-ip-logs.index') }}" class="mb-3">
          <div class="row">
             <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                <select class="form-control" id="type" name="type">
                 <option value="">All Users</option>
                 <option value="customer" {{ request('type') === 'customer' ? 'selected' : '' }}>Customer</option>
                 <option value="non_customer" {{ request('type') === 'non_customer' ? 'selected' : '' }}>Non-Customer</option>
                 <option value="guest" {{ request('type') === 'guest' ? 'selected' : '' }}>Guest</option>
               </select>
             </div>
            <div class="col-xl-3 col-md-6 mb-2">
               <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or email">
             </div>
            <div class="col-xl-3 col-md-6 mb-2">
               <input type="text" class="form-control" id="ip" name="ip" value="{{ request('ip') }}" placeholder="Enter IP address">
             </div>
            <div class="col-xl-3 col-md-6 mb-2">
               <button type="submit" class="btn btn-primary">Filter</button>
               @if(request('type') || request('search') || request('ip'))
                 <a href="{{ route('user-ip-logs.index') }}" class="btn btn-secondary ms-2">Clear</a>
               @endif
            </div>
          </div>
        </form>
        <table class="table table-bordered table-hover">
           <thead>
           <tr>
              <th>#</th>
              <th>User</th>
              <th>IP Address</th>
              <th>Action</th>
              <th>Logged At</th>
              <th>Actions</th>
           </tr>
           </thead>
           <tbody>
           @forelse ($logs as $index => $log)
             <tr>
               <td>{{ $index + 1 }}</td>
                <td>
                  @if($log->user_id)
                    <a href="{{ route('user.show', $log->user_id) }}">{{ $log->name }} ({{ $log->email }})</a>
                  @else
                    Guest
                  @endif
                </td>
                <td>{{ $log->ip_address }} @if($log->is_banned) <span class="badge bg-danger">Banned</span> @endif</td>
                <td>{{ $log->action }}</td>
                <td>{{ parse_date_time($log->created_at) }}</td>
                <td>
                 @if(!$log->is_banned)
                   <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#banModal" data-ip="{{ $log->ip_address }}">
                     Ban IP
                   </button>
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
       </div>
     </div>
   </div>

   <!-- Ban IP Modal -->
   <div class="modal fade" id="banModal" tabindex="-1" aria-labelledby="banModalLabel" aria-hidden="true">
     <div class="modal-dialog">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title" id="banModalLabel">Ban IP Address</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form action="{{ route('banned-ip.store') }}" method="POST">
           @csrf
           <div class="modal-body">
             <div class="mb-3">
               <label for="ip_address" class="form-label">IP Address</label>
               <input type="text" class="form-control" id="ip_address" name="ip_address" readonly>
             </div>
             <div class="mb-3">
               <label for="reason" class="form-label">Reason</label>
               <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
             </div>
           </div>
           <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
             <button type="submit" class="btn btn-danger">Ban IP</button>
           </div>
         </form>
       </div>
     </div>
   </div>

   <script>
     var banModal = document.getElementById('banModal');
     banModal.addEventListener('show.bs.modal', function (event) {
       var button = event.relatedTarget;
       var ip = button.getAttribute('data-ip');
       var modalBodyInput = banModal.querySelector('.modal-body input');
       modalBodyInput.value = ip;
     });
   </script>
 @endsection
