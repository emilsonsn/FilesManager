<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\ClientService\ClientService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{

    public function register(){
        return view('register');
    }

    public function dashboard(Request $request){
        $projectId = $request->session()->get('project_id');

        if (!$projectId) {
            abort(403, 'Projeto não selecionado');
        }

        $project = Project::findOrFail($projectId);

        $documents = Document::where('project_id', $project->id)->get();

        $documentsCount = $documents->count();

        $expiredDocuments = $documents->filter(function ($doc) {
            return $doc->expiration_date_A_C < now() || $doc->expiration_date_A_I < now();
        })->count();

        $nearExpiration = $documents->filter(function ($doc) {
            return ($doc->expiration_date_A_C >= now() && $doc->expiration_date_A_C <= now()->addDays(30))
                || ($doc->expiration_date_A_I >= now() && $doc->expiration_date_A_I <= now()->addDays(30));
        })->count();

        $filesCount = DocumentFile::whereIn('document_id', $documents->pluck('id'))->count();

        $documentsByMonth = Document::where('project_id', $project->id)
            ->selectRaw('MONTH(created_at) as m, COUNT(*) as total')
            ->groupBy('m')
            ->orderBy('m')
            ->get()
            ->mapWithKeys(function ($row) {
                return [date('M', mktime(0, 0, 0, $row->m, 1)) => $row->total];
            })
            ->toArray();

        $documentsByClassification = $documents
            ->groupBy('classification')
            ->map(function ($g) {
                return $g->count();
            })
            ->toArray();

        $documentsByBox = $documents
            ->groupBy('box')
            ->map(function ($g) {
                return $g->count();
            })
            ->toArray();

        $documentsToday = $documents->filter(function ($doc) {
            return ($doc->expiration_date_A_C == now()->toDateString()) ||
                ($doc->expiration_date_A_I == now()->toDateString());
        });

        $archivedCount = $documents->where('archive_date', '!=', null)->count();
        $activeCount = $documents->where('archive_date', null)->count();

        $collections = \App\Models\DocumentCollection::whereHas('documentLoans.document', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })->get();

        $loaned = $collections->filter(function ($c) {
            return $c->return_date == null;
        })->flatMap(function ($c) {
            return $c->documentLoans->pluck('document_id');
        })->unique()->count();

        $returned = $collections->filter(function ($c) {
            return $c->return_date != null;
        })->flatMap(function ($c) {
            return $c->documentLoans->pluck('document_id');
        })->unique()->count();

        $totalUsers = $project->users()->count();
        
        return view('dashboard', [
            'project' => $project,
            'documentsCount' => $documentsCount,
            'expiredDocuments' => $expiredDocuments,
            'nearExpiration' => $nearExpiration,
            'filesCount' => $filesCount,
            'documentsByMonth' => $documentsByMonth,
            'documentsByClassification' => $documentsByClassification,
            'documentsByBox' => $documentsByBox,
            'documentsToday' => $documentsToday,
            'archivedCount' => $archivedCount,
            'activeCount' => $activeCount,
            'loaned' => $loaned,
            'returned' => $returned,
            'totalUsers' => $totalUsers,
        ]);        
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function projects(Request $request): Renderable|RedirectResponse
    {
        $auth = auth()->user();

        if ($auth->is_admin) {
            $projectIds = Project::pluck('id');
        } else {
            $projectIds = $auth->projects()->pluck('project_id');
        }

        if ($projectIds->count() === 1) {
            $projectId = $projectIds->first();
            return redirect()->route('documents', ['project_id' => $projectId]);
        }

        $projects = Project::whereIn('id', $projectIds)->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))->get();

        return view('projects', compact('projects'));
    }

    public function temporalitys()
    {
        return view('temporalitys');
    }

    public function documents(Request $request, $project_id)
    {
        return $this->setProject($request);
    }

    public function setProject(Request $request)
    {
        $project_id = $request->project_id;
        $request->session()->put('project_id', $project_id);
        return view('documents', ['project_id' => $project_id]);
    }

    public function collections()
    {
        return view('collections');
    }

    public function users()
    {
        return view('users');
    }

    public function logs()
    {
        return view('logs');
    }

    public function alerts()
    {
        return view('alerts');
    }

    public function labels(Request $request)
    {
        $idArray = $request->input('ids');
        if (!is_array($idArray)) {
            $idArray = explode(',', $idArray);
        }
        $documents = Document::whereIn('id', $idArray)->get();
        return view('prints.label', compact('documents'));
    }

    public function loan_form($id)
    {
        return view('prints.loan_form', ['id' => $id]);
    }

    public function box()
    {
        return view('prints.box');
    }

    public function cabinet()
    {
        return view('prints.cabinet');
    }

    public function elimination_list()
    {
        return view('elimination_list');
    }

    public function eliminations($project_id)
    {
        return view('eliminations', ['project_id' => $project_id]);
    }

    public function reports()
    {
        return view('reports');
    }

    public function show_document($document_id)
    {
        return view('show.document', ['document_id' => $document_id]);
    }

    public function document_collection($id)
    {
        return view('show.document_collection', ['id' => $id]);
    }

    public function print_elimination_list($list_id)
    {
        return view('prints.elimination_list', ['list_id' => $list_id]);
    }
}
