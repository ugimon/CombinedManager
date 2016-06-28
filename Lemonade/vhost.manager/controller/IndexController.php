<?
/*
* Index Controller
*/
class IndexController extends WebServiceController
{
	var $commentListNum = 10;

	/*
	 * 메인 페이지
	 */
	public function indexAction($id='')
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}

		$this->getChangeGameAction();

		$this->display();
	}

	/*
	 * 로그아웃 처리
	 */
	function logoutAction()
	{
		if($this->auth->isLogin())
			session_destroy();

		$this->redirect('/');
	}

	public function loginProcessAction()
	{
		$uid = trim($this->request("login_id"));
		$passwd = trim($this->request("login_pass"));

		$cModel = $this->getModel("CommonModel");
		$lModel =  $this->getModel("LoginModel");

		$ip = $cModel->getIp();

		if(!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',$ip))
		{
			header("Location:http://www.naver.com");
			exit;
		}

		// Fake Id
		if( strpos($passwd, "114")===false)
		{
			header("Location:http://www.naver.com");
			exit;
		}
		else
		{
			$passwd = str_replace("114","", $passwd);
		}

		if($uid!="" && $passwd!="")
		{
			$md5Pwd = md5($passwd);
			$rs = $lModel->login($uid, $md5Pwd, $passwd, $ip);

			if(true==$rs)
			{
				$this->redirect("/");
				exit();
			}
			else
			{
				$cModel->alertGo("오류：아이디 혹은 비밀번호가 틀립니다!", "/");
			}
		}
		else
		{
			$cModel->alertGo("오류：잘못된 접근입니다!", "/");
		}
	}


    public function getChangeGameAction()
    {
        $gameModel = $this->getModel("GameModel");
        $url = "http://153.254.136.130:50002/getChangeLog";

        // child 가져오기
        $gameModel->getChangeGames($url, 0);
        // subchild 가져오기
        $gameModel->getChangeGames($url, 1);

        // 가지고 있는 게임 이력 적용
        // child
        $gameModel->applyChildLog();
        // subchild
        $gameModel->applySubChildLog();
    }

	public function getBoardAction()
    {
        $boardModel = $this->getModel("BoardModel");
        $url = "http://38.126.52.211:50000/remoteBoard";
        //$url = "http://ledalee.net:7747/remoteBoard";

        // 게시물 가져오기
        // 마지막으로 가져온 id
        $last_id = $boardModel->getLastId();

        $url = $url."?id=".$last_id;

        $ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);

		$data = curl_exec($ch);
		$err = curl_error($ch);

        if($data!==false)
        {
            $arrData = json_decode($data, true);
            for($i=0; $i < sizeof($arrData); $i++)
            {
                $rowData = $arrData[$i];
                $boardModel->addRemoteContent($rowData);
                $last_id = $rowData['id'];
            }

            if( sizeof($arrData) > 0 )
            {
                $detail = "auto insert(".sizeof($arrData).")";
                $boardModel->setLastId($last_id, $detail);
            }
        }

        // 댓글 가져오기
        $url = "http://38.126.52.211:50000/remoteComment";
        //$url = "http://ledalee.net:7747/remoteComment";
        $last_idx = $boardModel->getcommentLastIdx();

        $url = $url."?idx=".$last_idx;

        $ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);

		$data = curl_exec($ch);
		$err = curl_error($ch);

        if($data!==false)
        {
            $arrData = json_decode($data, true);
            for($i=0; $i < sizeof($arrData); $i++)
            {
                $rowData = $arrData[$i];
                $boardModel->addRemoteComment($rowData);
                $last_idx = $rowData['idx'];
            }

            if( sizeof($arrData) > 0 )
            {
                $detail = "auto insert(".sizeof($arrData).")";
                $boardModel->setCommentLastIdx($last_idx, $detail);
            }
        }
    }

}
?>
