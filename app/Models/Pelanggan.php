<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Libraries\Datagrid;

class Pelanggan extends Model
{

  protected $table = "pelanggan";
  protected $primaryKey = "id_pelanggan";
  public $timestamps = false;

  public static function getData($input){
    $table = 'pelanggan';
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
          return $data;
      });

      return $data;
  }
}
