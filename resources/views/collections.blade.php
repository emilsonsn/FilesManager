@extends('layouts.app')

@section('content')
  @php
    use App\Models\Document;
    use App\Models\User;
    use App\Models\Project;
    use Carbon\Carbon;
    use App\Models\DocumentCollection;
    $search = $_GET['search'] ?? null;
    $auth = auth()->user();
    $document_search = $_GET['document_search'] ?? null;
    $requested_document_id = $_GET['document_id'] ?? null;
    $autoOpenModal = false;
    $collectionData = null;

    if ($auth->is_admin) $projectIds = Project::get()->pluck('id');
    else $projectIds = $auth->projects()->pluck('project_id');
  
    $documentCollections = DocumentCollection::whereHas('document', function($query) use ($projectIds) {
      $query->whereIn('project_id', $projectIds);
    });

    if ($search) {
        $documentCollections->where('loan_author', 'like', "%{$search}%")
                           ->orWhere('loan_receiver', 'like', "%{$search}%");
    }

    if ($document_search) {
      $documentCollections->where('document_id', $document_search);
    }

    if ($auth->read_collection) {
      $documentCollections = $documentCollections->get();
      $documents = Document::get();
      $users = User::get();
    }

    if ($requested_document_id) {
      $collectionData = DocumentCollection::where('document_id', $requested_document_id)
                                          ->whereNull('return_date')
                                          ->first();
      $autoOpenModal = true;
    }
  @endphp

  @vite(['resources/sass/dashboard.scss'])
  <div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4">Controle de Acervo e Protocolo</h1>
    @if($auth->create_collection)
      <a href="#" class="fs-1 c-green add" data-bs-toggle="modal" data-bs-target="#addDocumentCollectionModal">
        <i class="fa-solid fa-circle-plus"></i>
      </a>
    @endif

    <form action="" class="row mb-3">
      <div class="col-md-3">
        <input value="{{$search}}" type="text" name="search" placeholder="Buscar por Autor/Recebedor do Empréstimo" class="form-control">
      </div>
      <div class="col-md-6">
        <select name="document_search" id="" class="form-control">
          <option value="">Filtre por documento</option>
            @foreach ($documents as $document)
                <option {{$document->id == $document_search ? 'selected' : ''}} value="{{$document->id}}">{{$document->id}} - {{$document->doc_number}}</option>
            @endforeach
        </select>
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
            <th scope="col">Data do Empréstimo</th>
            <th class="text-center" scope="col">Autor do Empréstimo</th>
            <th class="text-center" scope="col">Recebedor do Empréstimo</th>
            <th class="text-center" scope="col">Gênero</th>
            <th class="text-center" scope="col">Data de Devolução</th>
            <th class="text-center" scope="col">Autor da Devolução</th>
            <th class="text-center" scope="col">Recebedor da Devolução</th>
            <th class="text-center" scope="col">Documento</th>
            <th class="text-center" scope="col">Situação</th>
            <th class="text-center" scope="col">Ações</th>
          </tclass="text-center" r>
        </thead>
        <tbody>
          @foreach ($documentCollections as $collection)
            <tr>
              <th scope="row">{{ $collection->id }}</th>
              <td class="text-center">{{ Carbon::parse($collection->loan_date)->format('d/m/Y') }}</td>
              <td class="text-center">{{ $collection->loan_author }}</td>
              <td class="text-center">{{ $collection->loan_receiver }}</td>
              <td class="text-center">{{ $collection->gender }}</td>
              <td class="text-center">{{ $collection->return_date? Carbon::parse($collection->return_date)->format('d/m/Y') : '----' }}</td>
              <td class="text-center">{{ $collection->return_author ?? '----' }}</td>
              <td class="text-center">{{ $collection->receiver_author ?? '----' }}</td>
              <td class="text-center">{{ $collection->document->doc_number }}</td>
              <td class="text-center" style="color: {{ $collection->return_date ? 'green' : 'red' }} !important">{{ $collection->return_date ? 'Devolvido' : 'Emprestado' }}</td>
              <td>
                <a href="#" class="me-2 print-loan_form" data-url="{{ route('loan_form', ['id' => $collection->id]) }}">
                  <i class="fa-solid fa-print"></i>
                </a>
                @if($auth->edit_collection)
                  <a href="#" class="edit-collection" data-gender="{{$collection->gender}}" data-id="{{ $collection->id }}" data-loan_date="{{ $collection->loan_date }}" data-loan_author="{{ $collection->loan_author }}" data-loan_receiver="{{ $collection->loan_receiver }}" data-return_date="{{ $collection->return_date }}" data-return_author="{{ $collection->return_author }}" data-receiver_author="{{ $collection->receiver_author }}" data-document_id="{{ $collection->document_id }}" data-user_id="{{ $collection->user_id }}">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                @endif
                @if($auth->delete_collection)
                  <a href="{{route('delete.loan', ['id' => $collection->id])}}" class="delete-collection"><i class="fa-solid fa-trash ms-3"></i></a>
                @endif
            </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="addDocumentCollectionModal" tabindex="-1" aria-labelledby="addDocumentCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Aumenta o tamanho do modal -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addDocumentCollectionModalLabel">Adicionar Coleção de Documentos</h5>
        </div>
        <form action="{{ route('create.loan') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="id">

            <div class="mb-3 col-md-6">
              <label for="document_id" class="form-label">Documento</label>
              <select name="document_id" id="document_id" class="form-control" required>
                <option value="">Escolha um documento</option>
                @foreach ($documents as $document)
                    <option value="{{$document->id}}">{{$document->id}} - {{$document->description}}</option>
                @endforeach
              </select>
            </div>

            <div class="row">
              <div class="mb-3 col-md-3">
                <label for="loan_date" class="form-label">Data do Empréstimo</label>
                <input type="date" class="form-control" id="loan_date" name="loan_date" required>
              </div>
              <div class="mb-3 col-md-3">
                <label for="loan_author" class="form-label">Autor do Empréstimo</label>
                <input type="text" class="form-control" id="loan_author" name="loan_author" required>
              </div>
              <div class="mb-3 col-md-3">
                <label for="loan_receiver" class="form-label">Recebedor do Empréstimo</label>
                <input type="text" class="form-control" id="loan_receiver" name="loan_receiver" required>
              </div>
              <div class="mb-3 col-md-3">
                <label for="gender" class="form-label">Gênero</label>
                <select class="form-control" id="gender" name="gender" required>
                  <option value="Textual">Selecione uma opção</option>
                  <option value="Textual">Textual</option>
                  <option value="Cartográfico">Cartográfico</option>
                  <option value="Audiovisual">Audiovisual</option>
                  <option value="Multimídia">Multimídia</option>
                  <option value="Micrográfico">Micrográfico</option>
                  <option value="Digital">Digital</option>
                </select>
              </div>
              <div class="mb-3 col-md-4">
                <label for="return_date" class="form-label">Data de Devolução</label>
                <input type="date" class="form-control" id="return_date" name="return_date">
              </div>
              <div class="mb-3 col-md-4">
                <label for="return_author" class="form-label">Autor da Devolução</label>
                <input type="text" class="form-control" id="return_author" name="return_author">
              </div>
              <div class="mb-3 col-md-4">
                <label for="receiver_author" class="form-label">Recebedor da Devolução</label>
                <input type="text" class="form-control" id="receiver_author" name="receiver_author">
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

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var printButtons = document.querySelectorAll('.print-loan_form');

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

    document.addEventListener('DOMContentLoaded', function () {
      var editButtons = document.querySelectorAll('.edit-collection');
      var modal = document.getElementById('addDocumentCollectionModal');
      var modalForm = modal.querySelector('form');

      modal.addEventListener('show.bs.modal', function () {
        modalForm.reset();
        modalForm.querySelector('[name="id"]').value = '';
      });

      editButtons.forEach(function (button) {
        button.addEventListener('click', function () {
          var id = button.getAttribute('data-id');
          var loan_date = button.getAttribute('data-loan_date');
          var loan_author = button.getAttribute('data-loan_author');
          var loan_receiver = button.getAttribute('data-loan_receiver');
          var gender = button.getAttribute('data-gender');
          var return_date = button.getAttribute('data-return_date');
          var return_author = button.getAttribute('data-return_author');
          var receiver_author = button.getAttribute('data-receiver_author');
          var document_id = button.getAttribute('data-document_id');
          var user_id = button.getAttribute('data-user_id');

          modalForm.querySelector('[name="id"]').value = id;
          modalForm.querySelector('[name="loan_date"]').value = loan_date;
          modalForm.querySelector('[name="loan_author"]').value = loan_author;
          modalForm.querySelector('[name="loan_receiver"]').value = loan_receiver;
          modalForm.querySelector('[name="gender"]').value = gender;
          modalForm.querySelector('[name="return_date"]').value = return_date;
          modalForm.querySelector('[name="return_author"]').value = return_author;
          modalForm.querySelector('[name="receiver_author"]').value = receiver_author;
          modalForm.querySelector('[name="document_id"]').value = document_id;

          var modalInstance = new bootstrap.Modal(modal);
          modalInstance.show();
        });
      });

      @if($autoOpenModal)
        var autoOpenModal = true;
        var collectionData = @json($collectionData);

        if (autoOpenModal && collectionData) {
          modalForm.querySelector('[name="id"]').value = collectionData.id;
          modalForm.querySelector('[name="loan_date"]').value = collectionData.loan_date;
          modalForm.querySelector('[name="loan_author"]').value = collectionData.loan_author;
          modalForm.querySelector('[name="gender"]').value = collectionData.gender;
          modalForm.querySelector('[name="loan_receiver"]').value = collectionData.loan_receiver;
          modalForm.querySelector('[name="return_date"]').value = collectionData.return_date;
          modalForm.querySelector('[name="return_author"]').value = collectionData.return_author;
          modalForm.querySelector('[name="receiver_author"]').value = collectionData.receiver_author;
          modalForm.querySelector('[name="document_id"]').value = collectionData.document_id;

          var modalInstance = new bootstrap.Modal(modal);
          modalInstance.show();
        } else if (autoOpenModal && !collectionData) {
          modalForm.querySelector('[name="document_id"]').value = '{{ $requested_document_id }}';

          var modalInstance = new bootstrap.Modal(modal);
          modalInstance.show();
        }
      @endif
    });

    $('.delete-collection').on('click', function(e) {
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
  </script>
@endsection
