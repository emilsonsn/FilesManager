<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProject;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request){
        $request = $request->all();

        if($request['id']){
            
            if(!$request['password']){
                unset($request['password']);
            }

            $resetPermissions = [
                'read_temporality'=> false,
                'create_temporality'=> false,
                'edit_temporality'=> false,
                'delete_temporality'=> false,
                'read_collection'=> false,
                'create_collection'=> false,
                'edit_collection'=> false,
                'delete_collection'=> false
            ];

            $user = User::find($request['id']);
            $user->update($resetPermissions);
            $user->update($request);
        }else{

            User::create($request);
        }
        return redirect('users');
    }

    public function delete($id){
        User::find($id)->delete();
        return redirect('users');
    }

    public function assing_projects(Request $request){
        $request = $request->all();

        foreach($request['projects'] as $project){
            UserProject::create([
                'user_id' => $request['user_id'],
                'project_id' => $project
            ]);
        }
        return redirect('users');
    }

    public function assing_delete(Request $request){
        $user_id = $request->user_id;
        $project_id = $request->project_id;
        UserProject::where('user_id', $user_id)
            ->where('project_id', $project_id)
            ->delete();
        
        return redirect('users');
    }

    public function user_projects($id)
    {
        $user = User::findOrFail($id);
        $projects = $user->projects()->get(); // Supondo que o relacionamento 'projects' está definido no modelo User
        return response()->json(['projects' => $projects]);
    }
}
