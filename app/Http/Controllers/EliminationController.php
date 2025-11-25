<?php

namespace App\Http\Controllers;

use App\Models\Elimination;
use App\Models\Log;
use Illuminate\Http\Request;

class EliminationController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        if(isset($data['id'])){
            Elimination::find($data['id'])->update($data);
        }else{
            Elimination::create($data);
        }

        Log::create([
            'user_id' => auth()->user()->id,
            'description' => "Criou/Editou uma eliminação com id {$data['id']}"
        ]);

        return redirect()->back();
    }
    

    public function delete($id){
        $elimination = Elimination::find($id);

        Log::create([
            'user_id' => auth()->user()->id,
            'description' => "Deletou uma eliminação $elimination->description"
        ]);

        $elimination->delete();
        return redirect()->back();
    }
}
