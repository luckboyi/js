<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JCrop extends Model {
	var $filepath;
	var $picname;
	var $x;
	var $y;
	var $w;
	var $h;
	var $tw;
	var $th;

	public function __construct($filepath, $picname, $x, $y, $w, $h, $tw, $th) {

		$this->filepath = $filepath;
		$this->picname = $picname;
		$this->x = $x;
		$this->y = $y;
		$this->w = $w;
		$this->h = $h;
		$this->tw = $tw;
		$this->th = $th;
	}

	public function crop($uid = 0) {
		$picname = $this->picname;
		$filepath = $this->filepath;
		$x = $this->x;
		$y = $this->y;
		$w = $this->w;
		$h = $this->h;
		$tw = $this->tw;
		$th = $this->th;

		$ext = substr($picname, strrpos($picname, '.') + 1);

		switch ($ext) {
		case "png":
			$image = imagecreatefrompng($picname);
			break;
		case "jpeg":

			$image = imagecreatefromjpeg($picname);
			break;
		case "jpg":
			$image = imagecreatefromjpeg($picname);
			break;
		case "gif":

			$image = imagecreatefromgif($picname);
			break;
		}

		$dst_r = ImageCreateTrueColor($tw, $th);
		$this->setTransparency($image, $dst_r, $ext);
		imagecopyresampled($dst_r, $image, 0, 0, $x, $y, $tw, $th, $w, $h);
		imagedestroy($image);

		$file = $filepath . $uid . "." . $ext;
		$goalFile = $filepath . $uid . "." . 'jpg';

		// Window系统
		/*$path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
        $descPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $goalFile;*/

		switch ($ext) {
		case "png":
			imagepng($dst_r, ($file != null ? $file : ''));
			break;
		case "jpeg":
			imagejpeg($dst_r, ($file ? $file : ''), 90);
			break;
		case "jpg":
			imagejpeg($dst_r, ($file ? $file : ''), 90);
			break;
		case "gif":
			imagegif($dst_r, ($file ? $file : ''));
			break;
		}

		// 将图片转换为.jpg格式
		if ($ext != 'jpg') {
			$this->ImageToJPG($file, $goalFile, $tw, $th);
		}

		$openModel = new OpenModel();
		// 得到目标路径，第二个参数
		$sendParam = $this->getGoalPath($uid);
		$openModel->rsyncImages($goalFile, $sendParam);
		if (file_exists($goalFile)) {
			$returndata = array(
				"status" => '1',
				"file" => $file,
				"error" => '',
				'goalFile' => $goalFile,
			);
		} else {
			$returndata = array(
				"status" => '0',
				"file" => '',
				"error" => '生成文件出错！',
				'goalFile' => '',
			);
		}
		return $returndata;
	}

	public function setTransparency($imgSrc, $imgDest, $ext) {

		if ($ext == "png" || $ext == "gif") {
			$trnprt_indx = imagecolortransparent($imgSrc);
			// If we have a specific transparent color
			if ($trnprt_indx >= 0) {
				// Get the original image's transparent color's RGB values
				$trnprt_color = imagecolorsforindex($imgSrc, $trnprt_indx);
				// Allocate the same color in the new image resource
				$trnprt_indx = imagecolorallocate($imgDest, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				// Completely fill the background of the new image with allocated color.
				imagefill($imgDest, 0, 0, $trnprt_indx);
				// Set the background color for new image to transparent
				imagecolortransparent($imgDest, $trnprt_indx);
			}
			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($ext == "png") {
				// Turn off transparency blending (temporarily)
				imagealphablending($imgDest, true);
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($imgDest, 0, 0, 0, 127);
				// Completely fill the background of the new image with allocated color.
				imagefill($imgDest, 0, 0, $color);
				// Restore transparency blending
				imagesavealpha($imgDest, true);
			}

		}
	}

	public function ImageToJPG($srcFile, $dstFile, $towidth, $toheight) {
		$quality = 100;
		$data = @GetImageSize($srcFile);
		switch ($data['2']) {
		case 1:
			$im = imagecreatefromgif($srcFile);
			break;
		case 2:
			$im = imagecreatefromjpeg($srcFile);
			break;
		case 3:
			$im = imagecreatefrompng($srcFile);
			break;
		case 6:
			$im = ImageCreateFromBMP($srcFile);
			break;
		}
		$dstX = $srcW = @ImageSX($im);
		$dstY = $srcH = @ImageSY($im);
		$srcW = @ImageSX($im);
		$srcH = @ImageSY($im);

		//$towidth,$toheight
		if ($toheight / $srcW > $towidth / $srcH) {
			$b = $toheight / $srcH;
		} else {
			$b = $towidth / $srcW;
		}
		//计算出图片缩放后的宽高
		// floor 舍去小数点部分，取整
		$new_w = floor($srcW * $b);
		$new_h = floor($srcH * $b);
		$dstX = $new_w;
		$dstY = $new_h;
		$ni = @imageCreateTrueColor($dstX, $dstY);

		@ImageCopyResampled($ni, $im, 0, 0, 0, 0, $dstX, $dstY, $srcW, $srcH);
		@ImageJpeg($ni, $dstFile, $quality);
		@imagedestroy($im);
		@imagedestroy($ni);
		//www.veryhuo.com/a/view/36032.html
		if (file_exists($srcFile)) {
			// 如果文件存在就删除
			unlink($srcFile);
		}
	}

	public function getGoalPath($uid) {
		$path1 = $uid % 100;
		$path2 = $uid % 100000;
		//return $path1 . "/" . $path2 . "/" . $uid . ".png";
		return "face/" . $path1 . "/" . $path2 . "/";
	}
}