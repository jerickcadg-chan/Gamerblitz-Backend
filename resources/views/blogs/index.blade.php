@extends('layouts.app', ['activePage' => 'blog'])

@section('content')
  <div class="page-header mb-3 d-block">
    <a href="{{ route('blog.create') }}" class="btn btn-primary">New Blog</a>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('deposit.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">All Data</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form class="row mb-4">
          <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title ...">
          </div>
          <div class="col-md-3">
            <select name="status" class="form-control select2">
              <option value="">Status</option>
              <option value="draft" {{ request('status')==='draft' ? 'selected' : '' }}>Draft</option>
              <option value="published" {{ request('status')==='published' ? 'selected' : '' }}>Published</option>
            </select>
          </div>
          <div class="col-md-6 mb-2 pt-2">
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
            <a href="{{ url()->current() }}" class="btn btn-sm btn-danger">Reset</a>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-responsive">
            <thead>
            <tr>
              <th>Title</th>
              <th>Category</th>
              <th>Status</th>
              <th>Author</th>
              <th>Published At</th>
              <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($blogs as $blog)
              <tr>
                <td>{{ $blog->title }}</td>
                <td>{{ $blog->category?->name }}</td>
                <td>
                    <span class="badge {{ $blog->status==='published' ? 'bg-success' : 'bg-secondary' }}">
                      {{ ucfirst($blog->status) }}
                    </span>
                </td>
                <td>{{ $blog->author?->name }}</td>
                <td>{{ parse_date($blog->published_at) }}</td>
                <td>
                  @include('master.action', [
                    'view_url' => route('blog.show', $blog),
                    'edit_url' => route('blog.edit', $blog),
                    'delete_url' => route('blog.destroy', $blog),
                ])
                </td>
              </tr>
            @empty
              <tr><td colspan="100%" class="text-center text-muted">No data.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $blogs->links() }}</div>
      </div>
    </div>
  </div>
@endsection
