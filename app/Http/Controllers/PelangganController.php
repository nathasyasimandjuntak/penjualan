<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use Auth,Redirect,File,DB, Validator, Session, Hash;

class PelangganController extends Controller
{
  private $title = "Pelanggan";
  private $menuActive = "pelanggan";

  public function index()
  {
    $data = array();

    $data['title'] = "Pelanggan";
    $data['judul'] = "Menamplkan Halaman Pelanggan";
    $data['menuActive'] = "pelanggan";
    return view('admin.pelanggan.main',$data);
  }
  public function datagrid(Request $request){
    $data = Pelanggan::getData($request);
    return response()->json($data);
  }
  public function create(Request $request){
    $data = array();
    $data['title'] = $this->title;
    $data['judul'] = "Menamplkan Halaman Pelanggan";
    $data['menuActive'] = $this->menuActive;
    $data['edit'] = '';
    $data['id'] = '';
    $data['data'] = null;
    if (isset($request->edit) && !empty($request->edit) && !empty($request->id)) {
      if ($request->edit == 'edit') {
        $data['edit'] = 'true';
        $data['data'] = Pelanggan::find($request->id);
        $data['id'] = $request->id;}
    }else {
      $data['edit'] = 'false';
    }
    return view('admin.pelanggan.add',$data);
  }
  public function store(Request $request)
  {
  if ($request->edit=='false') {
    $newdata = new Pelanggan;
  } else {
    $newdata = Pelanggan::find($request->id);
    if ($newdata) {
      //
    }else {
      $newdata = new Pelanggan;}}
      $newdata->kode_pelanggan = $request->kode_pelanggan;
      $newdata->nama_pelanggan = $request->nama_pelanggan;
  $newdata->save();
  if ($newdata) {
    session()->flash('status', 'Task was successful!');
    session()->flash('type', 'success');
    return Redirect::route('pelanggan');
  }
  return 'false';
}
public function show(Request $request)
{
  $id = $request->id;
  $data['data'] = Pelanggan::find($id);
  if ($data['data']) {
    $content = view('admin.pelanggan.show',$data)->render();
    return ['status'=>'success','content'=>$content];
  }
  return ['status'=>'failed','content'=>''];
}
public function destroy(Request $request)
{
  $data = Pelanggan::find($request->id);
  if ($data) {
    $data->delete();
    return ['status' => 'success'];
  }
  return 'false';
}

}
