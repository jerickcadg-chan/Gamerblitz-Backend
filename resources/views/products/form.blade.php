@extends('layouts.app', [
    'activePage' => 'product',
])

@php
  $isEdit = isset($product);
@endphp

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }}</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $isEdit ? 'Edit Data' : 'Create Data' }}</li>
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
            <label for="name_input" class="required">Name</label>
            <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
              id="name_input" placeholder="e.g. Mobile Legends" value="{{ old('name', $product->name ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            <label for="code_input" class="required">Code</label>
            <input type="text" class="form-control {{ $errors->has('code') ? ' is-invalid' : '' }}" name="code"
              id="code_input" placeholder="e.g. ML, VAL, FF, etc" value="{{ old('code', $product->code ?? '') }}"
              required>
            @include('alerts.feedback', ['field' => 'code'])
          </div>

          <div class="form-group">
            <label for="category_input" class="required">Category</label>
            <select class="form-control {{ $errors->has('product_category_id') ? ' is-invalid' : '' }}"
              name="product_category_id" id="category_input" required>
              <option value="">Select category</option>
              @foreach (\App\Models\ProductCategory::all() as $category)
                <option value="{{ $category->id }}"
                  {{ (string) old('product_category_id', $product->product_category_id ?? '') === (string) $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'product_category_id'])
          </div>

          <div class="form-group">
            <label for="provider_input" class="required">Provider</label>
            <select id="provider_input" class="form-control" name="provider">
              @php $v = old('provider', $product->provider ?? ''); @endphp
              @foreach ($providers as $providerName => $providerDisplayName)
                <option value="{{ $providerName }}" {{ $v === $providerName ? 'selected' : '' }}>
                  {{ $providerDisplayName }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'provider'])
          </div>

          <div class="form-group">
            <label for="provider_code_input" class="required">Provider Product Code</label>
            <input type="text" class="form-control {{ $errors->has('provider_code') ? ' is-invalid' : '' }}"
              name="provider_code" id="provider_code_input" placeholder="e.g. ML, VAL, FF, etc"
              value="{{ old('provider_code', $product->provider_code ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'provider_code'])
          </div>

          <div class="form-group">
            <label for="provider_country_input" class="required">Country</label>
            <select id="provider_country_input" class="form-control" name="provider_country">
              @php $v = old('provider_country', $product->provider_country ?? ''); @endphp
              @foreach ($countries as $countryCode => $countryName)
                <option value="{{ $countryCode }}" {{ strtoupper($v) === strtoupper($countryCode) ? 'selected' : '' }}>
                  {{ $countryName }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'provider_country'])
          </div>

          <div class="form-group">
            <label for="company_input" class="required">Company</label>
            <input type="text" class="form-control {{ $errors->has('company') ? ' is-invalid' : '' }}" name="company"
              id="company_input" placeholder="e.g. Moonton" value="{{ old('company', $product->company ?? '') }}"
              required>
            @include('alerts.feedback', ['field' => 'company'])
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_user_input" class="required">Markup User (%)</label>
                <input type="number" min="0"
                  class="form-control {{ $errors->has('markup_user') ? ' is-invalid' : '' }}" name="markup_user"
                  id="markup_user_input" placeholder="0" value="{{ old('markup_user', $product->markup_user ?? '') }}"
                  required>
                @include('alerts.feedback', ['field' => 'markup_user'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_reseller_silver_input" class="required">Markup Reseller Silver (%)</label>
                <input type="number" min="0"
                  class="form-control {{ $errors->has('markup_reseller_silver') ? ' is-invalid' : '' }}"
                  name="markup_reseller_silver" id="markup_reseller_silver_input" placeholder="0"
                  value="{{ old('markup_reseller_silver', $product->markup_reseller_silver ?? '') }}" required>
                @include('alerts.feedback', ['field' => 'markup_reseller_silver'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_reseller_gold_input" class="required">Markup Reseller Gold (%)</label>
                <input type="number" min="0"
                  class="form-control {{ $errors->has('markup_reseller_gold') ? ' is-invalid' : '' }}"
                  name="markup_reseller_gold" id="markup_reseller_gold_input" placeholder="0"
                  value="{{ old('markup_reseller_gold', $product->markup_reseller_gold ?? '') }}" required>
                @include('alerts.feedback', ['field' => 'markup_reseller_gold'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="markup_reseller_vip_input" class="required">Markup Reseller VIP (%)</label>
                <input type="number" min="0"
                  class="form-control {{ $errors->has('markup_reseller_vip') ? ' is-invalid' : '' }}"
                  name="markup_reseller_vip" id="markup_reseller_vip_input" placeholder="0"
                  value="{{ old('markup_reseller_vip', $product->markup_reseller_vip ?? '') }}" required>
                @include('alerts.feedback', ['field' => 'markup_reseller_vip'])
              </div>
            </div>
          </div>

          <div class="form-group" id="description-group">
            <label for="description_input" class="required">
              Description
              (
              <label class="form-check-label" for="is_raw_description_input">Raw</label>
              <input type="checkbox" class="form-check-input mt-0" id="is_raw_description_input"
                     name="is_raw_description" value="1"
                {{ old('is_raw_description', $product->is_raw_description ?? false) ? 'checked' : '' }}>
              )
            </label>

            {{-- Raw textarea --}}
            <textarea class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}"
                      id="description_textarea"
                      rows="10">{{ old('description', $product->description ?? '') }}</textarea>

            {{-- Quill editor --}}
            <div id="quill-wrapper">
              <div class="quill-editor">
                {!! old('description', $product->description ?? '') !!}
              </div>
              <textarea class="d-none quill-editor-hidden" id="description_input"></textarea>
            </div>

            @include('alerts.feedback', ['field' => 'description'])
          </div>

          <div class="form-group">
            <label class="required">Input Format</label>

            {{-- Builder UI --}}
            <div x-data="inputFormatBuilder">
              <template x-for="(field, i) in fields" :key="i">
                <div class="border p-3 mb-3 rounded">
                  <div class="row">
                    <div class="col-md-3">
                      <input type="text" class="form-control" placeholder="Name"
                             x-model="field.name" @input="updateHidden()">
                    </div>
                    <div class="col-md-2">
                      <select class="form-control" x-model="field.type" @change="updateHidden()">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="option">Option</option>
                        <option value="email">Email</option>
                        <option value="password">Password</option>
                        <option value="tel">Tel</option>
                        <option value="textarea">Textarea</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <input type="text" class="form-control" placeholder="Label"
                             x-model="field.label" @input="updateHidden()">
                    </div>
                    <div class="col-md-3">
                      <input type="text" class="form-control" placeholder="Placeholder"
                             x-model="field.placeholder" @input="updateHidden()">
                    </div>
                    <div class="col-md-1 text-end">
                      <button type="button" class="btn btn-danger btn-sm" @click="removeField(i)">✕</button>
                    </div>
                  </div>

                  {{-- Kalau type = option, tampilkan sub-option --}}
                  <div class="mt-2" x-show="field.type === 'option'">
                    <h6>Options</h6>
                    <template x-for="(opt, j) in field.options" :key="j">
                      <div class="row mb-2">
                        <div class="col-md-5">
                          <input type="text" class="form-control" placeholder="Option Name"
                                 x-model="opt.name" @input="updateHidden()">
                        </div>
                        <div class="col-md-5">
                          <input type="text" class="form-control" placeholder="Option Value"
                                 x-model="opt.value" @input="updateHidden()">
                        </div>
                        <div class="col-md-2">
                          <button type="button" class="btn btn-sm btn-danger" @click="removeOption(i,j)">✕</button>
                        </div>
                      </div>
                    </template>
                    <button type="button" class="btn btn-sm btn-secondary" @click="addOption(i)">+ Add Option</button>
                  </div>
                </div>
              </template>

              <button type="button" class="btn btn-primary" @click="addField()">+ Add Field</button>
            </div>

            {{-- Hidden input untuk simpan JSON --}}
            <input type="hidden" name="input_format" id="input_format_input"
                   value="{{ old('input_format', $product->input_format ?? '[]') }}">

            @include('alerts.feedback', ['field' => 'input_format'])
          </div>

          <div class="form-group">
            <label for="how_to_order_input" class="required">How to Order</label>
            <div class="quill-editor">{!! old('how_to_order', $product->how_to_order ?? '') !!}</div>
            <textarea class="d-none quill-editor-hidden" name="how_to_order" id="how_to_order_input"></textarea>
            @include('alerts.feedback', ['field' => 'how_to_order'])
          </div>

          <div class="form-group">
            <label for="cover">Cover</label>
            <input type="file" name="cover" class="form-control {{ $errors->has('cover') ? ' is-invalid' : '' }}"
              accept="image/*">
            @include('alerts.feedback', ['field' => 'cover'])
            @if ($isEdit && !empty($product->default_cover))
              <p class="d-block text-small mt-2">Current</p>
              <a href="{{ $product->product_cover }}" target="_blank">
                <img src="{{ $product->product_cover }}" height="100" alt="image preview" />
              </a>
            @endif
            @include('alerts.feedback', ['field' => 'cover'])
          </div>

          <div class="form-group">
            <label for="picture" class="required">Picture</label>
            <input type="file" name="picture"
              class="form-control {{ $errors->has('picture') ? ' is-invalid' : '' }}" accept="image/*">
            @include('alerts.feedback', ['field' => 'picture'])
            @if ($isEdit && !empty($product->default_picture))
              <p class="d-block text-small mt-2">Current</p>
              <a href="{{ $product->product_picture }}" target="_blank">
                <img src="{{ $product->product_picture }}" height="100" alt="image preview" />
              </a>
            @endif
            @include('alerts.feedback', ['field' => 'picture'])
          </div>

          @if ($isEdit)
            <div class="form-group">
              <label class="required d-block">Status</label>
              <label class="me-3">
                <input type="radio" name="status" value="active"
                  {{ old('status', $product->status ?? 'active') === 'active' ? 'checked' : '' }}>
                Active
              </label>
              <label>
                <input type="radio" name="status" value="inactive"
                  {{ old('status', $product->status ?? 'active') === 'inactive' ? 'checked' : '' }}>
                Inactive
              </label>
              @include('alerts.feedback', ['field' => 'status'])
            </div>
          @else
            <input type="hidden" name="status" value="active">
          @endif

          <div class="form-group">
            <label for="input_meta_title">Meta Title</label>
            <input type="text" class="form-control {{ $errors->has('meta_title') ? ' is-invalid' : '' }}"
              name="meta_title" id="input_meta_title" value="{{ old('meta_title', $product->meta_title ?? '') }}">
            @include('alerts.feedback', ['field' => 'meta_title'])
          </div>

          <div class="form-group">
            <label for="input_meta_keyword">Meta Keyword</label>
            <input type="text" class="form-control {{ $errors->has('meta_keyword') ? ' is-invalid' : '' }}"
              name="meta_keyword" id="input_meta_keyword"
              value="{{ old('meta_keyword', $product->meta_keyword ?? '') }}">
            @include('alerts.feedback', ['field' => 'meta_keyword'])
          </div>

          <div class="form-group">
            <label for="input_meta_description">Meta Description</label>
            <textarea class="form-control {{ $errors->has('meta_description') ? ' is-invalid' : '' }}" name="meta_description"
              id="input_meta_description">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'meta_description'])
          </div>

          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ route('product.index') }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection

<x-quill-editor />

@push('js')
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const checkbox = document.getElementById("is_raw_description_input");
      const rawTextarea = document.getElementById("description_textarea");
      const quillHidden = document.getElementById("description_input");
      const quillWrapper = document.getElementById("quill-wrapper");

      function toggleDescription() {
        if (checkbox.checked) {
          rawTextarea.name = "description";
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
          quillHidden.name = "description";
        }
      }

      checkbox.addEventListener("change", toggleDescription);
      toggleDescription(); // initial load
    });
  </script>

  <script>
    document.addEventListener("alpine:init", () => {
      Alpine.data("inputFormatBuilder", () => ({
        fields: JSON.parse(document.getElementById("input_format_input").value || "[]"),

        addField() {
          this.fields.push({
            name: "",
            type: "text",
            label: "",
            placeholder: "",
            options: []
          });
          this.updateHidden();
        },

        removeField(i) {
          this.fields.splice(i, 1);
          this.updateHidden();
        },

        addOption(i) {
          this.fields[i].options.push({ name: "", value: "" });
          this.updateHidden();
        },

        removeOption(i, j) {
          this.fields[i].options.splice(j, 1);
          this.updateHidden();
        },

        updateHidden() {
          document.getElementById("input_format_input").value =
            JSON.stringify(this.fields, null, 2);
        }
      }))
    });
  </script>
@endpush
