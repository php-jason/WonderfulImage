<?php

function getdmmimglist(){
  global $dmmcurl;
  $dmmcurl->config['url'] = 'http://www.dmm.co.jp/digital/videoa/-/detail/=/cid=71gas00283/';
  $html = $dmmcurl->getHtml();
  preg_match_all('#<a name="sample-image" id="sample-image\d+"><img src="([^-]+)-([^"]+)" border="0" alt="" class="mg-b6"></a>#Uis',$html,$match);
  if(!isset($match[1])){
     return false;
  }
  $imglist = array();
  foreach($match[1] as $k => $val){
     $imglist[] = sprintf('%sjp-%s',$val,$match[2][$k]);
  }
  return $imglist;
}

function getarzonimglist($avkey = ''){
  if( !$avkey){
     return false;
  }
  global $arzoncurl,$arzondomain;
  for($i = 0;$i < 2 ;$i++){
    $arzoncurl->config['url'] = sprintf('%sitemlist.html?t=&m=all&s=&q=%s',$arzondomain,$avkey);
    $html = $arzoncurl->getHtml();
    preg_match('#<dt>\s+<a href="([^"]+)" title="[^"]+">\s+<img src="[^"]+" alt="[^"]+" border="0" /></a>\s+</dt>#Uis',$html,$match);
    if(isset($match[1])){
       break;
    }
    preg_match('#<td class="yes"><a href="([^"]+)"><img src="img/agecheck/yes.jpg" alt="[^"]+" width="212" height="43" border="0" /></a></td>#Uis',$html,$match);
    if(isset($match[1])){
       $arzoncurl->config['url'] = sprintf('%s%s',$arzondomain,$match[1]);
       $html = $arzoncurl->getHtml();
       continue;
    }
    break;
  }
//  file_put_contents('arzon.html',$html);
  if( !isset($match[1])){
     return false;
  }
  $arzoncurl->config['url'] = sprintf('%s%s',$arzondomain,$match[1]);
  $html = $arzoncurl->getHtml();
  preg_match_all('#<a rel="lightbox\[items\]" href="([^"]+)" title="[^"]+"><img src="[^"]+" alt="[^"]+" width="\d+" height="\d+" border="0"></a>#Uis',$html,$match);
  if( !isset($match[1])){
     return false;
  }
  return count($match[1]) ? $match[1] : false ;
}

