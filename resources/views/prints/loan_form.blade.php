@php
    use App\Models\DocumentCollection;
    use Carbon\Carbon;

    $documentCollection = DocumentCollection::find($id);
    $url = route('show.document_collection', ['id' => $documentCollection->id]);
    
    $documentLoans = $documentCollection->documentLoans;

    $project = $documentCollection->documentLoans[0]->document->project;

@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Empréstimo de Documentos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80% !important;
            margin: 0 auto;
            margin-left: 10px;
        }
        .container .table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        .table th, .table td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
        }
        .logo {
            margin: 0 auto;
            display: flex;
            align-content: center;
            justify-content: center;
        }
        .logo img {
            max-height: 90px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .number {
            text-align: right;
        }
        .data-section {
            margin-top: 20px;
        }
        .data-section p {
            margin: 5px 0;
        }
        .data-section .label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
        }
        .data-section .value {
            display: inline-block;
        }
        .footer {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    @for($i = 0; $i < 2; $i++)
        <div class="container">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2">
                            <div class="logo">
                                @if($project->image_path)
                                    <img src="{{ asset('storage/' . $project->image_path) }}" alt="{{ $project->name }}" class="img-fluid">
                                @else
                                    <img src="https://static.vecteezy.com/system/resources/previews/005/337/799/original/icon-image-not-found-free-vector.jpg" alt="">
                                @endif
                            </div>
                        </th>
                        <th colspan="7" class="title">FORMULÁRIO DE EMPRÉSTIMO DE DOCUMENTOS</th>
                        <th class="number">
                            <div class="d-flex">
                                CAD{{ $documentCollection->id }}/2024
                                {{QrCode::size(100)->generate($url)}}
                            </div>
                        </th>
                    </tr>                
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2"><strong>SETOR/UNIDADE:</strong></td>
                        <td colspan="8">{{$documentCollection->sector}}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>SOLICITANTE:</strong></td>
                        <td colspan="5">{{$documentCollection->loan_receiver}}</td>
                        <td colspan="1"><strong>TEL. / RAMAL:</strong></td>
                        <td colspan="2">{{$documentCollection->tel}}</td>
                    </tr>
                    <tr>
                        <td><strong>DATA DE ARQUIVAMENTO</strong></td>
                        <td><strong>CÓD. DE CLASSIFICAÇÃO</strong></td>
                        <td><strong>CAIXA Nº</strong></td>
                        <td><strong>ARMÁRIO</strong></td>
                        <td><strong>GAVETA</strong></td>
                        <td><strong>PASTA Nº</strong></td>
                        <td><strong>CLASSIFICAÇÃO DA INFORMAÇÃO</strong></td>
                        <td><strong>GÊNERO</strong></td>
                        <td colspan="3"><strong>DESCRIÇÃO DOS DOCUMENTOS</strong></td>
                    </tr>
                    @foreach ($documentLoans as $documentLoan)
                        <tr>                        
                            <td>{{Carbon::parse($documentLoan->document->archive_date)->format('d/m/Y')}}</td>
                            <td>{{$documentLoan->document->temporality->code}}</td>
                            <td>{{$documentLoan->document->box}}</td>
                            <td>{{$documentLoan->document->cabinet}}</td>
                            <td>{{$documentLoan->document->drawer}}</td>
                            <td>{{$documentLoan->document->qtpasta}}</td>
                            <td>{{$documentLoan->document->classification}}</td>
                            <td>{{$documentCollection->gender}}</td>
                            <td colspan="3">{{$documentLoan->document->id}} - {{$documentLoan->document->description}}</td>
                        </tr>                        
                    @endforeach
                    <tr>
                        <td colspan="1"><strong>OBSERVAÇÕES</strong></td>
                        <td colspan="9">{{$documentCollection->observations}}</td>
                    </tr>
                    <tr>
                        <td colspan="7"><strong>TOTAL</strong></td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="5"><strong>NOME/ASSINATURA DO SOLICITANTE: {{$documentCollection->loan_receiver}}</strong></td>
                        <td colspan="5"><strong>AUTOR DA DEVOLUÇÃO: </strong> {{$documentCollection->return_author}}</td>
                        
                    </tr>
                    <tr>
                        <td colspan="5"><strong>NOME DO RESPONSÁVEL PELO EMPRÉSTIMO: {{$documentCollection->loan_author}}</strong></td>
                        <td colspan="5"><strong>NOME DO RESPONSÁVEL PELA DECOLUÇÃO: {{$documentCollection->receiver_author}}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="5"><strong>DATA DO EMPRÉSTIMO: {{isset($documentCollection->loan_date) ? Carbon::parse($documentCollection->loan_date)->format('d/m/Y') : ''}}</strong></td>
                        <td colspan="5"><strong>DATA DA DEVOLUÇÃO: {{isset($documentCollection->return_date) ? Carbon::parse($documentCollection->return_date)->format('d/m/Y') : ''}}</strong></td>
                    </tr>
                </tbody>
            </table>
            <hr style="margin-top: 40px;">
            
        </div>
    @endfor
</body>
</html>
