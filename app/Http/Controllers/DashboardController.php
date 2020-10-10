<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Users;
use Auth,Redirect,File,DB, Validator, Session, Hash;

class DashboardController extends Controller
{
  private $title = "Dashboard";
  private $menuActive = "pelanggan";

  public function index()
  {
    $data = array();

    $data['title'] = "Dashboard";
    $data['judul'] = "Menamplkan Halaman Pelanggan";
    $data['menuActive'] = "pelanggan";
    return view('admin.dashboard.main',$data);
  }
  public function datagrid(Request $request){
    $data = Pelanggan::getData($request);
    return response()->json($data);
  }
}
