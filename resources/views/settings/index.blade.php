@extends('layouts.app', ['activePage' => 'setting'])

@section('content')
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('setting.update') }}" enctype="multipart/form-data">
          @csrf @method('PUT')

          {{-- Website Setting --}}
          <h4 class="mb-2">Website Setting</h4>
          <hr class="mb-4">

          <div class="form-group">
            <label>Website Name</label>
            <input type="text" class="form-control {{ $errors->has('settings.brand_name') ? 'is-invalid' : '' }}"
              name="settings[brand_name]" value="{{ old('settings.brand_name', $settings['brand_name'] ?? '') }}">
            @include('alerts.feedback', ['field' => 'settings.brand_name'])
          </div>

          <div class="form-group">
            <label>Title (Homepage Title)</label>
            <input type="text" class="form-control {{ $errors->has('settings.title') ? 'is-invalid' : '' }}"
              name="settings[title]" value="{{ old('settings.title', $settings['title'] ?? '') }}">
            @include('alerts.feedback', ['field' => 'settings.title'])
          </div>

          <div class="form-group">
            <label>Logo</label>
            <div class="mb-2">
              @if (!empty($settings['logo_url']))
                <img src="{{ $settings['logo_url'] }}" alt="Logo" style="height:64px">
              @endif
            </div>
            <input type="file" class="form-control {{ $errors->has('files.logo') ? 'is-invalid' : '' }}"
              name="files[logo]" accept="image/*">
            @include('alerts.feedback', ['field' => 'files.logo'])
          </div>

          <div class="form-group">
            <label>Favicon</label>
            <div class="mb-2">
              @if (!empty($settings['favicon_url']))
                <img src="{{ $settings['favicon_url'] }}" alt="Favicon" style="height:48px">
              @endif
            </div>
            <input type="file" class="form-control {{ $errors->has('files.favicon') ? 'is-invalid' : '' }}"
              name="files[favicon]" accept="image/*">
            @include('alerts.feedback', ['field' => 'files.favicon'])
          </div>

          <div class="form-group">
            <label>Keywords</label>
            <input type="text" class="form-control {{ $errors->has('settings.keywords') ? 'is-invalid' : '' }}"
              name="settings[keywords]" value="{{ old('settings.keywords', $settings['keywords'] ?? '') }}">
            @include('alerts.feedback', ['field' => 'settings.keywords'])
          </div>

          <div class="form-group">
            <label>Meta Description</label>
            <textarea class="form-control tinymce {{ $errors->has('settings.meta_description') ? 'is-invalid' : '' }}"
              name="settings[meta_description]">{{ old('settings.meta_description', $settings['meta_description'] ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'settings.meta_description'])
          </div>

          {{-- Pop Up Setting --}}
          <h4 class="mb-2 mt-5">Pop Up Setting</h4>
          <hr class="mb-4">

          <div class="form-group">
            <label>Pop Up Title</label>
            <input type="text" class="form-control" name="settings[popup_title]"
              value="{{ old('settings.popup_title', $settings['popup_title'] ?? '') }}">
          </div>

          <div class="form-group">
            <label>Pop Up Description</label>
            <textarea class="form-control tinymce" name="settings[popup_description]">{{ old('settings.popup_description', $settings['popup_description'] ?? '') }}</textarea>
          </div>

          <div class="form-group">
            <label>Pop Up Image</label>
            <div class="mb-2">
              @if (!empty($settings['popup_image_url']))
                <img src="{{ $settings['popup_image_url'] }}" alt="Popup" style="height:80px">
              @endif
            </div>
            <input type="file" class="form-control {{ $errors->has('files.popup_image') ? 'is-invalid' : '' }}"
              name="files[popup_image]" accept="image/*">
            @include('alerts.feedback', ['field' => 'files.popup_image'])
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label>Pop Up Button Title</label>
              <input type="text" class="form-control" name="settings[popup_button_title]"
                value="{{ old('settings.popup_button_title', $settings['popup_button_title'] ?? '') }}">
            </div>
            <div class="form-group col-md-6">
              <label>Pop Up Button Link</label>
              <input type="text" class="form-control" name="settings[popup_button_link]"
                value="{{ old('settings.popup_button_link', $settings['popup_button_link'] ?? '') }}">
            </div>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label>Pop Up Button Status</label>
              <select class="form-control" name="settings[popup_button_status]">
                @php $v = old('settings.popup_button_status', $settings['popup_button_status'] ?? 'off'); @endphp
                <option value="on" {{ $v === 'on' ? 'selected' : '' }}>On</option>
                <option value="off" {{ $v === 'off' ? 'selected' : '' }}>Off</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label>Pop Up Status</label>
              <select class="form-control" name="settings[popup_status]">
                @php $v = old('settings.popup_status', $settings['popup_status'] ?? 'off'); @endphp
                <option value="on" {{ $v === 'on' ? 'selected' : '' }}>On</option>
                <option value="off" {{ $v === 'off' ? 'selected' : '' }}>Off</option>
              </select>
            </div>
          </div>

          {{-- Flash Sale Setting --}}
          <h4 class="mb-2 mt-5">Flash Sale Setting</h4>
          <hr class="mb-4">

          <div class="form-group">
            <label>Flash Sale Expiry (date time)</label>
            <input type="datetime-local" class="form-control" name="settings[flash_sale_expiry]"
              value="{{ old('settings.flash_sale_expiry', $settings['flash_sale_expiry'] ?? '') }}">
          </div>

          {{-- Lapak Gaming --}}
          <h4 class="mb-2 mt-5">Lapak Gaming</h4>
          <hr class="mb-4">

          <div class="form-group">
            <label>API Key</label>
            <input type="text" class="form-control" name="settings[lapakgaming_api_key]"
              value="{{ old('settings.lapakgaming_api_key', $settings['lapakgaming_api_key'] ?? '') }}">
          </div>

          {{-- Xendit --}}
          <h4 class="mb-2 mt-5">Xendit</h4>
          <hr class="mb-4">

          <div class="form-group">
            <label>Secret Api Key</label>
            <input type="text" class="form-control" name="settings[xendit_secret_key]"
              value="{{ old('settings.xendit_secret_key', $settings['xendit_secret_key'] ?? '') }}">
          </div>
          <div class="form-group">
            <label>Callback Key</label>
            <input type="text" class="form-control" name="settings[xendit_callback_key]"
              value="{{ old('settings.xendit_callback_key', $settings['xendit_callback_key'] ?? '') }}">
          </div>

          <h4 class="mb-2 mt-5">$ Currency</h4>
          <div class="form-group">
            <label>System Base Currency (used for reports and displaying data in admin panel)</label>
            <select class="form-control" name="settings[base_currency]">
              @php $v = old('settings.base_currency', $settings['base_currency'] ?? 'USD'); @endphp
              @foreach ($currencies as $currency)
                @php
                  $symbol = $currency['symbol'] ?? null;
                  $code = $currency['code'] ?? '';
                @endphp
                <option value="{{ $code }}" {{ $v === $code ? 'selected' : '' }}>
                  {{ $symbol ? "($symbol) " : '' }}{{ $code }} / {{ $currency['name'] ?? '' }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Social Media Setting --}}
          <h4 class="mb-2 mt-5">Social Media Setting</h4>
          <hr class="mb-4">

          <div class="row">
            <div class="form-group col-md-6">
              <label>Whatsapp</label>
              <input type="text" class="form-control" name="settings[social_whatsapp]"
                value="{{ old('settings.social_whatsapp', $settings['social_whatsapp'] ?? '') }}">
            </div>
            <div class="form-group col-md-6">
              <label>Instagram</label>
              <input type="text" class="form-control" name="settings[social_instagram]"
                value="{{ old('settings.social_instagram', $settings['social_instagram'] ?? '') }}">
            </div>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label>Facebook</label>
              <input type="text" class="form-control" name="settings[social_facebook]"
                value="{{ old('settings.social_facebook', $settings['social_facebook'] ?? '') }}">
            </div>
            <div class="form-group col-md-6">
              <label>Tiktok</label>
              <input type="text" class="form-control" name="settings[social_tiktok]"
                value="{{ old('settings.social_tiktok', $settings['social_tiktok'] ?? '') }}">
            </div>
          </div>

          <div class="form-group">
            <label>Youtube</label>
            <input type="text" class="form-control" name="settings[social_youtube]"
              value="{{ old('settings.social_youtube', $settings['social_youtube'] ?? '') }}">
          </div>

          {{-- Terms and Conditions --}}
          <h4 class="mb-2 mt-5">Terms and Conditions</h4>
          <hr class="mb-4">

          <div class="form-group">
            <textarea class="form-control tinymce" name="settings[terms]">{{ old('settings.terms', $settings['terms'] ?? '') }}</textarea>
          </div>

          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('js')
  <x-tinymce-script />
@endpush
