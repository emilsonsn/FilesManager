<?php

namespace App\Http\Controllers;

use App\Models\DocumentCollection;
use App\Models\DocumentLoan;
use App\Models\Log;
use Illuminate\Http\Request;

class DocumentCollectionController extends Controller
{
    public function create(Request $request){
        $request = $request->all();
        $auth = auth()->user();
        $document_ids = isset($request['document_ids']) ? $request['document_ids'] : [];

        if($request['id']){
            $request['user_id'] = $auth->id;
            $documentCollection = DocumentCollection::find($request['id']);
            $documentCollection->documentLoans()->delete();
            $documentCollection->update($request);
            foreach($document_ids as $document_id){
                DocumentLoan::create([
                    'document_id' => $document_id,
                    'document_collection_id' => $documentCollection->id
                ]);
            }
        }else{
            $request['user_id'] = $auth->id;
            $documentCollection = DocumentCollection::whereNull('return_date')
            ->whereHas('documentLoans', function($query) use($document_ids){
                $query->whereIn('document_id', $document_ids);                
            })->count();

            if(!$documentCollection){
                $documentCollection = DocumentCollection::create($request);
                foreach($document_ids as $document_id){
                    DocumentLoan::create([
                        'document_id' => $document_id,
                        'document_collection_id' => $documentCollection->id
                    ]);
                }
            }
        }
        $document_ids = implode('' ,$request['document_ids'] ?? []);

        Log::create([
            'user_id' => auth()->user()->id,
            'description' => "Criou/Editou um acervo para o documento $document_ids"
        ]);

        return redirect()->route('document.collections');
    }

    public function delete($id){
        $document = DocumentCollection::find($id);

        Log::create([
            'user_id' => auth()->user()->id,
            'description' => "Deletou o acervo do documento $document->id"
        ]);

        $document->delete();
        return redirect()->route('document.collections');
    }
}
