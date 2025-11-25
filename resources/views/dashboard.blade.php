@extends('layouts.app')

@section('content')
<div class="col-md-10 content mt-4 content w-100 h-100">
    <h1 class="pt-4">Dashboard — {{ $project->name }}</h1>

    <div class="row mt-4">
        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center">
                <h4>Total de Documentos</h4>
                <h2>{{ $documentsCount }}</h2>
            </div>
        </div>
        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center">
                <h4>Arquivos Anexados</h4>
                <h2>{{ $filesCount }}</h2>
            </div>
        </div>
        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center">
                <h4>Doc. Vencidos</h4>
                <h2 class="text-danger">{{ $expiredDocuments }}</h2>
            </div>
        </div>
        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center ">
                <h4>Doc. Próx. Vencimento</h4>
                <h2 class="text-warning">{{ $nearExpiration }}</h2>
            </div>
        </div>

        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center">
                <h4>Doc. Ativos</h4>
                <h2>{{ $activeCount }}</h2>
            </div>
        </div>

        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center">
                <h4>Doc. Arquivados</h4>
                <h2>{{ $archivedCount }}</h2>
            </div>
        </div>

        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center">
                <h4>Doc. Emprestados</h4>
                <h2>{{ $loaned }}</h2>
            </div>
        </div>

        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center">
                <h4>Doc. Devolvidos</h4>
                <h2>{{ $returned }}</h2>
            </div>
        </div>

        <div class="col-md-3 mt-4">
            <div class="card p-3 text-center">
                <h4>Usuários no Projeto</h4>
                <h2>{{ $totalUsers }}</h2>
            </div>
        </div>


    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card p-3">
                <h5>Documentos que vencem hoje</h5>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Titular</th>
                            <th>Descrição</th>
                            <th>Vencimento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documentsToday as $doc)
                        <tr>
                            <td>{{ $doc->doc_number }}</td>
                            <td>{{ $doc->holder_name }}</td>

                            <td>{{ $doc->description }}</td>

                            <td>
                                {{ $doc->expiration_date_A_C ?? $doc->expiration_date_A_I }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Cadastro de documentos</h5>
                <canvas id="documentsByMonth"></canvas>
            </div>
        </div>
        <div class="col-md-6" style="max-height: 200px">
            <div class="card p-3">
                <h5>Classificação</h5>
                <canvas id="documentsByClassification"></canvas>
            </div>
        </div>

    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Documentos por Caixa</h5>
                <canvas id="documentsByBox"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
var ctxMonth = document.getElementById('documentsByMonth').getContext('2d')
new Chart(ctxMonth, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($documentsByMonth)) !!},
        datasets: [{
            label: 'Documentos',
            data: {!! json_encode(array_values($documentsByMonth)) !!},
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.2)',
            tension: 0.3
        }]
    }
})

var ctxClass = document.getElementById('documentsByClassification').getContext('2d')
new Chart(ctxClass, {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_keys($documentsByClassification)) !!},
        datasets: [{
            data: {!! json_encode(array_values($documentsByClassification)) !!},
            backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6f42c1','#17a2b8']
        }]
    }
})

var ctxBox = document.getElementById('documentsByBox').getContext('2d')
new Chart(ctxBox, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($documentsByBox)) !!},
        datasets: [{
            label: 'Documentos',
            data: {!! json_encode(array_values($documentsByBox)) !!},
            backgroundColor: '#17a2b8'
        }]
    }
})
</script>
@endsection
