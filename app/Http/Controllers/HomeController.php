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
  public function __construct()
  {
    $this->middleware('auth');
  }

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

  public function documents($project_id)
  {
    return view('documents', ['project_id' => $project_id]);
  }
  public function collections()
  {
    return view('collections');
  }

  public function users(){
    return view('users');
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
  
}
