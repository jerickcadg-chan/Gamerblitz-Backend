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

          {{-- Maintenance Mode Toggle --}}
          @php $maintenanceOn = old('settings.maintenance_mode', $settings['maintenance_mode'] ?? 'off') === 'on'; @endphp
          <div class="form-group">
            <label class="font-weight-bold d-block mb-2">
              Maintenance Mode
              <span class="badge {{ $maintenanceOn ? 'badge-danger' : 'badge-success' }} ms-2">
                {{ $maintenanceOn ? 'ON' : 'OFF' }}
              </span>
            </label>
            <div class="p-3 rounded d-flex align-items-center gap-3"
              style="background:{{ $maintenanceOn ? '#fff3cd' : '#d4edda' }}; border:1px solid {{ $maintenanceOn ? '#ffc107' : '#28a745' }};">
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch"
                  id="maintenance_mode_toggle"
                  name="settings[maintenance_mode]"
                  value="on"
                  {{ $maintenanceOn ? 'checked' : '' }}
                  onchange="this.form.submit()">
                <label class="form-check-label" for="maintenance_mode_toggle">
                  {{ $maintenanceOn
                    ? '⚠️ Website is currently in Maintenance Mode — customers cannot access the site'
                    : '✅ Website is live and accessible to customers' }}
                </label>
              </div>
            </div>
            <small class="text-muted mt-1 d-block">When enabled, all customer-facing pages show a maintenance notice. The admin panel stays accessible.</small>
          </div>
          <hr class="mb-4">

          <div class="form-group">
            <label>Brand Name</label>
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
            <label>Alternative Logo</label>
            <div class="mb-2">
              @if (!empty($settings['logo_alt_url']))
                <img src="{{ $settings['logo_alt_url'] }}" alt="Logo" style="height:64px">
              @endif
            </div>
            <input type="file" class="form-control {{ $errors->has('files.logo_alt') ? 'is-invalid' : '' }}"
              name="files[logo_alt]" accept="image/*">
            @include('alerts.feedback', ['field' => 'files.logo_alt'])
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
            <label>Notification Email</label>
            <input type="text" class="form-control {{ $errors->has('settings.notif_mail') ? 'is-invalid' : '' }}"
              name="settings[notif_mail]" value="{{ old('settings.notif_mail', $settings['notif_mail'] ?? '') }}">
            @include('alerts.feedback', ['field' => 'settings.notif_mail'])
          </div>

          <div class="form-group">
            <label>Meta Description</label>
            <textarea name="settings[meta_description]"
              class="form-control {{ $errors->has('settings.meta_description') ? 'is-invalid' : '' }}">{{ old('settings.meta_description', $settings['meta_description'] ?? '') }}</textarea>
            @include('alerts.feedback', ['field' => 'settings.meta_description'])
          </div>

          <div class="form-group">
            <label>Meta Image</label>
            <div class="mb-2">
              @if (!empty($settings['meta_image_url']))
                <img src="{{ $settings['meta_image_url'] }}" alt="Meta Image" style="height:80px">
              @endif
            </div>
            <input type="file" class="form-control {{ $errors->has('files.meta_image') ? 'is-invalid' : '' }}"
              name="files[meta_image]" accept="image/*">
            @include('alerts.feedback', ['field' => 'files.meta_image'])
          </div>

          <div class="form-group">
            <label>Google Tag Manager ID</label>
            <input type="text" class="form-control {{ $errors->has('settings.gtm_id') ? 'is-invalid' : '' }}"
              name="settings[gtm_id]" value="{{ old('settings.gtm_id', $settings['gtm_id'] ?? '') }}">
            @include('alerts.feedback', ['field' => 'settings.gtm_id'])
          </div>

          <div class="form-group">
            <label>Primary Color</label>
            <input type="color"
              class="form-control form-control-color {{ $errors->has('primary_color') ? ' is-invalid' : '' }}"
              name="settings[primary_color]" value="{{ old('primary_color', $settings['primary_color']) }}"
              title="Choose your color">
            @include('alerts.feedback', ['field' => 'primary_color'])
          </div>

          <!-- Fallback margin setting -->
          <h4 class="mb-2 mt-5">Fallback Margin Setting</h4>
          <hr class="mb-4">

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="margin_public_input">Margin Public (%)</label>
                <input type="number" min="0" step="0.01"
                  class="form-control {{ $errors->has('settings.margin_public') ? ' is-invalid' : '' }}"
                  name="settings[margin_public]" id="margin_public_input" placeholder="0"
                  value="{{ old('settings.margin_public', $settings['margin_public'] ?? '') }}">
                @include('alerts.feedback', ['field' => 'settings.margin_public'])
              </div>
              <div class="form-group">
                <label for="balance_public_input">Minimum Balance Public</label>
                <input type="number" min="0" step="0.01"
                  class="form-control {{ $errors->has('settings.balance_public') ? ' is-invalid' : '' }}"
                  name="settings[balance_public]" id="balance_public_input" placeholder="0"
                  value="{{ old('settings.balance_public', $settings['balance_public'] ?? '') }}">
                @include('alerts.feedback', ['field' => 'settings.balance_public'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="margin_silver_input">Margin Silver (%)</label>
                <input type="number" min="0" step="0.01"
                  class="form-control {{ $errors->has('settings.margin_silver') ? ' is-invalid' : '' }}"
                  name="settings[margin_silver]" id="margin_silver_input" placeholder="0"
                  value="{{ old('settings.margin_silver', $settings['margin_silver'] ?? '') }}">
                @include('alerts.feedback', ['field' => 'settings.margin_silver'])
              </div>
              <div class="form-group">
                <label for="balance_silver_input">Minimum Balance Silver</label>
                <input type="number" min="0" step="0.01"
                  class="form-control {{ $errors->has('settings.balance_silver') ? ' is-invalid' : '' }}"
                  name="settings[balance_silver]" id="balance_silver_input" placeholder="0"
                  value="{{ old('settings.balance_silver', $settings['balance_silver'] ?? '') }}">
                @include('alerts.feedback', ['field' => 'settings.balance_silver'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="margin_gold_input">Margin Gold (%)</label>
                <input type="number" min="0" step="0.01"
                  class="form-control {{ $errors->has('settings.margin_gold') ? ' is-invalid' : '' }}"
                  name="settings[margin_gold]" id="margin_gold_input" placeholder="0"
                  value="{{ old('settings.margin_gold', $settings['margin_gold'] ?? '') }}">
                @include('alerts.feedback', ['field' => 'settings.margin_gold'])
              </div>
              <div class="form-group">
                <label for="balance_gold_input">Minimum Balance Gold</label>
                <input type="number" min="0" step="0.01"
                  class="form-control {{ $errors->has('settings.balance_gold') ? ' is-invalid' : '' }}"
                  name="settings[balance_gold]" id="balance_gold_input" placeholder="0"
                  value="{{ old('settings.balance_gold', $settings['balance_gold'] ?? '') }}">
                @include('alerts.feedback', ['field' => 'settings.balance_gold'])
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="margin_vip_input">Margin VIP (%)</label>
                <input type="number" min="0" step="0.01"
                  class="form-control {{ $errors->has('settings.margin_vip') ? ' is-invalid' : '' }}"
                  name="settings[margin_vip]" id="margin_vip_input" placeholder="0"
                  value="{{ old('settings.margin_vip', $settings['margin_vip'] ?? '') }}">
                @include('alerts.feedback', ['field' => 'settings.margin_vip'])
              </div>
              <div class="form-group">
                <label for="balance_vip_input">Minimum Balance VIP</label>
                <input type="number" min="0" step="0.01"
                  class="form-control {{ $errors->has('settings.balance_vip') ? ' is-invalid' : '' }}"
                  name="settings[balance_vip]" id="balance_vip_input" placeholder="0"
                  value="{{ old('settings.balance_vip', $settings['balance_vip'] ?? '') }}">
                @include('alerts.feedback', ['field' => 'settings.balance_vip'])
              </div>
            </div>
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
            <label>
              Pop Up Description
              (
              <label class="form-check-label" for="is_raw_description_input">Raw</label>
              <input type="checkbox" class="form-check-input mt-0" id="is_raw_description_input"
                name="settings[is_raw_popup_description]" value="1"
                {{ old('settings.is_raw_popup_description', $settings['is_raw_popup_description'] ?? false) ? 'checked' : '' }}>
              )
            </label>
            {{-- Raw textarea --}}
            <textarea class="form-control {{ $errors->has('settings.popup_description') ? 'is-invalid' : '' }}"
              id="description_textarea" rows="10">{{ old('settings.popup_description', $settings['popup_description'] ?? '') }}</textarea>

            {{-- Quill editor --}}
            <div id="quill-wrapper">
              <div class="quill-editor">{!! old('settings.popup_description', $settings['popup_description'] ?? '') !!}</div>
              <textarea class="quill-editor-hidden d-none {{ $errors->has('settings.popup_description') ? 'is-invalid' : '' }}">{!! old('settings.popup_description', $settings['popup_description'] ?? '') !!}</textarea>
            </div>
            @include('alerts.feedback', ['field' => 'settings.popup_description'])
          </div>


          <div class="form-group">
            <label>Pop Up Image</label>
            <div class="mb-2">
              @if (!empty($settings['popup_image_url']))
                <img src="{{ $settings['popup_image_url'] }}" alt="Popup" style="height:80px"
                  id="file_popup_image_preview">
              @endif
            </div>
            <div class="input-group mb-3">
              <input type="file" class="form-control {{ $errors->has('files.popup_image') ? 'is-invalid' : '' }}"
                name="files[popup_image]" accept="image/*" id="file_popup_image_input">
              <input type="hidden" name="clear_files[popup_image]" value="false" id="clear_popup_image_flag">
              <button type="button" class="btn btn-danger" id="clearBtn"
                onclick="clearFile('popup_image')">Clear</button>
            </div>
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
          {{-- <h4 class="mb-2 mt-5">Flash Sale Setting</h4>
          <hr class="mb-4">

          <div class="form-group">
            <label>Flash Sale Expiry (date time)</label>
            <input type="datetime-local" class="form-control" name="settings[flash_sale_expiry]"
              value="{{ old('settings.flash_sale_expiry', $settings['flash_sale_expiry'] ?? '') }}">
          </div> --}}

          {{-- Lapak Gaming --}}
          @if (in_array('lapakgaming', $supportProviders))
            <h4 class="mb-2 mt-5">Lapak Gaming</h4>
            <hr class="mb-4">

            <div class="form-group">
              <label>API Url</label>
              <input type="text" class="form-control" name="settings[lapakgaming_api_url]"
                placeholder="e.g. https://dev.lapakgaming.com"
                value="{{ old('settings.lapakgaming_api_url', $settings['lapakgaming_api_url'] ?? '') }}">
            </div>

            <div class="form-group">
              <label>API Key (Token)</label>
              <input type="text" class="form-control" name="settings[lapakgaming_api_token]"
                value="{{ old('settings.lapakgaming_api_token', $settings['lapakgaming_api_token'] ?? '') }}">
            </div>

            <div class="form-group">
              <label>Whitelist IP <small><em>(Allow these IP to access the callback endpoint)</em></small></label>
              <input type="text" class="form-control" name="settings[lapakgaming_ip]"
                placeholder="e.g. '123.123.123.123,124.124.124.124'"
                value="{{ old('settings.lapakgaming_ip', $settings['lapakgaming_ip'] ?? '') }}">
            </div>
          @endif

          {{-- Vexagame --}}
          @if (in_array('vexagame', $supportProviders))
            <h4 class="mb-2 mt-5">VexaGame</h4>
            <hr class="mb-4">

            <div class="form-group">
              <label>API Url</label>
              <input type="text" class="form-control" name="settings[vexagame_api_url]"
                placeholder="e.g. https://dev.vexagame.com"
                value="{{ old('settings.vexagame_api_url', $settings['vexagame_api_url'] ?? '') }}">
            </div>

            <div class="form-group">
              <label>API Key (Token)</label>
              <input type="text" class="form-control" name="settings[vexagame_api_token]"
                value="{{ old('settings.vexagame_api_token', $settings['vexagame_api_token'] ?? '') }}">
            </div>

            <div class="form-group">
              <label>Callback Token</label>
              <input type="text" class="form-control" name="settings[vexagame_callback_token]"
                value="{{ old('settings.vexagame_callback_token', $settings['vexagame_callback_token'] ?? '') }}">
            </div>
          @endif

          {{-- Dynasty GDS --}}
          @if (in_array('dynasty_dgs', $supportProviders))
            <h4 class="mb-2 mt-5">Dynasty GDS</h4>
            <hr class="mb-4">

            <div class="form-group">
              <label>API Url</label>
              <input type="text" class="form-control" name="settings[dynasty_gds_api_url]"
                placeholder="e.g. https://dev.dynastydgs.com"
                value="{{ old('settings.dynasty_gds_api_url', $settings['dynasty_gds_api_url'] ?? '') }}">
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="text" class="form-control" name="settings[dynasty_gds_email]"
                value="{{ old('settings.dynasty_gds_email', $settings['dynasty_gds_email'] ?? '') }}">
            </div>

            <div class="form-group">
              <label>Password</label>
              <input type="password" class="form-control" name="settings[dynasty_gds_password]"
                value="{{ old('settings.dynasty_gds_password', $settings['dynasty_gds_password'] ?? '') }}">
            </div>
          @endif

          {{-- Whitelabel --}}
          @if (in_array('whitelabel', $supportProviders))
            <h4 class="mb-2 mt-5">{{ config('app.provider_whitelabel', 'Whitelabel') }}</h4>
            <hr class="mb-4">
            {{-- Locked reveal button --}}
            <div id="gpds-locked" class="text-center py-3">
              <button type="button" class="btn btn-outline-primary btn-reveal-credentials" data-section="gpds">
                <i class="fas fa-lock me-2"></i> View {{ config('app.provider_whitelabel', 'GPDS') }} Credentials
              </button>
            </div>
            {{-- Hidden credentials (revealed after auth) --}}
            <div id="gpds-credentials" style="display:none;">
              <div class="form-group">
                <label>API Url</label>
                <input type="text" class="form-control" name="settings[whitelabel_api_url]"
                  placeholder="e.g. https://dev.whitelabel.com"
                  value="{{ old('settings.whitelabel_api_url', $settings['whitelabel_api_url'] ?? '') }}">
              </div>
              <div class="form-group">
                <label>API Key (Token)</label>
                <input type="text" class="form-control" name="settings[whitelabel_api_token]"
                  value="{{ old('settings.whitelabel_api_token', $settings['whitelabel_api_token'] ?? '') }}">
              </div>
              <div class="form-group">
                <label>Callback Token</label>
                <input type="text" class="form-control" name="settings[whitelabel_callback_token]"
                  value="{{ old('settings.whitelabel_callback_token', $settings['whitelabel_callback_token'] ?? '') }}">
              </div>
            </div>
          @endif

          {{-- Xendit --}}
          @if (in_array('xendit', $supportPayments))
            <h4 class="mb-2 mt-5">Xendit</h4>
            <hr class="mb-4">
            {{-- Locked reveal button --}}
            <div id="xendit-locked" class="text-center py-3">
              <button type="button" class="btn btn-outline-primary btn-reveal-credentials" data-section="xendit">
                <i class="fas fa-lock me-2"></i> View Xendit Credentials
              </button>
            </div>
            {{-- Hidden credentials (revealed after auth) --}}
            <div id="xendit-credentials" style="display:none;">
              <div class="form-group">
                <label>API Url</label>
                <input type="text" class="form-control" name="settings[xendit_api_url]"
                  value="{{ old('settings.xendit_api_url', $settings['xendit_api_url'] ?? '') }}">
              </div>
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
            </div>
          @endif

          {{-- Hitpay --}}
          @if (in_array('hitpay', $supportPayments))
            <h4 class="mb-2 mt-5">Hitpay</h4>
            <hr class="mb-4">

            <div class="form-group">
              <label>API Url</label>
              <input type="text" class="form-control" name="settings[hitpay_api_url]"
                value="{{ old('settings.hitpay_api_url', $settings['hitpay_api_url'] ?? '') }}">
            </div>
            <div class="form-group">
              <label>Api Key</label>
              <input type="text" class="form-control" name="settings[hitpay_api_key]"
                value="{{ old('settings.hitpay_api_key', $settings['hitpay_api_key'] ?? '') }}">
            </div>
            <div class="form-group">
              <label>Salt Key</label>
              <input type="text" class="form-control" name="settings[hitpay_salt_key]"
                value="{{ old('settings.hitpay_salt_key', $settings['hitpay_salt_key'] ?? '') }}">
            </div>
          @endif

          {{-- BillPlz --}}
          @if (in_array('billplz', $supportPayments))
            <h4 class="mb-2 mt-5">BillPlz</h4>
            <hr class="mb-4">

            <div class="form-group">
              <label>API Url</label>
              <input type="text" class="form-control" name="settings[billplz_api_url]"
                value="{{ old('settings.billplz_api_url', $settings['billplz_api_url'] ?? '') }}">
            </div>
            <div class="form-group">
              <label>Api Key</label>
              <input type="text" class="form-control" name="settings[billplz_api_key]"
                value="{{ old('settings.billplz_api_key', $settings['billplz_api_key'] ?? '') }}">
            </div>
            <div class="form-group">
              <label>Signature Payment</label>
              <input type="text" class="form-control" name="settings[billplz_signature_payment]"
                value="{{ old('settings.billplz_signature_payment', $settings['billplz_signature_payment'] ?? '') }}">
            </div>
          @endif

          {{-- Mpay --}}
          @if (in_array('mpay', $supportPayments))
            <h4 class="mb-2 mt-5">Mpay</h4>
            <hr class="mb-4">
            {{-- Locked reveal button --}}
            <div id="mpay-locked" class="text-center py-3">
              <button type="button" class="btn btn-outline-primary btn-reveal-credentials" data-section="mpay">
                <i class="fas fa-lock me-2"></i> View Mpay Credentials
              </button>
            </div>
            {{-- Hidden credentials (revealed after auth) --}}
            <div id="mpay-credentials" style="display:none;">
              <div class="form-group">
                <label>Mpay API Url</label>
                <input type="text" class="form-control" name="settings[mpay_api_url]"
                  value="{{ old('settings.mpay_api_url', $settings['mpay_api_url'] ?? '') }}">
              </div>
              <div class="form-group">
                <label>Mpay App ID</label>
                <input type="text" class="form-control" name="settings[mpay_app_id]"
                  value="{{ old('settings.mpay_app_id', $settings['mpay_app_id'] ?? '') }}">
              </div>
              <div class="form-group">
                <label>Mpay Sign Key</label>
                <input type="text" class="form-control" name="settings[mpay_sign_key]"
                  value="{{ old('settings.mpay_sign_key', $settings['mpay_sign_key'] ?? '') }}">
              </div>
            </div>
          @endif

          {{-- Cryptomus --}}
          @if (in_array('cryptomus', $supportPayments))
            <h4 class="mb-2 mt-5">Cryptomus</h4>
            <hr class="mb-4">

            <div class="form-group">
              <label>Cryptomus API Url</label>
              <input type="text" class="form-control" name="settings[cryptomus_api_url]"
                value="{{ old('settings.cryptomus_api_url', $settings['cryptomus_api_url'] ?? '') }}">
            </div>
            <div class="form-group">
              <label>Cryptomus Merchant ID</label>
              <input type="text" class="form-control" name="settings[cryptomus_merchant_id]"
                value="{{ old('settings.cryptomus_merchant_id', $settings['cryptomus_merchant_id'] ?? '') }}">
            </div>
            <div class="form-group">
              <label>Cryptomus Api Key</label>
              <input type="text" class="form-control" name="settings[cryptomus_api_key]"
                value="{{ old('settings.cryptomus_api_key', $settings['cryptomus_api_key'] ?? '') }}">
            </div>
          @endif

          <h4 class="mb-2 mt-5">Deposit Setting</h4>
          <div class="form-group">
            <label>Minimum Deposit Amount</label>
            <input type="text" class="form-control" name="settings[deposit_min_amount]"
              value="{{ old('settings.deposit_min_amount', $settings['deposit_min_amount'] ?? '') }}">
          </div>

          <div class="form-group">
            <label>Maximum Deposit Amount</label>
            <input type="text" class="form-control" name="settings[deposit_max_amount]"
              value="{{ old('settings.deposit_max_amount', $settings['deposit_max_amount'] ?? '') }}">
          </div>

          <div class="form-group">
            <label>Affiliate Percentage</label>
            <input type="text" class="form-control" name="settings[affiliate_percentage]"
              value="{{ old('settings.affiliate_percentage', $settings['affiliate_percentage'] ?? '') }}">
          </div>

          <h4 class="mb-2 mt-5">$ Base Currency</h4>
          <div class="form-group">
            <label>System Base Currency</label>
            <select class="form-control" name="settings[base_currency]"
              @isset($settings['base_currency']) disabled @endisset>
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
            <label>
              <br />
              <small>This will be used for reports and displaying data in admin panel.</small><br />
              <small>⚠️ Please note that if you change the base currency, all future transactions will use the selected
                currency for converting the transaction amounts.</small><br />
            </label>
          </div>

          <h4 class="mb-2 mt-5">Exchangerates.io</h4>
          <hr class="mb-4">

          <div class="form-group">
            <label>Exchangerates Api Url</label>
            <input type="text" class="form-control" name="settings[exchangerates_api_url]"
              value="{{ old('settings.exchangerates_api_url', $settings['exchangerates_api_url'] ?? '') }}">
          </div>
          <div class="form-group">
            <label>Exchangerates Api Key</label>
            <input type="text" class="form-control" name="settings[exchangerates_api_key]"
              value="{{ old('settings.exchangerates_api_key', $settings['exchangerates_api_key'] ?? '') }}">
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

          <div class="row">
            <div class="form-group col-md-6">
              <label>Telegram</label>
              <input type="text" class="form-control" name="settings[social_telegram]"
                value="{{ old('settings.social_telegram', $settings['social_telegram'] ?? '') }}">
            </div>
            <div class="form-group col-md-6">
              <label>Youtube</label>
              <input type="text" class="form-control" name="settings[social_youtube]"
                value="{{ old('settings.social_youtube', $settings['social_youtube'] ?? '') }}">
            </div>
          </div>

          {{-- Terms and Conditions --}}
          <h4 class="mb-2 mt-5">Terms and Conditions</h4>
          <hr class="mb-4">

          <div class="form-group">
            <div class="quill-editor">{!! old('settings.terms', $settings['terms'] ?? '') !!}</div>
            <textarea name="settings[terms]" id="input_terms"
              class="quill-editor-hidden d-none {{ $errors->has('settings.terms') ? 'is-invalid' : '' }}">{!! old('settings.terms', $settings['terms'] ?? '') !!}</textarea>
            @include('alerts.feedback', ['field' => 'settings.terms'])
          </div>

          <button type="submit" class="btn btn-primary">Save</button>
        </form>
      </div>
    </div>
  </div>
@endsection

<x-quill-editor />

@push('js')
  <script>
    function clearFile(key) {
      const input = document.getElementById(`file_${key}_input`);
      document.getElementById(`clear_${key}_flag`).value = 'true';
      document.getElementById(`file_${key}_preview`).classList.add('d-none');
      input.value = '';
    }

    document.addEventListener("DOMContentLoaded", function() {
      const checkbox = document.getElementById("is_raw_description_input");
      const rawTextarea = document.getElementById("description_textarea");
      const quillHidden = document.getElementById("description_input");
      const quillWrapper = document.getElementById("quill-wrapper");

      function toggleDescription() {
        if (checkbox.checked) {
          rawTextarea.name = "settings[popup_description]";
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
          quillHidden.name = "settings[popup_description]";
        }
      }

      checkbox.addEventListener("change", toggleDescription);
      toggleDescription(); // initial load
    });
  </script>
@endpush


{{-- ============================================================
     Credentials Reveal Modal (Password + 2FA)
     ============================================================ --}}
<div class="modal fade" id="credentialsAuthModal" tabindex="-1" aria-labelledby="credentialsAuthModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="credentialsAuthModalLabel">
          <i class="fas fa-shield-alt me-2 text-warning"></i> Verify Identity
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3">Enter your account password and 2FA code to view these credentials.</p>
        <div class="mb-3">
          <label class="form-label fw-semibold">Password</label>
          <input type="password" id="cred-password" class="form-control" placeholder="Your account password" autocomplete="current-password">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">2FA Code</label>
          <input type="text" id="cred-otp" class="form-control" placeholder="6-digit authenticator code" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
        </div>
        <div id="cred-error" class="alert alert-danger py-2 small" style="display:none;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="cred-verify-btn">
          <i class="fas fa-unlock me-1"></i> Verify &amp; View
        </button>
      </div>
    </div>
  </div>
</div>

@push('js')
<script>
(function () {
  var currentSection = null;

  document.addEventListener('DOMContentLoaded', function () {
    // Open modal when any "View ... Credentials" button is clicked
    document.querySelectorAll('.btn-reveal-credentials').forEach(function (btn) {
      btn.addEventListener('click', function () {
        currentSection = this.dataset.section;
        document.getElementById('cred-password').value = '';
        document.getElementById('cred-otp').value = '';
        document.getElementById('cred-error').style.display = 'none';
        var modal = new bootstrap.Modal(document.getElementById('credentialsAuthModal'));
        modal.show();
        setTimeout(function () { document.getElementById('cred-password').focus(); }, 400);
      });
    });

    // Verify on button click
    document.getElementById('cred-verify-btn').addEventListener('click', function () {
      var password = document.getElementById('cred-password').value.trim();
      var otp      = document.getElementById('cred-otp').value.trim();
      var errEl    = document.getElementById('cred-error');
      var btn      = this;

      if (!password || !otp) {
        errEl.textContent = 'Please enter both your password and 2FA code.';
        errEl.style.display = 'block';
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Verifying...';
      errEl.style.display = 'none';

      fetch('/admin/verify-credentials', {
        credentials: 'include',
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
        body: JSON.stringify({ password: password, one_time_password: otp }),
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-unlock me-1"></i> Verify &amp; View';

        if (data.success) {
          var lockedEl = document.getElementById(currentSection + '-locked');
          var credsEl  = document.getElementById(currentSection + '-credentials');
          if (lockedEl) lockedEl.style.display = 'none';
          if (credsEl)  credsEl.style.display  = 'block';
          bootstrap.Modal.getInstance(document.getElementById('credentialsAuthModal')).hide();
        } else {
          errEl.textContent = data.message || 'Verification failed. Please try again.';
          errEl.style.display = 'block';
        }
      })
      .catch(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-unlock me-1"></i> Verify &amp; View';
        errEl.textContent = 'An error occurred. Please try again.';
        errEl.style.display = 'block';
      });
    });

    // Allow Enter key to submit
    ['cred-password', 'cred-otp'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) {
        el.addEventListener('keydown', function (e) {
          if (e.key === 'Enter') document.getElementById('cred-verify-btn').click();
        });
      }
    });
  });
})();
</script>
@endpush
