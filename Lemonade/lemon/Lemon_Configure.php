<?
class Lemon_Configure
{
	public static $config = array();

	public static function readConfig($kind,$userPath=false)
	{
		global $vhost;

		$filename = dirname(__file__)."/../".$vhost."/config/".($userPath===false?"":"user/").$kind.".ini";

		if(is_file($filename))
		{
			if(!array_key_exists($kind,self::$config))
			{
				self::$config[$kind] = parse_ini_file($filename,true);
			}

			return self::$config[$kind];
		}
		else
			return '';
	}

	public static function writeConfig($kind,$content)
	{
		global $vhost;
		$fp=fopen(dirname(__file__)."/../".$vhost."/config/user/".$kind.".ini",'w');
		$iniContent='';

		for($i=0;$i<sizeof($content);$i++)
		{
			$key=key($content);
			if(is_array($content[$key]))
			{
				$iniContent.="[".$key."]\n";
				for($j=0;$j<sizeof($content[$key]);$j++)
				{
					$key2=key($content[$key]);
					$iniContent.=$key2."=\"".current($content[$key])."\"\n";
					next($content[$key]);
				}
			}
			else
			{
				$iniContent.=$key."=\"".current($content)."\"\n";
			}
			next($content);
		}
		fwrite($fp,$iniContent);
		fclose($fp);
	}
}
?>