@extends('layouts.app', ['activePage' => 'page_description'])

@section('content')
  <div class="page-header">
    <h3 class="page-title">{{ $isEdit ? 'Edit Page Description' : 'Create Page Description' }}</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('page-descriptions.index') }}">Page Description</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? 'Edit' : 'Create' }}</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
          @csrf
          @if ($isEdit)
            @method('PUT')
          @endif

          <div class="row">
            <div class="form-group col-md-6">
              <label class="required">Name</label>
              <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                value="{{ old('name', $pageDescription->name) }}" required>
              @include('alerts.feedback', ['field' => 'name'])
            </div>
            <div class="form-group col-md-6">
              <label>Slug</label>
              <input type="text" name="slug" class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}"
                value="{{ old('slug', $pageDescription->slug) }}"
                placeholder="Enter the slug from the website, e.g., 'home' for Home page or 'games' for Games page">
              @include('alerts.feedback', ['field' => 'slug'])
            </div>
          </div>

          <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
              value="{{ old('title', $pageDescription->content?->title ?? null) }}"></input>
            @include('alerts.feedback', ['field' => 'title'])
          </div>

          <div class="form-group">
            <label class="required">
              Content
              (
              <label class="form-check-label" for="is_raw_content_input">Raw</label>
              <input type="checkbox" class="form-check-input mt-0" id="is_raw_content_input" name="is_raw_content_input"
                value="1" {{ old('is_raw_content_input', false) ? 'checked' : '' }}>
              )
            </label>

            {{-- Raw textarea --}}
            <textarea class="form-control {{ $errors->has('content') ? 'is-invalid' : '' }}" id="content_textarea" rows="10">{{ old('content', $pageDescription->content?->content ?? null) }}</textarea>

            {{-- Quill editor --}}
            <div id="quill-wrapper">
              <div class="quill-editor">{!! old('content', $pageDescription->content?->content ?? null) !!}</div>
              <textarea name="content" class="d-none quill-editor-hidden {{ $errors->has('content') ? 'is-invalid' : '' }}"
                id="content_input" required></textarea>
            </div>
            @include('alerts.feedback', ['field' => 'content'])
          </div>

          <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Save Changes' : 'Create' }}</button>
          <a href="{{ route('page-descriptions.index') }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection

<x-quill-editor />

@push('js')
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const checkbox = document.getElementById("is_raw_content_input");
      const rawTextarea = document.getElementById("content_textarea");
      const quillHidden = document.getElementById("content_input");
      const quillWrapper = document.getElementById("quill-wrapper");

      function toggleDescription() {
        if (checkbox.checked) {
          rawTextarea.name = "content";
          rawTextarea.style.display = '';
          rawTextarea.disabled = false;

          quillWrapper.style.display = 'none';
          quillHidden.disabled = true;
          quillHidden.removeAttribute("name");
        } else {
          rawTextarea.removeAttribute("name");
          rawTextarea.style.display = 'none';
          rawTextarea.disabled = true;

          quillWrapper.style.display = '';
          quillHidden.disabled = false;
          quillHidden.name = "content";
        }
      }

      checkbox.addEventListener("change", toggleDescription);
      toggleDescription();
    });
  </script>
@endpush
