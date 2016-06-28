<?
$vhost = "vhost.user";
$docroot = $_SERVER['DOCUMENT_ROOT'];


function __autoload($className)
{
	global $vhost, $docroot; 
	
	if(substr($className,0,6)=="Lemon_")
	{
		include_once $docroot."/../Lemonade/lemon/".$className.".php";
	}
	else if(strpos($className,"Controller"))
	{
		global $vhost;
		
		$dir = "";
		if(strpos($className, "_")!==false)
		{
			$sub = explode("_",$className);
			for($i=0;$i<sizeof($sub)-1;$i++)
			{
				$dir .= strtolower($sub[$i])."/";
			}
		}

		include_once $docroot."/../Lemonade/".$vhost."/controller/".$dir.$className.".php";
	}
	else if(strpos($className,"Model"))
	{
		include_once $docroot."/../Lemonade/vhost.common/model/".$className.".php";
	}
	else if($className=="Template_")
	{
		include_once $docroot."/../Lemonade/Template_/".$className.".class.php";
	}
	else
	{
		include_once $docroot."/../Lemonade/".$vhost."/lib/".$className.".php";
	}
}

session_set_cookie_params( 0, "/", $_SERVER['HTTP_HOST']);
session_save_path("./session"); 
ini_set("session.cache_expire", 1200);
ini_set("session.gc_maxlifetime", 1200);
session_start();

$service = new Lemon_Service();

$partner = $_REQUEST["partner"];
if($partner!="")
{
	SetCookie("partner_sn",$partner,0);
}

try
{
    return $service->run();	
}
catch(Lemon_ScriptException $e)
{
    echo $e;
    exit;   
}
catch(Lemon_HtmlException $e)
{
    echo $e;
    exit;   
}
?>
