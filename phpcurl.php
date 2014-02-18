<?php
class CurlModel{
  public $config = array();
  public $postval = array();
  protected $ch = '';
  public $cookie_file = '';

  public function __construct(){
    $this->config = array(
    'cookie' => 'cookie',
    'useragent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:24.0) Gecko/20100101 Firefox/24.0',
//    'proxy' => '192.168.1.254:9993',
    'header' => 0
    );
    $this->ch = curl_init();

  }
  public function getHtml(){
    $url = $this->config['url'];
    unset($this->config['url']);
    curl_setopt($this->ch, CURLOPT_URL, $url);
    curl_setopt($this->ch,CURLOPT_HEADER,intval($this->config['header']));
//lighttpd server
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($this->ch, CURLOPT_MAXREDIRS, 1);
    if(isset($this->config['referer'])){
       curl_setopt ($this->ch,CURLOPT_REFERER,$this->config['referer']);
    }
   //如果你想把一个头包含在输出中，设置这个选项为一个非零值。
    curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,1); ///设置不输出在浏览器上
    curl_setopt($this->ch,CURLOPT_POST,count($this->postval));
    /////如果你想PHP去做一个正规的HTTP POST，设置这个选  项为一个非零值。这个POST是普通的 application/x-www-from-urlencoded 类型，多数被HTML表单使用。
    if(count($this->postval) > 0){
       curl_setopt($this->ch,CURLOPT_POSTFIELDS,$this->postval);
    }
    ////传递一个作为HTTP "POST"操作的所有数据的字符串。
    $this->cookie_file = ROOTPATH.'/cookie/'.$this->config['cookie'];
    if(!file_exists($this->cookie_file)){
      touch($this->cookie_file);
      @chmod($this->cookie_file,0777);
    }
    if(isset($this->config['proxy']) && $this->config['proxy']){
       curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, 1);
       curl_setopt($this->ch, CURLOPT_PROXY ,$this->config['proxy']);
       curl_setopt($this->ch, CURLOPT_PROXYTYPE, 7);
    }
    if(isset($this->config['useragent']) && $this->config['useragent']){
       curl_setopt($this->ch, CURLOPT_USERAGENT, $this->config['useragent']);
    }
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:8.8.8.8', 'CLIENT-IP:8.8.8.8'));  //构造IP
    curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookie_file);
    curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookie_file);
    /////把返回来的cookie信息保存在$cookie_jar文件中
    $this->html = curl_exec($this->ch);///执行
    $this->postval = array();
    $this->config['url'] = $url;
    $this->config['referer'] = $url;
    if(!$this->html){
       echo curl_error($this->ch),"\n";
    }
    return $this->html;
  }
  public function download(){
    $this->config['savefile'] = isset($this->config['savefile']) ? $this->config['savefile'] :basename($this->config['url']) ;
    $h_file = fopen($this->config['savefile'], 'wb');
    curl_setopt($this->ch, CURLOPT_HEADER, 0);  
    curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);  
    curl_setopt($this->ch, CURLOPT_TIMEOUT, 10000);  
    curl_setopt($this->ch, CURLOPT_URL, $this->config['url']);  
    curl_setopt($this->ch, CURLOPT_FILE, $h_file);  
    if(isset($this->config['referer'])){
       curl_setopt ($this->ch,CURLOPT_REFERER,$this->config['referer']);
    }
    if(isset($this->config['proxy']) && $this->config['proxy']){
       curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, 1);
       curl_setopt($this->ch, CURLOPT_PROXY ,$this->config['proxy']);
       curl_setopt($this->ch, CURLOPT_PROXYTYPE, 7);
    }
    if(isset($this->config['useragent']) && $this->config['useragent']){
       curl_setopt($this->ch, CURLOPT_USERAGENT, $this->config['useragent']);
    }
    //curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); // 阻止对证书的合法性的检查  
    //curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在  
    if(isset($this->config['offset'])){
       curl_setopt($h_curl, CURLOPT_RESUME_FROM, $this->config['offset']);
    }
    //curl_setopt($h_curl, CURLOPT_RETURNTRANSFER, true);  
    $curl_success = curl_exec($this->ch);  
    fclose($h_file);
    return $curl_success;  
  }
  public function __destruct(){
     curl_close($this->ch);
  }

}

