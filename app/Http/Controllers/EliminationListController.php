<?php

namespace App\Http\Controllers;

use App\Models\EliminationList;
use App\Models\EliminationListFile;
use App\Models\Log;
use App\Traits\WasabiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EliminationListController extends Controller
{
    use WasabiTrait;
    public function create(Request $request){
        $data = $request->all();
        $data['project_id'] = session('project_id');

        DB::beginTransaction();
        try {
            if(isset($data['id'])){
                $eliminationList = EliminationList::find($data['id']);
                $eliminationList->update($data);
            }else{
                $eliminationList = EliminationList::create($data);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filePath = $this->uploadToWasabi($file);
                    if($filePath){
                        EliminationListFile::create([
                            'elimination_list_id' => $eliminationList->id,
                            'name' => $file->getClientOriginalName(),
                            'path' => $filePath,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Erro ao salvar a lista de eliminação.']);
        }

        Log::create([
            'user_id' => auth()->user()->id,
            'description' => "Criou/Editou uma lista de eliminação"
        ]);

        return redirect()->back();
    }

    public function show($id){
        $eliminationList = EliminationList::where('id', $id)->with('files')->get();
        return response()->json($eliminationList[0]);
    }

    public function files($id){
        $eliminationListFiles = EliminationListFile::where('elimination_list_id', $id)->get();
        foreach($eliminationListFiles as $indice => $eliminationListFile){
            $eliminationListFiles[$indice]->path = $this->getPresignedUrlWasabi($eliminationListFile->path);
        }
        return response()->json($eliminationListFiles);
    }

    public function deleteFile($id)
    {
        $file = EliminationListFile::find($id);
        if ($file) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
            return response()->json(['message' => 'Arquivo apagado com sucesso.'], 200);
        }
        return response()->json(['message' => 'Arquivo não encontrado.'], 404);
    }

    public function delete($id){
        $eliminationList = EliminationList::find($id);

        Log::create([
            'user_id' => auth()->user()->id,
            'description' => "Deletou uma lista de eliminação $eliminationList->organ"
        ]);

        $eliminationList->delete();
        return redirect()->back();
    }
}
