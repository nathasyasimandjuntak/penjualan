<?php namespace App\Http\Libraries;

class Datagrid {

	public function field_name_replacer($input, $replace_field)
	{
		foreach ($replace_field as $field_name) {
			if ($input['dataSearch']) {
				$field = $input['dataSearch']['field'];
                if ($field == $field_name['old_name']) {
                	$input['dataSearch']['field'] = $field_name['new_name'];
                }
            }
            if ($input['sort']) {
				$field = $input['sort'];
                if ($field == $field_name['old_name']) {
                	$input['sort'] = $field_name['new_name'];
                }
            }
		}

		return $input;
	}

	public function get_rows($table, $param)
	{
		$select = $table->select( \DB::raw($param['select']) )
						->skip($param['input']['limit'])
						->take($param['input']['offset']);
						
		if (!empty($param['input']['sort'])) {
			$input = $this->field_name_replacer($param['input'], $param['replace_field']);
			$select = $select->orderBy($input['sort'], $input['order']);
		} else {
			$select = $select->orderBy($this->primaryKey);
		}

		if ($param['input']['dataSearch']) {
			$input = $this->field_name_replacer($param['input'], $param['replace_field']);
			$select = (!empty($input['dataSearch']['field'])) ? $select->where($input['dataSearch']['field'], 'like', '%' . $input['dataSearch']['value'] . '%') : $select;	
			$select = (!empty($input['dataSearch']['from_date'])) ? $select->where($param['from_date_field'], '>=', $input['dataSearch']['from_date'].' 00:00:00') : $select;
			$select = (!empty($input['dataSearch']['to_date'])) ? $select->where($param['to_date_field'], '<=', $input['dataSearch']['to_date'].' 23:59:59') : $select;
		}
						
		return $select->get();
	}

	public function get_total_rows($table, $param)
	{
		$select_total = $table->select( \DB::raw('sql_calc_found_rows ' . $param['select']) );
		
		if ($param['input']['dataSearch']) {
			$input = $this->field_name_replacer($param['input'], $param['replace_field']);
			$select_total = (!empty($input['dataSearch']['field'])) ? $select_total->where($input['dataSearch']['field'], 'like', '%' . $input['dataSearch']['value'] . '%') : $select_total;	
		}

		$select_total = $select_total->get();
		$select_total = \DB::select( \DB::raw("select found_rows() as total;") );

		return $select_total[0]->total;
	}

	public function datagrid_query($param, $modifier)
	{
		$table = $modifier( \DB::table($param['table']) );
		$select = $this->get_rows($table, $param);
		$select_total = $this->get_total_rows($table, $param);
		
		return ['total' => $select_total, 'rows' => $select];
	}

	public function datagrid_query_byYear($param, $modifier, $tahun)
	{
		$table = $modifier( \DB::table($param['table'])->where('date_neraca','like','%'.$tahun.'%') );
		$select = $this->get_rows($table, $param);
		$select_total = $this->get_total_rows($table, $param);
		
		return ['total' => $select_total, 'rows' => $select];
	}

	public function datagrid_query_custome1($param, $modifier, $koloms, $isis)
	{
		$table = $modifier( \DB::table($param['table'])->where($koloms,'=',$isis));
		$select = $this->get_rows($table, $param);
		$select_total = $this->get_total_rows($table, $param);
		
		return ['total' => $select_total, 'rows' => $select];
	}

	public function datagrid_query_between($param, $modifier, $koloms, $isis1, $isis2)
	{
		$table = $modifier( \DB::table($param['table'])->whereBetween($koloms,[$isis1,$isis2]));
		$select = $this->get_rows($table, $param);
		$select_total = $this->get_total_rows($table, $param);
		
		return ['total' => $select_total, 'rows' => $select];
	}

	public function datagrid_query_custome1_between($param, $modifier, $koloms, $isis, $koloms2, $isis1, $isis2)
	{
		$table = $modifier( \DB::table($param['table'])->where($koloms,'=',$isis)->whereBetween($koloms2,[$isis1,$isis2]));
		$select = $this->get_rows($table, $param);
		$select_total = $this->get_total_rows($table, $param);
		
		return ['total' => $select_total, 'rows' => $select];
	}

	public function datagrid_query_byMounth($param, $modifier, $koloms, $isis)
	{
		$table = $modifier( \DB::table($param['table'])->where($koloms,'like',$isis.'%') );
		$select = $this->get_rows($table, $param);
		$select_total = $this->get_total_rows($table, $param);
		
		return ['total' => $select_total, 'rows' => $select];
	}

	public function datagrid_query_byOPMounth($param, $modifier, $koloms, $isis, $kolom2, $isi2)
	{
		$table = $modifier( \DB::table($param['table'])->where($koloms,'=',$isis)->where($kolom2,'like',$isi2.'%') );
		$select = $this->get_rows($table, $param);
		$select_total = $this->get_total_rows($table, $param);
		
		return ['total' => $select_total, 'rows' => $select];
	}
}