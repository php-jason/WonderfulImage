<?php

require_once ROOTPATH.'/wideimage/WideImage.php';

class Picimg{
  public $canvas = 'canvas.jpg';
  public $basecanvas = 'basecanvas.png';
  public $config = array();
  public $sonimg = array('w' => 240,'h' => 135);
 
  /*
   $gap 间隔 left|top|center|bottom
   $size 尺寸
  */
  public function WonderfulAtlas($gap = '32|57|8|31', $size = '',$pct = 100){
     $this->canvas = $this->canvas ? $this->canvas : $this->config['canvas'];
     if( !file_exists($this->canvas)){
        echo $this->canvas," 畫板不存在!\n";return false;
     }
     $paramsize = $this->getpicnum($gap , $size );
//var_dump($paramsize);exit;
     if($size){
        $size = explode('-',$size);
        $this->sonimg['w'] = $size[0];
        $this->sonimg['h'] = $size[1];
     }
     $imgsetcount = count($this->config['imgsets']);
     if(2 * $paramsize['size']['w'] > $imgsetcount ){
        echo " 貼圖數量太少了! $imgsetcount\n";return false;
     }
     $crop = 0;
     if( $paramsize['size']['w'] * $paramsize['siz']['h'] > $imgsetcount ){
        $paramsize['siz']['h'] = floor( $imgsetcount / $paramsize['size']['w'] );
        $crop = 1;
     }
     $oimg = WideImage::load($this->canvas);
     $ow = $oimg->getWidth();
     $oh = $oimg->getHeight();
     $pos = 0;
     for($x = 0; $x < $paramsize['size']['w']; $x++){
        for($y = 0; $y < $paramsize['size']['h']; $y++){
           if( !file_exists($this->canvas)){
              echo $this->config['imgsets'][$pos]," 貼圖不存在!\n";return false;
           }
           $simg = WideImage::load($this->config['imgsets'][$pos]);
           $simg = $simg->resize($this->sonimg['w'],$this->sonimg['h']);
           if(0 == $pos){
              $sw = $simg->getWidth();
              $sh = $simg->getHeight();
           }
           $left = $x * ($paramsize['gap']['center'] + $sw) + $paramsize['gap']['left'];
           $top = $y * ($paramsize['gap']['center'] + $sh) + $paramsize['gap']['top'];
           $oimg = $oimg->merge($simg, $left, $top, $pct);
           $pos ++;
//           @unlink($this->config['imgsets'][$pos]);
        }
     }
     if( $crop ){
       $h = $paramsize['gap']['top'] + $paramsize['size']['h'] * ( $paramsize['gap']['center'] + $sw) - $paramsize['gap']['center'] + $paramsize['gap']['bottom'];
       $oimg = $oimg->crop(0,0,$ow,$h);
     }
     $oimg->saveToFile($this->config['savefile']);
  }
  /*
   $gap 間隔 left|top|center
   $size w-h
  */
  public function getpicnum($gap = '', $size = ''){
     $this->canvas = $this->canvas ? $this->canvas : $this->config['canvas'];
     if( !file_exists($this->canvas)){
        echo $this->canvas," 畫板不存在!\n";return false;
     }
     $oimg = WideImage::load($this->canvas);
     $gap = explode('|',$gap);
     $gapsize = array();
     $gapsize['left'] = $gap[0];
     $gapsize['top'] = $gap[1];
     $gapsize['center'] = $gap[2];
     $gapsize['bottom'] = $gap[3];
     $ow = $oimg->getWidth() - $gapsize['left'];
     $oh = $oimg->getHeight() - $gapsize['top'];

     if($ow < 0 || $oh < 0 ){
        return false;
     }
     if($size){
        $size = explode('|',$size);
        $this->sonimg['w'] = $size[0];
        $this->sonimg['h'] = $size[1];
     }
     $w = floor($ow / $this->sonimg['w']);
     $h = floor($oh / $this->sonimg['h']);
     while(1){
        $width = ($w - 1) * $gapsize['center'] + $w * $this->sonimg['w'];
        $height = ($h - 1) * $gapsize['center'] + $h * $this->sonimg['h'];
        if($width <= $ow && $height <= $oh){
           break;
        }
        if($width > $ow){
           $w --;
        }
        if($height > $oh){
           $h --;
        }
     }
     return array('gap' => array('left' => $gapsize['left'], 'top' => $gapsize['top'], 'center' => $gapsize['center'],'bottom' => $gapsize['bottom']),'size' => array('w' => $w, 'h' => $h));
  }
  /*
  size:尺寸
  save:保存路径
  */
  public function getcanvasfile($size = '',$save = './'){
     if( !$size){
       return false;
     }
     if(!file_exists($this->basecanvas)){
        echo "底版不存在!\n";exit;
     }
     $s = explode('X',$size);
     $img = WideImage::load($this->basecanvas);
/*
     $ow = $img->getWidth();
     $oh = $img->getHeight();
     $h = ($ow * $s[1]) / $s[0];
//echo $ow,'-',$oh,' ',$ow,'-',$h;exit;
     $img = $img->crop(0,0,$ow,$h);
*/
     $img = $img->resize($s[0],$s[1],'fill','any');
     $savefile = $save.$size.'.jpg'; 
     $img->saveToFile($savefile);
     $img = WideImage::load($savefile);
     echo $img->getWidth(),' ',$img->getHeight();
  }
} 
