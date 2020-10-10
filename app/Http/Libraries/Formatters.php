<?php 
namespace App\Http\Libraries;
use App\Http\Models\Order;
use App\Http\Models\OrderDetail;
use Sentinel;
class Formatters
{	
	public static function currencyformat($angka,$satuan='true',$sign='Rp. ',$backsign='')
	{
		$angka = (int) $angka;
		switch ($angka){
			default :
				if ($satuan)
					$output = $sign.number_format($angka,0,",",".").$backsign;
				else
					$output = number_format($angka,0,",",".");
				break;
		}
		return $output;
	}
	public static function tgl_indo($tgl,$format='') {
		$tanggal = substr($tgl,8,2);
		if ($format=='short')
			$bulan	 = substr($tgl,5,2);
		elseif ($format=='alphashort')
			$bulan	 = substr(Formatters::get_bulan(substr($tgl,5,2)),0,3);
		else
			$bulan	 = Formatters::get_bulan(substr($tgl,5,2));
		$tahun	 = substr($tgl,0,4);
		return $tanggal.' '.$bulan.' '.$tahun;
	}
	public static function get_bulan($bln){
		switch ($bln){
			case 1 :
				return "Januari";
				break;
			case 2:
				return "Februari";
				break;
			case 3:
				return "Maret";
				break;
			case 4:
				return "April";
				break;
			case 5:
				return "Mei";
				break;
			case 6:
				return "Juni";
				break;
			case 7:
				return "Juli";
				break;
			case 8:
				return "Agustus";
				break;
			case 9:
				return "September";
				break;
			case 10:
				return "Oktober";
				break;
			case 11:
				return "November";
				break;
			case 12:
				return "Desember";
				break;
		}
	}
	public static function trim_text($input, $length, $ellipses = true, $ellipses_string = '...', $ellipses_url = '#', $strip_html = true) 
	{
	    //strip tags, if desired
	    if ($strip_html) {
	        $input = strip_tags($input);
	    }
	  
	    //no need to trim, already shorter than trim length
	    if (strlen($input) <= $length) {
	        return $input;
	    }
	  
	    //find last space within length
	    $last_space = strrpos(substr($input, 0, $length), ' ');
	    $trimmed_text = substr($input, 0, $last_space);
	  
	    //add ellipses (...)
	    if ($ellipses) {
	        $trimmed_text .= " ".$ellipses_string;
	    }
	  
	    return $trimmed_text;
	}
	public static function tokenreplacement($string,$idUser=0,$idInvoice=0)
	{
		if ($idUser>0)
		{
            $user = Sentinel::findById($idUser);
			$target = array(
				'{user_email}',
				'{user_firstname}',
				'{user_lastname}',
				'{user_fullname}',
				'{user_username}',
				'{user_gender}',
				'{user_phone}',
				'{user_address}',
				'{user_province}',
				'{user_city}',
				'{user_subdistrict}',
				'{user_zip}'
			);
	        $replacement = array(
	        	$user->email,
	        	$user->first_name,
	        	$user->last_name,
	        	$user->first_name.' '.$user->last_name,
	        	$user->username(),
	        	$user->gender(),
	        	$user->phone(),
	        	$user->address(),
	        	$user->province(),
	        	$user->city(),
	        	$user->subdistrict(),
	        	$user->zip(),
	        );
	        $string = str_replace($target, $replacement, $string);
		}
        if ($idInvoice>0)
		{
			$order = Order::where('id',$idInvoice)->first();
			$orderDetil = Formatters::invoice_detil($idInvoice);
			$target = array(
				'{invoice_code}',
				'{invoice_detail}',
				'{invoice_uniquenumber}'
			);
	        $replacement = array(
	        	$order->order_code,
	        	$orderDetil,
	        	$order->unique_number
	        );
	        $string = str_replace($target, $replacement, $string);
		}
	    return $string;
	}
	public static function invoice_detil($idInvoice)
    {
    	$data['invoice'] 		= Order::where('id',$idInvoice)->first();
    	$data['invoiceDetail'] 	= OrderDetail::where('order_id',$idInvoice)->get();
        $invoiceDetail = view('emails.invoice_detail')->with('data', $data);
        return $invoiceDetail;
    }
}
