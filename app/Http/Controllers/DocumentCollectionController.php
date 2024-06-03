<?php

namespace App\Http\Controllers;

use App\Models\DocumentCollection;
use Illuminate\Http\Request;

class DocumentCollectionController extends Controller
{
    public function create(Request $request){
        $request = $request->all();
        $auth = auth()->user();

        if($request['id']){
            $request['user_id'] = $auth->id;
            DocumentCollection::find($request['id'])->update($request);
        }else{
            $request['user_id'] = $auth->id;
            DocumentCollection::create($request);
        }
        return redirect()->route('document.collections');
    }

    public function delete($id){
        DocumentCollection::find($id)->delete();
        return redirect()->route('document.collections');
    }
}
