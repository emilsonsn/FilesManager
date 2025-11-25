<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProject;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request){
        $request = $request->all();

        $resetPermissions = [
            'read_doc' => false,
            'create_doc' => false,
            'edit_doc' => false,
            'delete_doc' => false,
            'read_temporality'=> false,
            'create_temporality'=> false,
            'edit_temporality'=> false,
            'delete_temporality'=> false,
            'read_collection'=> false,
            'create_collection'=> false,
            'edit_collection'=> false,
            'delete_collection'=> false,
            'is_active'=> false,
            'create_projects' => false,
            'create_elimination' => false,
            'read_elimination' => false,
            'edit_elimination' => false,
            'delete_elimination' => false,
            'print_generate' => false,
        ];

        $isOutRegister = isset($request['register']);

        if(isset($request['id'])){
            
            if(!$request['password']){
                unset($request['password']);
            }

            $user = User::find($request['id']);
            $user->update($resetPermissions);
            $user->update($request);
        }else{

            if(isset($isOutRegister)){
                $request['is_admin'] = false;
                $request['upload_limit'] = 0;
                
                $request = array_merge($request, $resetPermissions);
            }

            User::create($request);
        }
        
        $viewRedirect = $isOutRegister
            ? route('register', ['success' => 1])
            : route('users');

        return redirect($viewRedirect);
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