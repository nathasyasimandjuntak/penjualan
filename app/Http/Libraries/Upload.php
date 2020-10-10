<?php namespace App\Http\Libraries;

use Storage;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Orchestra\Imagine\Facade as Imagine;
use App\Http\Libraries\ImageResize;
use Imagine\Filter\Basic\WebOptimization;

class Upload {

	public static function saveImage($upload_path, $file)
	{
		$detectedType = exif_imagetype($file);
		$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG);
		$allow = in_array($detectedType, $allowedTypes);
		if($allow)
		{
			$file_name = str_random(3) . '-' . date('dmYhis') . '.' . $file->getClientOriginalExtension();
			$file_contents = \File::get($file);
			Storage::put($upload_path . 'original-' . $file_name, $file_contents);
			//resizing image
			foreach (\Config::get('resize-image.size') as $key => $value) {
				$image = new ImageResize(public_path() . '/' . $upload_path . 'original-' . $file_name);
				$size_prefix = $value['width'] . 'x' . $value['height'] . '-';
				$image->crop($value['width'], $value['height']);
				$image->save(public_path() . '/' . $upload_path . $size_prefix . $file_name);
				$optimization = new WebOptimization(public_path() . '/' . $upload_path . 'original-' . $file_name);
				$optimization->apply(Imagine::open(public_path() . '/' . $upload_path . 'original-' . $file_name));
			}
			return $file_name;
		} else {
			return "File tidak didukung!";
		}
	}

	public static function saveImageUpload($upload_path, $file)
	{
		$detectedType = exif_imagetype($file);
		$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG);
		$allow = in_array($detectedType, $allowedTypes);
		// $dpi = new \App\Http\Libraries\Upload::get_dpi($file);
		// if($dpi < 300){
		// 	return "File tidak didukung!";
		// }
		if($allow)
		{
			$file_name = str_random(3) . '-' . date('dmYhis') . '.' . $file->getClientOriginalExtension();
			$file_contents = \File::get($file);
			Storage::put($upload_path . 'original-' . $file_name, $file_contents);
			//resizing image
			$image = Imagine::open(storage_path() . $upload_path . 'original-' . $file_name);
			$oldWidth = $image->getSize()->getWidth();
			$oldHeight = $image->getSize()->getHeight();
			if($oldWidth < 3000 || $oldWidth > 5000 || $oldHeight < 3000 || $oldHeight > 5000) {
				return "error";
			}
			$newWidth = 300;
			$newHeight = round($oldHeight * (300 / $oldWidth));
			$image->resize(new Box($newWidth, $newHeight))->save(storage_path() . $upload_path  . 'resized-' . $file_name);
			return $file_name;
		} else {
			return "error";
		}
	}

	public static function deleteImage($upload_path, $file)
	{
		foreach (\Config::get('resize-image.size') as $key => $value) {
			$filename = $value['width'] . 'x' . $value['height'] . "-" . $file;
			Storage::delete($upload_path."/".$filename);
		}
		Storage::delete($upload_path."/original-".$file);
		
	}

	public static function get_dpi($filename){
    $a = fopen($filename,'r');
    $string = fread($a,20);
    fclose($a);

    $data = bin2hex(substr($string,14,4));
    $x = substr($data,0,4);
    $y = substr($data,0,4);

    return array(hexdec($x),hexdec($y));
} 

}
