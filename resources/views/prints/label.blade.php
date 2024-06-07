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
        .label-content {
            display: flex;
            flex-wrap: wrap;
        }
        img {
            max-width: 50px;
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
        .barcode {
            display: block;
            margin: 0 auto;
            width: 100% !important;
            height: auto !important;
        }
    </style>
</head>
<body>
    <div class="label-content">
        @for($i=0; $i<8; $i++)
            <table class="table-container">
                <thead>
                    <tr>
                        <th colspan="2" class="table-header">
                            <div style="display: flex; justify-content: flex-start;">
                                <img src="{{ asset('assets/logoCaio.png') }}" alt="Logo da Empresa" style="padding: 5px; margin-left: 30px;">
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-left: 10px;">NOME DO TITULAR:</td>
                        <td>{{ mb_strtoupper($document->holder_name, 'UTF-8') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">NÚMERO DO DOCUMENTO:</td>
                        <td>{{ mb_strtoupper($document->doc_number, 'UTF-8') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">CÓDIGO DE CLASSIFICAÇÃO:</td>
                        <td>{{ mb_strtoupper($document->classification, 'UTF-8') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">ÁREA:</td>
                        <td>{{ mb_strtoupper($document->temporality->area, 'UTF-8') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">PRAZO DE GUARDA INTERMEDIÁRIA:</td>
                        <td>{{ mb_strtoupper($document->temporality->intermediate_custody_period, 'UTF-8') }} {{ $document->temporality->intermediate_custody_period > 1 ? 'ANOS' : 'ANO' }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">DESTINAÇÃO FINAL:</td>
                        <td>{{ mb_strtoupper($document->temporality->final_destination, 'UTF-8') }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">ANO DE ARQUIVAMENTO:</td>
                        <td>{{ Carbon::parse($document->archive_date)->year }}</td>
                    </tr>
                    @if($document->box)
                        <tr>
                            <td style="padding-left: 10px;">CAIXA:</td>
                            <td>{{ mb_strtoupper($document->box, 'UTF-8') }}</td>
                        </tr>   
                    @else
                        <tr>
                            <td style="padding-left: 10px;">ARMÁRIO:</td>
                            <td>{{ mb_strtoupper($document->cabinet, 'UTF-8') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2" class="text-center p-2">
                            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($document->id, 'C39+', 3, 100) }}" alt="Código de Barras" class="barcode">
                        </td>
                    </tr>
                </tbody>
            </table>
        @endfor
    </div>
</body>
</html>
