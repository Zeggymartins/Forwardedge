@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Reset Password</h3>
    <form method="POST" action="{{ route('ajax.resetPassword') }}">
        @csrf
        <input type="hidden" name="token" value="{{ request()->query('token') }}">
        <input type="hidden" name="email" value="{{ request()->query('email') }}">

        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="New Password" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>
@endsection
