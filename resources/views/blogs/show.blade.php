@extends('layouts.app', ['activePage' => 'blog'])

@push('assets')
  <style>
    .content img {
      max-width: 100%;
      height: auto;
    }
  </style>
@endpush

@section('content')
  <div class="page-header">
    <h3 class="page-title">{{ $blog->title }}</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blogs</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="mb-3">
          <span class="badge {{ $blog->status==='published' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($blog->status) }}</span>
          <span class="ms-2 text-small">{{ $blog->category?->name }} • by {{ $blog->author?->name }}</span>
        </div>
        @if($blog->thumbnail)
          <img src="{{ asset('storage/'.$blog->thumbnail) }}" alt="thumb" class="mb-3" style="max-height:220px">
        @endif
        <div class="content">
          {!! $blog->content !!}
        </div>
      </div>
    </div>
  </div>
@endsection
