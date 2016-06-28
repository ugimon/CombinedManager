<?

/*
* WebService Controller
*/

class WebServiceController extends Lemon_Controller
{
	var $auth  = "";

	public function notfound()
	{
		echo "not found";
	}

	public function display()
	{
		$this->view->print_('index');
	}

	/*
	* 각 Controller별 Custom
	*/
	public function layoutDefine($type='')
	{
	}

	/*
	* 전체 틀 공통 포함 파일들
	*/
	public function commonDefine($type='')
	{
		//제한 IP
		$this->adminmemberchk();

		// 권한
		$this->setAuth();

		// 중복로그인 체크
		$this->doubleLoginCheck();

		$moneyModel = $this->getModel("MoneyModel");
		$cartModel = $this->getModel("CartModel");

		$todayChargeTotal = $moneyModel->getTodayTotalCharge();
		$todayExchangeTotal = $moneyModel->getTodayTotalExchange();
		$totalmemberMoney		= $moneyModel->totalmemberMoney();
		$totalmemberMileage	= $moneyModel->totalmemberMileage();


		$rsi = $cartModel->getTotalBetMoney('','','','', '', 0);
		$currentBetting = $rsi['total_betting'];

		$this->view->assign('today_charge', $todayChargeTotal);
		$this->view->assign('today_exchange', $todayExchangeTotal);
		$this->view->assign('totalmemberMoney', $totalmemberMoney);
		$this->view->assign('totalmemberMileage', $totalmemberMileage);
		$this->view->assign('currentBetting', $currentBetting);

		// 헤더/푸터
		$this->view->define("index","layout/layout.html");
		$this->view->define(array("header"=>"header/header.html", "top" => "header/top.html", "footer" => "footer/footer.html", "left" =>"left/left.html", "right" => "right/right.html", "right_normal" => "right/right_normal.html"));
	}

	/*
	* 팝업용 포함 파일들
	*/
	public function popupDefine($type='')
	{
		//제한 IP
		$this->adminmemberchk();

		// 권한
		$this->setAuth();

		// 중복로그인 체크
		$this->doubleLoginCheck();

		// 헤더/푸터
		$this->view->define("index","layout/layout.popup.html");
		$this->view->define(array("header"=>"header/header.html"));
	}

	/*
	* 전체 틀 공통 포함 파일들
	*/
	public function iframeDefine()
	{
		// 권한
		$this->setAuth();

		// 중복로그인 체크
		$this->doubleLoginCheck();

		$this->view->define('index', 'layout/layout.iframe.html');
		$this->view->define(array('header' => 'header/header.html', 'top' => 'header/top.html', 'footer' => 'footer/footer.html', 'left' =>'left/left.html'));

		$this->view->assign('footer', 'footer/footer.html');
	}

	function menuSetting($menu)
	{
		$this->view->assign('submenu', $menu);
	}

	/**
	 * 로그인 표시 처리
	 */
	public function setAuth()
	{
		$this->auth = Lemon_Instance::getObject("Lemon_Auth");

		$this->view->assign('sess_member_id', $this->auth->getId());
		$this->view->assign('sess_member_name', $this->auth->getName());
		$this->view->assign('sess_member_level', $this->auth->getLevel());
		$this->view->assign('sess_member_login', $this->auth->isLogin());
		$this->view->assign('sess_member_super', $this->auth->isSuper());
	}

	/*
	 * 중복 로그인 체크 - 세션삭제
	 */
	public function doubleLoginCheck()
	{
		if($this->auth->isLogin()){
			$model = Lemon_Instance::getObject("MemberModel", true);
			$rs = $model->getById($this->auth->getId());

			if($rs['uniqid']!=$_SESSION['member']['uniqid'])
			{
				//session_destroy();
				$this->redirect('/');
			}
		}
	}

	/*
	 * ajax 공통 헤더
	 */
	function setAjaxHeader()
	{
		header("Content-Type: text/html; charset=UTF-8");
		header("Cache-Control: no-cache");
		header("Pragma: no-cache");
	}

	/*
	** 파일 업로드
	*/
	public function getFile($upfile, $dbfile, $fsize='' )
	{
		if($_FILES[$upfile]["name"]!="")
		{
			$conf = Lemon_Configure::readConfig('path');

			$tmpfile = $conf[$upfile]."/". $dbfile ;
			if (file_exists($tmpfile))
			{
				@unlink ($tmpfile) ;
			}
			$file_name = $upfile .'_'. time(). '_'. rand(1000,9999) ;

			$fu = new Lemon_FileUpload($_FILES[$upfile], $conf[$upfile]."/". $file_name  );
			// setFile($srcFile,$saveFile) ;
			$fu->setOverwrite();
			$fu->upload();

			return $file_name.".".$fu->getFileExt() ;
			//$update['filenm'] = $_FILES["filenm"]["name"] ;
			//$update['files'] = str_replace( $_SERVER['DOCUMENT_ROOT'], "", $fu->getSaveFile() ) ; //
		}
		else
		{
			return $dbfile ;
		}
	}

	/* 권한 체크 */
	function loginPermit($level=6)
	{
		if(!$this->auth->isLogin())
		{
			$this->redirect('/logout');
			exit;
		}

		if ( $this->auth->getLevel() > $level )
		{
			throw new Lemon_ScriptException("w. 잘못된 접근입니다..");
			exit;
		}
	}

	/* model help function */
	function getModel($model)
	{
		return Lemon_Instance::getObject($model,true);
	}

	function request($param, $default='')
	{
		if($default!=''){
			return ($this->req->request($param))?$this->req->request($param):$default;
		}
		return $this->req->request($param);
	}

	function displayPage($total, $pageNum=10, $act='')
	{
		$page 	 = !($this->request('page'))?'1':intval($this->request('page'));
		$perpage = !($this->request('perpage'))?'30' :intval($this->request('perpage'));
		$pageMaker 	= Lemon_Instance::getObject("Lemon_Page");
		$pageMaker->setListNum($perpage);
		$pageMaker->setPageNum(10);
		$pageMaker->setPage($page, $total);
		$pagelist 	= $pageMaker->pageList($act);

		$this->view->assign('total', $total);
		$this->view->assign('perpage', $perpage);
		$this->view->assign('pagelist', $pagelist);
		$this->view->assign('page', $page);

		return $pageMaker;
	}

	function post($param)
	{
		return $this->req->post($param);
	}

	function request0($param, $default)
	{
		return ($this->req->request($param))?$this->req->request($param):$default;
	}

	function alertRedirect($message, $url)
	{
		$str = "<script language='javascript'>" ;
		$str = $str . "alert('" . $message . "');" ;
		$str = $str . "</script>";
		$str = $str . "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=".$url."'>";
		echo $str;
	}

	function alert0($str)
	{
		header("Content-Type: text/html; charset=UTF-8");
		echo "<script>alert('".$str."');</script>";
	}

	function echo0($strText)
	{
		echo $strText."<br>";
	}

	//관리자 IP 확인 후 실행
	function adminMemberChk()
	{
		$remoteIp = $_SERVER["HTTP_X_FORWARDED_FOR"];

		if(!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',$remoteIp))
		{
			session_destroy();
			header("Location:http://www.naver.com");
			exit;
		}

		$etc_model = $this->getModel("EtcModel");

		// MemoModel에 writeGroupMemo 함수에도 이 값이 동일하게 사용된다. 변경시 같이 변경
		if( 1!=$etc_model->is_admin_ip($remoteIp))
		{
			session_destroy();
			header("Location:http://www.naver.com");
			exit;
		}
	}

	public function loginAction()
	{
		$this->adminMemberChk();

		$this->view->define("index","login.html");
		$this->view->print_('index');
	}
}
?>
