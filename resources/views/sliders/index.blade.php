@extends('layouts.app', [
    'activePage' => 'slider',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('slider.index') }}">{{ $title }}</a></li>
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
              <input type="text" class="form-control" name="name" placeholder="Search slider name" value="{{ request('name') }}">
            </form>
          </div>
          <div class="col-md-8 text-lg-end">
            <a href="{{ $createLink }}" class="btn btn-primary">Create data</a>
          </div>
        </div>
        <table class="table table-bordered table-hover">
          <thead>
          <tr>
            <th>No</th>
            <th> Name </th>
            <th> URL </th>
            <th> Period </th>
            <th> Slider </th>
            <th> Action </th>
          </tr>
          </thead>
          <tbody>
          @forelse ($sliders as $index => $slider)
            <tr>
              <td>{{ $sliders->firstItem() + $index }}</td>
              <td>{{ $slider->name }}</td>
              <td><a href="{{ $slider->url }}" target="_blank">Click</a></td>
              <td>{{ parse_date($slider->start_date) }} - {{ parse_date($slider->end_date) }}</td>
              <td>
                <a href="{{ $slider->picture->url }}" target="_blank">
                  <img src="{{ $slider->picture->url }}" class="w-50" style="border-radius: 0px" alt="">
                </a>
              </td>
              <td>
                @include('master.action', [
                    'view_url' => route('slider.show', $slider),
                    'edit_url' => route('slider.edit', $slider),
                    'delete_url' => route('slider.destroy', $slider)
                ])
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
          {!! $sliders->appends(request()->query())->links() !!}
        </div>
      </div>
    </div>
  </div>
@endsection
