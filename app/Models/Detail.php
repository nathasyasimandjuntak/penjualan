<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Libraries\Datagrid;

class Detail extends Model
{
  protected $table = "detail";
  protected $primaryKey = "id_detail";
  public $timestamps = false;


  public static function getData($input){
    $table = 'detail';
    $select = '*';

    $replace_field  = [
    //       ['old_name' => 'kehadiran', 'new_name' => 'statusKehadiran'],
      ];

      $param = [
        'input' => $input->all(),
        'select' => $select,
        'table' => $table,
        'replace_field' => $replace_field,
      ];

      $datagrid = new Datagrid;
      $data = $datagrid->datagrid_query($param, function($data){
          return $data->join('transaksi', 'transaksi.id_transaksi', 'detail.transaksi_id')->join('barang', 'barang.id_barang', 'detail.barang_id');
      });

      return $data;
  }
}
