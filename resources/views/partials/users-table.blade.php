<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nome</th>
      <th>Email</th>
      <th>Projetos</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($list as $user)
      <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>
          @foreach ($user->projects as $project)
            {{ $project->project->name }},
          @endforeach
        </td>
        <td>
          <a href="#" class="me-2 projects" data-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#projectsModal">
            <i class="fa-solid fa-house-chimney-medical"></i>
          </a>

          <a href="#" class="edit-user"
            data-id="{{ $user->id }}"
            data-name="{{ $user->name }}"
            data-email="{{ $user->email }}"
            data-upload_limit="{{ $user->upload_limit }}"
            data-active="{{ $user->is_active }}"
            data-create_projects="{{ $user->create_projects }}"
            data-read_doc="{{ $user->read_doc }}"
            data-create_doc="{{ $user->create_doc }}"
            data-edit_doc="{{ $user->edit_doc }}"
            data-delete_doc="{{ $user->delete_doc }}"
            data-read_temporality="{{ $user->read_temporality }}"
            data-create_temporality="{{ $user->create_temporality }}"
            data-edit_temporality="{{ $user->edit_temporality }}"
            data-delete_temporality="{{ $user->delete_temporality }}"
            data-read_collection="{{ $user->read_collection }}"
            data-create_collection="{{ $user->create_collection }}"
            data-edit_collection="{{ $user->edit_collection }}"
            data-delete_collection="{{ $user->delete_collection }}"
            data-read_elimination="{{ $user->read_elimination }}"
            data-create_elimination="{{ $user->create_elimination }}"
            data-edit_elimination="{{ $user->edit_elimination }}"
            data-delete_elimination="{{ $user->delete_elimination }}"
            data-print_generate="{{ $user->print_generate }}"
          >
            <i class="fa-solid fa-pen"></i>
          </a>

          <a href="{{ route('delete.user', ['id' => $user->id]) }}" class="delete-user">
            <i class="fa-solid fa-trash ms-3"></i>
          </a>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
