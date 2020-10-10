<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Barang;
use App\Models\Detail;
use Auth,Redirect,File,DB, Validator, Session, Hash;

class DetailController extends Controller
{
  private $title = "Detail";
  private $menuActive = "detail";

  public function index()
  {
    $data = array();

    $data['title'] = "Detail";
    $data['judul'] = "Menampilkan Halaman Detail";
    $data['menuActive'] = "detail";
    return view('admin.detail.main',$data);
  }
  public function datagrid(Request $request){
    $data = Detail::getData($request);
    return response()->json($data);
  }
  public function create(Request $request){
    $data = array();
    $data['title'] = $this->title;
    $data['judul'] = "Menamplkan Halaman Detail";
    $data['menuActive'] = $this->menuActive;
    $data['edit'] = '';
    $data['id'] = '';
    $data['barang'] = Barang::all();
    $data['transaksi'] = Transaksi::all();
    $data['data'] = null;

    if (isset($request->edit) && !empty($request->edit) && !empty($request->id)) {
      if ($request->edit == 'edit') {
        $data['edit'] = 'true';
        $data['data'] = Detail::find($request->id);
        $data['barang'] = Barang::all();
        $data['transaksi'] = Transaksi::all();
        $data['id'] = $request->id;
      }
    }else {
      $data['edit'] = 'false';
    }

    return view('admin.detail.add',$data);
  }
  public function store(Request $request)
  {
  if ($request->edit=='false') {
    $newdata = new Detail;
  } else {
    $newdata = Detail::find($request->id);
    if ($newdata) {
      //
    }else {
      $newdata = new Detail;
    }
  }
  $newdata->transaksi_id = $request->transaksi_id;
  $newdata->barang_id = $request->barang_id;
  $newdata->qty = $request->qty;

  $newdata->save();
  // return $newdata;
  if ($newdata) {
    session()->flash('status', 'Task was successful!');
    session()->flash('type', 'success');
    return Redirect::route('detail');
  }

  return 'false';
}
public function show(Request $request)
{
  $id = $request->id;
return  $data['data'] = Detail::join('transaksi','transaksi.id_transaksi','detail.transaksi_id')
                          ->join('barang','barang.id_barang','detail.barang_id')
                          ->where('id_detail',$id)
                          ->first();
  if ($data) {
    $content = view('admin.detail.show',$data)->render();
    return ['status'=>'success','content'=>$content];
  }
  return ['status'=>'failed','content'=>''];
}
public function destroy(Request $request)
{
  $data = Detail::find($request->id);
  if ($data) {
    $data->delete();
    return ['status' => 'success'];
  }
  return 'false';
}
}
