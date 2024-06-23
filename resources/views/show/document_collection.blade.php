<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Empréstimo</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h2 {
            color: rgb(38, 102, 185);
        }
        .mb-3 {
            background: rgb(247, 245, 245);
            margin: 5px;
            border-radius: 10px;
            padding: 10px;
        }
        label {
            color: rgb(38, 102, 185);
            font-weight: 600;
        }
        .document{
            box-shadow: 2px 4px 8px rgba(0, 0, 0, 0.4);
        }
        .naodevolvido, .devolvido{
            font-size: 1rem;
            width: 150px;
            padding: 5px 10px;
            margin: 0 10px;
            margin-left: 0 !important;
            border-radius: 10px;
            text-align: center;
        }

        .naodevolvido{
            background: red;
            color: white;
        }

        .devolvido{
            background: rgb(10, 145, 39);
            color: white;
        }
    </style>
</head>
<body>
    @php
        use App\Models\DocumentCollection;
        use Carbon\Carbon;
        $documentCollection = DocumentCollection::find($id);
    @endphp

    <div class="container">
        <h2>
            Empréstimo encontrado
            @if($documentCollection->return_date)
                <div class="devolvido">Devolvido</div>
            @else
                <div class="naodevolvido">Não devolvido</div>
            @endif
        </h2>
        <div class="row">
            <div class="mb-3 col-md-3">
                <label for="loan_date" class="form-label">Data do Empréstimo</label>
                <p>{{ Carbon::parse($documentCollection->loan_date)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="loan_author" class="form-label">Autor do Empréstimo</label>
                <p>{{ $documentCollection->loan_author }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="loan_receiver" class="form-label">Receptor do Empréstimo</label>
                <p>{{ $documentCollection->loan_receiver }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="gender" class="form-label">Gênero</label>
                <p>{{ $documentCollection->gender }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="return_date" class="form-label">Data de Retorno</label>
                <p>{{ Carbon::parse($documentCollection->return_date)->format('d/m/Y') }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="sector" class="form-label">Setor</label>
                <p>{{ $documentCollection->sector }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="return_author" class="form-label">Autor do Retorno</label>
                <p>{{ $documentCollection->return_author }}</p>
            </div>
            <div class="mb-3 col-md-3">
                <label for="receiver_author" class="form-label">Receptor do Autor</label>
                <p>{{ $documentCollection->receiver_author }}</p>
            </div>
            <div class="mb-3 col-md-12">
                <label for="observations" class="form-label">Observações</label>
                <p>{{ $documentCollection->observations }}</p>
            </div>
            <div class="mb-3 col-md-12">
                <label for="user" class="form-label">Usuário</label>
                <p>{{ $documentCollection->user->name }}</p>
            </div>
            <div class="mb-3 col-md-12">
                <h3>Documentos Emprestados</h3>
                <div class="row col-12">
                    @foreach ($documentCollection->documentLoans as $documentLoan)
                        <div class="mb-3 document">
                            <label for="document_id" class="form-label">ID do Documento</label>
                            <p>{{ $documentLoan->document->id }}</p>
                            <label for="document_description" class="form-label">Descrição do Documento</label>
                            <p>{{ $documentLoan->document->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
</body>
</html>
