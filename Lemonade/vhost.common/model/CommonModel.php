<?php

class CommonModel extends Lemon_Model 
{
	function getIp()
	{
		$ip=false;
		if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
		{
			$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) 
			{ 
				array_unshift($ips, $ip); 
				$ip = FALSE; 
			}
			for ($i = 0; $i < count($ips); $i++) 
			{
				if (!eregi ("^(10|172.16|192.168).", $ips[$i])) 
				{
					$ip = $ips[$i];break;
				}
			}
		}
		return ($ip ? $ip : $_SERVER['HTTP_X_FORWARDED_FOR']);
	}
	
	function alertGo($text, $url)
	{
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><script language=javascript>alert('".$text."');window.location='".$url."';</script>";
	}
}

?>