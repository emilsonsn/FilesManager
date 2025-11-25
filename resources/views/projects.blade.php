@php
    $search ??= '';
    $auth = auth()->user();
@endphp

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="{{asset('assets/favicon.png')}}">


  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <!-- Bootstrap JS -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  {{-- Shoices --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

  <!-- Add this line before including Bootstrap's JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Styles -->
  @vite(['resources/sass/app.scss', 'resources/sass/normalize.scss', 'resources/sass/menu.scss'])

  <!-- Scripts -->
  @vite(['resources/js/app.js'])

</head>
    @vite(['resources/sass/dashboard.scss'])
    @include('layouts.app_header')
    <div style="display: flex; height: 100vh; overflow: hidden">
      <div class="main p-3 pb-0 pt-5 w-100 h-100">
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
            @if(auth()->user()->is_admin || auth()->user()->create_projects)
            <a href="#" class="card add" data-bs-toggle="modal" data-bs-target="#addProjectModal">
              <i class="fa-solid fa-plus"></i>
          </a>
          
            @endif
            @foreach ($projects as $project)
              <div class="card">
                @if(!$project->documents->count() && $auth->is_admin)
                  <span class="delete" onclick="confirmDelete('{{ route('delete.project', ['id' => $project->id]) }}')"><i class="fa-solid fa-trash"></i></span>
                @endif
                @if($auth->is_admin)
                <span class="edit edit-btn" data-size="{{$project->size}}" data-id="{{$project->id}}" data-name="{{$project->name}}"><i class="fa-solid fa-edit"></i></span>
                @endif
                  <a href="{{route('documents', ['project_id' => $project->id])}}" class="link-project d-flex">
                      @if($project->image_path)
                          <img src="{{ asset('storage/' . $project->image_path) }}" alt="{{ $project->name }}" class="img-fluid" width="70">
                      @else
                          <i class="fa-solid fa-layer-group"></i>
                      @endif
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
              <form id="addProjectForm" action="{{route('create.project')}}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="id" id="projectId">
                  <div class="mb-3">
                      <label for="projectName" class="form-label">Nome do Projeto</label>
                      <input type="text" class="form-control" id="projectName" name="name" required>
                  </div>
                  <div class="mb-3">
                    <label for="limitSize" class="form-label">Limite de armazenamento (MB)</label>
                    <input type="number" class="form-control" id="limitSize" name="size" required>
                </div>
                  <div class="mb-3">
                      <label for="projectImage" class="form-label">Imagem do Projeto</label>
                      <input type="file" class="form-control" id="projectImage" name="image">
                  </div>
                  <button type="submit" class="btn btn-primary">Salvar</button>
              </form>
          </div>
      </div>
  </div>
</div>

  {{-- @endsection --}}
  
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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const addProjectModal = new bootstrap.Modal(document.getElementById('addProjectModal'));
    const addProjectForm = document.getElementById('addProjectForm');

    // Limpar formulário ao clicar no botão de adicionar
    document.querySelector('a.card.add').addEventListener('click', function() {
      addProjectForm.reset();
      document.getElementById('projectId').value = '';
    });

    // Preencher formulário ao clicar no botão de editar
    document.querySelectorAll('.edit-btn').forEach(button => {
      button.addEventListener('click', function() {
        const projectId = this.dataset.id;
        const projectName = this.dataset.name;
        const size = this.dataset.size;

        document.getElementById('projectId').value = projectId;
        document.getElementById('projectName').value = projectName;
        document.getElementById('limitSize').value = size;

        addProjectModal.show();
      });
    });
  });
</script>


    