@php
    use App\Models\EliminationList;
    use Carbon\Carbon;
    $eliminationList = EliminationList::find($list_id);
@endphp

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Lista de Eliminação</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        div.container{
            /* width: 100% !important; */
            margin-top: 20px;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 0.6rem;
            /* width: 100vw; */
        }
        .name{
            font-size: 1.1rem;
        }
        .table th, .table td {
            width: 90vw !important;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .grey {
            background-color: #e6e5e5 !important;
        }
        .header, .footer {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td colspan="1">
                        @if($eliminationList->project->image_path)
                            <img src="{{ asset('storage/' . $eliminationList->project->image_path) }}" alt="{{ $eliminationList->project->name }}" class="img-fluid" width="70">
                        @else
                            <img src="https://static.vecteezy.com/system/resources/previews/005/337/799/original/icon-image-not-found-free-vector.jpg" alt="">
                        @endif
                    </td>

                    <td colspan="4" class="name">
                       <strong>{{ $eliminationList->project->name }}</strong> <br>
                        GESTÃO DA INFORMAÇÃO E DOCUMENTOS
                    </td>

                    <td colspan="3">
                       <strong>
                        @php                                 
                            $url = route('elimination_list', ['list_id' => $eliminationList->id]);
                        @endphp
                            {{QrCode::size(60)->generate($url)}}
                       </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <strong>LISTAGEM DE ELIMINAÇÃO DE DOCUMENTOS</strong>
                    </td>

                    <td colspan="4">
                        <strong>ORGÃO/SETOR:</strong> {{$eliminationList->organ}}
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <strong>UNIDADE/SETOR:</strong> {{$eliminationList->unit}}
                    </td>

                    <td colspan="4">
                        <strong>LISTAGEM Nª:</strong> {{$eliminationList->list_number}}
                    </td>
                </tr>

                <tr class="grey">
                    <th>CÓDIGO REFERENTE À CLASSIFICAÇÃO</th>
                    <th>DESCRITOR DO CÓDIGO</th>
                    <th>TIPO DE DOCUMENTO</th>
                    <th>DADOS DO DOCUMENTO</th>
                    <th>DATAS ABRANGÊNCIA</th>
                    <th>Nº CAIXA</th>
                    <th>ARMÁRIO</th>
                    <th>OBSERVAÇÃO</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($eliminationList->eliminations as $elimination)
                    <tr>
                        <td>{{ $elimination->temporality->code }}</td>
                        <td>{{ $elimination->temporality->area . ' ' . $elimination->temporality->function . ' ' . $elimination->temporality->sub_function }}</td>
                        <td>{{ $elimination->temporality->tipology }}</td>
                        <td>{{ $elimination->id .' - ' . $elimination->description }}</td>
                        <td>{{ Carbon::parse($elimination->initial_date)->format('Y') .' a ' . Carbon::parse($elimination->created_at)->format('Y') }}</td>
                        <td>{{ $elimination->box ?? '--------' }}</td>
                        <td>{{ $elimination->cabinet ?? '--------' }}</td>
                        <td>{{ $elimination->observations }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="3">
                        <div style="padding-top: 10px; ">
                            <hr>
                            RESPONSÁVEL PELA SELEÇÃO
                        </div>
                    </td>
                    <td colspan="2">
                        <div style="padding-top: 10px; ">
                            <hr>
                            RESPONSÁVEL PELA UNIDADE/SETOR
                        </div>
                    </td>
                    <td colspan="3"> 
                        <div style="padding-top: 10px; ">
                            <hr>
                            PRESIDENTE DA COMISSÃO PERMANENTE DE AVALIAÇÃO DE DOCUMENTOS - CPAD
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
