@extends('layouts.app', [
    'activePage' => 'payment_method',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> Page {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('payment_method.index') }}">{{ $title }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ isset($paymentMethod) ? 'Edit Data' : 'Create Data' }}
        </li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
          @csrf
          @isset($paymentMethod) @method('PUT') @endif

          <div class="form-group">
            <label for="name_input" class="required">Name</label>
            <input type="text" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
              id="name_input" placeholder="Enter Name" value="{{ old('name', $paymentMethod->name ?? '') }}" required>
            @include('alerts.feedback', ['field' => 'name'])
          </div>

          <div class="form-group">
            <label for="slug_input" class="required">Slug</label>
            <input type="text" class="form-control {{ $errors->has('slug') ? ' is-invalid' : '' }}" name="slug"
              id="slug_input" placeholder="Unique slug (e.g.: bca-va, qris)"
              value="{{ old('slug', $paymentMethod->slug ?? '') }}" required
              @if ($paymentMethod->slug ?? '' === \App\Models\PaymentMethod::BALANCE) readonly @endif>
            @include('alerts.feedback', ['field' => 'slug'])
          </div>

          <div class="form-group">
            <label for="admin_fee_input" class="required">Admin Fee</label>
            <input type="number" step="0.01" class="form-control {{ $errors->has('admin_fee') ? ' is-invalid' : '' }}"
              name="admin_fee" id="admin_fee_input" placeholder="0"
              value="{{ old('admin_fee', $paymentMethod->admin_fee ?? 0) }}" required>
            @include('alerts.feedback', ['field' => 'admin_fee'])
          </div>

          <div class="form-group">
            <label for="admin_type_input" class="required">Admin Type</label>
            <select class="form-control {{ $errors->has('admin_type') ? ' is-invalid' : '' }}" name="admin_type"
              id="admin_type_input" required>
              @foreach (['nominal' => 'Nominal', 'percentage' => 'Percentage', 'no-admin' => 'No Admin'] as $val => $label)
                <option value="{{ $val }}"
                  {{ old('admin_type', $paymentMethod->admin_type ?? '') == $val ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'admin_type'])
          </div>

          <div class="form-group">
            <label for="vendor_input" class="required">Vendor</label>
            <select class="form-control {{ $errors->has('vendor') ? ' is-invalid' : '' }}" name="vendor"
              id="vendor_input" required>
              @foreach (['xendit' => 'Xendit', 'manual' => 'Manual', 'hitpay' => 'Hitpay', 'billplz' => 'BillPlz', 'mpay' => 'Mpay', 'cryptomus' => 'Cryptomus'] as $val => $label)
                @if (in_array($val, $supportPayments) || $val === 'manual')
                  <option value="{{ $val }}"
                    {{ old('vendor', $paymentMethod->vendor ?? '') == $val ? 'selected' : '' }}>
                    {{ $label }}
                  </option>
                @endif
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'vendor'])
          </div>

          <div class="form-group">
            <label for="type_input" class="required">Type</label>
            <select class="form-control {{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" id="type_input"
              required>
              @foreach (['all' => 'All', 'topup' => 'Top-Up', 'deposit' => 'Deposit'] as $val => $label)
                <option value="{{ $val }}"
                  {{ old('type', $paymentMethod->type ?? '') == $val ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'type'])
          </div>

          <div class="form-group">
            <label for="account_number_input">Account Number (For Manual)</label>
            <input type="text" class="form-control {{ $errors->has('account_number') ? ' is-invalid' : '' }}"
              name="account_number" id="account_number_input" placeholder="Account No. / E-Wallet No."
              value="{{ old('account_number', $paymentMethod->account_number ?? '') }}">
            @include('alerts.feedback', ['field' => 'account_number'])
          </div>

          <div class="form-group">
            <label for="category_input" class="required">Category</label>
            <select name="category" id="category_input"
              class="form-control {{ $errors->has('category') ? ' is-invalid' : '' }}" required>
              @foreach (\App\Constants\PaymentCategoryConstant::all() as $key => $label)
                <option value="{{ $label }}"
                  {{ old('category', $paymentMethod->category ?? '') == $label ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="required">Status</label><br>
            <label class="me-3">
              <input type="radio" name="is_active" value="1"
                {{ old('is_active', $paymentMethod->is_active ?? 1) == 1 ? 'checked' : '' }}> Aktif
            </label>
            <label>
              <input type="radio" name="is_active" value="0"
                {{ old('is_active', $paymentMethod->is_active ?? 1) == 0 ? 'checked' : '' }}> Non Aktif
            </label>
            @include('alerts.feedback', ['field' => 'is_active'])
          </div>

          <div class="form-group">
            <label for="ordering_input">Ordering</label>
            <input type="number" class="form-control {{ $errors->has('ordering') ? ' is-invalid' : '' }}"
              name="ordering" id="ordering_input" value="{{ old('ordering', $paymentMethod->ordering ?? '') }}">
            @include('alerts.feedback', ['field' => 'ordering'])
          </div>

          <div class="form-group">
            <label class="required">Currency</label>
            <select name="currency_code" id="currency_code"
              class="form-control {{ $errors->has('currency_code') ? 'is-invalid' : '' }}" required>
              @php
                $v = old('currency_code', isset($paymentMethod) ? @$paymentMethod->currency_code : 'USD');
              @endphp
              @foreach (\App\Constants\CurrencyConstant::all() as $currency)
                @php
                  $symbol = $currency['symbol'] ?? null;
                  $code = $currency['code'] ?? '';
                @endphp
                <option value="{{ $code }}" {{ $v === $code ? 'selected' : '' }}>
                  {{ $symbol ? "($symbol) " : '' }}{{ $code }} / {{ $currency['name'] ?? '' }}
                </option>
              @endforeach
            </select>
            @include('alerts.feedback', ['field' => 'currency_codes'])
          </div>

          <div class="form-group">
            <label class="required">Aditional Input</label>

            {{-- Builder UI --}}
            <div x-data="inputFormatBuilder">
              <template x-for="(field, i) in fields" :key="i">
                <div class="mb-3 rounded border p-3">
                  <div class="row">
                    <div class="col-md-3">
                      <input type="text" class="form-control" placeholder="Name" x-model="field.name"
                        @input="updateHidden()">
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
                      <input type="text" class="form-control" placeholder="Label" x-model="field.label"
                        @input="updateHidden()">
                    </div>
                    <div class="col-md-3">
                      <input type="text" class="form-control" placeholder="Placeholder" x-model="field.placeholder"
                        @input="updateHidden()">
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
                          <input type="text" class="form-control" placeholder="Option Name" x-model="opt.name"
                            @input="updateHidden()">
                        </div>
                        <div class="col-md-5">
                          <input type="text" class="form-control" placeholder="Option Value" x-model="opt.value"
                            @input="updateHidden()">
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
            <input type="hidden" name="additional_input" id="additional_input_input"
              value="{{ old('additional_input', json_encode($paymentMethod->additional_input ?? [])) }}">

            @include('alerts.feedback', ['field' => 'additional_input'])
          </div>

          <div class="form-group">
            <label for="picture" class="required">Picture</label>
            <input type="file" name="default_picture"
              class="form-control {{ $errors->has('default_picture') ? ' is-invalid' : '' }}" accept="image/*">
            @include('alerts.feedback', ['field' => 'default_picture'])
            @if (!empty($paymentMethod->picture))
              <p class="d-block text-small mt-2">Current</p>
              <a href="{{ $paymentMethod->picture_url }}" target="_blank">
                <img src="{{ $paymentMethod->picture_url }}" height="100" alt="image preview" />
              </a>
            @endif
            @include('alerts.feedback', ['field' => 'picture'])
          </div>

          <div class="form-group" id="description-group">
            <label for="description_input" class="required">
              Description
              (
              <label class="form-check-label" for="is_raw_description_input">Raw</label>
              <input type="checkbox" class="form-check-input mt-0" id="is_raw_description_input"
                name="is_raw_description" value="1"
                {{ old('is_raw_description', $paymentMethod->is_raw_description ?? false) ? 'checked' : '' }}>
              )
            </label>

            {{-- Raw textarea --}}
            <textarea class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}" id="description_textarea"
              rows="10">{{ old('description', $paymentMethod->description ?? '') }}</textarea>

            {{-- Quill editor --}}
            <div id="quill-wrapper">
              <div class="quill-editor">
                {!! old('description', $paymentMethod->description ?? '') !!}
              </div>
              <textarea class="d-none quill-editor-hidden" id="description_input"></textarea>
            </div>

            @include('alerts.feedback', ['field' => 'description'])
          </div>

          <button type="submit" class="btn btn-primary">Submit</button>
          <a href="{{ route('payment_method.index') }}" class="btn btn-light">Cancel</a>
        </form>
      </div>
    </div>
  </div>
@endsection

<x-quill-editor />

@push('js')
  <script>
    document.addEventListener("DOMContentLoaded", function() {
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
        fields: JSON.parse(document.getElementById("additional_input_input").value || "[]"),

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
          this.fields[i].options.push({
            name: "",
            value: ""
          });
          this.updateHidden();
        },

        removeOption(i, j) {
          this.fields[i].options.splice(j, 1);
          this.updateHidden();
        },

        updateHidden() {
          document.getElementById("additional_input_input").value =
            JSON.stringify(this.fields, null, 2);
        }
      }))
    });
  </script>
@endpush
