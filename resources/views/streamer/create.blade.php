@extends('layouts.app', [
    'activePage' => 'streamer',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('streamer.index') }}">Streamers</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('streamer.store') }}" method="POST">
          @csrf
          
          <div class="form-group">
            <label for="code">Streamer Code *</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" 
              id="code" name="code" value="{{ old('code') }}" 
              placeholder="e.g., NINJA, POKIMANE" style="text-transform: uppercase;">
            @error('code')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">This code will be used by customers during checkout</small>
          </div>
          
          <div class="form-group">
            <label for="channel_name">Channel Name *</label>
            <input type="text" class="form-control @error('channel_name') is-invalid @enderror" 
              id="channel_name" name="channel_name" value="{{ old('channel_name') }}" 
              placeholder="e.g., Ninja's Gaming Channel">
            @error('channel_name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="platform">Platform *</label>
            <select class="form-control @error('platform') is-invalid @enderror" id="platform" name="platform">
              <option value="">Select Platform</option>
              <option value="YouTube" {{ old('platform') == 'YouTube' ? 'selected' : '' }}>YouTube</option>
              <option value="Twitch" {{ old('platform') == 'Twitch' ? 'selected' : '' }}>Twitch</option>
              <option value="Facebook Gaming" {{ old('platform') == 'Facebook Gaming' ? 'selected' : '' }}>Facebook Gaming</option>
              <option value="Kick" {{ old('platform') == 'Kick' ? 'selected' : '' }}>Kick</option>
              <option value="TikTok" {{ old('platform') == 'TikTok' ? 'selected' : '' }}>TikTok</option>
              <option value="Other" {{ old('platform') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('platform')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="channel_url">Channel URL</label>
            <input type="text" class="form-control @error('channel_url') is-invalid @enderror" 
              id="channel_url" name="channel_url" value="{{ old('channel_url') }}" 
              placeholder="https://youtube.com/...">
            @error('channel_url')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="user_email">Link to User (Email)</label>
            <input type="email" class="form-control @error('user_email') is-invalid @enderror"
              id="user_email" name="user_email" value="{{ old('user_email') }}"
              placeholder="Enter user email to link this streamer to their account">
            @error('user_email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Leave empty if not linking to a user account yet</small>
          </div>

          <div class="form-group">
            <label for="commission_rate">Commission Rate (%)</label>
            <input type="number" step="0.1" min="0" max="100" 
              class="form-control @error('commission_rate') is-invalid @enderror"
              id="commission_rate" name="commission_rate" value="{{ old('commission_rate', 1) }}">
            @error('commission_rate')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Default is 1%</small>
          </div>
          
          <div class="form-group">
            <label for="discount_rate">Discount Rate (%)</label>
            <input type="number" step="0.1" min="0" max="100" 
              class="form-control @error('discount_rate') is-invalid @enderror"
              id="discount_rate" name="discount_rate" value="{{ old('discount_rate', 0.5) }}">
            @error('discount_rate')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Discount given to viewers who use this code. Default is 0.5%</small>
          </div>
          <div class="form-group">
            <label for="status">Status *</label>
            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
              <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="mt-4">
            <button type="submit" class="btn btn-gradient-primary">Create Streamer</button>
            <a href="{{ route('streamer.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection