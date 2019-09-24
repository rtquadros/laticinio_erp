<?php

class FilterDb{
  // Senitiza datas
  public static function sanitizeDate($data){
    $data = preg_replace("([^0-9/])", "", $data);
    $data = date("Y-m-d",strtotime(str_replace('/','-',$data)));
    return $data;
  }

  public static function brDate($date){
 	  return str_replace('-', '/', date("d-m-Y", strtotime($date)));	
  }

  public static function sanitizeMoney($money){
	  if (strpos($money, ',') !== false) {
		  $money = str_replace('.', '', $money);
		  $money = str_replace(',','.',$money);
	  }
	  $money = filter_var($money, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	  return $money;	
  }
}