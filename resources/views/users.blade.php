@extends('layouts.app')

@section('content')
  @php
    use App\Models\User;
    use App\Models\Project;
    $search = $_GET['search'] ?? null;
    $users = User::where('is_admin', 0);

    if($search){
        $users->where('name', 'like', '%'.$search.'%')
              ->orWhere('email', 'like', '%'.$search.'%');
    }

    $users = $users->get();
    $projects = Project::get();
  @endphp

  <style>
    .table-projects tr td{
      width: 33.33%;
    }
  </style>
    
  @vite(['resources/sass/dashboard.scss'])
  <div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4">Usuários</h1>
    <a href="#" class="fs-1 c-green add" data-bs-toggle="modal" data-bs-target="#addUserModal">
      <i class="fa-solid fa-circle-plus"></i>
    </a>

    <form action="" class="row mb-3">
      <div class="col-md-3">
        <input value="{{$search}}" type="text" name="search" placeholder="Buscar por nome ou email" class="form-control">
      </div>
      <div class="col-md-2">
        <input type="submit" class="btn btn-primary" value="Filtrar">
      </div>
    </form>

    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nome</th>
            <th scope="col">Email</th>
            <th scope="col">Projetos</th>
            <th scope="col">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @foreach ($user->projects as $project)
                    {{$project->project->name . ' ,'}}
                  @endforeach
                </td>
                <td>
                  <a href="#" class="me-2 projects" data-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#projectsModal">
                    <i class="fa-solid fa-house-chimney-medical"></i>
                  </a>
                  <a href="#" class="edit-user" data-id="{{ $user->id }}" data-upload_limit="{{ $user->upload_limit }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-is_admin="{{ $user->is_admin }}" data-read_doc="{{ $user->read_doc }}" data-create_doc="{{ $user->create_doc }}" data-edit_doc="{{ $user->edit_doc }}" data-delete_doc="{{ $user->delete_doc }}" data-read_temporality="{{ $user->read_temporality }}" data-create_temporality="{{ $user->create_temporality }}" data-edit_temporality="{{ $user->edit_temporality }}" data-delete_temporality="{{ $user->delete_temporality }}" data-read_collection="{{ $user->read_collection }}" data-create_collection="{{ $user->create_collection }}" data-edit_collection="{{ $user->edit_collection }}" data-delete_collection="{{ $user->delete_collection }}">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                  <a href="{{route('delete.user', ['id' => $user->id])}}" class="delete-user"><i class="fa-solid fa-trash ms-3"></i></a>
                </td>
                
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Adicionar Usuário</h5>
          
        </div>
        <form action="{{ route('create.user') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="id">
            <div class="mb-3">
              <label for="name" class="form-label">Nome</label>
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Senha</label>
              <input type="password" class="form-control" id="password" name="password" autocomplete="false">
            </div>
            <div class="mb-3">
              <label for="upload_limit" class="form-label">Limite de upload (MB)</label>
              <input type="number" step="1" class="form-control" id="upload_limit" name="upload_limit">
            </div>
            <div class="mb-3">
              <label for="permissions" class="form-label">Permissões</label>
              <div class="row">
                @foreach(['read_doc' => 'Ler Documentos', 'create_doc' => 'Criar Documentos', 'edit_doc' => 'Editar Documentos', 'delete_doc' => 'Deletar Documentos', 'read_temporality' => 'Ler Temporalidades', 'create_temporality' => 'Criar Temporalidades', 'edit_temporality' => 'Editar Temporalidades', 'delete_temporality' => 'Deletar Temporalidades', 'read_collection' => 'Ler Coleções', 'create_collection' => 'Criar Coleções', 'edit_collection' => 'Editar Coleções', 'delete_collection' => 'Deletar Coleções'] as $field => $label)
                  <div class="col-md-6">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="1" id="{{ $field }}" name="{{ $field }}">
                      <label class="form-check-label" for="{{ $field }}">
                        {{ $label }}
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
          <div class="modal-footer">
            
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal for Projects -->
<div class="modal fade" id="projectsModal" tabindex="-1" aria-labelledby="projectsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="projectsModalLabel">Gerenciar Projetos do Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="projectsForm" method="POST" action="{{route('assing.projects')}}">
          @csrf
          <input type="hidden" name="user_id" id="user_id">
          <div class="mb-3">
            <label for="projects" class="form-label">Projetos</label>
            <select name="projects[]" id="projects" class="form-control" multiple>
              @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Adicionar ao Projeto</button>
        </form>
        <h5 class="mt-4">Projetos do Usuário</h5>
        <div class="table-container">
          <table class="table table-striped table-projects" style="width: 400px">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">Nome do Projeto</th>
              </tr>
            </thead>
            <tbody id="userProjects">
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


  <script>

document.addEventListener('DOMContentLoaded', function () {
  var projectsButtons = document.querySelectorAll('.projects');
  var projectsModal = document.getElementById('projectsModal');
  var projectsForm = projectsModal.querySelector('form');
  var userProjectsTable = document.getElementById('userProjects');

  projectsButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var userId = button.getAttribute('data-id');
      projectsForm.querySelector('#user_id').value = userId;

      userProjectsTable.innerHTML = '';

      fetch(`/user/${userId}/projects`)
        .then(response => response.json())
        .then(data => {
          data.projects.forEach(userProject => {
            var row = document.createElement('tr');
            var cellId = document.createElement('td');
            var cellName = document.createElement('td');
            var cellDelete = document.createElement('td');
            var aDelete = document.createElement('a');
            aDelete.href = `user/assing/delete?user_id=${userId}&project_id=${userProject.project.id}`
            aDelete.innerHTML = 'Retirar <i class="fa-solid fa-link-slash"></i>'
            cellId.textContent = userProject.id;
            cellName.textContent = userProject.project.name;
            cellDelete.appendChild(aDelete);
            row.appendChild(cellId);
            row.appendChild(cellName);
            row.appendChild(cellDelete);
            userProjectsTable.appendChild(row);
          });
        });

      var modalInstance = new bootstrap.Modal(projectsModal);
      modalInstance.show();
    });
  });
});

    document.addEventListener('DOMContentLoaded', function () {
      var editButtons = document.querySelectorAll('.edit-user');
      var modal = document.getElementById('addUserModal');
      var modalForm = modal.querySelector('form');

      editButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          var id = button.getAttribute('data-id');
          var name = button.getAttribute('data-name');
          var email = button.getAttribute('data-email');
          var upload_limit = button.getAttribute('data-upload_limit');

          var permissions = {
            read_doc: button.getAttribute('data-read_doc'),
            create_doc: button.getAttribute('data-create_doc'),
            edit_doc: button.getAttribute('data-edit_doc'),
            delete_doc: button.getAttribute('data-delete_doc'),
            read_temporality: button.getAttribute('data-read_temporality'),
            create_temporality: button.getAttribute('data-create_temporality'),
            edit_temporality: button.getAttribute('data-edit_temporality'),
            delete_temporality: button.getAttribute('data-delete_temporality'),
            read_collection: button.getAttribute('data-read_collection'),
            create_collection: button.getAttribute('data-create_collection'),
            edit_collection: button.getAttribute('data-edit_collection'),
            delete_collection: button.getAttribute('data-delete_collection'),
          };

          modalForm.querySelector('[name="id"]').value = id;
          modalForm.querySelector('[name="name"]').value = name
          modalForm.querySelector('[name="email"]').value = email;
          modalForm.querySelector('[name="upload_limit"]').value = upload_limit;

          for (var key in permissions) {
            if (permissions.hasOwnProperty(key)) {
              modalForm.querySelector('[name="' + key + '"]').checked = permissions[key] == 1;
            }
          }

          var modalInstance = new bootstrap.Modal(modal);
          modalInstance.show();
        });
      });

      modal.addEventListener('show.bs.modal', function () {
        modalForm.reset();
        modalForm.querySelector('[name="id"]').value = '';
      });

      $('.delete-user').on('click', function(e) {
        e.preventDefault();
        var deleteUrl = $(this).attr('href');

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
            window.location.href = deleteUrl;
          }
        })
      });
    });
  </script>

@endsection
