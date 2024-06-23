<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentCollection;
use App\Models\Loan;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportController extends Controller
{
    public function generateDocumentsReport(Request $request)
    {
        $projectId = $request->input('project_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $documents = Document::where('project_id', $projectId);

        if ($startDate && $endDate) {
            $documents->whereBetween('created_at', [$startDate, $endDate]);
        }else if($startDate){
            $documents->whereDate('created_at', '>', $startDate);
        }else if($endDate){
            $documents->whereDate('created_at', '<', $startDate);
        }

        $documents = $documents->get();

        return Excel::download(new class($documents) implements FromCollection, WithHeadings {
            protected $documents;

            public function __construct($documents)
            {
                $this->documents = $documents;
            }

            public function collection()
            {
                return $this->documents;
            }

            public function headings(): array
            {
                return [
                    'ID',
                    'Número do Documento',
                    'Nome do Titular',
                    'Descrição',
                    'Caixa',
                    'Quantidade de Pastas',
                    'Armário',
                    'Gaveta',
                    'Classificação',
                    'Versão',
                    'Situação A.C',
                    'Situação A.I',
                    'Data Inicial',
                    'Data de Arquivamento',
                    'Data de Expiração A.C',
                    'Data de Expiração A.I',
                    'Observações',
                    'Tags',
                    'ID da Temporalidade',
                    'ID do Projeto',
                    'ID do Usuário',
                    'Data de Empréstimo',
                    'Autor do Empréstimo',
                    'Receptor do Empréstimo',
                    'Gênero',
                    'Data de Retorno',
                    'Autor do Retorno',
                    'Autor do Recebimento',
                    'ID do Documento',
                    'ID do Usuário',
                    'Data de criação',
                    'Data de atualização'
                ];
            }                
            
        }, 'documentos.xlsx');
    }

    public function generateLoansReport(Request $request)
    {
        $documentId = $request->input('document_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = DocumentCollection::query();

        if ($documentId) {
            $query->where('document_id', $documentId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }else if($startDate){
            $query->whereDate('created_at', '>', $startDate);
        }else if($endDate){
            $query->whereDate('created_at', '<', $startDate);
        }

        $loans = $query->get();

        return Excel::download(new class($loans) implements FromCollection, WithHeadings {
            protected $loans;

            public function __construct($loans)
            {
                $this->loans = $loans;
            }

            public function collection()
            {
                return $this->loans;
            }

            public function headings(): array
            {
                return [
                    'ID',
                    'Data de Empréstimo',
                    'Autor do Empréstimo',
                    'Receptor do Empréstimo',
                    'Gênero',
                    'Data de Retorno',
                    'Autor do Retorno',
                    'Autor do Recebimento',
                    'ID do Documento',
                    'ID do Usuário',
                    'Data de criação',
                    'Data de atualização'
                ];
            }
        }, 'emprestimos.xlsx');
    }
}
