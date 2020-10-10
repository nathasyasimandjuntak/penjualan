<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use Auth,Redirect,File,DB, Validator, Session, Hash;

class TransaksiController extends Controller
{
  private $title = "Transaksi";
  private $menuActive = "transaksi";

  public function index()
  {
    $data = array();

    $data['title'] = "Transaksi";
    $data['judul'] = "Menamplkan Halaman Transaksi";
    $data['menuActive'] = "transaksi";
    return view('admin.transaksi.main',$data);
  }
  public function datagrid(Request $request){
    $data = Transaksi::getData($request);
    return response()->json($data);
  }
  public function create(Request $request){
    $data = array();
    $data['title'] = $this->title;
    $data['judul'] = "Menamplkan Halaman Transaksi";
    $data['menuActive'] = $this->menuActive;
    $data['title'] = "Transaksi";
    $data['judul'] = "Menamplkan Halaman Transaksi";
    $data['menuActive'] = "transaksi";
    $data['edit'] = '';
    $data['id'] = '';
    $data['pelanggan'] = Pelanggan::all();
    $data['data'] = null;

    if (isset($request->edit) && !empty($request->edit) && !empty($request->id)) {
      if ($request->edit == 'edit') {
        $data['edit'] = 'true';
        $data['data'] = Transaksi::find($request->id);
        $data['pelanggan'] = Pelanggan::all();
        $data['id'] = $request->id;
      }
    }else {
      $data['edit'] = 'false';
    }

    return view('admin.transaksi.add',$data);
  }
  public function store(Request $request)
  {
  if ($request->edit=='false') {
    $newdata = new Transaksi;
  } else {
    $newdata = Transaksi::find($request->id);
    if ($newdata) {
      //
    }else {
      $newdata = new Transaksi;
    }
  }
  $newdata->no_faktur = $request->no_faktur;
  $newdata->tgl = $request->tgl;
  $newdata->pelanggan_id = $request->pelanggan_id;

  $newdata->save();
  // return $newdata;
  if ($newdata) {
    session()->flash('status', 'Task was successful!');
    session()->flash('type', 'success');
    return Redirect::route('transaksi');
  }

  return 'false';
}
public function show(Request $request)
{
  $id = $request->id;
  $data['data'] = Transaksi::join('pelanggan','pelanggan.id_pelanggan','transaksi.pelanggan_id')->where('id_transaksi',$id)->first();
  if ($data) {
    $content = view('admin.transaksi.show',$data)->render();
    return ['status'=>'success','content'=>$content];
  }
  return ['status'=>'failed','content'=>''];
}
public function destroy(Request $request)
{
  $data = Transaksi::find($request->id);
  if ($data) {
    $data->delete();
    return ['status' => 'success'];
  }
  return 'false';
}
}
