@extends('layouts.app')

@section('content')
@vite(['resources/js/tags.js'])

@php
    use App\Models\EliminationList;
    use App\Models\EliminationListFile;

    $search = $_GET['search'] ?? null;
    $status = $_GET['status'] ?? null;
    $order_by = $_GET['order_by'] ?? 'id';
    $order_form = $_GET['order_form'] ?? 'asc';
    $list_id = $_GET['list_id'] ?? null;

    $organ_search = $_GET['organ_search'] ?? null;
    $unit_search = $_GET['unit_search'] ?? null;
    $responsible_selection_search = $_GET['responsible_selection_search'] ?? null;
    $responsible_unit_search = $_GET['responsible_unit_search'] ?? null;
    $president_search = $_GET['president_search'] ?? null;
    $date_from = $_GET['date_from'] ?? null;
    $date_to = $_GET['date_to'] ?? null;

    $auth = auth()->user();
    $eliminationLists = EliminationList::orderBy($order_by, $order_form);

    if($list_id){
        $eliminationLists->where('id', $list_id);
    }

    if ($search) {
        $eliminationLists->where(function($query) use ($search) {
            $query->where('organ', 'like', "%{$search}%")
                ->orWhere('unit', 'like', "%{$search}%")
                ->orWhere('responsible_selection', 'like', "%{$search}%")
                ->orWhere('responsible_unit', 'like', "%{$search}%")
                ->orWhere('president', 'like', "%{$search}%");
        });
    }

    if ($status) {
        $eliminationLists->where('status', $status);
    }

    if ($organ_search) {
        $eliminationLists->where('organ', 'like', "%{$organ_search}%");
    }

    if ($unit_search) {
        $eliminationLists->where('unit', 'like', "%{$unit_search}%");
    }

    if ($responsible_selection_search) {
        $eliminationLists->where('responsible_selection', 'like', "%{$responsible_selection_search}%");
    }

    if ($responsible_unit_search) {
        $eliminationLists->where('responsible_unit', 'like', "%{$responsible_unit_search}%");
    }

    if ($president_search) {
        $eliminationLists->where('president', 'like', "%{$president_search}%");
    }

    if($date_from && $date_to) {
        $eliminationLists->whereBetween('created_at', [$date_from, $date_to]);
    }else if($date_from){
        $eliminationLists->where('created_at', '>', $date_from);
    }else if($date_to){
        $eliminationLists->where('created_at', '<', $date_from);
    }

    $eliminationLists = $eliminationLists->paginate(15);
@endphp


@vite(['resources/sass/dashboard.scss'])

<div class="document-loading" id="document-loading" style="display:none;">
    <img src="https://cdn.pixabay.com/animation/2023/10/08/03/19/03-19-26-213_512.gif" alt="">
</div>

<div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4 d-flex justify-content-space-around" style="flex-wrap: wrap">
        Listas de Eliminação
    </h1>

    @if($auth->create_elimination)
        <a href="#" class="fs-1 c-green add" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
            <i class="fa-solid fa-circle-plus"></i>
        </a>
    @endif

    <form action="" class="row mb-3">
        <div class="col-md-12 mt-2">
            <label for="search">Buscar geral</label>
            <input value="{{$search}}" type="text" id="search" name="search" class="form-control">
        </div>
        <div class="col-md-2 mt-2">
            <label for="organ_search">Órgão</label>
            <input value="{{ request('organ_search') }}" type="text" id="organ_search" name="organ_search" class="form-control">
        </div>
        <div class="col-md-2 mt-2">
            <label for="unit_search">Unidade</label>
            <input value="{{ request('unit_search') }}" type="text" id="unit_search" name="unit_search" class="form-control">
        </div>
        <div class="col-md-2 mt-2">
            <label for="responsible_selection_search">Responsável pela Seleção</label>
            <input value="{{ request('responsible_selection_search') }}" type="text" id="responsible_selection_search" name="responsible_selection_search" class="form-control">
        </div>
        <div class="col-md-2 mt-2">
            <label for="responsible_unit_search">Responsável pela Unidade</label>
            <input value="{{ request('responsible_unit_search') }}" type="text" id="responsible_unit_search" name="responsible_unit_search" class="form-control">
        </div>
        <div class="col-md-2 mt-2">
            <label for="president_search">Presidente</label>
            <input value="{{ request('president_search') }}" type="text" id="president_search" name="president_search" class="form-control">
        </div>
        <div class="col-md-2 mt-2">
            <label for="status">Status</label>
            <select class="form-control" name="status" id="status">
                <option value="">Selecione uma opção</option>
                <option value="em_construcao" {{ $status == 'em_construcao' ? 'selected' : '' }}>Em Construção</option>
                <option value="em_avaliacao" {{ $status == 'em_avaliacao' ? 'selected' : '' }}>Em Avaliação</option>
                <option value="concluida" {{ $status == 'concluida' ? 'selected' : '' }}>Concluída</option>
            </select>
        </div>
        <div class="col-md-2 mt-2">
            <label for="date_from">Data de inicio</label>
            <input value="{{ request('date_from') }}" type="date" id="date_from" name="date_from" class="form-control">
        </div>
        <div class="col-md-2 mt-2">
            <label for="date_to">Data de fim</label>
            <input value="{{ request('date_to') }}" type="date" id="date_to" name="date_to" class="form-control">
        </div>
        <div class="col-md-2 mt-2">
            <label for="order_by">Ordenar por</label>
            <select class="form-control" name="order_by" id="order_by">
                <option value="id" {{ $order_by == 'id' ? 'selected' : '' }}>ID</option>
                <option value="organ" {{ $order_by == 'organ' ? 'selected' : '' }}>Órgão</option>
                <option value="unit" {{ $order_by == 'unit' ? 'selected' : '' }}>Unidade</option>
                <option value="responsible_selection" {{ $order_by == 'responsible_selection' ? 'selected' : '' }}>Responsável pela Seleção</option>
                <option value="responsible_unit" {{ $order_by == 'responsible_unit' ? 'selected' : '' }}>Responsável pela Unidade</option>
                <option value="president" {{ $order_by == 'president' ? 'selected' : '' }}>Presidente</option>
                <option value="status" {{ $order_by == 'status' ? 'selected' : '' }}>Status</option>
            </select>
        </div>
        <div class="col-md-2 mt-2">
            <label for="order_form">Forma de ordenação</label>
            <select class="form-control" name="order_form" id="order_form">
                <option value="asc" {{ $order_form == 'asc' ? 'selected' : '' }}>Ascendente</option>
                <option value="desc" {{ $order_form == 'desc' ? 'selected' : '' }}>Descendente</option>
            </select>
        </div>
        <div class="col-md-2 mt-2">
            <input type="submit" class="btn btn-primary mt-4" value="Aplicar">
        </div>
    </form>
    
    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Número</th>
                    <th scope="col">Órgão</th>
                    <th scope="col">Unidade</th>
                    <th scope="col">Responsável pela Seleção</th>
                    <th scope="col">Responsável pela Unidade</th>
                    <th scope="col">Presidente</th>
                    <th scope="col">Status</th>
                    <th scope="col">Observações</th>
                    <th scope="col" class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($eliminationLists as $list)
                    <tr style="cursor: pointer;">
                        <th scope="row">{{ $list->id }}</th>
                        <td class="text-center">{{ $list->list_number }}</td>
                        <td class="text-center">{{ $list->organ }}</td>
                        <td class="text-center">{{ $list->unit }}</td>
                        <td class="text-center">{{ $list->responsible_selection }}</td>
                        <td class="text-center">{{ $list->responsible_unit }}</td>
                        <td class="text-center">{{ $list->president }}</td>
                        <td class="text-center">
                            {{
                                $list->status == 'em_construcao' ? '🔴' : (
                                    $list->status == 'em_avaliacao' ? '🔵' :  (
                                        $list->status == 'concluida' ? '🟢' : '-----------'
                                    )
                                )
                            }}
                            {{ ucfirst(str_replace('_', ' ', $list->status)) }}
                        </td>
                        <td class="text-center">{{ $list->observations }}</td>
                        <td class="text-center fs-4">
                            <a href="#" class="edit-document" style="color: rgb(50, 127, 243) !important;" data-info="1" data-id="{{ $list->id }}">
                                <i class="fa-solid fa-circle-info"></i>
                            </a>
                            <a href="#" class="edit-document ms-2 me-2" style="color: rgb(25, 85, 175) !important;" data-copy="1" data-id="{{ $list->id }}">
                                <i class="fa-solid fa-copy"></i>
                            </a>
                            @if($auth->edit_elimination)
                                <a href="#" class="edit-document ms-2 me-2" style="color: rgb(0, 0, 0) !important;" data-id="{{ $list->id }}">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="#" class="me-2 c-green" data-bs-toggle="collapse" data-bs-target="#collapse{{ $list->id }}" aria-expanded="false" aria-controls="collapse{{ $list->id }}">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
    
                                <a href="#" class="print-link" data-list="{{$list->id}}">
                                    <i class="fa-solid fa-print me-2"></i>
                                </a>                            
                                
                                <a href="#" class="c-blue view-files fs-4" data-id="{{ $list->id }}">
                                    <i class="fa-solid fa-folder-open"></i>
                                </a>
                            @else
                                -----
                            @endif
                            @if($auth->delete_elimination)
                                <a href="{{route('delete.elimination-list', ['id' => $list->id])}}" class="delete-document"><i class="fa-solid fa-trash ms-3"></i></a>
                            @endif
                        </td>
                    </tr>
                    <tr class="collapse" id="collapse{{ $list->id }}" style="padding: 5px !important;">
                        <td colspan="10" style="padding: 5px !important;">
                            <div class="d-flex flex-wrap" style="width: 100%;">
                                <div class="row col-12">
                                    <div class="col-md-1"><strong>ID</strong></div>
                                    <div class="col-md-1"><strong>Código</strong></div>
                                    <div class="col-md-1"><strong>Número</strong></div>
                                    <div class="col-md-1"><strong>Titular</strong></div>
                                    <div class="col-md-1"><strong>Descrição</strong></div>
                                    <div class="col-md-1"><strong>Caixa</strong></div>
                                    <div class="col-md-1"><strong>Armário</strong></div>
                                    <div class="col-md-1"><strong>Gavetas</strong></div>
                                    <div class="col-md-1"><strong>qtd. Pastas</strong></div>
                                    <div class="col-md-1"><strong>Sit. A.C</strong></div>
                                    <div class="col-md-2"><strong>Sit. A.I</strong></div>
                                </div>
                                @foreach ($list->eliminations as $elimination)
                                    <div class="d-flex justify-content-between col-12 mb-2" style="border-top: 1px solid rgb(226, 226, 226);">
                                        <div class="col-md-1">#{{ $elimination->id }}</div>
                                        <div class="col-md-1">{{ $elimination->temporality->code }}</div>
                                        <div class="col-md-1">{{ $elimination->doc_number }}</div>
                                        <div class="col-md-1">{{ $elimination->holder_name }}</div>
                                        <div class="col-md-1">{{ $elimination->description }}</div>
                                        <div class="col-md-1">{{ $elimination->box  ?? '-------'}}</div>
                                        <div class="col-md-1">{{ $elimination->cabinet ?? '-------' }}</div>
                                        <div class="col-md-1">{{ $elimination->drawer ?? '-------' }}</div>
                                        <div class="col-md-1">{{ $elimination->qtpasta ?? '-------' }}</div>
                                        <div class="col-md-1">{{ $elimination->situationAC }}</div>
                                        <div class="col-md-2">{{ $elimination->situationAI }}</div>
                                    </div>
                                    
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            
        </table>
        <div class="d-flex justify-content-center">            
            {{ count($eliminationLists) ? $eliminationLists->appends(request()->query())->links() : '' }}
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentModalLabel">Adicionar Lista de Eliminação</h5>
            </div>
            <form action="{{ route('create.elimination-list') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="list_number" class="form-label">Nª da lista</label>
                            <input type="text" class="form-control" id="list_number" name="list_number">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="organ" class="form-label">Órgão/Setor</label>
                            <input type="text" class="form-control" id="organ" name="organ">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="unit" class="form-label">Unidade/Setor</label>
                            <input type="text" class="form-control" id="unit" name="unit">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="responsible_selection" class="form-label">Responsável pela Seleção</label>
                            <input type="text" class="form-control" id="responsible_selection" name="responsible_selection">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="responsible_unit" class="form-label">Responsável pela Unidade/Setor</label>
                            <input type="text" class="form-control" id="responsible_unit" name="responsible_unit">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="president" class="form-label">Presidente da comissão</label>
                            <input type="text" class="form-control" id="president" name="president">
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Selecione uma opção</option>
                                <option value="em_construcao">🔴 Em Construção</option>
                                <option value="em_avaliacao">🔵 Em Avaliação</option>
                                <option value="concluida">🟢 Concluída</option>
                            </select>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="observations" class="form-label">Observações</label>
                            <textarea class="form-control" id="observations" name="observations"></textarea>
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="file" class="form-label">Arquivos</label>
                            <input type="file" class="form-control" id="file" name="files[]" multiple>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="submit-button" type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para exibir arquivos do documento -->
<div class="modal fade" id="viewFilesModal" tabindex="-1" aria-labelledby="viewFilesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewFilesModalLabel">Arquivos da Lista de Eliminação</h5>
            </div>
            <div class="modal-body">
                <ul id="fileList" class="list-group">
                    <!-- Arquivos serão listados aqui -->
                </ul>
            </div>
            <div class="modal-footer">
                <a href="#" id="downloadAllFiles" class="btn btn-primary">Baixar Todos</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const printLinks = document.querySelectorAll('.print-link');
        printLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const listId = this.getAttribute('data-list');
                const url = `/print/elimination_list/${listId}`;
                window.open(url, '_blank', 'width=1500,height=600');
            });
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var viewButtons = document.querySelectorAll('.view-files');
    var fileList = document.getElementById('fileList');
    var viewFilesModal = new bootstrap.Modal(document.getElementById('viewFilesModal'));
    var downloadAllFilesButton = document.getElementById('downloadAllFiles');

    viewButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var eliminationListId = button.getAttribute('data-id');
            fetch(`/elimination-lists/${eliminationListId}/files`)
                .then(response => response.json())
                .then(data => {
                    fileList.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(file => {
                            var listItem = document.createElement('li');
                            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                            listItem.innerHTML = `
                                ${new Date(file.created_at).toLocaleDateString('pt-BR')} - ${file.name}
                                <div>
                                    <a href="${file.path}" target="_blank" class="btn btn-sm btn-primary">Abrir</a>
                                    <button class="btn btn-sm btn-danger delete-file" data-file-id="${file.id}">Apagar</button>
                                </div>
                            `;
                            fileList.appendChild(listItem);
                        });
                    } else {
                        fileList.innerHTML = '<li class="list-group-item">Nenhum arquivo encontrado</li>';
                    }
                    downloadAllFilesButton.href = `/elimination-lists/${eliminationListId}/download-all`;
                    viewFilesModal.show();

                    var deleteFileButtons = document.querySelectorAll('.delete-file');
                    deleteFileButtons.forEach(function (deleteButton) {
                        deleteButton.addEventListener('click', function () {
                            var fileId = this.getAttribute('data-file-id');
                            if (confirm('Você tem certeza que deseja apagar este arquivo?')) {
                                fetch(`/elimination-lists/files/${fileId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                })
                                .then(response => {
                                    if (response.ok) {
                                        this.closest('li').remove();
                                    } else {
                                        alert('Erro ao apagar o arquivo.');
                                    }
                                })
                                .catch(error => console.error('Erro:', error));
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('Erro ao carregar arquivos:', error);
                    fileList.innerHTML = '<li class="list-group-item">Erro ao carregar arquivos</li>';
                    viewFilesModal.show();
                });
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var editButtons = document.querySelectorAll('.edit-document');
    var addDocumentButton = document.querySelector('.add');
    var modal = document.getElementById('addDocumentModal');
    var modalForm = modal.querySelector('form');

    addDocumentButton.addEventListener('click', function () {
        modalForm.reset();
        const token = modalForm.querySelectorAll('input[name="_token"]')[0].value;
        modalForm.querySelectorAll('input[name="id"]')[0].value = '';
        modalForm.querySelectorAll('select').forEach(function(select) {
            select.value = '';
        });
        modalForm.querySelectorAll('textarea').forEach(function(textarea) {
            textarea.value = '';
        });
        modalForm.querySelectorAll('input[name="_token"]')[0].value = token;
    });

    editButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var id = button.getAttribute('data-id');
            var info = button.getAttribute('data-info');
            var copy = button.getAttribute('data-copy');
            
            if(info){
                modalForm.querySelector('.modal-footer button').style.display = 'none';
            }else{
                modalForm.querySelector('.modal-footer button').style.display = 'block';
            }

            fetch(`/elimination-lists/${id}`)
                .then(response => response.json())
                .then(data => {
                    if(!copy){
                        modalForm.querySelector('[name="id"]').value = data.id;
                    }
                    modalForm.querySelector('[name="organ"]').value = data.organ;
                    modalForm.querySelector('[name="unit"]').value = data.unit;
                    modalForm.querySelector('[name="responsible_selection"]').value = data.responsible_selection;
                    modalForm.querySelector('[name="responsible_unit"]').value = data.responsible_unit;
                    modalForm.querySelector('[name="president"]').value = data.president;
                    modalForm.querySelector('[name="status"]').value = data.status;
                    modalForm.querySelector('[name="observations"]').value = data.observations;

                    var modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                });
        });
    });

    $('.delete-document').on('click', function(e) {
        e.preventDefault();
        var deleteUrl = $(this).attr('href');

        Swal.fire({
            title: 'Você tem certeza?',
            text: "Você não poderá reverter isso!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, exclua isso!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteUrl;
            }
        });
    });
});

document.getElementById('submit-button').addEventListener('click', () => {
    document.getElementById('document-loading').style.display = 'flex';
});
</script>

@endsection
