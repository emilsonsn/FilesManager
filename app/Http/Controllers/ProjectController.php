<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function create(Request $request){
        $request = $request->all();
        Project::create($request);
        return redirect()->route('dashboard');
    }

    public function delete($id){
        Project::find($id)->delete();
        return redirect('dashboard');
    }
}
