@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="col-md-5">

    @if(request('success'))
      <div class="d-flex justify-content-center">
        <img src="https://static.vecteezy.com/system/resources/previews/009/591/411/non_2x/check-mark-icon-free-png.png" width="200">
      </div>
      
      <div class="alert alert-success text-center">
        Seu cadastro foi enviado com sucesso. Aguarde a aprovação do administrador.
      </div>

    @else
      <h2 class="mb-4 text-center">Criar Conta</h2>

      <form action="{{ route('register.user') }}" method="POST" class="p-4 border rounded shadow-sm bg-white">
        @csrf
        <input type="hidden" name="register" value="1">

        <div class="mb-3">
          <label class="form-label">Nome</label>
          <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Senha</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <p class="text-muted small">
          Após criar seu cadastro, um administrador deverá aprovar seu acesso ao sistema.
        </p>

        <button type="submit" class="btn btn-primary w-100">Criar Conta</button>
      </form>
    @endif

  </div>
</div>
@endsection
