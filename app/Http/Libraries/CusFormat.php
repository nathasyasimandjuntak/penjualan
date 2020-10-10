<?php
namespace App\Http\Libraries;
use App\Models\AutoCode;

class CusFormat
{
	public static function Invoice($category)
	{
		$tahun = date('Y');
		$bulan = date('m');
		$waktu = date('Y-m');
		$kode = AutoCode::where('kategori',$category)->where('waktu', $waktu)->first();
		$rowBulan = CusFormat::convert_romawo($bulan);
		if (!empty($kode)) {
			$pecahKodeLama = explode('/',$kode->kode);
			$UrutKodeBaru = $pecahKodeLama[0] + 1;

			if (strlen($UrutKodeBaru) == 1) { $kodeBaru = '0000'.$UrutKodeBaru; }
			elseif(strlen($UrutKodeBaru) == 2){ $kodeBaru = '000'.$UrutKodeBaru; }
			elseif(strlen($UrutKodeBaru) == 3){ $kodeBaru = '00'.$UrutKodeBaru; }
			elseif(strlen($UrutKodeBaru) == 4){ $kodeBaru = '0'.$UrutKodeBaru; }
			elseif(strlen($UrutKodeBaru) > 4){ $kodeBaru = $UrutKodeBaru; }
			$hasilKode = $kodeBaru.'/'.$category.'/'.$rowBulan.'/'.$tahun;

			$autoCode = AutoCode::find($kode->id_auto_code);
		}else{
			$hasilKode = '00001/'.$category.'/'.$rowBulan.'/'.$tahun;

			$autoCode = new AutoCode;
			$autoCode->kategori = $category;
		}
		$autoCode->kode = $hasilKode;
		$autoCode->waktu = $waktu;
		$autoCode->save();

		return $hasilKode;
	}

	public static function convert_romawo($angka)
	{
		$hasil = "";
		if ($angka < 1 || $angka > 5000) {
			$hasil .= "Batas Angka 1 s/d 5000";
		}else{
			while ($angka >= 1000) {
				$hasil .= "M";
				$angka = $angka - 1000;
			}
		}

		if ($angka >= 500) {
			if ($angka > 500) {
				if ($angka >= 900) {
					$hasil .= "CM";
					$angka = $angka - 900;
				} else {
					$hasil .= "D";
					$angka = $angka - 500;
				}
			}
		}

		while ($angka >= 100) {
			if ($angka >= 400) {
				$hasil .= "CD";
				$angka = $angka - 400;
			} else {
				$angka = $angka - 100;
			}
		}

		if ($angka >= 50) {
			if ($angka >= 90) {
				$hasil .= "XC";
				$angka = $angka - 90;
			} else {
				$hasil .= "L";
				$angka = $angka - 50;
			}
		}

		while ($angka >= 10) {
			if ($angka >= 40) {
				$hasil .= "XL";
				$angka = $angka - 40;
			} else {
				$hasil .= "X";
				$angka = $angka - 10;
			}
		}

		if ($angka >= 5) {
			if ($angka == 9) {
				$hasil .= "IX";
				$angka = $angka - 9;
			} else {
				$hasil .= "V";
				$angka = $angka - 5;
			}
		}

		while ($angka >= 1) {
			if ($angka == 4) {
				$hasil .= "IV";
				$angka = $angka - 4;
			} else {
				$hasil .= "I";
				$angka = $angka - 1;
			}
		}
		return $hasil;
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

	public static function get_tgl_schedule($tgl_awal, $week, $hari)
	{
		// $tgl_awal = '2019-10-01'; // minggu
		// $week = ['minggu1','minggu3'];
		// $hari = ['rabu','kamis'];
		$tglAwal = date('Y-m-d', strtotime($tgl_awal));
		$i = 0;

		$tglAwalBulan = date('Y-m', strtotime($tglAwal)).'-01';
		$hariAwalBulan = date('w', strtotime($tglAwalBulan));
		switch ($hariAwalBulan) {
			case '0': $nameDayFirst = 'minggu';break;
			case '1': $nameDayFirst = 'senin';break;
			case '2': $nameDayFirst = 'selasa';break;
			case '3': $nameDayFirst = 'rabu';break;
			case '4': $nameDayFirst = 'kamis';break;
			case '5': $nameDayFirst = 'jumat';break;
			case '6': $nameDayFirst = 'sabtu';break;
		}
		foreach ($hari as $days) {
			if ($days == $nameDayFirst) {
				$stW = 'false';
				foreach ($week as $w) {
					if ($w == 'minggu1') {
						$data['tanggal'][$i] = $tglAwalBulan;
						$i++;
					}elseif ($w == 'minggu2') {
						$tglW2 = date('Y-m-d', strtotime("+7 day", strtotime($tglAwalBulan)));
						$data['tanggal'][$i] = $tglW2;
						$i++;
					}elseif ($w == 'minggu3') {
						$tglW3 = date('Y-m-d', strtotime("+14 day", strtotime($tglAwalBulan)));
						$data['tanggal'][$i] = $tglW3;
						$i++;
					}elseif ($w == 'minggu4') {
						$tglW4 = date('Y-m-d', strtotime("+21 day", strtotime($tglAwalBulan)));
						$data['tanggal'][$i] = $tglW4;
						$i++;
					}
				}
			}else{
				$cekTgl = $tglAwal;
				$hr = 'false';
				while ($hr == 'false') {
					$hrTglBesok = date('Y-m-d', strtotime("+1 day", strtotime($cekTgl)));
					$cekHari = date('w', strtotime($hrTglBesok));

					switch ($cekHari) {
						case '0': $nameDay = 'minggu';break;
						case '1': $nameDay = 'senin';break;
						case '2': $nameDay = 'selasa';break;
						case '3': $nameDay = 'rabu';break;
						case '4': $nameDay = 'kamis';break;
						case '5': $nameDay = 'jumat';break;
						case '6': $nameDay = 'sabtu';break;
					}

					if ($nameDay == $days) { $hr = 'true' ; }
					$cekTgl = $hrTglBesok;
				}

				foreach ($week as $w) {
					if ($w == 'minggu1') {
						$data['tanggal'][$i] = $cekTgl;
						$i++;
					}elseif ($w == 'minggu2') {
						$tglW2 = date('Y-m-d', strtotime("+7 day", strtotime($cekTgl)));
						$data['tanggal'][$i] = $tglW2;
						$i++;
					}elseif ($w == 'minggu3') {
						$tglW3 = date('Y-m-d', strtotime("+14 day", strtotime($cekTgl)));
						$data['tanggal'][$i] = $tglW3;
						$i++;
					}elseif ($w == 'minggu4') {
						$tglW4 = date('Y-m-d', strtotime("+21 day", strtotime($cekTgl)));
						$data['tanggal'][$i] = $tglW4;
						$i++;
					}
				}
			}
		}
		return $data;
	}

	public static function convert_day($tanggal){
		$hari = date('D',strtotime($tanggal));
		$pakai = '';
		switch ($hari) {
			case 'Sun': $pakai = 'Minggu'; break;
			case 'Mon': $pakai = 'Senin'; break;
			case 'Tue': $pakai = 'Selasa'; break;
			case 'Wed': $pakai = 'Rabu'; break;
			case 'Thu': $pakai = 'Kamis'; break;
			case 'Fri': $pakai = 'Jumat'; break;
			case 'Sat': $pakai = 'Sabtu'; break;
			
			default:
				$pakai = '';
				break;
		}

		return $pakai;
	}
}
