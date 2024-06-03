@extends('layouts.app')

@section('content')
  @php
    use App\Models\Document;
    use App\Models\Project;
    use Carbon\Carbon;

    $auth = auth()->user();

    if($auth->is_admin) $projectIds = Project::get()->pluck('id');
    else $projectIds = $auth->projects()->pluck('project_id');

    $documents = Document::where(function($query) {
        $query->where('expiration_date_A_C', '>=', Carbon::now())
              ->where('expiration_date_A_C', '<=', Carbon::now()->addMonths(3))
              ->orWhere(function($query) {
                  $query->where('expiration_date_A_I', '>=', Carbon::now())
                        ->where('expiration_date_A_I', '<=', Carbon::now()->addMonths(3));
              });
      })->whereIn('project_id', $projectIds)->get();
  @endphp
    
  @vite(['resources/sass/dashboard.scss'])
  <div class="col-md-10 mt-4 content w-100 h-100">
    <h1 class="pt-4">Alertas de expiração</h1>

    <div class="table-container">
      <table class="table table-striped">
        <thead>
          <tr>
            <th class="text-center" scope="col">ID</th>
            <th class="text-center" scope="col">Nª do documento</th>
            <th class="text-center" scope="col">Data de expiração A.C</th>
            <th class="text-center" scope="col">Data de expiração A.I</th>
            <th class="text-center" scope="col">Dias para vencer</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($documents as $document)
            @php
              $daysToExpireAC = Carbon::now()->diffInDays(Carbon::parse($document->expiration_date_A_C), false);
              $daysToExpireAI = Carbon::now()->diffInDays(Carbon::parse($document->expiration_date_A_I), false);
            @endphp

            @if ($daysToExpireAC > 0 && $daysToExpireAC <= 90)
              <tr>
                <td class="text-center" scope="row">{{ $document->id }}</td>
                <td class="text-center">{{ $document->doc_number }}</td>
                <td class="text-center">{{ Carbon::parse($document->expiration_date_A_C)->format('d/m/Y') }}</td>
                <td class="text-center">{{ Carbon::parse($document->expiration_date_A_I)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $daysToExpireAC }} dias para vencer A.C</td>
              </tr>
            @elseif ($daysToExpireAI > 0 && $daysToExpireAI <= 90)
              <tr>
                <td class="text-center" scope="row">{{ $document->id }}</td>
                <td class="text-center">{{ $document->doc_number }}</td>
                <td class="text-center">{{ Carbon::parse($document->expiration_date_A_C)->format('d/m/Y') }}</td>
                <td class="text-center">{{ Carbon::parse($document->expiration_date_A_I)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $daysToExpireAI }} dias para vencer A.I</td>
              </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

@endsection
