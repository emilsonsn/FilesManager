@extends('layouts.app')

@section('content')
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  @vite(['resources/sass/login.scss'])

  <main class="container-fluid" style="width: 100%; height: 100vh">
    <div class="row justify-content-center" style="height: 100%;">
      <div style="position: relative"
           class="bg-login col-md-8 d-none d-md-flex align-items-center justify-content-center">
        <div style="
        z-index: 99;
         align-items: center;
        justify-content: center;
        overflow: hidden;
        display: flex;">
        <lottie-player class="login-animation" src="https://lottie.host/aa52dfab-b423-4232-9689-f1acfd2f8988/EmBEqL8JG9.json" background="#ffffff" speed="1" loop autoplay direction="1" mode="normal"></lottie-player>
        <script>
            var host = document.querySelector( '.login-animation' )
            var style = document.createElement( 'style' )
            style.innerHTML = 'g[transform="matrix(0.5,0,0,0.5,686.2139892578125,54.50499725341797)"] {display: none !important; }'
            host.shadowRoot.appendChild( style )            
        </script>
        </div>
      </div>
      <div class="form-container col-md-4 r-bg-login">
        <img class="logo" src="{{asset('assets/logoCaio.png')}}" alt="">
        <form method="POST" action="{{ route('login') }}">
          @csrf
          <div class="row mb-3">
            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>

            <div class="col-md-6">
              <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

              @error('email')
              <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Senha') }}</label>

            <div class="col-md-6">
              <input id="password" type="password"
                     class="form-control @error('password') is-invalid @enderror" name="password" required
                     autocomplete="current-password">

              @error('password')
              <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6 offset-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                  {{ old('remember') ? 'checked' : '' }}>

                <label class="form-check-label" for="remember">
                  {{ __('Lembre de mim') }}
                </label>
              </div>
            </div>
          </div>

          <div class="row mb-0">
            <div class="col-md-6 offset-md-4">
              <button type="submit" class="btn" style="width: 100%; ">
                {{ __('Entrar') }}
              </button>

            </div>
            <div class="col-md-8 offset-md-4">
              {{-- @if (Route::has('password.request'))
                <a class="btn btn-link" href="{{ route('password.request') }}">
                  {{ __('Esqueceu sua senha?') }}
                </a>
              @endif --}}

            </div>
          </div>
        </form>
      </div>
    </div>
  </main>
@endsection
