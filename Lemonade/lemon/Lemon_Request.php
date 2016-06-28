<?php
class Lemon_Request 
{
	public function __construct()
	{
		 $this->xssClean();
	}

	public function escapeStr($str) 
	{
		$searchArr = array("\'","\"","&","<",">");
		$replaceArr = array("'","&quot;","&amp;","&lt;","&gt;");
		$str = str_replace($searchArr, $replaceArr, $str);
		
		return $str;
	}

	public function noAllowWordCheck($str)
	{
		// 관리자 예외사항 처리 - Begin
		$remoteIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
		
		if(!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',$remoteIp))
		{
			session_destroy();
			header("Location:http://www.google.com");
			exit;
		}
		
		$etc_model = Lemon_Instance::getObject("EtcModel",true);
		if( 1==$etc_model->is_admin_ip($remoteIp))
		{
			return;
		}
		// 관리자 예외사항 처리 - End
		
		if($str!='' && !is_numeric($str))
		{
			if(preg_match("/(insert.*into|delete.*from|update.*set|select.*from|embed|union.*select|substr|substring|lpad|rpad|group by|having|ifnull|conv|group_concat|ascii|\.swf|_allowScriptAccess)/i",$str,$match)!=0)
			{
				$log_model	= Lemon_Instance::getObject("HackingLogModel",true);
				$log_model->insert($_SESSION['member']["id"], "인젝션 공격", $str);
				
				session_destroy();
				header("Location:http://www.google.com");
				exit;
			}
		}
	}

	public function xssClean()
	{
		$xss = Lemon_Instance::getObject('Lemon_XSSCleaner');
		
		// GET 인자에 대해서 xss검사를 한다
		foreach($_GET as $k => $v)
		{
			if(preg_match("/[^a-zA-Z0-9\-\_]/",$k))
			{
				unset($_GET[$k]);
				continue;
			}

			$_GET[$k] = $this->escapeStr($xss->run($v));
			$this->noAllowWordCheck($_GET[$k]);
		}

		// POST 인자에 대해서 xss검사를 한다
		foreach($_POST as $k => $v)
		{
			// 배열일 경우 다시 루프를 돌리면서 확인한다.
			if(is_array($v))
			{
				foreach($v as $k2 => $v2)
				{
					if(trim($v2)!='')
					{
						$v[$k2] = trim($xss->run($v2));
						$this->noAllowWordCheck($v2);
					}
				}
			}
			else
			{
				$v = str_replace("　","",$v);
				if(trim($v)!='')
				{
					$v = trim($xss->run($v));
					$this->noAllowWordCheck($v);
				}
			}

			$_POST[$k] = $v;
		}
		
		//REQEUST 인자에 대해서 xss검사를 한다
		foreach($_REQUEST as $k => $v)
		{
			// 배열일 경우 다시 루프를 돌리면서 확인한다.
			if(is_array($v))
			{
				foreach($v as $k2 => $v2)
				{
					if(trim($v2)!='')
					{
						$v[$k2] = trim($xss->run($v2));
						$this->noAllowWordCheck($v2);
					}
				}
			}
			else
			{
				$v = str_replace("　","",$v);
				if(trim($v)!='')
				{
					$v = trim($xss->run($v));
					$this->noAllowWordCheck($v);
				}
			}
			
			$_REQUEST[$k] = $v;
		}

		// SERVER 인자에 대해서 xss검사를 한다
		foreach($_SERVER as $k => $v)
		{
			$_SERVER[$k] = $this->escapeStr($xss->run($v));
		}

		if($_GET['page']!='' && !is_numeric($_GET['page']))
		{
			throw new Lemon_ScriptException("올바른 페이지번호가 아닙니다");
			exit;
		}
	}

	/**
	 * POST / GET 파라미터 리턴
	 *
	 * @param $var 객체명
	 * @return 객체값
	 */
	public function getParameter($var) 
	{
		if (isset($_GET[$var])) return $_GET[$var];
		else if (isset($_POST[$var])) return $_POST[$var];
		else if (isset($_COOKIE[$var])) return $_COOKIE[$var];
		else return '';
	}
	
	public function request($var)
	{
		if (isset($_REQUEST[$var])) 
			return $_REQUEST[$var];
		else return '';
	}
	
	public function get($var)
	{
		if (isset($_GET[$var])) 
			return $_GET[$var];
		else return '';
	}
	
	public function post($var)
	{
		if (isset($_POST[$var])) 
			return $_POST[$var];
		else return '';
	}
	
	public function cookie($var)
	{
		if (isset($_COOKIE[$var])) 
			return $_COOKIE[$var];
		else return '';
	}

	/**
	 * 파라미터 체크
	 *
	 * @param $str 문자열
	 * @param $type 체크형식 (number : 숫자만허용, string : 숫자와영문자허용)
	 */
	public function checkParameter($str, $type='string') {
		$bCheck = false;
		switch($type) {
			case 'number' :
				$pattern = '/[^0-9]/';
				if (preg_match($pattern,$str) > 0)
					$bCheck = true;
				break;
			case 'string' :
				$pattern = '/([^a-z0-9:\-\/\.\,\(\)\_])/i';
				if (preg_match($pattern,$str) > 0)
					$bCheck = true;
				break;
			case 'strAll' :
				$pattern = '/[^\xa1-\xfea-z0-9:\-\/\.\,\(\)\_\s]/i';
				if (preg_match($pattern,$str) > 0)
					$bCheck = true;
				break;
		} //switch
		if ($bCheck)
			throw new Lemon_ScriptException("허용불가 단어가 포함되어 있습니다");
	}
	
	public function isNumberParameter($str) 
	{
		if($str=="")
			return 0;
			
		$pattern = '/[^0-9]/';
		if (preg_match($pattern,$str) > 0)
			return 0;
		
		return 1;
	}

	/**
	 * HTML 태그 삭제
	 *
	 * @param $str 태그 포함 문자열
	 * @return 문자열
	 */
	public function get_strip_tags($str) {
		$tags = array(
						"html", "head", "body", "title", "h1", "h2", "h3", "h4", "h5",
						"p", "br", "pre", "font", "hr", "img", "map", "ul", "ol", "menu",
						"dir", "dl", "center", "blockquote", "strong", "b", "em", "embed",
						"i", "kbd", "code", "tt", "body", "dfn", "cite", "samp", "var", "sub",
						"sup", "basepoint", "blink", "u", "a", "address", "table", "tr", "td",
						"nobr", "wbr", "form", "textarea", "input", "frameset", "noframes", "frame",
						"img", "div", "tbody", "span", "link", "script", "tont", "object", "param",
						"area", "iframe", "meta", "script", "style", "!embed", "li", "select",
						"marquee"
					);

		// 주석 제거
		$str = preg_replace("/<!--[\w\W]*-->/U","",$str);

		// 스크립트 제거
		$str = preg_replace("/<script [\w\W]+<\/script>/iU","",$str);

		if (preg_match_all("/<[\/]*([^>]*)[\/]*>/", $str, $match)) {
			for($i=0;$i<sizeof($match[1]);$i++){
				// 매치된 태그에 대해 공백 구분으로 나눈다 ex. font color='red'
				$tmp = explode(" ",$match[1][$i]);
				if (in_array(strtolower($tmp[0]),$tags)) {		// 태그어인지 검사
					if ($allow!="") {
						if (!in_array(strtolower($tmp[0]),$allow)) {	// 허용태그에 포함안된 태그이면 제거
							$str = preg_replace("/<[\/]*".$tmp[0]."[^>]*[\/]*>/i","", $str);
						}
					} else {
						$str = preg_replace("/<[\/]*".$tmp[0]."[^>]*[\/]*>/i","",$str);
					}
				}
			}
		}

		// &nbsp;, 2byte 공백문자 제거. 공백여러개는 공백한개로
		$str = str_replace(array("&nbsp;","　"), array(" "," "), $str);
		// 공백 문자 여러개 제거
		$str = preg_replace("/[ ]+/"," ",$str);
		// 공백제거후 리턴
		return trim($str);
	}
}
?>