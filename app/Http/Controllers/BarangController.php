<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Auth,Redirect,File,DB, Validator, Session, Hash;

class BarangController extends Controller
{
  private $title = "Barang";
  private $menuActive = "barang";
  public function index()
  {
    $data = array();

    $data['title'] = "Barang";
    $data['judul'] = "Menamplkan Halaman Barang";
    $data['menuActive'] = "barang";
    return view('admin.barang.main',$data);
  }
  public function datagrid(Request $request){
    $data = Barang::getData($request);
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
        $data['data'] = Barang::find($request->id);
        $data['id'] = $request->id;}
    }else {
      $data['edit'] = 'false';
    }
    return view('admin.barang.add',$data);
  }
  public function store(Request $request)
  {
  if ($request->edit=='false') {
    $newdata = new Barang;
  } else {
    $newdata = Barang::find($request->id);
    if ($newdata) {
      //
    }else {
      $newdata = new Barang;}}
      $newdata->kode_barang = $request->kode_barang;
      $newdata->nama_barang = $request->nama_barang;
      $newdata->harga = $request->harga;
  $newdata->save();
  if ($newdata) {
    session()->flash('status', 'Task was successful!');
    session()->flash('type', 'success');
    return Redirect::route('barang');
  }
  return 'false';
}
public function show(Request $request)
{
  $id = $request->id;
  $data['data'] = Barang::find($id);
  if ($data['data']) {
    $content = view('admin.barang.show',$data)->render();
    return ['status'=>'success','content'=>$content];
  }
  return ['status'=>'failed','content'=>''];
}
public function destroy(Request $request)
{
  $data = Barang::find($request->id);
  if ($data) {
    $data->delete();
    return ['status' => 'success'];
  }
  return 'false';
}

}
