@php
    use App\Models\Project;
    $data = json_decode($_GET['data']);
    $project_id = $data[0][10];
    
    $project = Project::find($project_id);

@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .box-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin: 0 auto;
        }
        .template-container {
            margin-left: 20px;
            width: 280px;
            border: 1px solid black;
            padding: 10px;
            font-family: Arial, sans-serif;
            line-height: 1.2;
        }
        .template-header,
        .template-section,
        .template-footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .template-header .title,
        .template-footer .title {
            font-size: 14px;
            font-weight: bold;
        }
        .template-section {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
            padding: 5px 0;
        }
        .template-section .classification-code {
            font-size: 24px;
            font-weight: bold;
        }
        .template-section .qr-code {
            display: inline-block;
            margin-left: 10px;
        }
        .template-section .info-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }
        .template-section .info-row .label {
            font-weight: bold;
        }
        .template-section .highlight {
            font-size: 20px;
            font-weight: bold;
            color: red;
        }
        table {
            margin-bottom: 40px;
            min-width: 345px;
            height: 480px;
            border-collapse: collapse;
            border: 1px solid rgb(223, 223, 223);
        }

        td{
            border: 1.5px solid black !important;
        }
        td, th {
            border: 1px solid black;
            padding: 0 !important;
            text-align: center;
            border: none;
            font-size: 0.7rem;
        }
        .value{
            font-size: 0.9rem;
            font-weight: 600;
        }
        .code{
            font-size: 2rem;
        }
        .box{
            color: red;
            font-size: 3rem;
        }
        img{
            width: 60px;
        }
    </style>
</head>
<body>
    <div class="box-container">
        @foreach ($data as $item)
            <table>
                <tr>
                    <td>
                        @if($project->image_path)
                            <img src="{{ asset('storage/' . $project->image_path) }}" alt="{{ $project->name }}" class="img-fluid" width="70">
                        @else
                            <img src="https://static.vecteezy.com/system/resources/previews/005/337/799/original/icon-image-not-found-free-vector.jpg" alt="">
                        @endif
                    </td>

                    <td>
                        {{$project->name}} <br>
                        <div class="value">{{$item[0]}}</div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <div class="value">{{$item[1]}}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="display: flex; justify-content:center; align-items:center; width:100%; margin: 0 auto;">
                            <div>
                                Código de classificação:
                                <div class="classification-code">
                                    <div class="value code">{{$item[2]}}</div>
                                </div>                    
                            </div>
                            <div class="qr-code" style="margin-left: 20px">
                                @php                                 
                                    $url = route('documents', ['project_id' => $project->id, 'box_search' => $item[9]]);
                                @endphp
                                {{QrCode::size(60)->generate($url)}}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Área:
                        <div class="value">{{$item[3]}}</div>
                    </td>
                    <td>
                        Prazo de guarda intermediária:
                        <div class="value">{{$item[4]}}</div>
                    </td>
                </tr>            
                <tr>
                    <td colspan="2">
                        <div class="value code">{{$item[5]}}</div>
                    </td>
                </tr>

                <tr>
                    <td>
                            Ano de arquivamento:
                        <div class="value">{{$item[6]}}</div>
                    </td>
                    <td>
                            Localização:
                        <div class="value">{{$item[7]}}</div>
                    </td>
                </tr>
                <tr>
                    <td  class="title">
                            Destinação Final:
                        <div class="value">{{$item[8]}}</div>
                    </td>
                    <td class="highlight">
                            Caixa:
                        <div class="value code box">{{$item[9]}}</div>
                    </td>
                </tr>
            </table>        
        @endforeach
    </div>
</body>
</html>

<script>
    window.onload = function() {
        window.print();
    }
</script>

