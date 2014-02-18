<?php

define('ROOTPATH',dirname(__FILE__));

require_once ROOTPATH.'/picimg.php';
require_once ROOTPATH.'/phpcurl.php';
require_once ROOTPATH.'/function.php';

$arzondomain = 'http://www.arzon.jp/';

$pic = new Picimg();

//获取尺寸的画板
//$pic->getcanvasfile('800X652');exit;


$dmmcurl = new CurlModel();
$dmmcurl->config['cookie'] = 'cookiedmm';
$dmmcurl->config['proxy'] = '192.168.1.254:8889';

$arzoncurl = new CurlModel();
$arzoncurl->config['cookie'] = 'cookiearzon';
$arzoncurl->config['proxy'] = '192.168.1.254:9990';

//$info = getdmmimglist();
$avkeys = array('BEB-089','TIN-027','DGL-045','DANDY-348','MKD-S59','OKSN-173','CAND-120','HJMO-265','ANX-033','WANZ-103','EKDV-340','UMD-419','NHDTA-432');
foreach($avkeys as $avkey){
  $info = getarzonimglist($avkey);
  var_dump($info);
  sleep(30);
}
exit;

$pic->config['imgsets'] = $info;
$pic->config['savefile'] = 'example.jpg';
$pic->WonderfulAtlas();

//var_dump($info);


