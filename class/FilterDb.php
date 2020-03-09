<?php

class FilterDb{
  // Senitiza datas
  public static function sanitizeDate($data){
    if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])$/", $data)){
      $data = str_replace('/','-',preg_replace("([^0-9/-])", "", $data));
      if(substr_count($data, "-") > 1) $data = date("Y-m-d",strtotime($data));
      elseif(substr_count($data, "-") == 1){
        $data = explode("-", $data);
        $data = date(implode("-", array($data[1], $data[0])));
      }
    }
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

  public static function sanitizeSerialize($data){
    $data = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
    return serialize($data);
  }
}