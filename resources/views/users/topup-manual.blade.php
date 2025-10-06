@extends('layouts.app', [
    'activePage' => 'user',
])

@section('content')
    <div class="page-header">
        <h3 class="page-title"> Page {{ $title }} </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ $indexLink }}">User</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
            </ol>
        </nav>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $actionLink }}">
                    @csrf
                    <div class="form-group">
                        <label for="input_amount">User</label>
                        <input type="text" name="name" class="form-control"
                        id="input_amount" placeholder="Enter amount" value="{{ $user->name }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="input_amount">Current Balance</label>
                        <input type="text" name="name" class="form-control"
                        id="input_amount" placeholder="Enter amount" value="{{ $user?->balance?->amount ?? 0 }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="input_amount">Top-up Amount</label>
                        <input type="number" name="amount" step="any" class="form-control {{ $errors->has('amount') ? ' is-invalid' : '' }}"
                        id="input_amount" placeholder="Enter amount" value="{{ old('name') }}" required>
                        @include('alerts.feedback', ['field' => 'amount'])
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ $indexLink }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
