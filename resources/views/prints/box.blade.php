@php
    use App\Models\Project;
    $project_id = explode(',',$_GET[0])[10];

    $project = Project::find($project_id);

@endphp
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template</title>
    <style>
        .box-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
        }
        .template-container {
            margin-left: 20px;
            width: 300px;
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
            margin-top:  10px;
            max-width: 210px;
            height: 400px;
            margin-left: 20px;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid black;
            padding: 0 !important;
            text-align: center;
            font-size: 0.8rem;
        }
        .value{
            font-weight: 600;
        }
        img{
            width: 60px;
        }
    </style>
</head>
<body>
    <a href="{{route('documents', ['project_id' => $project->id])}}" style="width:40px; display:block; margin: 20px; text-decoration: none; border: none; background: rgb(47, 139, 226); border-radius: 20%; padding: 10px; color:white;">Voltar</a>
    <div class="box-container">
        @foreach ($_GET as $item)
            @php
                $item = explode(',', $item);
            @endphp
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
                    
                    <td>
                        código de classificação:
                        <div class="classification-code">
                            <div class="value">{{$item[2]}}</div>
                        </div>
                    </td>
                    <td>                        
                        <div class="qr-code">
                            @php                                 
                                $url = route('documents', ['project_id' => $project->id, 'box_search' =>$item[9]])
                            @endphp
                            {{QrCode::size(60)->generate($url)}}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        Area: <br>
                        <div class="value">{{$item[3]}}</div>
                    </td>
                    <td>
                        Prazo de guarda fase intermediária: <br>
                        <div class="value">{{$item[4]}}</div>
                    </td>
                </tr>            
                <tr>
                    <td colspan="2">
                        <div class="value">{{$item[5]}}</div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <div class="value">{{$item[6]}}</div>
                    </td>
                    <td>
                        <div class="value">{{$item[7]}}</div>
                    </td>
                </tr>
                <tr>
                    <td  class="title">
                        Destinação Final:<br>
                        <div class="value">{{$item[8]}}</div>
                    </td>
                    <td class="highlight">
                        <div class="value">{{$item[9]}}</div>
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

