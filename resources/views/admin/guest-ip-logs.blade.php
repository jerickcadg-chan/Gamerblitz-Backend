@extends('layouts.app', [
    'activePage' => 'guest-ip-logs',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Guest IP Logs </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guest-ip-logs.index') }}">Guest IP Logs</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

   <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
           <thead>
           <tr>
             <th>#</th>
             <th>IP Address</th>
             <th>Action</th>
             <th>Logged At</th>
             <th>Status</th>
             <th>Actions</th>
           </tr>
           </thead>
          <tbody>
           @forelse ($logs as $index => $log)
             <tr>
               <td>{{ $index + 1 }}</td>
               <td>{{ $log->ip_address }}</td>
               <td>{{ $log->action }}</td>
               <td>{{ parse_date_time($log->created_at) }}</td>
               <td>
                 @if($log->is_banned)
                   <span class="badge bg-danger">Banned</span>
                 @else
                   <span class="badge bg-success">Active</span>
                 @endif
               </td>
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