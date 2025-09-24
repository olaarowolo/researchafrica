@extends('afriscribe.layouts.landing')

@section('page_title', 'AfriScribe Login')

@section('page_description', 'Login to your AfriScribe account')

@section('content')
<div class="login-container">
    <div class="login-form">
        <h2>Login to AfriScribe</h2>
        <form method="POST" action="{{ route('afriscribe.login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>
@endsection

@section('custom-styles')
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #f8f9fa;
}

.login-form {
    background: #fff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.login-form h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: #0c1e35;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #0c1e35;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.btn {
    width: 100%;
    padding: 0.75rem;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
}

.btn:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
}
@endsection
