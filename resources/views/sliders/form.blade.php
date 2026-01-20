@extends('layouts.app', [
    'activePage' => 'slider',
])

@php
    $isEdit = isset($slider);
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('slider.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? "Edit" : "Add" }} List</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $actionLink }}" enctype="multipart/form-data">
          @csrf
          @if($isEdit) @method('PUT') @endif
          <div class="form-group">
            <label for="input_name" class="required">Name</label>
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" id="input_name" placeholder="Enter name" value="{{ old('name', @$slider->name) }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>
          <div class="form-group">
            <label for="input_url" class="required">URL</label>
            <input type="text" name="url" class="form-control {{ $errors->has('url') ? ' is-invalid' : '' }}" id="input_url" placeholder="Enter URL" value="{{ old('url', @$slider->url) }}" required>
            @include('alerts.feedback', ['field' => 'url'])
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="input_start_date" class="required">Start Date</label>
                <input type="date" name="start_date" class="form-control {{ $errors->has('start_date') ? ' is-invalid' : '' }}" id="input_start_date" placeholder="Enter start date" value="{{ old('start_date', @$slider->start_date) }}" required>
                @include('alerts.feedback', ['field' => 'start_date'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="input_end_date" class="required">End Date</label>
                <input type="date" name="end_date" class="form-control {{ $errors->has('end_date') ? ' is-invalid' : '' }}" id="input_end_date" placeholder="Enter end date" value="{{ old('end_date', @$slider->end_date) }}" required>
                @include('alerts.feedback', ['field' => 'end_date'])
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="input_picture">Slider</label>
            <input type="file" name="picture" class="form-control {{ $errors->has('picture') ? ' is-invalid' : '' }}" id="input_picture" accept="image/*">
            @if(isset($slider) && $slider->picture)
              <a href="{{ $slider->picture->url }}" target="_blank">
                <img src="{{ $slider->picture->url }}" alt="" class="w-auto mt-2" style="height: 100px" />
              </a>
              <small>Make empty if don't want to change the picture</small>
            @endif
            @include('alerts.feedback', ['field' => 'picture'])
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection
