@extends('layouts.app')
@section('content')

@php
$setting = \App\Models\Setting::where('status', 1)->first();
@endphp

<style>
* {
  box-sizing: border-box;
}
body {
  margin: 0;
  font-family: sans-serif;
}
a {
  color: #666;
  font-size: 14px;
  display: block;
}
.login-title {
  text-align: center;
}
#login-page {
  display: flex;
}
.notice {
  font-size: 13px;
  text-align: center;
  color: #666;
}
.login {
  width: 30%;
  height: 100vh;
  background: #FFF;
  padding: 70px;
}
.login a {
  margin-top: 25px;
  text-align: center;
}
.form-login {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  align-content: center;
}
.form-login label {
  text-align: left;
  font-size: 13px;
  margin-top: 10px;
  margin-left: 20px;
  display: block;
  color: #666;
}
.input-email,
.input-password {
  width: 100%;
  background: #ededed;
  border-radius: 25px;
  margin: 4px 0 10px 0;
  padding: 10px;
  display: flex;
}
.icon {
  padding: 4px;
  color: #666;
  min-width: 30px;
  text-align: center;
}
input[type="email"],
input[type="password"] {
  width: 100%;
  border: 0;
  background: none;
  font-size: 16px;
  padding: 4px 0;
  outline: none;
}
button[type="submit"] {
  width: 100%;
  border: 0;
  border-radius: 25px;
  padding: 14px;
  background: #060606ff;
  color: #FFF;
  display: inline-block;
  cursor: pointer;
  font-size: 16px;
  font-weight: bold;
  margin-top: 10px;
  transition: ease all 0.3s;
}
button[type="submit"]:hover {
  opacity: 0.9;
}
.background {
  width: 70%;
  padding: 40px;
  height: 100vh;
  background: linear-gradient(60deg, rgba(47, 48, 43, 0.5), rgba(31, 49, 42, 0.7)), url('https://picsum.photos/1280/720') center no-repeat;
  background-size: cover;
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  justify-content: flex-end;
  align-content: center;
  flex-direction: row;
}
.background h1 {
  max-width: 420px;
  color: #FFF;
  text-align: right;
  padding: 0;
  margin: 0;
}
.background p {
  max-width: 650px;
  color: #1a1a1a;
  font-size: 15px;
  text-align: right;
  padding: 0;
  margin: 15px 0 0 0;
}
.created {
  margin-top: 40px;
  text-align: center;
}
.created p {
  font-size: 13px;
  font-weight: bold;
  color: #008552;
}
.created a {
  color: #666;
  font-weight: normal;
  text-decoration: none;
  margin-top: 0;
}
.checkbox label {
  display: inline;
  margin: 0;
}
</style>

<div id="login-page">
  <div class="login">
    <h4 class="login-title">Research Africa | Global Admin Login</h4>
    <p class="notice">Please login to access the system</p>
    <form method="POST" action="{{ route('admin.submit-login') }}" class="form-login">
      @csrf
      <label for="email">E-mail</label>
      <div class="input-email">
        <i class="fas fa-envelope icon"></i>
        <input id="email" type="email" name="email" placeholder="Enter your e-mail" required value="{{ old('email') }}">
      </div>
      @error('email')
        <div class="text-danger ms-3">{{ $message }}</div>
      @enderror
      <label for="password">Password</label>
      <div class="input-password">
        <i class="fas fa-lock icon"></i>
        <input id="password" type="password" name="password" placeholder="Enter your password" required>
      </div>
      @error('password')
        <div class="text-danger ms-3">{{ $message }}</div>
      @enderror
      <div class="checkbox">
        <label for="remember">
          <input type="checkbox" name="remember" id="remember">
          Remember me
        </label>
      </div>
      <button type="submit"><i class="fas fa-door-open"></i> Sign in</button>
    </form>
    @if(Route::has('admin.password.email'))
      <a href="{{ route('admin.password.request') }}">Forgot your password?</a>
    @endif
    <div class="created">
      <p>Created by <a href="https://codepen.io/kelvinqueiroz/">Kelvin Queiróz</a></p>
    </div>
  </div>
  <div class="background">
    <h1>Welcome to the Admin Portal. Please login to manage the system securely.</h1>
  </div>
</div>

@endsection
