<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Documento</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h2{
            color: rgb(38, 102, 185);
        }
        .mb-3{
            background: rgb(247, 245, 245);
            margin: 5px;
            border-radius: 10px;
            padding: 10px;
        }
        label{
            color: rgb(38, 102, 185);
            font-weight: 600;
        }

    </style>
</head>
<body>
    @php
        use App\Models\Document;
        use Carbon\Carbon;
        $document = Document::find($document_id);
    @endphp

    <div class="container">
        <h2>Documento encontrado</h2>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label for="temporality_id" class="form-label">Código de classificação</label>
                <p>{{ $document->temporality->code }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="area" class="form-label">Área</label>
                <p>{{ $document->temporality->area }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="function" class="form-label">Função</label>
                <p>{{ $document->temporality->function }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="sub_function" class="form-label">Sub-função</label>
                <p>{{ $document->temporality->sub_function }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="activity" class="form-label">Atividade</label>
                <p>{{ $document->temporality->activity }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="tipology" class="form-label">Tipologia</label>
                <p>{{ $document->temporality->tipology }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="current_custody_period" class="form-label">Prazo de Guarda Corrente</label>
                <p>{{ $document->temporality->current_custody_period }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="intermediate_custody_period" class="form-label">Prazo de Guarda Intermediária</label>
                <p>{{ $document->temporality->intermediate_custody_period }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="final_destination" class="form-label">Destinação Final</label>
                <p>{{ $document->temporality->final_destination }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="doc_number" class="form-label">Nª do documento</label>
                <p>{{ $document->doc_number }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="holder_name" class="form-label">Nome do Titular</label>
                <p>{{ $document->holder_name }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="initial_Carbon::parse(" class="form-label">Data inicia)->format('d/m/Y')l</label>
                <p>{{ Carbon::parse($document->initial_date)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="archive_date" class="form-label">Data de arquivamento</label>
                <p>{{ Carbon::parse($document->archive_date)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="expiration_date_A_C" class="form-label">Data de expiração A.C</label>
                <p>{{ Carbon::parse($document->expiration_date_A_C)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="expiration_date_A_I" class="form-label">Data de expiração A.I</label>
                <p>{{ Carbon::parse($document->expiration_date_A_I)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="type" class="form-label">Tipo de arquivamento</label>
                <p>{{ $document->type == 1 ? 'Caixa' : 'Armário' }}</p>
            </div>
            @if ($document->type == 1)
            <div class="mb-3 col-md-3">
                <label for="box" class="form-label">Caixa</label>
                <p>{{ $document->box }}</p>
            </div>
            @else
            <div class="mb-3 col-md-3">
                <label for="cabinet" class="form-label">Armário</label>
                <p>{{ $document->cabinet }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="drawer" class="form-label">Gaveta</label>
                <p>{{ $document->drawer }}</p>
            </div>
            @endif
            <div class="mb-3 col-md-3">
                <label for="qtpasta" class="form-label">Quantidade de Pastas</label>
                <p>{{ $document->qtpasta }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="classification" class="form-label">Classificação da informação</label>
                <p>{{ $document->classification }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="version" class="form-label">Versão</label>
                <p>{{ $document->version }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="situationAC" class="form-label">Situação A.C</label>
                <p>{{ $document->situationAC }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="situationAI" class="form-label">Situação A.I</label>
                <p>{{ $document->situationAI }}</p>
            </div>
            <div class="mb-3 col-md-12">
                <label for="tags" class="form-label">Tags</label>
                <p>{{ $document->tags }}</p>
            </div>
            <div class="mb-3 col-md-12">
                <label for="description" class="form-label">Descrição</label>
                <p>{{ $document->description }}</p>
            </div>
            <div class="mb-3 col-md-12">
                <label for="observations" class="form-label">Observações</label>
                <p>{{ $document->observations }}</p>
            </div>
            <div class="mb-3 col-md-12">
                <label for="files" class="form-label">Arquivos</label>
                @foreach ($document->files as $file)
                    <p><a href="{{ asset('storage/' . $file->path) }}" target="_blank">{{ $file->name }}</a></p>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
</body>
</html>
