
@php
    use App\Models\Document;
    use Carbon\Carbon;
    $document = Document::find($id);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reprodução da Tabela</title>
    <style>
        .label-content{
            display: flex;
            flex-wrap: wrap;
        }
        .table-container {
            width: 40%;
            font-size: 0.7rem;
            margin: 10px auto 0 auto;
            border-collapse: collapse;
            text-align: left;
            font-family: Arial, sans-serif;
        }
        .table-container th, .table-container td {
            border: 1px solid black;
            padding: 1px;
        }
        .table-header {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body >
    <div class="label-content">
        @for($i=0; $i<8; $i++)
            <table class="table-container">
                <thead>
                    <tr>
                        <th colspan="2" class="table-header">VisioDoc</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-left: 10px;">UNIDADE:</td>
                        <td>USO</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">SEÇÃO:</td>
                        <td>LICITAÇÃO</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">CÓDIGO DE CLASSIFICAÇÃO:</td>
                        <td>{{$document->temporality->code}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">ATIVIDADE:</td>
                        <td>{{$document->temporality->activity}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">PRAZO DE GUARDA INTERMEDIÁRIA:</td>
                        <td>{{$document->temporality->intermediate_custody_period}} ANOS</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">DESTINAÇÃO FINAL:</td>
                        <td>{{$document->temporality->final_destination}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">ANO DE ARQUIVAMENTO:</td>
                        <td>{{Carbon::parse($document->archive_date)->format('d/m/Y')}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">CAIXA:</td>
                        <td>{{$document->box}}</td>
                    </tr>
                </tbody>
            </table>
        @endfor
    </div>
</body>
</html>
