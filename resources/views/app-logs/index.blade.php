@extends('layouts.app', [
    'activePage' => 'app log',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('app-log.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="mt-3">
          <form>
            <div class="row g-xl-2 mb-3">
              @include('master.date-range', [
                  'col' => 'col-md-3',
                  'timePicker' => true,
              ])
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <input class="form-control" type="text" name="message" value="{{ request('message') }}"
                  placeholder="Message">
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2">
                <input class="form-control" type="text" name="context" value="{{ request('context') }}"
                  placeholder="Context">
              </div>
              <div class="col-md-6 col-lg-4 col-xl-2 mb-2 pt-2">
                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">Reset</a>
              </div>
            </div>
          </form>
          <div class="table-responsive">
            <table class="table-bordered table-hover table-responsive table">
              <thead>
                <tr class="text-center">
                  <th>#</th>
                  <th>Message</th>
                  <th>Level</th>
                  <th>Source</th>
                  <th>Context</th>
                  <th>Payload</th>
                  <th>Stack Trace</th>
                  <th>User Agent</th>
                  <th>IP Address</th>
                  <th>User ID</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($appLogs as $index => $appLog)
                  <tr>
                    <td>{{ $appLogs->firstItem() + $index }}</td>
                    <td>{{ $appLog->message }}</td>
                    <td>{{ $appLog->level }}</td>
                    <td>{{ $appLog->source }}</td>
                    <td>{{ $appLog->context }}</td>
                    <td>{{ $appLog->payload }}</td>
                    <td>{{ $appLog->stack_trace }}</td>
                    <td>{{ $appLog->user_agent }}</td>
                    <td>{{ $appLog->ip_address }}</td>
                    <td>{{ $appLog->user_id }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="100%" class="text-center">No Data</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-2">
            {!! $appLogs->appends(request()->query())->links() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
