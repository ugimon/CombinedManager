<?
/*
* 세션에 들어있는 로긴 정보를 가져옴
*/

class Lemon_Auth 
{
	public $rSession = '';

	function __construct()
	{
		$this->rSession = $_SESSION;
	}

	function getSn()
	{
		return $this->rSession['member']["sn"];
	}
	
	function getName()
	{
		return $this->rSession['member']["name"];
	}
	
	function getId()
	{
		return $this->rSession['member']["id"];
	}
	
	function getLevel()
	{
		return $this->rSession['member']["level"];
	}
	
	function getState()
	{
		return $this->rSession['member']["state"];
	}

	function isLogin()
	{
		if($this->rSession['member']["id"]!='' && $this->rSession['member']["state"]!='S')
			return true;
		return false;
	}
	
	function getPartnerLevel()
	{
		return ($this->rSession['member']["parent_sn"]==0) ? 1:2;
	}
	
	public function isAdmin()
	{
		if($this->rSession['member']["level"]<=2)
			return true;
		else
			return false;
	}

	public function isSuper()
	{
		if($this->rSession['member']["level"]==1)
			return true;
		else
			return false;
	}	
	
	function getRate()
	{
		return $this->rSession['member']["rate"];
	}
}

?>