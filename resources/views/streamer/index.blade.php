@extends('layouts.app', [
    'activePage' => 'streamer',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('streamer.index') }}">Streamers</a></li>
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
              <input type="text" class="form-control" name="search" placeholder="Search code or channel name"
                value="{{ request('search') }}">
            </form>
          </div>
          <div class="col-md-2 mb-2">
            <form method="get">
              <select class="form-control" name="status" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
            </form>
          </div>
          <div class="col-md-6 mb-2 text-end">
            <a href="{{ route('streamer.create') }}" class="btn btn-gradient-primary btn-sm">
              <i class="mdi mdi-plus"></i> Add Streamer
            </a>
          </div>
        </div>
        <table class="table-bordered table-hover table">
          <thead>
            <tr>
              <th>#</th>
              <th>Code</th>
              <th>Channel</th>
              <th>Platform</th>
              <th>Balance</th>
              <th>Total Earnings</th>
              <th>Status</th>
              <th>Created</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($streamers as $index => $streamer)
              <tr>
                <td>{{ $streamers->firstItem() + $index }}</td>
                <td><strong>{{ $streamer->code }}</strong></td>
                <td>
                  {{ $streamer->channel_name }}
                  @if($streamer->channel_url)
                    <br><a href="{{ $streamer->channel_url }}" target="_blank" class="text-primary small">View Channel</a>
                  @endif
                </td>
                <td>{{ $streamer->platform }}</td>
                <td>{{ currency_format($streamer->balance) }}</td>
                <td>{{ currency_format($streamer->total_earnings) }}</td>
                <td>
                  @if($streamer->status === 'active')
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-secondary">Inactive</span>
                  @endif
                </td>
                <td>{{ $streamer->created_at->format('Y-m-d H:i') }}</td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="{{ route('streamer.show', $streamer->id) }}" class="btn btn-gradient-info btn-sm" title="View">
                      <i class="mdi mdi-eye"></i>
                    </a>
                    <a href="{{ route('streamer.edit', $streamer->id) }}" class="btn btn-gradient-warning btn-sm" title="Edit">
                      <i class="mdi mdi-pencil"></i>
                    </a>
                    <form action="{{ route('streamer.destroy', $streamer->id) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this streamer?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-gradient-danger btn-sm" title="Delete">
                        <i class="mdi mdi-delete"></i>
                      </button>
                    </form>
                  </div>
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
          {!! $streamers->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection