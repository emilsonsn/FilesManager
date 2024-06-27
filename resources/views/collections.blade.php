@extends('layouts.app')

@section('content')
  @php
    use App\Models\Document;
    use App\Models\User;
    use App\Models\Project;
    use Carbon\Carbon;
    use App\Models\DocumentCollection;
    $auth = auth()->user();
    $search = $_GET['search'] ?? null;
    $document_search = $_GET['document_search'] ?? null;
    $requested_document_id = $_GET['document_id'] ?? null;
    $gender_search = $_GET['gender_search'] ?? null;
    $situation_search = $_GET['situation_search'] ?? null;
    $loan_date_search = $_GET['loan_date_search'] ?? null;
    $return_date_search = $_GET['return_date_search'] ?? null;
    $code_search = $_GET['code_search'] ?? null;
    $autoOpenModal = false;
    $collectionData = null;

    $project_id = session('project_id');
    
    $documentCollections = DocumentCollection::whereHas('documentLoans', function($query) use ($project_id) {
      $query->whereHas('document', function($query2) use($project_id){
        $query2->where('project_id', $project_id);
      });
    });

    if ($search) {
      $documentCollections->where(function($query) use ($search) {
        $query->where('loan_author', 'like', "%{$search}%")
              ->orWhere('loan_receiver', 'like', "%{$search}%");
      });
    }

    if ($document_search) {
      $documentCollections->whereHas('documentLoans', function($query) use ($document_search) {
        $query->where('document_id', $document_search);
      });
    }

    if ($gender_search) {
      $documentCollections->where('gender', $gender_search);
    }

    if ($loan_date_search) {
      $documentCollections->where('loan_date', $loan_date_search);
    }

    if ($return_date_search) {
      $documentCollections->where('return_date', $return_date_search);
    }

    if ($code_search) {
      $newCode = trim(str_replace('/2024', '', str_replace('CAD', '', $code_search)));      
      $documentCollections->where('id', $newCode);
    }

    if ($situation_search) {
      if ($situation_search == "Emprestado") {
        $documentCollections->whereNull('return_date');
      } else {
        $documentCollections->whereNotNull('return_date');
      }
    }

    if ($auth->read_collection) {
      $documentCollections = $documentCollections->get();
      $documents = Document::where('project_id', $project_id )->get();
      $users = User::get();
    }

    if ($requested_document_id) {
      $collectionData = DocumentCollection::whereHas('documentLoans', function($query) use ($requested_document_id) {
          $query->where('document_id', $requested_document_id);
      })->whereNull('return_date')->with('documentLoans')->first();
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
      <div class="col-md-3 mt-2">
        <label>Autor/Recebedor</label>
        <input value="{{$search}}" type="text" name="search" placeholder="Busca por Autor/Recebedor do empréstimo" class="form-control">
      </div>

      <div class="col-md-2 mt-2">
        <label>Código de identificação</label>
        <input value="{{$code_search}}" type="text" name="code_search" placeholder="Código" class="form-control">
      </div>

      <div class="col-md-2 mt-2">
        <label>Documento</label>
        <select name="document_search" id="" class="form-control">
          <option value="">Filtre por documento</option>
            @foreach ($documents as $document)
                <option {{$document->id == $document_search ? 'selected' : ''}} value="{{$document->id}}">{{$document->id}} - {{$document->doc_number}}</option>
            @endforeach
        </select>
      </div>
      <div class="col-md-2 mt-2">
        <label>Gênero</label>
        <select name="gender_search" id="" class="form-control">
          <option value="">Filtre por gênero</option>
          <option {{$gender_search == 'Textual' ? 'selected' : ''}} value="Textual">Textual</option>
          <option {{$gender_search == 'Cartográfico' ? 'selected' : ''}} value="Cartográfico">Cartográfico</option>
          <option {{$gender_search == 'Audiovisual' ? 'selected' : ''}} value="Audiovisual">Audiovisual</option>
          <option {{$gender_search == 'Multimídia' ? 'selected' : ''}} value="Multimídia">Multimídia</option>
          <option {{$gender_search == 'Micrográfico' ? 'selected' : ''}} value="Micrográfico">Micrográfico</option>
          <option {{$gender_search == 'Digital' ? 'selected' : ''}} value="Digital">Digital</option>
        </select>
      </div>
      <div class="col-md-2 mt-2">
        <label>Situação</label>
        <select name="situation_search" id="" class="form-control">
          <option value="">Filtre por situação</option>
          <option {{$situation_search == 'Emprestado' ? 'selected' : ''}} value="Emprestado">Emprestado</option>
          <option {{$situation_search == 'Devolvido' ? 'selected' : ''}} value="Devolvido">Devolvido</option>            
        </select>
      </div>
      <div class="col-md-2 mt-2">        
        <label>Data de emprestimo</label>
        <input value="{{$loan_date_search}}" type="date" name="loan_date_search" class="form-control">
      </div>
      <div class="col-md-2 mt-2">        
        <label>Data da devolução</label>
        <input value="{{$return_date_search}}" type="date" name="return_date_search" class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <input type="submit" class="btn btn-primary" value="Filtrar">
      </div>
    </form>

    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">Código</th>
            <th scope="col">Data do Empréstimo</th>
            <th class="text-center" scope="col">Autor do Empréstimo</th>
            <th class="text-center" scope="col">Recebedor do Empréstimo</th>
            <th class="text-center" scope="col">Gênero</th>
            <th class="text-center" scope="col">Data de Devolução</th>
            <th class="text-center" scope="col">Autor da Devolução</th>
            <th class="text-center" scope="col">Recebedor da Devolução</th>
            <th class="text-center" scope="col">Situação</th>
            <th class="text-center" scope="col">Ações</th>
          </tclass="text-center" r>
        </thead>
        <tbody>
          @foreach ($documentCollections as $collection)
            <tr>
              <th scope="row">CAD{{ $collection->id }}/2024</th>              
              <td class="text-center">{{ Carbon::parse($collection->loan_date)->format('d/m/Y') }}</td>
              <td class="text-center">{{ $collection->loan_author }}</td>
              <td class="text-center">{{ $collection->loan_receiver }}</td>
              <td class="text-center">{{ $collection->gender }}</td>
              <td class="text-center">{{ $collection->return_date? Carbon::parse($collection->return_date)->format('d/m/Y') : '----' }}</td>
              <td class="text-center">{{ $collection->return_author ?? '----' }}</td>
              <td class="text-center">{{ $collection->receiver_author ?? '----' }}</td>
              <td class="text-center" style="color: {{ $collection->return_date ? 'green' : 'red' }} !important">{{ $collection->return_date ? 'Devolvido' : 'Emprestado' }}</td>
              <td>
                <a href="#" class="me-2 print-loan_form" data-url="{{ route('loan_form', ['id' => $collection->id]) }}">
                  <i class="fa-solid fa-print"></i>
                </a>
                @if($auth->edit_collection)
                  <a href="#" class="edit-collection" data-type="{{$collection->type}}" data-observations="{{$collection->observations}}" data-documents="{{json_encode($collection->documentLoans)}}" data-sector="{{$collection->sector}}" data-gender="{{$collection->gender}}" data-id="{{ $collection->id }}" data-loan_date="{{ $collection->loan_date }}" data-loan_author="{{ $collection->loan_author }}" data-loan_receiver="{{ $collection->loan_receiver }}" data-return_date="{{ $collection->return_date }}" data-tel="{{$collection->tel}}" data-return_author="{{ $collection->return_author }}" data-receiver_author="{{ $collection->receiver_author }}" data-user_id="{{ $collection->user_id }}">
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

  <div class="modal fade" id="addDocumentCollectionModal" tabindex="-1" aria-labelledby="addDocumentCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentCollectionModalLabel">Adicionar Coleção de Documentos</h5>
            </div>
            <form action="{{ route('create.loan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="mb-3 col-md-8">
                            <label for="document_id" class="form-label">Documentos</label>
                            <select id="document_id" class="form-control">
                              @foreach ($documents as $document)
                                  <option value="{{$document->id}}">{{$document->id}} - {{$document->description}}</option>
                              @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-md-4 d-flex align-items-end">       
                            <button type="button" class="btn btn-primary" id="addDocumentButton">
                              Adicionar
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <ul id="selectedDocumentsList" class="list-group">
                                <!-- Documentos selecionados serão adicionados aqui -->
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-6 mb-2">
                          <label for="type">Tipo</label>
                          <select name="type" id="" class="form-control">
                            <option value="loan">Empréstimo</option>
                            <option value="transfer">Transfência</option>
                          </select>
                        </div>
                      </div>
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
                                <option value="">Selecione uma opção</option>
                                <option value="Textual">Textual</option>
                                <option value="Cartográfico">Cartográfico</option>
                                <option value="Audiovisual">Audiovisual</option>
                                <option value="Multimídia">Multimídia</option>
                                <option value="Micrográfico">Micrográfico</option>
                                <option value="Digital">Digital</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-3">
                          <label for="sector" class="form-label">Setor/Unidade</label>
                          <input type="text" class="form-control" id="sector" name="sector">
                        </div>

                        <div class="mb-3 col-md-3">
                          <label for="tel" class="form-label">Tel/Ramal</label>
                          <input type="text" class="form-control" id="tel" name="tel">
                        </div>
                        
                        <div class="mb-3 col-md-3">
                            <label for="return_date" class="form-label">Data de Devolução</label>
                            <input type="date" class="form-control" id="return_date" name="return_date">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="return_author" class="form-label">Autor da Devolução</label>
                            <input type="text" class="form-control" id="return_author" name="return_author">
                        </div>
                        <div class="mb-3 col-md-3">
                            <label for="receiver_author" class="form-label">Recebedor da Devolução</label>
                            <input type="text" class="form-control" id="receiver_author" name="receiver_author">
                        </div>
                        <div class="col-md-12">
                          <textarea placeholder="Observações do emprestimo" name="observations" id="observations" cols="2" class="form-control"></textarea>
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
      const addDocumentButton = document.getElementById('addDocumentButton');
      const documentSelect = document.getElementById('document_id');
      const selectedDocumentsList = document.getElementById('selectedDocumentsList');

      // Função para adicionar documento na lista
      function addDocumentToList(documentId, documentText) {
          const listItem = document.createElement('div');
          listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
          listItem.innerHTML = `
              ${documentText}
              <input type="hidden" name="document_ids[]" value="${documentId}">
              <button type="button" class="btn btn-danger btn-sm remove-document">Remover</button>
          `;
          selectedDocumentsList.appendChild(listItem);

          // Remover item da lista ao clicar no botão de remover
          listItem.querySelector('.remove-document').addEventListener('click', function () {
              selectedDocumentsList.removeChild(listItem);
          });
      }

      // Adiciona documento ao clicar no botão
      addDocumentButton.addEventListener('click', function () {
          const selectedOption = documentSelect.options[documentSelect.selectedIndex];
          const documentId = selectedOption.value;
          const documentText = selectedOption.text;

          if (documentId) {
              addDocumentToList(documentId, documentText);
          }
      });

      // Carregar documentos ao abrir o modal de edição
      const editButtons = document.querySelectorAll('.edit-collection');
      const modal = document.getElementById('addDocumentCollectionModal');
      const modalForm = modal.querySelector('form');

      modal.addEventListener('show.bs.modal', function () {
          modalForm.reset();
          modalForm.querySelector('[name="id"]').value = '';
          selectedDocumentsList.innerHTML = ''; // Limpar a lista de documentos

          const documentsData = modalForm.querySelector('input[name="documents_data"]').value;
          if (documentsData) {
              const documents = JSON.parse(documentsData);
              documents.forEach(document => {
                  addDocumentToList(document.id, `${document.id} - ${document.description}`);
              });
          }
      });

      editButtons.forEach(function (button) {
          button.addEventListener('click', function () {
              const id = button.getAttribute('data-id');
              const loan_date = button.getAttribute('data-loan_date');
              const loan_author = button.getAttribute('data-loan_author');
              const loan_receiver = button.getAttribute('data-loan_receiver');
              const gender = button.getAttribute('data-gender');
              const return_date = button.getAttribute('data-return_date');
              const return_author = button.getAttribute('data-return_author');
              const receiver_author = button.getAttribute('data-receiver_author');
              const user_id = button.getAttribute('data-user_id');
              const sector = button.getAttribute('data-sector');
              const documents = JSON.parse(button.getAttribute('data-documents'));
              const observations = button.getAttribute('data-observations');
              const type = button.getAttribute('data-type');
              const tel = button.getAttribute('data-tel');
              
              modalForm.querySelector('[name="id"]').value = id;
              modalForm.querySelector('[name="loan_date"]').value = loan_date;
              modalForm.querySelector('[name="loan_author"]').value = loan_author;
              modalForm.querySelector('[name="loan_receiver"]').value = loan_receiver;
              modalForm.querySelector('[name="gender"]').value = gender;
              modalForm.querySelector('[name="return_date"]').value = return_date;
              modalForm.querySelector('[name="return_author"]').value = return_author;
              modalForm.querySelector('[name="receiver_author"]').value = receiver_author;
              modalForm.querySelector('[name="sector"]').value = sector;
              modalForm.querySelector('[name="observations"]').value = observations;
              modalForm.querySelector('[name="type"]').value = type;
              modalForm.querySelector('[name="tel"]').value = tel;

              selectedDocumentsList.innerHTML = '';
              documents.forEach(document => {
                  addDocumentToList(document.document.id, `${document.document.id} - ${document.document.description ?? ''}`);
              });

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
          modalForm.querySelector('[name="sector"]').value = collectionData.sector;
          modalForm.querySelector('[name="observations"]').value = collectionData.observations;
          modalForm.querySelector('[name="type"]').value = collectionData.type;

          const documents = collectionData.document_loans;

          documents.forEach(document => {
                  addDocumentToList(document.document.id, `${document.document.id} - ${document.document.description ?? ''}`);
              });

          var modalInstance = new bootstrap.Modal(modal);
          modalInstance.show();
        } else if (autoOpenModal && !collectionData) {
          var modalInstance = new bootstrap.Modal(modal);
          modalInstance.show();
        }
      @endif
  });
</script>

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
