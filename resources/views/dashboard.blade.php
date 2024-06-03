@extends('layouts.app')

@section('content')

    @php
        use App\Models\Project;
        $search = $_GET['search'] ?? null;
        $projects = Project::query();
        $auth = auth()->user();

        if($auth->is_admin) $projectIds = Project::get()->pluck('id');
        else $projectIds = $auth->projects()->pluck('project_id');

        if($search){
            $projects->where('name', 'like', '%'.$search.'%');
        }

        $projects = $projects->whereIn('id', $projectIds)->get();

    @endphp
    
    @vite(['resources/sass/dashboard.scss'])
    <div class="col-md-10 mt-4 content w-100 h-100">
        <h1 class="pt-4">Projetos</h1>
        <form action="" class="row mb-3">
            <div class="col-md-4">
                <input type="text" name="search" placeholder="Buscar..." class="form-control" value="{{ $search }}">
            </div>
            <div class="col-md-4">
                <input type="submit" class="btn btn-primary" value="Filtrar">
            </div>
        </form>
        <div class="d-flex cards">
            <a href="#" class="card add" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="fa-solid fa-plus"></i>
            </a>
            @foreach ($projects as $project)
            <div class="card">
              @if(!$project->documents->count() && $auth->is_admin)
                <span class="delete" onclick="confirmDelete('{{ route('delete.project', ['id' => $project->id]) }}')"><i class="fa-solid fa-trash"></i></span>
              @endif
                <a href="{{route('documents', ['project_id' => $project->id])}}" class="link-project">
                    <i class="fa-solid fa-layer-group"></i>
                    <h3>{{$project->name}}</h3>                
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addProjectModalLabel">Novo Projeto</h5>
            
          </div>
          <div class="modal-body">
            <form id="addProjectForm" action="{{route('create.project')}}" method="POST">
              @csrf
              <div class="mb-3">
                <label for="projectName" class="form-label">Nome do Projeto</label>
                <input type="text" class="form-control" id="projectName" name="name" required>
              </div>
              <button type="submit" class="btn btn-primary">Salvar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script>
      function confirmDelete(url) {
        Swal.fire({
          title: 'Você tem certeza?',
          text: "Você não poderá reverter isso!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sim, exclua isso!'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = url;
          }
        });
      }
    </script>

@endsection
