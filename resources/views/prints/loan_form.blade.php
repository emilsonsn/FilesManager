@php
    use App\Models\DocumentCollection;
    use Carbon\Carbon;

    $documentCollection = DocumentCollection::find($id);
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
            width: 80%;
            margin: 0 auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
            text-align: center;
            vertical-align: middle;
        }
        .logo img {
            max-width: 100px;
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
    <div class="container">
        <table class="table">
            <thead>
                <tr>
                    <th colspan="2" class="logo"><img src="{{asset('assets/logoCaio.png')}}"alt="Logo da Empresa"></th>
                    <th colspan="7" class="title">FORMULÁRIO DE EMPRÉSTIMO DE DOCUMENTOS</th>
                    <th class="number">Nº {{$documentCollection->id}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2"><strong>SETOR/UNIDADE:</strong></td>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>SOLICITANTE:</strong></td>
                    <td colspan="6">{{$documentCollection->loan_receiver}}</td>
                    <td colspan="2"><strong>TEL. / RAMAL:</strong></td>
                </tr>
                <tr>
                    <td><strong>DATA DE ARQUIVAMENTO</strong></td>
                    <td><strong>CÓD. DE CLASSIFICAÇÃO</strong></td>
                    @if($documentCollection->document->box)
                        <td><strong>CAIXA Nº</strong></td>
                        <td><strong>PASTA Nº</strong></td>
                    @else
                        <td><strong>ARMÁRIO</strong></td>
                        <td><strong>GAVETA</strong></td>
                    @endif
                    <td><strong>CLASSIFICAÇÃO DA INFORMAÇÃO</strong></td>
                    <td><strong>VERSÃO DOC</strong></td>
                    <td><strong>GÊNERO</strong></td>
                    <td colspan="3"><strong>DESCRIÇÃO DOS DOCUMENTOS</strong></td>
                </tr>
                <tr>
                    <td>{{Carbon::parse($documentCollection->document->archive_date)->format('d/m/Y')}}</td>
                    <td>{{$documentCollection->document->classification}}</td>
                    @if($documentCollection->document->box)
                        <td>{{$documentCollection->document->box}}</td>
                        <td>{{$documentCollection->document->qtpasta}}</td>
                    @else
                        <td>{{$documentCollection->document->cabinet}}</td>
                        <td>{{$documentCollection->document->drawer}}</td>
                    @endif
                    <td>{{$documentCollection->document->classification}}</td>
                    <td>{{$documentCollection->document->version}}</td>
                    <td>{{$documentCollection->gender}}</td>
                    <td colspan="3">{{$documentCollection->document->description}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="7"><strong>TOTAL</strong></td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="5"><strong>NOME/ASSINATURA DO SOLICITANTE: {{$documentCollection->loan_receiver}}</strong></td>
                    <td colspan="5"><strong>DEVOLUÇÃO</strong></td>
                </tr>
                <tr>
                    <td colspan="5"><strong>NOME DO RESPONSÁVEL PELO EMPRÉSTIMO: {{$documentCollection->loan_author}}</strong></td>
                    <td colspan="5"><strong>NOME DO RESPONSÁVEL PELO RECEBIMENTO:</strong></td>
                </tr>
                <tr>
                    <td colspan="5"><strong>DATA DO EMPRÉSTIMO: {{Carbon::parse($documentCollection->loan_date)->format('d/m/Y')}}</strong></td>
                    <td colspan="5"><strong>DATA DA DEVOLUÇÃO:</strong></td>
                </tr>
            </tbody>
        </table>
        <hr style="margin-top: 40px;">
        
    </div>
</body>
</html>
