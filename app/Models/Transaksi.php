<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Libraries\Datagrid;

class Transaksi extends Model
{
  protected $table = "transaksi";
  protected $primaryKey = "id_transaksi";
  public $timestamps = false;


  public static function getData($input){
    $table = 'transaksi';
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
          return $data->join('pelanggan', 'pelanggan.id_pelanggan', 'transaksi.pelanggan_id');;
      });

      return $data;
  }
}
