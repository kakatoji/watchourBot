<?php

error_reporting(0);

class Watch{
  
  public function curl ($url, $post = 0, $httpheader = 0, $proxy = 0){ // url, postdata, http headers, proxy, uagent
        $co="cookie.txt";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR,$co);
        curl_setopt($ch, CURLOPT_COOKIEFILE,$co);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if($post){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if($httpheader){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        }
        if($proxy){
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch);
        if(!$httpcode) return "Curl Error : ".curl_error($ch); else{
            $header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            curl_close($ch);
            return array($header, $body);
        }
    }
  public function ban(){
    $ban="\e[1;35m
_  _ ____ _  _ ____ ___ ____  _ _ 
|_/  |__| |_/  |__|  |  |  |  | | 
| \_ |  | | \_ |  |  |  |__| _| |
    \n";
    echo $ban;
  }
  public function head($ref=0){
    $head[]="User-Agent: Mozilla/5.0 (Linux; Android 7.0; Redmi Note 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.101 Mobile Safari/537.36";
    $head[]="Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9";
    $head[]="Content-Type: application/x-www-form-urlencoded";
    $head[]="Referer: ".$ref;
    
    return $head;
  }
  
  public function get(){
    return $this->curl("https://watchhours.com/");
  }
  
  public function save($con,$data_post){
    if(!file_get_contents($con)){
      file_put_contents($con,"[]");
    }
    $json=json_decode(file_get_contents($con),1);
    $arr=array_merge($json,$data_post);
    file_put_contents($con,json_encode($arr,JSON_PRETTY_PRINT));
  }
  public function timer($tmr){
    $bl="\e[1;30m";$r="\e[1;31m";$g="\e[1;32m";
    $k="\e[1;33m";$bu="\e[1;34m";$p="\e[1;35m";
    $c="\e[1;36m";$w="\e[1;37m";$cr="\e[0m";
    $tim=time()+$tmr;
    while(true){
      echo "\r                   \r";
      $res=$tim-time();
      if($res < 1){break;}
      echo $c.date('H:i:s',$res);
      sleep(1);
    }
  }
  public function userme(){
    $bl="\e[1;30m";$r="\e[1;31m";$g="\e[1;32m";
    $k="\e[1;33m";$bu="\e[1;34m";$p="\e[1;35m";
    $c="\e[1;36m";$w="\e[1;37m";$cr="\e[0m";
    $user=readline("{$w}[+]username{$c}: ");
    $pass=readline("{$w}[+]password{$c}: ");
    $data=["username"=>$user,
           "password"=>$pass];
    $this->save("data.json",$data);
  }
  
  public function login(){
    $bl="\e[1;30m";$r="\e[1;31m";$g="\e[1;32m";
    $k="\e[1;33m";$bu="\e[1;34m";$p="\e[1;35m";
    $c="\e[1;36m";$w="\e[1;37m";$cr="\e[0m";
    system("clear");
    if(!file_exists("data.json")){
      $this->userme();
    }
    $con=json_decode(file_get_contents("data.json"),1);
    $this->ban();
    while(1){
    $get=$this->get();
    preg_match('#<input type="hidden" name="token" value="(.*?)" />#is',$get[1],$token);
    $head=$this->head("https://watchhours.com/");
    $data="token=".$token[1]."&user=".$con["username"]."&password=".$con["password"]."&remember=on&connect=";
    $login=$this->curl("https://watchhours.com/",$data,$head);
    if($login[0] == null){
      echo "{$r}[?]wait login again\n";
     break;
    }
    preg_match('#<span>(.*?)<b id="c_coins" class="text-warning">(.*?)</b> <small class="text-success">(.*?)</small></span>#is',$login[1],$prof);
    preg_match('#Total Views: (.*?)</span>#is',$login[1],$total);
    $one=explode("<span>",$login[1])[1];
    $one=explode("<br />",$one)[0];
    echo "{$w}[>] username: ".$p .trim($one).$cr."\n";
    echo "{$w}[>] Coins: ".$c.$prof[2].$prof[3].$cr."\n";
    echo "{$w}[>] Total view: ".$c.$total[1].$cr."\n";
  
  $head=$this->head("https://watchhours.com/index.php");
  $vid=$this->curl("https://watchhours.com/index.php?page=videos",$head);
  preg_match('#<div class="website_block" id="(.*?)">#is',$vid[1],$ids);
  $hed=$this->head("https://watchhours.com/?page=videos");
  $get=$this->curl("https://watchhours.com/?page=videos&vid=".$ids[1],$hed);
  preg_match("#var token = '(.*?)';#is",$get[1],$token);
  preg_match("#var length = (.*?);#is",$get[1],$time);
  preg_match("#var response = '(.*?)';#is",$get[1],$id);
   $this->timer($time[1]);
  $ats=$this->head("https://watchhours.com/?page=videos&vid=".$id[1]);
  $claim=$this->curl("https://watchhours.com/system/gateways/video.php","data=".$id[1]."&token=".$token[1],$ats);
  preg_match('#<div class="alert alert-success" role="alert"><b>(.*?)</b>(.*?)<b>(.*?)</b>!</div>#is',$claim[1],$clm);
  echo $g.$clm[1]." ".$clm[2]." ".$clm[3].$cr."\n";
  if($id[1] == 0){
    exit("{$r}Limit coba lagi besok!");
    }
   }
  }
}
$new=new Watch();
$new->login();
