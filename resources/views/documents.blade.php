@extends('layouts.app')

@section('content')
  @php
    use App\Models\Document;
    use App\Models\Project;
    use App\Models\Temporality;
    $search = $_GET['search'] ?? null;
    $initial_date = $_GET['initial_date'] ?? null;
    $archive_date = $_GET['archive_date'] ?? null;
    
    $auth = auth()->user();

    $document = Document::orderBy('id', 'desc');

    if($auth->is_admin) $projectIds = Project::get()->pluck('id');
    else $projectIds = $auth->projects()->pluck('project_id');
  
    if($search){      
        $document->where('doc_number', 'like', "%$search%")
          ->orWhere('description', 'like', "%$search%")
          ->orWhere('observations', 'like', "%$search%")
          ->orWhere('holder_name', 'like', "%$search%")
          ->orWhere('classification', 'like', "%$search%")
          ->orWhereHas('temporality', function($query) use ($search){
            $query->where('code','like',"%$search%" )
              ->orWhere('activity','like',"%$search%" )
              ->orWhere('tipology','like',"%$search%" )
              ->orWhere('function','like',"%$search%" )
              ->orWhere('sub_function','like',"%$search%" )
              ;
          });
    }

    if($initial_date){      
        $document->whereDate('initial_date', $initial_date);
    }

    if($archive_date){      
        $document->whereDate('archive_date', $initial_date);
    }

    if($auth->read_doc){
      $documents = $document->where('project_id', $project_id)->get();
      $projects = Project::whereIn('id', $projectIds)->get();
      $temporalitys = Temporality::get();
    }

  @endphp
    
  @vite(['resources/sass/dashboard.scss'])
  <div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4">Documentos</h1>
    @if($auth->create_doc)
      <a href="#" class="fs-1 c-green add" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
        <i class="fa-solid fa-circle-plus"></i>
      </a>
    @endif

    <form action="" class="row mb-3">
      <div class="col-md-3">
        <label for="search">Buscar</label>
        <input value="{{$search}}" type="text" id="search" name="search" placeholder="Buscar pelos dados do documento" class="form-control">
      </div>
      <div class="col-md-3">
        <label for="initial_date_filter">Data inicial</label>
        <input value="{{$initial_date}}" type="date" id="initial_date_filter" name="initial_date" class="form-control">
      </div>
      <div class="col-md-3">
        <label for="archive_date_filter">Data de arquivamento</label>
        <input value="{{$archive_date}}" type="date" id="archive_date_filter" name="archive_date" class="form-control">
      </div>
      <div class="col-md-2">
        <input type="submit" class="btn btn-primary mt-4" value="Filtrar">
      </div>
    </form>

    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Número do Documento</th>
            <th scope="col">Nome do Titular</th>
            <th scope="col">Descrição</th>
            <th scope="col">Caixa</th>
            <th scope="col">Quantidade de Pastas</th>
            <th scope="col">Arḿario</th>
            <th scope="col">Gavetas</th>
            <th scope="col">Código de Classificação</th>
            <th scope="col">Área</th>
            <th scope="col">Função</th>
            <th scope="col">Sub-Função</th>
            <th scope="col">Atividade</th>
            <th scope="col">Tipologia</th>
            <th scope="col">Prazo de Guarda Corrente</th>
            <th scope="col">Prazo de Guarda Intermediário</th>
            <th scope="col">Destinação Final</th>
            <th scope="col">Situação A.C</th>
            <th scope="col">Situação A.I</th>
            <th scope="col">Arquivos</th>
            <th scope="col">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($documents as $doc)
            <tr class="{{strpos($doc->situationAC . ' ' . $doc->situationAI, "Descartado") ? 'tr-grey' : ''}}">
              <th scope="row">{{ $doc->id }}</th>
              <td class="text-center">{{ $doc->doc_number }}</td>
              <td class="text-center">{{ $doc->holder_name }}</td>
              <td class="text-center">{{ $doc->description }}</td>
              <td class="text-center">{{ $doc->box ?? '----' }}</td>
              <td class="text-center">{{ $doc->qtpasta ?? '----' }}</td>
              <td class="text-center">{{ $doc->cabinet ?? '----' }}</td>
              <td class="text-center">{{ $doc->drawer ?? '----' }}</td>
              <td class="text-center">{{ $doc->temporality->code }}</td>
              <td class="text-center">{{ $doc->temporality->area }}</td>
              <td class="text-center">{{ $doc->temporality->function }}</td>
              <td class="text-center">{{ $doc->temporality->sub_function }}</td>
              <td class="text-center">{{ $doc->temporality->activity }}</td>
              <td class="text-center">{{ $doc->temporality->tipology }}</td>
              <td class="text-center">{{ $doc->temporality->current_custody_period }} ano(s)</td>
              <td class="text-center">{{ $doc->temporality->intermediate_custody_period }} ano(s)</td>
              <td class="text-center">{{ $doc->situationAC }}</td>
              <td class="text-center">{{ $doc->situationAI }}</td>
              <td class="text-center">{{ $doc->temporality->final_destination }}</td>
              <td>
                <a href="#" class="c-green view-files" data-id="{{ $doc->id }}">Ver/baixar</a>
              </td>
              <td>
                <a href="#" class="me-2 print-label" data-url="{{ route('label', ['id' => $doc->id]) }}">
                  <i class="fa-solid fa-print"></i>
                </a>
                <a href="{{ route('document.collections', ['document_id' => $doc->id]) }}" class="me-2">                  
                  <i class="fa-solid fa-box-open"></i>
                </a>
                @if($auth->edit_doc)
                  <a href="#" class="edit-document" data-gender="{{$doc->gender}}" data-archive_date="{{$doc->archive_date}}" data-initial_date="{{$doc->initial_date}}" data-id="{{ $doc->id }}" data-observations="{{ $doc->observations }}" data-project_id="{{ $doc->project_id }}" data-temporality_id="{{ $doc->temporality_id }}" data-doc_number="{{ $doc->doc_number }}" data-holder_name="{{ $doc->holder_name }}" data-description="{{ $doc->description }}" data-box="{{ $doc->box }}" data-qtpasta="{{ $doc->qtpasta }}" data-file="{{ $doc->file }}" data-cabinet="{{ $doc->cabinet }}" data-drawer="{{ $doc->drawer }}" data-classification="{{ $doc->classification }}" data-version="{{ $doc->version }}" data-situationac="{{ $doc->situationAC }}" data-situationai="{{ $doc->situationAI }}">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                @endif
                @if($auth->delete_doc)
                  <a href="{{route('delete.document', ['id' => $doc->id])}}" class="delete-document"><i class="fa-solid fa-trash ms-3"></i></a> 
                @endif
            </td>              
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Aumenta o tamanho do modal -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addDocumentModalLabel">Adicionar Documento</h5>
        </div>
        <form action="{{ route('create.document') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="id">
            <input type="hidden" name="project_id" value="{{$project_id}}">

            <div class="row">
              <div class="mb-3 col-md-3">
                <label for="temporality_id" class="form-label">Código de classificação</label>
                <select name="temporality_id" id="temporality_id" class="form-control">
                  <option value="">Escolha uma opção</option>
                  @foreach ($temporalitys as $temporality)
                      <option value="{{$temporality->id}}">{{$temporality->code}}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3 col-md-3">
                <label for="area" class="form-label">Área</label>
                <input type="text" class="form-control" id="area" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="function" class="form-label">Função</label>
                <input type="text" class="form-control" id="function" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="sub_function" class="form-label">Sub-função</label>
                <input type="text" class="form-control" id="sub_function" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="activity" class="form-label">Atividade</label>
                <input type="text" class="form-control" id="activity" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="tipology" class="form-label">Tipologia</label>
                <input type="text" class="form-control" id="tipology" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="current_custody_period" class="form-label">Prazo de Guarda Corrente</label>
                <input type="number" step="1" class="form-control" id="current_custody_period" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="intermediate_custody_period" class="form-label">Prazo de Guarda Intermediária</label>
                <input type="number" step="1" class="form-control" id="intermediate_custody_period" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="final_destination" class="form-label">Destinação Final</label>
                <input type="text" class="form-control" id="final_destination" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="doc_number" class="form-label">Nª do documento</label>
                <input type="text" class="form-control" id="doc_number" name="doc_number" required>
              </div>
              <div class="mb-3 col-md-3">
                <label for="initial_date" class="form-label">
                  Data inicial
                  <div onclick="setDates('initial_date')" class="btn btn-sm"><i class="fa-regular fa-calendar-check"></i></div>
                </label>
                <input type="date" class="form-control" id="initial_date" name="initial_date" required>
              </div>
              <div class="mb-3 col-md-3">
                <label for="archive_date" class="form-label">
                  Data de arquivamento
                  <div onclick="setDates('archive_date')" class="btn btn-sm"><i class="fa-regular fa-calendar-check"></i></div>
                </label>
                <input type="date" class="form-control" id="archive_date" name="archive_date" required>
              </div>
              <div class="mb-3 col-md-3">
                <label for="expiration_date_A_C" class="form-label">Data de expiração A.C</label>
                <input type="date" class="form-control" id="expiration_date_A_C" name="expiration_date_A_C" required readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="expiration_date_A_I" class="form-label">Data de expiração A.I</label>
                <input type="date" class="form-control" id="expiration_date_A_I" name="expiration_date_A_I" required readonly>
              </div>

              <div class="mb-3 col-md-3">
                <label for="holder_name" class="form-label">Nome do Titular</label>
                <input type="text" class="form-control" id="holder_name" name="holder_name" required>
              </div>

              <div class="mb-3 col-md-3">
                <label for="type" class="form-label">Tipo de arquivamento</label>
                <select class="form-control" id="type" name="type">
                  <option value="">Selecione caixa ou armário</option>
                  <option value="1">Caixa</option>
                  <option value="2">Armário</option>
                </select>
              </div>
              
              <div id="boxFields" class="row col-md-6" style="display:none;">
                <div class="mb-3 col-md-6">
                  <label for="box" class="form-label">Caixa</label>
                  <input type="text" class="form-control" id="box" name="box">
                </div>
                <div class="mb-3 col-md-6">
                  <label for="qtpasta" class="form-label">Quantidade de Pastas</label>
                  <input type="number" step="1" class="form-control" id="qtpasta" name="qtpasta">
                </div>
              </div>
              
              <div id="cabinetFields" class="row col-md-6" style="display:none;">
                <div class="mb-3 col-md-6">
                  <label for="cabinet" class="form-label">Armário</label>
                  <input type="text" class="form-control" id="cabinet" name="cabinet">
                </div>
                <div class="mb-3 col-md-6">
                  <label for="drawer" class="form-label">Gaveta</label>
                  <input type="text" class="form-control" id="drawer" name="drawer">
                </div>
              </div>

              <div class="mb-3 col-md-3">
                <label for="gender" class="form-label">Gênero</label>
                <select class="form-control" id="gender" name="gender" required>
                  <option value="Textual">Textual</option>
                  <option value="Cartográfico">Cartográfico</option>
                  <option value="Audiovisual">Audiovisual</option>
                  <option value="Multimídia">Multimídia</option>
                  <option value="Micrográfico">Micrográfico</option>
                  <option value="Digital">Digital</option>
                </select>
              </div>
              
              <div class="mb-3 col-md-3">
                <label for="classification" class="form-label">Classificação da informação</label>
                <select class="form-control" id="classification" name="classification" required>
                  <option value="Pública">Pública</option>
                  <option value="Interna">Interna</option>
                  <option value="Confidencial">Confidencial</option>
                </select>
              </div>
              <div class="mb-3 col-md-3">
                <label for="version" class="form-label">Versão</label>
                <select class="form-control" id="version" name="version" required>
                  <option value="Físico">Físico</option>
                  <option value="Físico">Digital</option>
                  <option value="Físico">Híbrido</option>
                </select>
              </div>              
              <div class="mb-3 col-md-3">
                <label for="situationAC" class="form-label">Situação A.C</label>
                <select class="form-control" id="situationAC" name="situationAC" required>
                  <option value="Transferido A.I">Transferido A.i</option>
                  <option value="Ativo">Ativo</option>
                  <option value="Descartado">Descartado</option>
                </select>
              </div>
              <div class="mb-3 col-md-3">
                <label for="situationAI" class="form-label">Situação A.I</label>
                <select class="form-control" id="situationAI" name="situationAI" required>
                  <option value="Recolhido A.P">Recolhido A.P</option>
                  <option value="Ativo">Ativo</option>
                  <option value="Descartado">Descartado</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Descrição</label>
              <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="mb-3">
              <label for="observations" class="form-label">Observações</label>
              <textarea class="form-control" id="observations" name="observations" required></textarea>
            </div>
            <div class="mb-3">
              <label for="file" class="form-label">Arquivos</label>
              <input type="file" class="form-control" id="file" name="files[]" multiple>
            </div>
            
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal para exibir arquivos do documento -->
  <div class="modal fade" id="viewFilesModal" tabindex="-1" aria-labelledby="viewFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewFilesModalLabel">Arquivos do Documento</h5>
          
        </div>
        <div class="modal-body">
          <ul id="fileList" class="list-group">
            <!-- Arquivos serão listados aqui -->
          </ul>
        </div>
        <div class="modal-footer">
          
        </div>
        <div class="modal-footer">
          <a href="#" id="downloadAllFiles" class="btn btn-primary">Baixar Todos</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
  var printButtons = document.querySelectorAll('.print-label');

  printButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      var url = button.getAttribute('data-url');
      var width = 1000;
      var height = 600;
      var left = (screen.width - width) / 2;
      var top = (screen.height - height) / 2;

      var popup = window.open(url, 'popup', 'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',scrollbars=no,resizable=no');
      popup.addEventListener('load', function () {
        popup.print();
      });
    });
  });
});

function setDates(input) {
  var initialDateInput = document.querySelector(`input#${input}`);
  var currentDate = new Date(initialDateInput.value);

  if (isNaN(currentDate.getTime())) {
    console.log(1)
    alert("Por favor, insira uma data inicial válida.");
    return;
  }

  var current_custody_period = parseInt(document.querySelector('#current_custody_period').value);
  var intermediate_custody_period = parseInt(document.querySelector('#intermediate_custody_period').value);

  if (isNaN(current_custody_period) || isNaN(intermediate_custody_period)) {
    alert("Por favor, selecione uma temporalidade válida.");
    return;
  }

  var expiration_date_A_C = new Date(currentDate);
  expiration_date_A_C.setFullYear(currentDate.getFullYear() + current_custody_period);
  document.getElementById('expiration_date_A_C').value = expiration_date_A_C.toISOString().split('T')[0];

  var expiration_date_A_I = new Date(currentDate);
  expiration_date_A_I.setFullYear(currentDate.getFullYear() + intermediate_custody_period);
  document.getElementById('expiration_date_A_I').value = expiration_date_A_I.toISOString().split('T')[0];
}

document.addEventListener('DOMContentLoaded', function () {
  var viewButtons = document.querySelectorAll('.view-files');
  var fileList = document.getElementById('fileList');
  var viewFilesModal = new bootstrap.Modal(document.getElementById('viewFilesModal'));

  viewButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var documentId = button.getAttribute('data-id');
      fetch(`/documents/${documentId}/files`)
        .then(response => response.json())
        .then(data => {
          fileList.innerHTML = '';
          if (data.length > 0) {
            data.forEach(file => {
              var listItem = document.createElement('li');
              listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
              listItem.innerHTML = `
                ${new Date(file.created_at).toLocaleDateString('pt-BR')} - ${file.name}
                <a href="/storage/${file.file_path}" target="_blank" class="btn btn-sm btn-primary">Abrir</a>
              `;
              fileList.appendChild(listItem);
            });
          } else {
            fileList.innerHTML = '<li class="list-group-item">Nenhum arquivo encontrado</li>';
          }
          viewFilesModal.show();
        })
        .catch(error => {
          console.error('Erro ao carregar arquivos:', error);
          fileList.innerHTML = '<li class="list-group-item">Erro ao carregar arquivos</li>';
          viewFilesModal.show();
        });
    });
  });
});

document.addEventListener('DOMContentLoaded', function () {
  var editButtons = document.querySelectorAll('.edit-document');
  var addDocumentButton = document.querySelector('.add');
  var modal = document.getElementById('addDocumentModal');
  var modalForm = modal.querySelector('form');
  var temporalitys = @json($temporalitys);

  addDocumentButton.addEventListener('click', function () {
    modalForm.reset(); // Limpa todos os campos do formulário
    modalForm.querySelectorAll('input[type="hidden"]').forEach(function(input) {
      input.value = ''; // Limpa campos ocultos
    });
    modalForm.querySelectorAll('select').forEach(function(select) {
      select.value = ''; // Reseta os selects
    });
    modalForm.querySelectorAll('textarea').forEach(function(textarea) {
      textarea.value = ''; // Limpa textareas
    });
    document.getElementById('area').value = '';
    document.getElementById('function').value = '';
    document.getElementById('sub_function').value = '';
    document.getElementById('activity').value = '';
    document.getElementById('tipology').value = '';
    document.getElementById('current_custody_period').value = '';
    document.getElementById('intermediate_custody_period').value = '';
    document.getElementById('final_destination').value = '';
    toggleFields();
  });

  function toggleFields() {
    var typeSelect = document.getElementById('type');
    var boxFields = document.getElementById('boxFields');
    var cabinetFields = document.getElementById('cabinetFields');

    if (typeSelect.value == '1') {
      boxFields.style.display = 'flex';
      cabinetFields.style.display = 'none';
      document.getElementById('cabinet').value = '';
      document.getElementById('drawer').value = '';
    } else if (typeSelect.value == '2') {
      boxFields.style.display = 'none';
      cabinetFields.style.display = 'flex';
      document.getElementById('box').value = '';
      document.getElementById('qtpasta').value = '';
    } else {
      boxFields.style.display = 'none';
      cabinetFields.style.display = 'none';
      document.getElementById('box').value = '';
      document.getElementById('qtpasta').value = '';
      document.getElementById('cabinet').value = '';
      document.getElementById('drawer').value = '';
    }
  }

  document.getElementById('type').addEventListener('change', toggleFields);
  toggleFields(); // Inicializa o estado dos campos

  editButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var id = button.getAttribute('data-id');
      var project_id = button.getAttribute('data-project_id');
      var doc_number = button.getAttribute('data-doc_number');
      var temporality_id = button.getAttribute('data-temporality_id');
      var holder_name = button.getAttribute('data-holder_name');
      var description = button.getAttribute('data-description');
      var box = button.getAttribute('data-box');
      var qtpasta = button.getAttribute('data-qtpasta');
      var file = button.getAttribute('data-file');
      var cabinet = button.getAttribute('data-cabinet');
      var observations = button.getAttribute('data-observations');
      var drawer = button.getAttribute('data-drawer');
      var initial_date = button.getAttribute('data-initial_date');
      var archive_date = button.getAttribute('data-archive_date');
      var classification = button.getAttribute('data-classification');
      var version = button.getAttribute('data-version');
      var situationAC = button.getAttribute('data-situationac');
      var situationAI = button.getAttribute('data-situationai');
      var gender = button.getAttribute('data-gender');
      

      modalForm.querySelector('[name="id"]').value = id;
      modalForm.querySelector('[name="project_id"]').value = project_id;
      modalForm.querySelector('[name="temporality_id"]').value = temporality_id;
      modalForm.querySelector('[name="holder_name"]').value = holder_name;
      modalForm.querySelector('[name="description"]').value = description;
      modalForm.querySelector('[name="box"]').value = box;
      modalForm.querySelector('[name="qtpasta"]').value = qtpasta;
      modalForm.querySelector('[name="doc_number"]').value = doc_number;
      modalForm.querySelector('[name="cabinet"]').value = cabinet;
      modalForm.querySelector('[name="observations"]').value = observations;
      modalForm.querySelector('[name="drawer"]').value = drawer;
      modalForm.querySelector('[name="initial_date"]').value = initial_date;
      modalForm.querySelector('[name="archive_date"]').value = archive_date;
      modalForm.querySelector('[name="classification"]').value = classification;
      modalForm.querySelector('[name="version"]').value = version;
      modalForm.querySelector('[name="situationAC"]').value = situationAC;
      modalForm.querySelector('[name="situationAI"]').value = situationAI;
      modalForm.querySelector('[name="gender"]').value = gender;

      if (box) {
        document.getElementById('type').value = '1';
      } else if (cabinet) {
        document.getElementById('type').value = '2';
      } else {
        document.getElementById('type').value = '';
      }

      toggleFields();

      var temporality = temporalitys.find(t => t.id == temporality_id);
      if (temporality) {
        modalForm.querySelector('#area').value = temporality.area;
        modalForm.querySelector('#function').value = temporality.function;
        modalForm.querySelector('#sub_function').value = temporality.sub_function;
        modalForm.querySelector('#activity').value = temporality.activity;
        modalForm.querySelector('#tipology').value = temporality.tipology;
        modalForm.querySelector('#current_custody_period').value = temporality.current_custody_period;
        modalForm.querySelector('#intermediate_custody_period').value = temporality.intermediate_custody_period;
        modalForm.querySelector('#final_destination').value = temporality.final_destination;

        var expiration_date_A_C = new Date(new Date(initial_date).setFullYear(new Date(initial_date).getFullYear() + parseInt(temporality.current_custody_period)));
        var expiration_date_A_I = new Date(new Date(initial_date).setFullYear(new Date(initial_date).getFullYear() + parseInt(temporality.intermediate_custody_period)));

        modalForm.querySelector('#expiration_date_A_C').value = expiration_date_A_C.toISOString().split('T')[0];
        modalForm.querySelector('#expiration_date_A_I').value = expiration_date_A_I.toISOString().split('T')[0];
      }

      var modalInstance = new bootstrap.Modal(modal);
      modalInstance.show();
    });
  });

  document.getElementById('temporality_id').addEventListener('change', function() {
    var temporality_id = this.value;
    var temporality = temporalitys.find(t => t.id == temporality_id);
    if (temporality) {
      document.getElementById('area').value = temporality.area;
      document.getElementById('function').value = temporality.function;
      document.getElementById('sub_function').value = temporality.sub_function;
      modalForm.querySelector('#activity').value = temporality.activity;
      modalForm.querySelector('#tipology').value = temporality.tipology;
      document.getElementById('current_custody_period').value = temporality.current_custody_period;
      document.getElementById('intermediate_custody_period').value = temporality.intermediate_custody_period;
      document.getElementById('final_destination').value = temporality.final_destination;
    }
  });

  $('.delete-document').on('click', function(e) {
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

document.addEventListener('DOMContentLoaded', function () {
  var viewButtons = document.querySelectorAll('.view-files');
  var fileList = document.getElementById('fileList');
  var viewFilesModal = new bootstrap.Modal(document.getElementById('viewFilesModal'));
  var downloadAllFilesButton = document.getElementById('downloadAllFiles');

  viewButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var documentId = button.getAttribute('data-id');
      fetch(`/documents/${documentId}/files`)
        .then(response => response.json())
        .then(data => {
          fileList.innerHTML = '';
          if (data.length > 0) {
            data.forEach(file => {
              var listItem = document.createElement('li');
              listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
              listItem.innerHTML = `
              ${new Date(file.created_at).toLocaleDateString('pt-BR')} - ${file.name}
                <a href="/storage/${file.file_path}" target="_blank" class="btn btn-sm btn-primary">Abrir</a>
              `;
              fileList.appendChild(listItem);
            });
          } else {
            fileList.innerHTML = '<li class="list-group-item">Nenhum arquivo encontrado</li>';
          }
          // Configure o botão de download para baixar todos os arquivos
          downloadAllFilesButton.href = `/documents/${documentId}/download-all`;
          viewFilesModal.show();
        })
        .catch(error => {
          console.error('Erro ao carregar arquivos:', error);
          fileList.innerHTML = '<li class="list-group-item">Erro ao carregar arquivos</li>';
          viewFilesModal.show();
        });
    });
  });
});

  </script>

@endsection
