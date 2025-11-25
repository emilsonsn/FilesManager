@extends('layouts.app')

@section('content')
  @php
    use App\Models\Log;
    use App\Models\User;
    use Carbon\Carbon;
    $auth = auth()->user();

    $user_search = $_GET['user_search'] ?? '';
    $description_search = $_GET['description_search'] ?? '';
    $datetime = $_GET['datetime'] ?? '';

    $logs = Log::orderBy('id', 'desc');

    if($user_search){
      $logs->where('user_id', $user_search);
    }

    if($description_search){
      $logs->where('description','like', "%$description_search%");
    }

    if($datetime){
      $logs->whereDate('created_at', $datetime);
    }

    $logs = $logs->get();

    $users = User::get();
  @endphp

    
  @vite(['resources/sass/dashboard.scss'])
  <div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4">Auditoria do sistema</h1>

    <form action="/logs">
      <div class="row col-12">
        <div class="col-md-2">      
          <select class="form-control" name="user_search" id="">
            <option value="">Filtrar por usuário</option>
            @foreach ($users as $user)
            <option value="{{$user->id}}">{{$user->name}}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">      
          <input class="form-control" type="text" name="description_search" value="{{$description_search}}" placeholder="Buscar por descrição">
        </div>
        <div class="col-md-2">      
          <input class="form-control" type="date" name="datetime" value="{{$datetime}}">
        </div>
        <div class="col-md-3">
          <input type="submit" class="btn btn-primary" value="Filtrar">
        </div>
        </div>
      </form>
    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="text-center" scope="col">ID</th>
            <th class="text-center" scope="col">Data e hora</th>          
            <th class="text-center" scope="col">Usuário</th>
            <th class="text-left" scope="col">Descrição</th>            
          </tr>
        </thead>
        <tbody>
          @foreach ($logs as $log)
            <tr>
              <td class="text-left">{{$log->id}}</td>
              <td class="text-center">{{Carbon::parse($log->created_at)->format('d/m/Y H:m:s')}}</td>
              <td class="text-center">{{$log->user->name}}</td>
              <td class="text-left">{{$log->description}}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

@endsection
