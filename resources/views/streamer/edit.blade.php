<?php /* This is blade.php - actual extension is .blade.php */ ?>
@extends('layouts.app', [
    'activePage' => 'streamer',
])

@section('content')
  <div class="page-header">
    <h3 class="page-title"> {{ $title }} </h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('streamer.index') }}">Streamers</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>
  </div>

  <div class="col-lg-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('streamer.update', $streamer->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="form-group">
            <label for="code">Streamer Code *</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror"
              id="code" name="code" value="{{ old('code', $streamer->code) }}"
              style="text-transform: uppercase;">
            @error('code')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="channel_name">Channel Name *</label>
            <input type="text" class="form-control @error('channel_name') is-invalid @enderror"
              id="channel_name" name="channel_name" value="{{ old('channel_name', $streamer->channel_name) }}">
            @error('channel_name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="platform">Platform *</label>
            <select class="form-control @error('platform') is-invalid @enderror" id="platform" name="platform">
              <option value="">Select Platform</option>
              <option value="YouTube" {{ old('platform', $streamer->platform) == 'YouTube' ? 'selected' : '' }}>YouTube</option>
              <option value="Twitch" {{ old('platform', $streamer->platform) == 'Twitch' ? 'selected' : '' }}>Twitch</option>
              <option value="Facebook Gaming" {{ old('platform', $streamer->platform) == 'Facebook Gaming' ? 'selected' : '' }}>Facebook Gaming</option>
              <option value="Kick" {{ $streamer->platform == 'Kick' ? 'selected' : '' }}>Kick</option>
              <option value="TikTok" {{ old('platform', $streamer->platform) == 'TikTok' ? 'selected' : '' }}>TikTok</option>
              <option value="Other" {{ old('platform', $streamer->platform) == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('platform')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="channel_url">Channel URL</label>
            <input type="text" class="form-control @error('channel_url') is-invalid @enderror"
              id="channel_url" name="channel_url" value="{{ old('channel_url', $streamer->channel_url) }}">
            @error('channel_url')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="user_email">Link to User (Email)</label>
            <input type="email" class="form-control @error('user_email') is-invalid @enderror"
              id="user_email" name="user_email"
              value="{{ old('user_email', $streamer->user ? $streamer->user->email : '') }}"
              placeholder="Enter user email to link this streamer to their account">
            @error('user_email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if($streamer->user)
              <small class="form-text text-success">Currently linked to: {{ $streamer->user->name }} ({{ $streamer->user->email }})</small>
            @else
              <small class="form-text text-muted">Leave empty if not linking to a user account</small>
            @endif
          </div>

          <div class="form-group">
            <label for="commission_rate">Commission Rate (%)</label>
            <input type="number" step="0.1" min="0" max="100"
              class="form-control @error('commission_rate') is-invalid @enderror"
              id="commission_rate" name="commission_rate"
              value="{{ old('commission_rate', $streamer->commission_rate ?? 1) }}">
            @error('commission_rate')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Default is 1%</small>
          </div>

          <div class="form-group">
            <label for="discount_rate">Discount Rate (%)</label>
            <input type="number" step="0.01" min="0" max="100"
              class="form-control @error('discount_rate') is-invalid @enderror"
              id="discount_rate" name="discount_rate"
              value="{{ old('discount_rate', $streamer->discount_rate ?? 0.5) }}">
            @error('discount_rate')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">Default is 0.5%. This discount is applied when customers use the streamer's code during checkout.</small>
          </div>

          <div class="form-group">
            <label for="status">Status *</label>
            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
              <option value="active" {{ old('status', $streamer->status) == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ old('status', $streamer->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-gradient-primary">Update Streamer</button>
            <a href="{{ route('streamer.index') }}" class="btn btn-light">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
