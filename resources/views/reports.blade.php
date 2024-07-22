@extends('layouts.app')

@section('content')
<style>
  .card{
    color: rgb(32, 32, 32) !important;
  }
</style>
  @php
    use App\Models\Project;
    use App\Models\Document;
    use Carbon\Carbon;

    $auth = auth()->user();

    if ($auth->is_admin) $projectIds = Project::get()->pluck('id');
    else $projectIds = $auth->projects()->pluck('project_id');
  
    $projects = Project::whereIn('id', $projectIds)->get();
    $documents = Document::whereHas('project', function($query) use($projectIds){
      $query->where('id', session('project_id'));
    })->get();
  @endphp

  @vite(['resources/sass/dashboard.scss'])
  <div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4">Relatórios do sistema</h1>
    <div class="cards">
      <div class="card" data-bs-toggle="modal" data-bs-target="#documentsModal">
        <h4>Documentos</h4>
      </div>
      <div class="card" data-bs-toggle="modal" data-bs-target="#loansModal">
        <h4>Empréstimos do documento</h4>
      </div>
    </div>    
  </div>

  <!-- Modal para Documentos -->
  <div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="documentsModalLabel">Relatório de Documentos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('generate.documents.report') }}" method="GET">
            <div class="mb-3">
              <label for="project" class="form-label">Projeto</label>
              <select name="project_id" id="project" class="form-control" @readonly(true)>
                <option value="">Selecione um projeto</option>
                @foreach($projects as $project)
                  <option {{$project->id == session('project_id') ? 'selected' : ''}} value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="start_date" class="form-label">Data de Início</label>
              <input type="date" name="start_date" id="start_date" class="form-control">
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label">Data de Fim</label>
              <input type="date" name="end_date" id="end_date" class="form-control">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Gerar Relatório</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Empréstimos -->
  <div class="modal fade" id="loansModal" tabindex="-1" aria-labelledby="loansModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loansModalLabel">Relatório de Empréstimos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('generate.loans.report') }}" method="GET">
            <div class="mb-3">
              <label for="document_loan" class="form-label">Documento</label>
              <select name="document_id" id="document_loan" class="form-control">
                <option value="">Selecione um documento</option>
                @foreach($documents as $document)
                  <option value="{{ $document->id }}">{{ $document->id }} - {{ $document->description }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label for="start_date_loan" class="form-label">Data de Início</label>
              <input type="date" name="start_date" id="start_date_loan" class="form-control">
            </div>
            <div class="mb-3">
              <label for="end_date_loan" class="form-label">Data de Fim</label>
              <input type="date" name="end_date" id="end_date_loan" class="form-control">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Gerar Relatório</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
