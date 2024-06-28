@extends('layouts.app')

@section('content')
@vite(['resources/js/tags.js'])

  @php
    use App\Models\Document;
    use App\Models\Project;
    use App\Models\Temporality;
    use App\Models\DocumentCollection;
    
    $initial_date = $_GET['initial_date'] ?? null;
    $archive_date = $_GET['archive_date'] ?? null;

    $classification_search = $_GET['classification_search'] ?? null;
    $box_search = $_GET['box_search'] ?? null;
    $cabinet_search = $_GET['cabinet_search'] ?? null;
    $drawer_search = $_GET['drawer_search'] ?? null;
    $destination_search = $_GET['destination_search'] ?? null;
    $version_search = $_GET['version_search'] ?? null;
    $loan_situation_search = $_GET['loan_situation_search'] ?? null;
    $order_by = $_GET['order_by'] ?? 'id';
    $order_form = $_GET['order_form'] ?? 'desc';
    $tags_search = $_GET['tags_search'] ?? '';
    $all_search = $_GET['all_search'] ?? '';
    $holder_search = $_GET['holder_search'] ?? '';
    $doc_number_search = $_GET['doc_number_search'] ?? '';
    
    $auth = auth()->user();
    $document = Document::orderBy($order_by, $order_form);

    if($classification_search){
        $document->where('classification', $classification_search);    }

    if($doc_number_search){
      $document->where('doc_number', $doc_number_search);
    }

    if($holder_search){
      $document->where('holder_name', 'like', "%$holder_search%");
    }
    
    if($all_search){
      $document->where('holder_name', 'like', "%$all_search%")
      ->orWhere('box', $all_search)
      ->orWhere('cabinet', $all_search)
      ->orWhere('description', $all_search)
      ->orWhere('drawer', $all_search)
      ->orWhere('tags', $all_search)
      ->orWhere('doc_number', $all_search);
    }

    if($box_search){      
        $document->where('box', $box_search);
    }

    if($cabinet_search){      
        $document->where('cabinet', $cabinet_search);
    }

    if($drawer_search){      
        $document->where('drawer', $drawer_search);
    }

    if($destination_search){      
        $document->whereHas('temporality', function($query) use($destination_search) {
          $query->where('final_destination', $destination_search);
        });
    }

    if($tags_search){
      $document->where('tags', 'like', "%$tags_search%");
    }

    if($version_search){      
        $document->where('version', $version_search);
          $document->orWhere('version', "$all_search");
    }

    if ($loan_situation_search) {
      if($loan_situation_search === "Emprestado"){
        $document->whereHas('documents_collections', function ($query) {
            $query->whereNotNull('return_date');
        });
      }else{
        $document->whereHas('documents_collections', function ($query) {
            $query->whereNull('return_date');
        });
      }
    }

    if($initial_date){      
        $document->whereDate('initial_date', $initial_date);
    }

    if($archive_date){      
        $document->whereDate('archive_date', $initial_date);
    }

    $documents = [];
    $temporalitys = [];

    if($auth->read_doc){
      $documents = $document->where('project_id', $project_id);
      $uniqueBoxes = Document::where('project_id', $project_id)
        ->whereNotNull('box')
        ->distinct()
        ->pluck('box');      

      $documents = $documents->get();
      $temporalitys = Temporality::get();
    }

    $project = Project::find($project_id);
  @endphp
    
  @vite(['resources/sass/dashboard.scss'])
  <div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4 d-flex justify-content-space-around" style="flex-wrap: wrap">
      <div>
        @if($project->image_path)      
          <img src="{{ asset('storage/' . $project->image_path) }}" width="70">
        @endif
      </div>






      Documentos

        <div>
          <button class="btn btn-sm btn-primary ms-2" id="generateBoxPrint">Gerar impressão de caixa</button>
        </div>
        
        <div>
          <button class="btn btn-sm btn-primary ms-2" id="generateCabinetPrint">Gerar impressão de Armário</button>
        </div>
      
    </h1>
    @if($auth->create_doc)
      <a href="#" class="fs-1 c-green add" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
        <i class="fa-solid fa-circle-plus"></i>
      </a>
    @endif

    <form action="" class="row mb-3">
      <div class="col-md-12 mt-2 mt-2">
        <label for="classification_search">Buscar documento</label>
        <input value="{{$all_search}}" type="text" id="all_search" name="all_search"  class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="classification_search">Código de classificação</label>
        <input value="{{$classification_search}}" type="text" id="classification_search" name="classification_search"  class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="box_search">Caixa</label>
        <input value="{{$box_search}}" type="text" id="box_search" name="box_search" class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="cabinet_search">Armário</label>
        <input value="{{$cabinet_search}}" type="text" id="cabinet_search" name="cabinet_search"class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="search">Gaveta</label>
        <input value="{{$drawer_search}}" type="text" id="drawer_search" name="drawer_search" class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="search">Destinação final</label>
        <select class="form-control" name="destination_search" id="">         
          <option value="">Selecione uma opção</option> 
          <option {{$destination_search == 'Permanente' ? 'selected' : ''}} value="Permanente">Permanente</option>          
          <option {{$destination_search == 'Eliminação' ? 'selected' : ''}} value="Eliminação">Eliminação</option>
        </select>      </div>
      <div class="col-md-2 mt-2">
        <label for="search">Versão do documento</label>
        <select class="form-control" name="version_search" id="">          
          <option value="">Selecione uma opção</option>
          <option {{$version_search == 'Físico' ? 'selected' : ''}} value="Físico">Físico</option>
          <option {{$version_search == 'Digital' ? 'selected' : ''}} value="Digital">Digital</option>
          <option {{$version_search == 'Híbrido' ? 'selected' : ''}} value="Híbrido">Híbrido</option>
        </select>
      </div>
      <div class="col-md-2 mt-2">
        <label for="search">Situação do empréstimo</label>
        <select class="form-control" name="loan_situation_search" id="">          
          <option value="">Selecione uma opção</option>
          <option {{$loan_situation_search == 'Emprestado' ? 'selected' : ''}} value="Emprestado">Emprestado</option>
          <option {{$loan_situation_search == 'Devolvido' ? 'selected' : ''}} value="Devolvido">Devolvido</option>          
        </select>
      </div>
      <div class="col-md-2 mt-2">
        <label for="classification_search">Número do documento</label>
        <input value="{{$doc_number_search}}" type="text" id="doc_number_search" name="doc_number_search"  class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="classification_search">Nome do titular</label>
        <input value="{{$holder_search}}" type="text" id="holder_search" name="holder_search"  class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="tags_search">Tags</label>
        <input value="{{$tags_search}}" type="text" id="tags_search" name="tags_search" class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="initial_date_filter">Data inicial</label>
        <input value="{{$initial_date}}" type="date" id="initial_date_filter" name="initial_date" class="form-control">
      </div>
      <div class="col-md-2 mt-2">
        <label for="archive_date_filter">Data de arquivamento</label>
        <input value="{{$archive_date}}" type="date" id="archive_date_filter" name="archive_date" class="form-control">
      </div>
      <div class="col-md-2 mt-3">
        <input type="submit" class="btn btn-primary mt-4" value="Filtrar">
      </div>
      <div class="col-12 row mt-2">
        <div class="col-md-2">
          <label for="order_by">Ordenar por</label>
          <select class="form-control" name="order_by" id="order_by">          
            <option value="">Selecione uma opção</option>
            <option value="id" {{ $order_by == 'id' ? 'selected' : '' }}>ID</option>
            <option value="doc_number" {{ $order_by == 'doc_number' ? 'selected' : '' }}>Número do Documento</option>
            <option value="holder_name" {{ $order_by == 'holder_name' ? 'selected' : '' }}>Nome do Titular</option>
            <option value="description" {{ $order_by == 'description' ? 'selected' : '' }}>Descrição</option>
            <option value="box" {{ $order_by == 'box' ? 'selected' : '' }}>Caixa</option>
            <option value="cabinet" {{ $order_by == 'cabinet' ? 'selected' : '' }}>Armário</option>
            <option value="drawer" {{ $order_by == 'drawer' ? 'selected' : '' }}>Gavetas</option>
            <option value="qtpasta" {{ $order_by == 'qtpasta' ? 'selected' : '' }}>Quantidade de Pastas</option>
            <option value="version" {{ $order_by == 'version' ? 'selected' : '' }}>Versão</option>
            <option value="classification" {{ $order_by == 'classification' ? 'selected' : '' }}>Código de Classificação</option>
            <option value="area" {{ $order_by == 'area' ? 'selected' : '' }}>Área</option>
            <option value="function" {{ $order_by == 'function' ? 'selected' : '' }}>Função</option>
            <option value="sub_function" {{ $order_by == 'sub_function' ? 'selected' : '' }}>Sub-Função</option>
            <option value="activity" {{ $order_by == 'activity' ? 'selected' : '' }}>Atividade</option>
            <option value="tipology" {{ $order_by == 'tipology' ? 'selected' : '' }}>Tipologia</option>
            <option value="current_custody_period" {{ $order_by == 'current_custody_period' ? 'selected' : '' }}>Prazo de Guarda Corrente</option>
            <option value="intermediate_custody_period" {{ $order_by == 'intermediate_custody_period' ? 'selected' : '' }}>Prazo de Guarda Intermediária</option>
            <option value="final_destination" {{ $order_by == 'final_destination' ? 'selected' : '' }}>Destinação Final</option>
            <option value="situationAC" {{ $order_by == 'situationAC' ? 'selected' : '' }}>Situação A.C</option>
            <option value="situationAI" {{ $order_by == 'situationAI' ? 'selected' : '' }}>Situação A.I</option>
          </select>
        </div>
        <div class="col-md-2">
          <label for="order_form">Forma de ordenação</label>
          <select class="form-control" name="order_form" id="order_form">          
            <option value="">Selecione uma opção</option>
            <option value="asc" {{ $order_form == 'asc' ? 'selected' : '' }}>Ascendente</option>          
            <option value="desc" {{ $order_form == 'desc' ? 'selected' : '' }}>Descendente</option>
          </select>
        </div>
      </div>
    </form>

    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Código de Classificação</th>
            <th scope="col">Número do Documento</th>
            <th scope="col">Nome do Titular</th>
            <th scope="col">Descrição</th>
            <th scope="col">Caixa</th>
            <th scope="col">Arḿario</th>
            <th scope="col">Gavetas</th>
            <th scope="col">Quantidade de Pastas</th>
            <th scope="col">Situação A.C</th>
            <th scope="col">Situação A.I</th>     
            <th scope="col" class="text-center">Status</th>
            <th scope="col">Detalhes</th>
            <th scope="col">Arquivos</th>
            <th scope="col" class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($documents as $doc)
            <tr class="{{strpos($doc->situationAC . ' ' . $doc->situationAI, "Descartado") !== false ? 'tr-grey' : ''}}">
              <th scope="row">{{ $doc->id }}</th>
              <td class="text-center">{{ $doc->temporality->code }}</td>
              <td class="text-center">{{ $doc->doc_number }}</td>
              <td class="text-center">{{ $doc->holder_name }}</td>
              <td class="text-center">{{ $doc->description }}</td>
              <td class="text-center">{{ $doc->box ?? '----' }}</td>
              <td class="text-center">{{ $doc->cabinet ?? '----' }}</td>
              <td class="text-center">{{ $doc->drawer ?? '----' }}</td>
              <td class="text-center">{{ $doc->qtpasta ?? '----' }}</td>

              <td class="text-center" style="font-weight: 600; color: {{!$doc->situationAC ? '' : ($doc->situationAC == 'Ativo' ? 'green' : 'red')}} !important;">{{ $doc->situationAC }}</td>
              <td class="text-center" style="font-weight: 600; color: {{!$doc->situationAI ? '' : ($doc->situationAI == 'Ativo' ? 'green' : 'red')}} !important;">{{ $doc->situationAI }}</td>
              @php
                  $documentCollection = DocumentCollection::whereNull('return_date')->whereHas('documentLoans', function($query) use($doc){
                    $query->where('document_id', $doc->id);
                  })->first();
                  $type = $documentCollection->type ?? 'Arquivado';
              @endphp
              <td class="text-center" style="font-weight: 600; color: {{($type == 'transfer' || $type == 'loan') ? 'red' : ''}} !important;">{{$type == 'transfer' ? 'Transferido' : ($type == 'loan' ? "Emprestado" : $type)}}</td>
              <td class="text-center fs-4">
                <a href="#" class="edit-document" style="color: rgb(50, 127, 243) !important;" data-edit="1" data-tags="{{$doc->tags}}" data-archive_date="{{$doc->archive_date}}" data-initial_date="{{$doc->initial_date}}" data-id="{{ $doc->id }}" data-observations="{{ $doc->observations }}" data-project_id="{{ $doc->project_id }}" data-temporality_id="{{ $doc->temporality_id }}" data-doc_number="{{ $doc->doc_number }}" data-holder_name="{{ $doc->holder_name }}" data-description="{{ $doc->description }}" data-box="{{ $doc->box }}" data-qtpasta="{{ $doc->qtpasta }}" data-file="{{ $doc->file }}" data-cabinet="{{ $doc->cabinet }}" data-drawer="{{ $doc->drawer }}" data-classification="{{ $doc->classification }}" data-version="{{ $doc->version }}" data-situationac="{{ $doc->situationAC }}" data-situationai="{{ $doc->situationAI }}">
                  <i class="fa-solid fa-circle-info"></i>
                </a>
              </td>
              <td>
                <a href="#" class="c-blue view-files fs-4" data-id="{{ $doc->id }}">
                  <i class="fa-solid fa-folder-open"></i>
                </a>
              </td>
              <td class="text-center">  
                @if($auth->edit_doc)
                  <a href="#" class="me-2 edit-document" data-tags="{{$doc->tags}}" data-archive_date="{{$doc->archive_date}}" data-initial_date="{{$doc->initial_date}}" data-id="{{ $doc->id }}" data-observations="{{ $doc->observations }}" data-project_id="{{ $doc->project_id }}" data-temporality_id="{{ $doc->temporality_id }}" data-doc_number="{{ $doc->doc_number }}" data-holder_name="{{ $doc->holder_name }}" data-description="{{ $doc->description }}" data-box="{{ $doc->box }}" data-qtpasta="{{ $doc->qtpasta }}" data-file="{{ $doc->file }}" data-cabinet="{{ $doc->cabinet }}" data-drawer="{{ $doc->drawer }}" data-classification="{{ $doc->classification }}" data-version="{{ $doc->version }}" data-situationac="{{ $doc->situationAC }}" data-situationai="{{ $doc->situationAI }}">
                    <i class="fa-solid fa-pen"></i>
                  </a>
                @endif
                <a href="#" class="me-2 print-label" data-url="{{ route('label', ['id' => $doc->id]) }}">
                  <i class="fa-solid fa-print"></i>
                </a>
                <a href="{{ route('document.collections', ['document_id' => $doc->id]) }}">                  
                  <i class="fa-solid fa-box-open"></i>
                </a>
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
                  <option value="">Selecione uma opção</option>
                  @foreach ($temporalitys as $temporality)
                      <option value="{{$temporality->id}}">{{$temporality->code}}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3 col-md-3">
                <label for="area" class="form-label">Área</label>
                <input type="text" class="form-control" id="area"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="function" class="form-label">Função</label>
                <input type="text" class="form-control" id="function"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="sub_function" class="form-label">Sub-função</label>
                <input type="text" class="form-control" id="sub_function"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="activity" class="form-label">Atividade</label>
                <input type="text" class="form-control" id="activity"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="tipology" class="form-label">Tipologia</label>
                <input type="text" class="form-control" id="tipology"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="current_custody_period" class="form-label">Prazo de Guarda Corrente</label>
                <input type="number" step="1" class="form-control" id="current_custody_period"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="intermediate_custody_period" class="form-label">Prazo de Guarda Intermediária</label>
                <input type="number" step="1" class="form-control" id="intermediate_custody_period"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="final_destination" class="form-label">Destinação Final</label>
                <input type="text" class="form-control" id="final_destination"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="doc_number" class="form-label">Nª do documento</label>
                <input type="text" class="form-control" id="doc_number" name="doc_number" >
              </div>

              <div class="mb-3 col-md-3">
                <label for="holder_name" class="form-label">Nome do Titular</label>
                <input type="text" class="form-control" id="holder_name" name="holder_name" >
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
                <input type="date" class="form-control" id="archive_date" name="archive_date">
              </div>
              <div class="mb-3 col-md-3">
                <label for="expiration_date_A_C" class="form-label">Data de expiração A.C</label>
                <input type="date" class="form-control" id="expiration_date_A_C" name="expiration_date_A_C"  readonly>
              </div>
              <div class="mb-3 col-md-3">
                <label for="expiration_date_A_I" class="form-label">Data de expiração A.I</label>
                <input type="date" class="form-control" id="expiration_date_A_I" name="expiration_date_A_I"  readonly>
              </div>

              <div class="mb-3 col-md-3">
                <label for="type" class="form-label">Tipo de arquivamento</label>
                <select class="form-control" id="type" name="type">
                  <option value="">Selecione uma opção</option>
                  <option value="1">Caixa</option>
                  <option value="2">Armário</option>
                </select>
              </div>
                <div  class="mb-3 col-md-3" id="boxFields" style="display:none;"> 
                  <label for="box" class="form-label">Caixa</label>
                  <input type="text" class="form-control" id="box" name="box">
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
                <label for="qtpasta" class="form-label">Quantidade de Pastas</label>
                <input type="number"  class="form-control" id="qtpasta" name="qtpasta">
              </div>
              
              <div class="mb-3 col-md-3">
                <label for="classification" class="form-label">Classificação da informação</label>
                <select class="form-control" id="classification" name="classification" >
                  <option value="">Selecione uma opção</option>
                  <option value="Pública">Pública</option>
                  <option value="Interna">Interna</option>
                  <option value="Confidencial">Confidencial</option>
                </select>
              </div>
              <div class="mb-3 col-md-3">
                <label for="version" class="form-label">Versão</label>
                <select class="form-control" id="version" name="version" >
                  <option  value="Físico">Físico</option>
                  <option  value="Digital">Digital</option>
                  <option  value="Híbrido">Híbrido</option>
                </select>
              </div>              
              <div class="mb-3 col-md-3">
                <label for="situationAC" class="form-label">Situação A.C</label>
                <select class="form-control" id="situationAC" name="situationAC" >
                  <option value="">Selecione uma opção</option>
                  <option style="background: red; color: white;" value="Transferido A.I">🔴 Transferido A.i</option>
                  <option style="background: green; color: white;" value="Ativo">🟢 Ativo</option>
                  <option style="background: red; color: white;" value="Descartado">🔴 Descartado</option>
                </select>
              </div>
              <div class="mb-3 col-md-3">
                <label for="situationAI" class="form-label">Situação A.I</label>
                <select class="form-control" id="situationAI" name="situationAI" >
                  <option value="">Selecione uma opção</option>
                  <option style="background: red; color: white;" value="Recolhido A.P">🔴 Recolhido A.P</option>
                  <option style="background: green; color: white;" value="Ativo">🟢 Ativo</option>
                  <option style="background: red; color: white;" value="Descartado">🔴 Descartado</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Tags</label>
              <input class="form-control" id="tags" name="tags" ></textarea>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Descrição</label>
              <textarea class="form-control" id="description" name="description" ></textarea>
            </div>
            <div class="mb-3">
              <label for="observations" class="form-label">Observações</label>
              <textarea class="form-control" id="observations" name="observations" ></textarea>
            </div>
            <div class="mb-3">
              <label for="file" class="form-label">Arquivos</label>
              <input type="file" class="form-control" id="file" name="files[]" multiple>
            </div>
            
          </div>
          <div class="modal-footer">
            <button id="submit-button" type="submit" class="btn btn-primary">Salvar</button>
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

  <!-- Modal para gerar impressão da caixa -->
  <div class="modal fade" id="generateBoxPrintModal" tabindex="-1" aria-labelledby="generateBoxPrintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="generateBoxPrintModalLabel">Gerar Impressão da Caixa</h5>
        </div>
        <div class="modal-body">
          <form id="boxPrintForm">
            <div class="row">
              <div class="mb-3 col-md-4">
                <label for="central_archive" class="form-label">Arquivo Central</label>
                <input type="text" class="form-control" id="central_archive" name="central_archive" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="organization" class="form-label">Opcional</label>
                <input type="text" class="form-control" id="organization" name="organization">
              </div>
              <div class="mb-3 col-md-4">
                <label for="classification_code" class="form-label">Código de Classificação</label>
                <input type="text" class="form-control" id="classification_code" name="classification_code" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="area" class="form-label">Área</label>
                <select type="text" class="form-control" id="area" name="area" required>
                  <option value="MEIO">Meio</option>
                  <option value="FIM">Fim</option>
                </select>
              </div>
              <div class="mb-3 col-md-4">
                <label for="custody_period" class="form-label">Prazo de Guarda</label>
                <input type="text" class="form-control" id="custody_period" name="custody_period" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="archive_year" class="form-label">Ano de Arquivamento</label>
                <input type="text" class="form-control" id="archive_year" name="archive_year" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="location" class="form-label">Localização</label>
                <input type="text" class="form-control" id="location" name="location" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="final_destination" class="form-label">Destinação Final</label>
                <select class="form-control" id="final_destination" name="final_destination" required>
                  <option value="Permanente">Permanente</option>
                  <option value="Eliminação">Eliminação</option>
                </select>
              </div>
              <div class="mb-3 col-md-4">
                <label for="box_number" class="form-label">Número da Caixa</label>
                <select class="form-control" name="box_number" id="box_number">
                  @foreach ($uniqueBoxes as $uniqueBox)
                      <option value="{{$uniqueBox}}">{{$uniqueBox}}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3 col-md-12">
                <label for="observations" class="form-label">Observações</label>
                <input type="text" class="form-control" id="observations" name="observations">
              </div>
            </div>
            <button type="button" class="btn btn-primary" id="addBoxPrint">Adicionar</button>
            <button type="button" class="btn btn-success" id="printBox">Imprimir</button>
          </form>

          <div class="table-container mt-5" style="overflow-x: scroll">
            <table class="table table-striped" id="boxPrintTable">
              <thead>
                <tr>
                  <th>Arquivo Central</th>
                  <th>Organização e Funcionamento</th>
                  <th>Código de Classificação</th>
                  <th>Área</th>
                  <th>Prazo de Guarda</th>
                  <th>Observações</th>
                  <th>Ano de Arquivamento</th>
                  <th>Localização</th>
                  <th>Destinação Final</th>
                  <th>Número da Caixa</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>    
  </div>

    <!-- Modal para gerar impressão de armário -->
  <div class="modal fade" id="generateCabinetPrintModal" tabindex="-1" aria-labelledby="generateCabinetPrintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="generateCabinetPrintModalLabel">Gerar Impressão do Armário</h5>
        </div>
        <div class="modal-body">
          <form id="cabinetPrintForm">
            <div class="row">
              <div class="mb-3 col-md-4">
                <label for="central_archive_cabinet" class="form-label">Arquivo Central</label>
                <input type="text" class="form-control" id="central_archive_cabinet" name="central_archive_cabinet" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="organization_cabinet" class="form-label">Opcional</label>
                <input type="text" class="form-control" id="organization_cabinet" name="organization_cabinet">
              </div>
              <div class="mb-3 col-md-4">
                <label for="classification_code_cabinet" class="form-label">Código de Classificação</label>
                <input type="text" class="form-control" id="classification_code_cabinet" name="classification_code_cabinet" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="area_cabinet" class="form-label">Área</label>
                <select type="text" class="form-control" id="area_cabinet" name="area_cabinet" required>
                  <option value="MEIO">Meio</option>
                  <option value="FIM">Fim</option>
                </select>
              </div>
              <div class="mb-3 col-md-4">
                <label for="custody_period_cabinet" class="form-label">Prazo de Guarda</label>
                <input type="text" class="form-control" id="custody_period_cabinet" name="custody_period_cabinet" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="archive_year_cabinet" class="form-label">Ano de Arquivamento</label>
                <input type="text" class="form-control" id="archive_year_cabinet" name="archive_year_cabinet" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="location_cabinet" class="form-label">Localização</label>
                <input type="text" class="form-control" id="location_cabinet" name="location_cabinet" required>
              </div>
              <div class="mb-3 col-md-4">
                <label for="final_destination_cabinet" class="form-label">Destinação Final</label>
                <select class="form-control" id="final_destination_cabinet" name="final_destination_cabinet" required>
                  <option value="Permanente">Permanente</option>
                  <option value="Eliminação">Eliminação</option>
                </select>
              </div>
              <div class="mb-3 col-md-4">
                <label for="cabinet_number" class="form-label">Número do Armário</label>
                <input class="form-control" name="cabinet_number" id="cabinet_number" type="number">
              </div>
              <div class="mb-3 col-md-4">
                <label for="drawer_number" class="form-label">Número da Gaveta</label>
                <input type="text" class="form-control" id="drawer_number" name="drawer_number" required>
              </div>
              <div class="mb-3 col-md-8">
                <label for="observations_cabinet" class="form-label">Observações</label>
                <input type="text" class="form-control" id="observations_cabinet" name="observations_cabinet">
              </div>
            </div>
            <button type="button" class="btn btn-primary" id="addCabinetPrint">Adicionar</button>
            <button type="button" class="btn btn-success" id="printCabinet">Imprimir</button>
          </form>

          <div class="table-container mt-5" style="overflow-x: scroll">
            <table class="table table-striped" id="cabinetPrintTable">
              <thead>
                <tr>
                  <th>Arquivo Central</th>
                  <th>Organização e Funcionamento</th>
                  <th>Código de Classificação</th>
                  <th>Área</th>
                  <th>Prazo de Guarda</th>
                  <th>Observações</th>
                  <th>Ano de Arquivamento</th>
                  <th>Localização</th>
                  <th>Destinação Final</th>
                  <th>Número do Armário</th>
                  <th>Número da Gaveta</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
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

  document.getElementById('generateBoxPrint').addEventListener('click', function () {
    var modal = new bootstrap.Modal(document.getElementById('generateBoxPrintModal'));
    modal.show();
  });

  caixas = []
  document.getElementById('addBoxPrint').addEventListener('click', function () {
    var form = document.getElementById('boxPrintForm');
    caixas.push([ form.central_archive.value, form.organization.value, form.classification_code.value, form.area.value, form.custody_period.value, form.observations.value, form.archive_year.value, form.location.value, form.final_destination.value, form.box_number.value, {{$project_id}}])
    console.log(caixas)
    addTableBox(caixas);
    form.reset();
  });

  function addTableBox(caixas){
    var table = document.getElementById('boxPrintTable').querySelector('tbody');

    table.innerHTML = '';

    
    caixas.forEach((caixa) => {
          var row = table.insertRow();
          row.insertCell(0).innerText = caixa[0];
          row.insertCell(1).innerText = caixa[1];
          row.insertCell(2).innerText = caixa[2];
          row.insertCell(3).innerText = caixa[3];
          row.insertCell(4).innerText = caixa[4];
          row.insertCell(5).innerText = caixa[5];
          row.insertCell(6).innerText = caixa[6];
          row.insertCell(7).innerText = caixa[7];
          row.insertCell(8).innerText = caixa[8];
          row.insertCell(9).innerText = caixa[9];
    })
  }

  document.getElementById('printBox').addEventListener('click', function () {
    var queryString = Object.keys(caixas).map(key => key + '=' + encodeURIComponent(caixas[key])).join('&');
    window.location.href = '/box?' + queryString;
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
    const token = modalForm.querySelectorAll('input[name="_token"]')[0].value
    modalForm.querySelectorAll('input[name="id"]')[0].value = '';
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
    modalForm.querySelectorAll('input[name="_token"]')[0].value = token
  });

  function toggleFields() {
    var typeSelect = document.getElementById('type');
    var boxFields = document.getElementById('boxFields');
    var cabinetFields = document.getElementById('cabinetFields');

    if (typeSelect.value == '1') {
      boxFields.style.display = 'block';
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
      var tags = button.getAttribute('data-tags');    
      var edit = button.getAttribute('data-edit');

      if (box) {
        document.getElementById('type').value = '1';
      } else if (cabinet) {
        document.getElementById('type').value = '2';
      } else {
        document.getElementById('type').value = '';
      }

      if (edit) {
        document.getElementById('submit-button').style.display = 'none';
        modalForm.querySelectorAll('input, select, textarea').forEach(function(element) {
            element.setAttribute('disabled', 'true');
        });
    } else {
        document.getElementById('submit-button').style.display = 'block';
        modalForm.querySelectorAll('input, select, textarea').forEach(function(element) {
            element.removeAttribute('disabled');
        });
    }


      toggleFields();

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
      modalForm.querySelector('[name="tags"]').value = tags;

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
                <div>
                  <a href="/storage/${file.file_path}" target="_blank" class="btn btn-sm btn-primary">Abrir</a>
                  <button class="btn btn-sm btn-danger delete-file" data-file-id="${file.id}">Apagar</button>
                </div>
              `;
              fileList.appendChild(listItem);
            });
          } else {
            fileList.innerHTML = '<li class="list-group-item">Nenhum arquivo encontrado</li>';
          }
          // Configure o botão de download para baixar todos os arquivos
          downloadAllFilesButton.href = `/documents/${documentId}/download-all`;
          viewFilesModal.show();

          // Adiciona evento de clique para os botões de exclusão
          var deleteFileButtons = document.querySelectorAll('.delete-file');
          deleteFileButtons.forEach(function (deleteButton) {
            deleteButton.addEventListener('click', function () {
              var fileId = this.getAttribute('data-file-id');
              if (confirm('Você tem certeza que deseja apagar este arquivo?')) {
                fetch(`/documents/files/${fileId}`, {
                  method: 'DELETE',
                  headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                  }
                })
                .then(response => {
                  if (response.ok) {
                    this.closest('li').remove();
                  } else {
                    alert('Erro ao apagar o arquivo.');
                  }
                })
                .catch(error => console.error('Erro:', error));
              }
            });
          });
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
  document.getElementById('generateCabinetPrint').addEventListener('click', function () {
    var modal = new bootstrap.Modal(document.getElementById('generateCabinetPrintModal'));
    modal.show();
  });

  armarios = []
  document.getElementById('addCabinetPrint').addEventListener('click', function () {
    var form = document.getElementById('cabinetPrintForm');
    armarios.push([
      form.central_archive_cabinet.value,
      form.organization_cabinet.value,
      form.classification_code_cabinet.value,
      form.area_cabinet.value,
      form.custody_period_cabinet.value,
      form.observations_cabinet.value,
      form.archive_year_cabinet.value,
      form.location_cabinet.value,
      form.final_destination_cabinet.value,
      form.cabinet_number.value,
      form.drawer_number.value,
      {{$project_id}}
    ]);
    addTableCabinet(armarios);
    form.reset();
  });

  function addTableCabinet(armarios) {
    var table = document.getElementById('cabinetPrintTable').querySelector('tbody');
    table.innerHTML = '';
    armarios.forEach((armario) => {
      var row = table.insertRow();
      row.insertCell(0).innerText = armario[0];
      row.insertCell(1).innerText = armario[1];
      row.insertCell(2).innerText = armario[2];
      row.insertCell(3).innerText = armario[3];
      row.insertCell(4).innerText = armario[4];
      row.insertCell(5).innerText = armario[5];
      row.insertCell(6).innerText = armario[6];
      row.insertCell(7).innerText = armario[7];
      row.insertCell(8).innerText = armario[8];
      row.insertCell(9).innerText = armario[9];
      row.insertCell(10).innerText = armario[10];
    });
  }

  document.getElementById('printCabinet').addEventListener('click', function () {
    var queryString = Object.keys(armarios).map(key => key + '=' + encodeURIComponent(armarios[key])).join('&');
    window.location.href = '/cabinet?' + queryString;
  });
});


  </script>

@endsection
