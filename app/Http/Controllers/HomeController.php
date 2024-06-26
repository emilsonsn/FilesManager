<?php

namespace App\Http\Controllers;

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

  public function label($id){
    return view('prints.label', ['id' => $id]);
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
  
  public function reports(){
    return view('reports');
  }

  public function show_document($document_id){
    return view('show.document', ['document_id' => $document_id]);
  }

  public function document_collection($id){
    return view('show.document_collection', ['id' => $id]);
  }

}
