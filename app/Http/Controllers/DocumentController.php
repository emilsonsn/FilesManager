<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class DocumentController extends Controller
{
    public function create(Request $request)
    {

        $auth = auth()->user();
        $maxSize = ($auth->upload_limit ?? 0) * 1024;

        $request->validate([
            'files.*' => "required|file|max:$maxSize",
        ]);

        $data = $request->all();
        $data['user_id'] = $auth->id;

        DB::beginTransaction();
        try {
            if (isset($data['id'])) {
                $document = Document::find($data['id']);
                if ($document) {
                    $document->update($data);
                }
            } else {
                $document = Document::create($data);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filePath = $file->store('documents', 'public');
                    DocumentFile::create([
                        'document_id' => $document->id,
                        'name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Erro ao salvar o documento.']);
        }
        return redirect()->back();
    }

    public function getFiles($id)
    {
        $document = Document::find($id);
        if ($document) {
            return response()->json($document->files);
        }
        return response()->json([], 404);
    }

    public function downloadAllFiles($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return redirect()->back()->withErrors(['msg' => 'Documento não encontrado.']);
        }

        $files = $document->files;

        if ($files->isEmpty()) {
            return redirect()->back()->withErrors(['msg' => 'Nenhum arquivo encontrado para este documento.']);
        }

        $zip = new ZipArchive;
        $fileName = 'document_files_' . $document->doc_number . '.zip';
        $zipFilePath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                $filePath = storage_path('app/public/' . $file->file_path);
                $zip->addFile($filePath, basename($filePath));
            }
            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            return redirect()->back()->withErrors(['msg' => 'Não foi possível criar o arquivo ZIP.']);
        }
    }


    public function delete($id)
    {
        $document = Document::find($id);
        if ($document) {
            foreach ($document->files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
            $document->delete();
        }
        return redirect()->back();
    }
}
