@extends('layouts.app', ['activePage' => 'blog'])

@section('content')
  <div class="page-header">
    <h3 class="page-title">{{ $isEdit ? 'Edit Blog' : 'Create Blog' }}</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blogs</a></li>
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

          <div class="form-group">
            <label class="required">Title</label>
            <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
              value="{{ old('title', $blog->title) }}" required>
            @include('alerts.feedback', ['field' => 'title'])
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label>Slug (optional)</label>
              <input type="text" name="slug" class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}"
                value="{{ old('slug', $blog->slug) }}" placeholder="auto from title if empty">
              @include('alerts.feedback', ['field' => 'slug'])
            </div>

            <div class="form-group col-md-6">
              <label class="required">Category</label>
              <select id="blog_category_select" name="blog_category_id"
                class="form-control {{ $errors->has('blog_category_id') ? 'is-invalid' : '' }}"
                data-placeholder="Select or type to add..." required>
                <option value=""></option>
                @foreach ($categories as $c)
                  <option value="{{ $c->id }}"
                    {{ (string) old('blog_category_id', $blog->blog_category_id) === (string) $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                  </option>
                @endforeach
              </select>
              @include('alerts.feedback', ['field' => 'blog_category_id'])
            </div>
          </div>

          <div class="form-group">
            <label class="required">Content</label>
            <div class="quill-editor">{!! old('content', $blog->content) !!}</div>
            <textarea name="content" class="d-none quill-editor-hidden {{ $errors->has('content') ? 'is-invalid' : '' }}" required></textarea>
            @include('alerts.feedback', ['field' => 'content'])
          </div>

          <div class="form-group">
            <label>Meta Description</label>
            <textarea name="meta_description" class="form-control {{ $errors->has('meta_description') ? 'is-invalid' : '' }}"
              rows="3">{{ old('meta_description', $blog->meta_description) }}</textarea>
            @include('alerts.feedback', ['field' => 'meta_description'])
          </div>

          <div class="form-group">
            <label>Meta Keyword</label>
            <input type="text" name="meta_keyword"
              class="form-control {{ $errors->has('meta_keyword') ? 'is-invalid' : '' }}"
              value="{{ old('meta_keyword', $blog->meta_keyword) }}"></input>
            @include('alerts.feedback', ['field' => 'meta_keyword'])
          </div>

          <div class="form-group">
            <label for="tags_input">Tags</label>
            <select id="tags_input" name="tags[]" class="form-control select2" multiple="multiple" style="width: 100%;">
              @foreach (\App\Models\Tag::all() as $tag)
                <option value="{{ $tag->id }}"
                  {{ isset($blog) && $blog->tags->pluck('id')->contains($tag->id) ? 'selected' : '' }}>
                  {{ $tag->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label>Thumbnail</label>
              <div class="d-flex align-items-center gap-2">
                <input type="file" id="thumbnail_file" name="thumbnail"
                  class="form-control {{ $errors->has('thumbnail') ? 'is-invalid' : '' }}" accept="image/*">
                <input type="text" id="thumbnail_url_input" name="thumbnail_url"
                  class="form-control d-none {{ $errors->has('thumbnail_url') ? 'is-invalid' : '' }} mt-2"
                  placeholder="https://example.com/image.jpg">
                <button type="button" id="toggle_thumbnail_input" class="btn btn-info btn-sm">
                  Use URL
                </button>
              </div>

              @if ($isEdit && $blog->thumbnail)
                <div class="mt-2">
                  <img src="{{ $blog->thumbnail_url }}" alt="thumb" style="height:80px">
                </div>
              @endif

              @include('alerts.feedback', ['field' => 'thumbnail'])
            </div>

            <div class="form-group col-md-6">
              <label class="required">Status</label>
              @php $st = old('status', $blog->status ?: 'draft'); @endphp
              <select name="status" class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" required>
                <option value="draft" {{ $st === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ $st === 'published' ? 'selected' : '' }}>Published</option>
              </select>
              @include('alerts.feedback', ['field' => 'status'])
            </div>
          </div>

          <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Save Changes' : 'Create' }}</button>
          <a href="{{ route('blog.index') }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection

<x-quill-editor />

@push('js')
  <script>
    $(function() {
      const $sel = $('#blog_category_select');

      $sel.select2({
        width: '100%',
        placeholder: $sel.data('placeholder') || 'Select…',
        tags: true, // izinkan nilai baru
        createTag: function(params) {
          const term = $.trim(params.term);
          if (term === '') return null;
          // tandai sebagai item baru
          return {
            id: 'new:' + term,
            text: term,
            newTag: true
          };
        },
        insertTag: function(data, tag) {
          // tampilkan item baru di urutan teratas
          data.unshift(tag);
        }
      });

      $sel.on('select2:select', function(e) {
        const data = e.params.data;
        if (!data || !data.id || !String(data.id).startsWith('new:')) return;

        const name = data.text;
        const tempId = data.id;

        // disable dulu biar tidak double klik
        $sel.prop('disabled', true);

        $.ajax({
            method: 'POST',
            url: '{{ route('blog-categories.quick-store') }}',
            data: {
              name: name,
              _token: '{{ csrf_token() }}'
            }
          })
          .done(function(resp) {
            // buat/replace option dengan ID asli dari server
            const realId = resp.id;
            // hapus option temp
            $sel.find('option[value="' + tempId.replace(/"/g, '\\"') + '"]').remove();
            // tambahkan option real
            if ($sel.find('option[value="' + realId + '"]').length === 0) {
              const newOpt = new Option(resp.name, realId, true, true);
              $sel.append(newOpt);
            }
            // set value ke id asli & trigger change
            $sel.val(String(realId)).trigger('change');
          })
          .fail(function(xhr) {
            alert('Failed creating category: ' + (xhr.responseJSON?.message || 'Unknown error'));
            // rollback: buang pilihan temp
            $sel.val('').trigger('change');
          })
          .always(function() {
            $sel.prop('disabled', false);
          });
      });
    });

    $('#tags_input').select2({
      tags: true, // bisa input baru langsung
      tokenSeparators: [','],
      placeholder: "Select or type tags"
    });

    document.addEventListener('DOMContentLoaded', function() {
      const toggleBtn = document.getElementById('toggle_thumbnail_input');
      const fileInput = document.getElementById('thumbnail_file');
      const urlInput = document.getElementById('thumbnail_url_input');

      let usingUrl = false;

      toggleBtn.addEventListener('click', () => {
        usingUrl = !usingUrl;

        if (usingUrl) {
          fileInput.classList.add('d-none');
          urlInput.classList.remove('d-none');
          toggleBtn.textContent = 'Use File';
          fileInput.value = '';
        } else {
          fileInput.classList.remove('d-none');
          urlInput.classList.add('d-none');
          toggleBtn.textContent = 'Use URL';
          urlInput.value = '';
        }
      });
    });
  </script>
@endpush
