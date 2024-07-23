<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Services\ClientService\ClientService;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function dashboard()
  {
    return view('dashboard');
  }

  public function temporalitys()
  {
    return view('temporalitys');
  }

  public function documents(Request $request, $project_id)
  {
    return $this->setProject($request);
  }

  public function setProject(Request $request)
  {
      $project_id = $request->project_id;
      $request->session()->put('project_id', $project_id);
      return view('documents', ['project_id' => $project_id]);
  }

  public function collections()
  {
    return view('collections');
  }

  public function users(){
    return view('users');
  }

  public function logs(){
    return view('logs');
  }

  public function alerts(){
    return view('alerts');
  }

  public function labels(Request $request)
  {
      $idArray = $request->input('ids');
      if(!is_array($idArray)){
        $idArray = explode(',', $idArray);
      }
      $documents = Document::whereIn('id', $idArray)->get();
      return view('prints.label', compact('documents'));
  }

  public function loan_form($id){
    return view('prints.loan_form', ['id' => $id]);
  }

  public function box(){
    return view('prints.box');
  }

  public function cabinet(){
    return view('prints.cabinet');
  }

  public function elimination_list(){
    return view('elimination_list');
  }

  public function eliminations($project_id){
    return view('eliminations', ['project_id' => $project_id]);
  }
  
  public function reports(){
    return view('reports');
  }

  public function show_document($document_id){
    return view('show.document', ['document_id' => $document_id]);
  }

  public function document_collection($id){
    return view('show.document_collection', ['id' => $id]);
  }

  public function print_elimination_list($list_id){
    return view('prints.elimination_list', ['list_id' => $list_id]);
  }

}
