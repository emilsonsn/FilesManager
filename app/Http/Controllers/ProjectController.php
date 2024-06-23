<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $data['image_path'] = $path;
        }
        Project::create($data);

        Log::create([
            'user_id' => auth()->user()->id,
            'description' => "Criou um projeto"
        ]);

        return redirect()->route('dashboard');
    }
    

    public function delete($id){
        $project = Project::find($id);

        Log::create([
            'user_id' => auth()->user()->id,
            'description' => "Deletou um projeto $project->description"
        ]);


        $project->delete();
        return redirect('dashboard');
    }
}
