@extends('layouts.app')

@section('content')
  @php
    use App\Models\Temporality;
    use App\Models\Project;
    use App\Models\VolatileColumn;
    $project_id = $_GET['project'] ?? null;
    $search = $_GET['search'] ?? null;
    $auth = auth()->user();

    if($auth->is_admin) $projectIds = Project::get()->pluck('id');
    else $projectIds = $auth->projects()->pluck('project_id');

    $temporality = Temporality::whereIn('project_id', $projectIds);
    
    if($project_id){
        $temporality->where('project_id', $project_id);
    }

    if($search){
        $temporality->where('code', 'like', "%$search%");
    }

    if($auth->read_temporality){
        $temporality = $temporality->get();
        $projects = Project::whereIn('id', $projectIds)->get();
    }
  @endphp
    
  @vite(['resources/sass/dashboard.scss'])
  <div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4">Temporalidades</h1>
    @if($auth->create_temporality)
      <a href="#" class="fs-1 c-green add" data-bs-toggle="modal" data-bs-target="#addTemporalityModal">
        <i class="fa-solid fa-circle-plus"></i>
      </a>
    @endif

    <form action="" class="row mb-3 col-md-6">
      <div class="col-md-3">
        <select name="project" class="form-control">
            <option value="">Selecione o projeto</option>
          @foreach ($projects as $project)
            <option {{$project_id == $project->id ? 'selected' : ''}} value="{{ $project->id }}">{{ $project->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <input value="{{$search}}" type="text" name="search" placeholder="Buscar por código" class="form-control">
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
            <th scope="col">Código de classificação</th>
            <th scope="col">Área</th>
            <th scope="col">Função</th>
            <th scope="col">Sub-Função</th>
            <th scope="col">Atividade</th>
            <th scope="col">Tipologia</th>
            <th scope="col">Prazo de Guarda Corrente</th>
            <th scope="col">Prazo de Guarda intermediário</th>
            <th scope="col">Destinação final</th>
            <th scope="col">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($temporality as $temp)
            <tr>
              <th scope="row">{{ $temp->id }}</th>
              <td>{{ $temp->code }}</td>
              <td>{{ $temp->area }}</td>
              <td>{{ $temp->function }}</td>
              <td>{{ $temp->sub_function }}</td>
              <td>{{ $temp->activity }}</td>
              <td>{{ $temp->tipology }}</td>
              <td>{{ $temp->current_custody_period }}</td>
              <td>{{ $temp->intermediate_custody_period }}</td>
              <td>{{ $temp->final_destination }}</td>
              <td>
                @if($auth->edit_temporality)
                  <a href="#" class="edit-temporality" data-id="{{ $temp->id }}" data-activity="{{ $temp->activity }}" data-tipology="{{ $temp->tipology }}" data-project_id="{{ $temp->project_id }}" data-code="{{ $temp->code }}" data-area="{{ $temp->area }}" data-function="{{ $temp->function }}" data-sub_function="{{ $temp->sub_function }}" data-current_custody_period="{{ $temp->current_custody_period }}" data-intermediate_custody_period="{{ $temp->intermediate_custody_period }}" data-final_destination="{{ $temp->final_destination }}">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                @endif
                @if($auth->delete_temporality)
                  <a href="{{route('delete.temporality', ['id' => $temp->id])}}" class="delete-temporality"><i class="fa-solid fa-trash ms-3"></i></a>
                @endif
            </td>
              
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="addTemporalityModal" tabindex="-1" aria-labelledby="addTemporalityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTemporalityModalLabel">Adicionar Temporalidade</h5>
          
        </div>
        <form action="{{ route('create.temporality') }}" method="POST">
          @csrf
          <div class="modal-body row">
            <input type="hidden" name="id">
            <div class="mb-3 col-md-6">
                <label for="project_id" class="form-label">Projeto</label>
                <select class="form-control" id="project_id" name="project_id" required>
                  @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                  @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="code" class="form-label">Código de classificação</label>
              <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="mb-3 col-md-6">
              <label for="area" class="form-label">Área</label>
              <select class="form-control" id="area" name="area" required>
                <option value="Meio">Meio</option>
                <option value="Fim">Fim</option>
              </select>
            </div>
            <div class="mb-3 col-md-6">
              <label for="function" class="form-label">Função</label>
              <input type="text" class="form-control" id="function" name="function" required>
            </div>
            <div class="mb-3 col-md-6">
              <label for="sub_function" class="form-label">Sub-Função</label>
              <input type="text" class="form-control" id="sub_function" name="sub_function" required>
            </div>
            <div class="mb-3 col-md-6">
              <label for="activity" class="form-label">Atividade</label>
              <input type="text" class="form-control" id="activity" name="activity" required>
            </div>
            <div class="mb-3 col-md-12">
              <label for="tipology" class="form-label">Tipologia</label>
              <input type="text" class="form-control" id="tipology" name="tipology" required>
            </div>
            <div class="mb-3 col-md-12">
              <label for="volatile_columns" class="form-label">Campos Adicionais</label>
              <div id="volatile_columns_container"></div>
              <button type="button" class="btn btn-secondary mt-2" id="add_volatile_column">Adicionar Campo</button>
            </div>
            <div class="mb-3 col-md-6">
              <label for="current_custody_period" class="form-label">Prazo de Guarda Corrente</label>
              <input type="number" step="1" class="form-control" id="current_custody_period" name="current_custody_period" required>
            </div>
            <div class="mb-3 col-md-6">
              <label for="intermediate_custody_period" class="form-label">Prazo de Guarda intermediário</label>
              <input type="number" step="1" class="form-control" id="intermediate_custody_period" name="intermediate_custody_period" required>
            </div>
            <div class="mb-3 col-md-6">
              <label for="final_destination" class="form-label">Destinação final</label>
              <input type="text" class="form-control" id="final_destination" name="final_destination" required>
            </div>
          </div>
          <div class="modal-footer">
            
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var editButtons = document.querySelectorAll('.edit-temporality');
    var modal = document.getElementById('addTemporalityModal');
    var modalForm = modal.querySelector('form');
    
    editButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        var id = button.getAttribute('data-id');
        var project_id = button.getAttribute('data-project_id');
        var code = button.getAttribute('data-code');
        var area = button.getAttribute('data-area');
        var func = button.getAttribute('data-function');
        var sub_function = button.getAttribute('data-sub_function');
        var activity = button.getAttribute('data-activity');
        var tipology = button.getAttribute('data-tipology');
        var current_custody_period = button.getAttribute('data-current_custody_period');
        var intermediate_custody_period = button.getAttribute('data-intermediate_custody_period');
        var final_destination = button.getAttribute('data-final_destination');

        modalForm.querySelector('[name="id"]').value = id;
        modalForm.querySelector('[name="project_id"]').value = project_id;
        modalForm.querySelector('[name="code"]').value = code;
        modalForm.querySelector('[name="area"]').value = area;
        modalForm.querySelector('[name="function"]').value = func;
        modalForm.querySelector('[name="sub_function"]').value = sub_function;
        modalForm.querySelector('[name="activity"]').value = activity;
        modalForm.querySelector('[name="tipology"]').value = tipology;
        modalForm.querySelector('[name="current_custody_period"]').value = current_custody_period;
        modalForm.querySelector('[name="intermediate_custody_period"]').value = intermediate_custody_period;
        modalForm.querySelector('[name="final_destination"]').value = final_destination;

        // Limpar campos voláteis
        var volatileContainer = document.getElementById('volatile_columns_container');
        volatileContainer.innerHTML = '';

        // Carregar campos voláteis
        fetch(`/temporality/${id}/volatile_columns`)
          .then(response => response.json())
          .then(data => {
            data.forEach(column => {
              addVolatileColumn(column.name, column.value);
            });
          });

        var modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
      });
    });

    document.getElementById('add_volatile_column').addEventListener('click', function() {
      addVolatileColumn();
    });

    function addVolatileColumn(name = '', value = '') {
      var container = document.getElementById('volatile_columns_container');
      var row = document.createElement('div');
      row.className = 'row mb-3';

      var nameField = document.createElement('div');
      nameField.className = 'col-md-6';
      nameField.innerHTML = `
        <label for="volatile_name" class="form-label">Nome do Campo</label>
        <input type="text" class="form-control" name="volatile_names[]" value="${name}">
      `;

      var valueField = document.createElement('div');
      valueField.className = 'col-md-6';
      valueField.innerHTML = `
        <label for="volatile_value" class="form-label">Valor</label>
        <input type="text" class="form-control" name="volatile_values[]" value="${value}">
      `;

      row.appendChild(nameField);
      row.appendChild(valueField);
      container.appendChild(row);
    }

    $('.delete-temporality').on('click', function(e) {
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
