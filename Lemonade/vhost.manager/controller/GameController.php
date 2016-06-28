<?
/*
* Index Controller
*/
class GameController extends WebServiceController
{

	var $commentListNum = 10;

	public function sadariAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/game/sadari.html");

		$model 	= $this->getModel("GameModel");
		$cModel = $this->getModel("CartModel");
		$leagueModel = $this->getModel("LeagueModel");

		$act  				= $this->request("act");
		$state  				= $this->request("state");
		$search					= $this->request("search");
		$perpage				= $this->request("perpage");
		$specialType		= 5;
		$categoryName 	= $this->request("categoryName");
		$gameType 			= $this->request("game_type");

		$sadariType 			= $this->request("sadari_type");

		$beginDate  		= $this->request('begin_date');
		$endDate 				= $this->request('end_date');
		$filterTeam			= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');
		$filterBettingTotal	= $this->request('filter_betting_total');

		if($act=='deadline_game')
		{
			$childSn = $this->request('child_sn'); //경기인텍스
			$model->modifyGameTime($childSn);
		}

		if($perpage=='') $perpage=50;

		$minBettingMoney='';
		if($filterBettingTotal!='')	$minBettingMoney=$filterBettingTotal*10000; /*만원단위*/

		if($filterTeam!='')
		{
			if($filterTeamType=='league')
			{
				$rs = $leagueModel->getListByLikeName($filterTeam);
				for($i=0; $i<sizeof($rs); ++$i)
				{
					$leagueSn[] = $rs[$i]['sn'];
				}
			}
			else if($filterTeamType=='home_team') {$homeTeam = Trim($filterTeam);}
			else if($filterTeamType=='away_team')	{$awayTeam = Trim($filterTeam);}
		}
		if($beginDate=="" || $endDate=="")
		{
			$beginDate 	= date("Y-m-d");
			$endDate		= date("Y-m-d",strtotime ("+1 days"));
		}

		if($sadariType != "0")
		{
			echo "bbb";

			if($sadariType == "1")
			{
				$homeTeam = "홀";
			}

			if($sadariType == "2")
			{
				$homeTeam = "3줄";
			}

			if($sadariType == "3")
			{
				$homeTeam = "좌측";
			}

			if($sadariType == "4")
			{
				$homeTeam = "좌3";
			}

			if($sadariType == "5")
			{
				$homeTeam = "우3";
			}
		}

		$page_act = "perpage=".$perpage."&state=".$state."&search=".$search."&special_type=5&categoryName=".$categoryName."&game_type=".$gameType."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_betting_total=".$filterBettingTotal;

		$bettingEnable = "";
		if($state=="20")
		{
			$filterState 		= "2";
			$bettingEnable 	= "1";
		}
		else if($state=="21")
		{
			$filterState = "2";
			$bettingEnable 	= "-1";
		}
		else
		{
			$filterState = $state;
		}

		$total = $model->getListTotal($filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);
		$pageMaker = $this->displayPage($total, $perpage, $page_act);
		$list = $model->getList($pageMaker->first, $pageMaker->listNum, $filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);

		for($i=0; $i<sizeof($list); ++$i)
		{
			$item = $cModel->getTeamTotalBetMoney($list[$i]['child_sn']);

			$list[$i]['home_total_betting'] = $item['home_total_betting'];
			$list[$i]['active_home_total_betting'] = $item['active_home_total_betting'];
			$list[$i]['home_count'] = $item['home_count'];

			$list[$i]['draw_total_betting'] = $item['draw_total_betting'];
			$list[$i]['active_draw_total_betting'] = $item['active_draw_total_betting'];
			$list[$i]['draw_count'] = $item['draw_count'];

			$list[$i]['away_total_betting'] = $item['away_total_betting'];
			$list[$i]['active_away_total_betting'] = $item['active_away_total_betting'];
			$list[$i]['away_count'] = $item['away_count'];

			$list[$i]['total_betting'] = $item['home_total_betting']+$item['draw_total_betting']+$item['away_total_betting'];
			$list[$i]['active_total_betting'] = $item['active_home_total_betting']+$item['active_draw_total_betting']+$item['active_away_total_betting'];
			$list[$i]['betting_count'] = $item['home_count']+$item['draw_count']+$item['away_count'];
		}

		$categoryList = $leagueModel->getCategoryList();

		$this->view->assign("special_type","$specialType");
		$this->view->assign("sadari_type","$sadariType");
		$this->view->assign("gameType",$gameType);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('filter_team', $filterTeam);
		$this->view->assign('filter_team_type', $filterTeamType);
		$this->view->assign('filter_betting_total', $filterBettingTotal);
		$this->view->assign("categoryName",$categoryName);
		$this->view->assign("categoryList",$categoryList);
		$this->view->assign("state",$state);
		$this->view->assign("top_list",$topList);
		$this->view->assign("list",$list);

		$this->display();
	}

	//▶ 게임설정
	public function gamelistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/game/game_list.html");

		$model 	= $this->getModel("GameModel");
		$cModel = $this->getModel("CartModel");
		$leagueModel = $this->getModel("LeagueModel");

		$act  				= $this->request("act");
		$state  				= $this->request("state");
		$search					= $this->request("search");
		$perpage				= $this->request("perpage");
		$specialType		= $this->request("special_type");
		$categoryName 	= $this->request("categoryName");
		$gameType 			= $this->request("game_type");

		$beginDate  		= $this->request('begin_date');
		$endDate 				= $this->request('end_date');
		$filterTeam			= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');
		$filterBettingTotal	= $this->request('filter_betting_total');

		if($act=='deadline_game')
		{
			$childSn = $this->request('child_sn'); //경기인텍스
			$model->modifyGameTime($childSn);
		}

		if($perpage=='') $perpage=50;

		$minBettingMoney='';
		if($filterBettingTotal!='')	$minBettingMoney=$filterBettingTotal*10000; /*만원단위*/

		if($filterTeam!='')
		{
			if($filterTeamType=='league')
			{
				$rs = $leagueModel->getListByLikeName($filterTeam);
				for($i=0; $i<sizeof($rs); ++$i)
				{
					$leagueSn[] = $rs[$i]['sn'];
				}
			}
			else if($filterTeamType=='home_team') {$homeTeam = Trim($filterTeam);}
			else if($filterTeamType=='away_team')	{$awayTeam = Trim($filterTeam);}
		}
		if($beginDate=="" || $endDate=="")
		{
			$beginDate 	= date("Y-m-d");
			$endDate		= date("Y-m-d",strtotime ("+1 days"));
		}

		$page_act = "perpage=".$perpage."&state=".$state."&search=".$search."&special_type=".$specialType."&categoryName=".$categoryName."&game_type=".$gameType."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_betting_total=".$filterBettingTotal;

		$bettingEnable = "";
		if($state=="20")
		{
			$filterState 		= "2";
			$bettingEnable 	= "1";
		}
		else if($state=="21")
		{
			$filterState = "2";
			$bettingEnable 	= "-1";
		}
		else
		{
			$filterState = $state;
		}

		$total = $model->getListTotal($filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);
		$pageMaker = $this->displayPage($total, $perpage, $page_act);
		$list = $model->getList($pageMaker->first, $pageMaker->listNum, $filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);

		for($i=0; $i<sizeof($list); ++$i)
		{
			$item = $cModel->getTeamTotalBetMoney($list[$i]['child_sn']);

			$list[$i]['home_total_betting'] = $item['home_total_betting'];
			$list[$i]['active_home_total_betting'] = $item['active_home_total_betting'];
			$list[$i]['home_count'] = $item['home_count'];

			$list[$i]['draw_total_betting'] = $item['draw_total_betting'];
			$list[$i]['active_draw_total_betting'] = $item['active_draw_total_betting'];
			$list[$i]['draw_count'] = $item['draw_count'];

			$list[$i]['away_total_betting'] = $item['away_total_betting'];
			$list[$i]['active_away_total_betting'] = $item['active_away_total_betting'];
			$list[$i]['away_count'] = $item['away_count'];

			$list[$i]['total_betting'] = $item['home_total_betting']+$item['draw_total_betting']+$item['away_total_betting'];
			$list[$i]['active_total_betting'] = $item['active_home_total_betting']+$item['active_draw_total_betting']+$item['active_away_total_betting'];
			$list[$i]['betting_count'] = $item['home_count']+$item['draw_count']+$item['away_count'];

		}

		$categoryList = $leagueModel->getCategoryList();

		$this->view->assign("special_type",$specialType);
		$this->view->assign("gameType",$gameType);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('filter_team', $filterTeam);
		$this->view->assign('filter_team_type', $filterTeamType);
		$this->view->assign('filter_betting_total', $filterBettingTotal);
		$this->view->assign("categoryName",$categoryName);
		$this->view->assign("categoryList",$categoryList);
		$this->view->assign("state",$state);
		$this->view->assign("top_list",$topList);
		$this->view->assign("list",$list);

		$this->display();
	}

	//▶ 게임설정
	public function configAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/game/config.html");

		$gameModel = $this->getModel("GameModel");

		$total = $gameModel->getParentTotal();
		$pageMaker = $this->displayPage($total, 10);
		$list = $gameModel->getParentList($pageMaker->first, $pageMaker->listNum);

		$this->view->assign("list",$list);

		$this->display();
	}

	//▶ 베팅 목록
	public function betlistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/game/betting_list.html");

		$model					= $this->getModel("GameModel");
		$cartModel				= $this->getModel("CartModel");
		$memberModel		= $this->getModel("MemberModel");
		$gameListModel 	= $this->getModel("GameListModel");

		$sel_result				= $this->request("sel_result");
		$mode 					= $this->request("mode");
		$activeBet				= $this->request("active_bet");
		$perpage				= $this->request("perpage");
		$selectKeyword		= $this->request("select_keyword");
		$keyword				= $this->request("keyword");
		$showDetail 			= $this->request("show_detail");
		$bettingNo 				= $this->request("betting_no");
		$begin_date 			= $this->request("begin_date");
		$end_date 				= $this->request("end_date");
		$type = $this->request("type");

		if($activeBet=='')
		{
			$activeBet = 0;
		}

		if($perpage=='')
		{
			$perpage = 30;
		}

		if($showDetail=='')
		{
			$showDetail = 0;
		}

		$where="";
		if($mode=="search")
		{
			if($sel_result==="0")
				$where.= " and a.result=0";
			elseif(($sel_result==="1"))
				$where.= " and a.result=1";
			elseif(($sel_result==="2"))
				$where.= " and a.result=2";

			if($keyword!="")
			{
				if($selectKeyword=="uid")
					$where.=" and c.uid like('%".$keyword."%') ";
				else if($selectKeyword=="nick")
					$where.=" and c.nick like('%".$keyword."%') ";
				else if($selectKeyword=="betting_no")
					$where.=" and a.betting_no like('%".$keyword."%') ";
			}
		}

		if($begin_date=="" || $end_date=="")
		{
			$begin_date = date("Y-m-d",strtotime ("-1 days"));
			$end_date = date("Y-m-d");
		}

		$page_act = "perpage=".$perpage."&sel_result=".$sel_result."&mode=".$mode."&active_bet=".$activeBet."&select_keyword=".$selectKeyword."&keyword=".$keyword."&show_detail=".$showDetail."&begin_date=".$begin_date."&end_date=".$end_date."&type=".$type;

		$where.=" and a.is_account=1 ";
		$where.=" and date(a.regdate) >= '".$begin_date."' and date(a.regdate) <= '".$end_date."' ";

		if($type=="")
		{
			$where.=" and (d.league_sn<>593 and d.league_sn<>594 and d.league_sn<>595 and d.league_sn<>596 and d.league_sn<>597 and d.league_sn<>505 and d.league_sn<>504 and d.league_sn<>503 and d.league_sn<>502 and d.league_sn<>501 and d.league_sn<>500 and d.league_sn<>584 and d.league_sn<>583 and d.league_sn<>570 and d.league_sn<>568 and d.league_sn<>569)";
		}
		else if($type=="ladder")
		{
			$where.=" and (d.league_sn=505 or d.league_sn=504 or d.league_sn=503 or d.league_sn=502 or d.league_sn=501)";
		}
		else if($type=="powerball")
		{
			$where.=" and (d.league_sn=584 or d.league_sn=583 or d.league_sn=570 or d.league_sn=569 or d.league_sn=568)";
		}
		else if($type=="snail_race")
		{
			$where.=" and (d.league_sn=500)";
		}
		else if($type=="daridari")
		{
			$where.=" and (d.league_sn >= 593 and d.league_sn <= 597)";
		}

		$total 			= $gameListModel->getAdminBettingListTotal("", $where);
		$pageMaker 	= $this->displayPage($total, $perpage, $page_act);
		$list 			= $gameListModel->getAdminBettingList("", $where, $pageMaker->first, $pageMaker->listNum);

		$sumList = $cartModel->getTotalBetMoney();

		$this->view->assign("type",$type);
		$this->view->assign("show_detail",$showDetail);
		$this->view->assign("select_keyword",$selectKeyword);
		$this->view->assign("keyword",$keyword);
		$this->view->assign("sel_result",$sel_result);
		$this->view->assign("active_bet",$activeBet);
		$this->view->assign("perpage",$perpage);
		$this->view->assign("betting_no",$bettingNo);
		$this->view->assign("list",$list);
		$this->view->assign("sumList",$sumList);
		$this->view->assign("begin_date",$begin_date);
		$this->view->assign("end_date",$end_date);

		$this->display();
	}


	//▶ 베팅 취소 목록
	public function betcancellistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/game/bet_cancel_list.html");

		$model 	= $this->getModel("GameModel");
		$cartModel = $this->getModel("CartModel");
		$memberModel = $this->getModel("MemberModel");
		$gameListModel 	= $this->getModel("GameListModel");
		$config_Model 	= $this->getModel("ConfigModel");

		$sel_result 		= $this->request("sel_result");
		$mode 					= $this->request("mode");
		$activeBet			= $this->request("active_bet");
		$perpage				= $this->request("perpage");
		$selectKeyword	= $this->request("select_keyword");
		$keyword				= $this->request("keyword");
		$showDetail 		= $this->request("show_detail");
		$bettingNo 			= $this->request("betting_no");
		$begin_date 			= $this->request("begin_date");
		$end_date 				= $this->request("end_date");

		//$config_Model->modifyAlramFlag("big_bet", "off");

		if($activeBet=='') 	{$activeBet = 0;}
		if($perpage=='') 		{$perpage = 30;}
		if($showDetail=='') {$showDetail = 1;}

		$where="";
		if($mode=="search")
		{
			if($sel_result==="0") 			$where.= " and a.result=0";
			elseif(($sel_result==="1"))	$where.= " and a.result=1";
			elseif(($sel_result==="2"))	$where.= " and a.result=2";

			if($keyword!="")
			{
				if($selectKeyword=="uid")							$where.=" and c.uid like('%".$keyword."%') ";
				else if($selectKeyword=="nick")				$where.=" and c.nick like('%".$keyword."%') ";
				else if($selectKeyword=="name")				$where.=" and c.name like('%".$keyword."%') ";
				else if($selectKeyword=="betting_no")	$where.=" and a.betting_no like('%".$keyword."%') ";
			}
		}

		if($begin_date=="" || $end_date=="")
		{
			$begin_date		= date("Y-m-d",strtotime ("-1 days"));
			$end_date		= date("Y-m-d");
		}

		$page_act = "perpage=".$perpage."&sel_result=".$sel_result."&mode=".$mode."&active_bet=".$activeBet."&select_keyword=".$selectKeyword."&keyword=".$keyword."&show_detail=".$showDetail."&begin_date=".$begin_date."&end_date=".$end_date;

		$where.=" and a.is_account=1 ";

		$where.=" and date(a.regdate) >= '".$begin_date."' and date(a.regdate) <= '".$end_date."' ";

		$total 			= $gameListModel->getAdminBettingCancelListTotal("", $where);
		$pageMaker 	= $this->displayPage($total, $perpage, $page_act);
		$list 			= $gameListModel->getAdminBettingCancelList("", $where, $pageMaker->first, $pageMaker->listNum);

		$sumList = $cartModel->getTotalBetMoney();

		$this->view->assign("show_detail",$showDetail);
		$this->view->assign("select_keyword",$selectKeyword);
		$this->view->assign("keyword",$keyword);
		$this->view->assign("sel_result",$sel_result);
		$this->view->assign("active_bet",$activeBet);
		$this->view->assign("perpage",$perpage);
		$this->view->assign("betting_no",$bettingNo);
		$this->view->assign("list",$list);
		$this->view->assign("sumList",$sumList);
		$this->view->assign("begin_date",$begin_date);
		$this->view->assign("end_date",$end_date);

		$this->display();
	}


	//▶ 베팅리스트-적특처리
	public function exceptionBetProcessAction()
	{
		$sn = $this->request("sn");
		$cartModel  = $this->getModel("CartModel");

		$cartModel->modifyExceptionBet($sn);

		$url = "/game/betlist";
		throw new Lemon_ScriptException("처리되었습니다.", "", "go", $url);
		exit;
	}

	//▶ 베팅 취소
	public function betcancelProcessAction()
	{
		$perpage					= $this->request("perpage");
		$select_keyword			= $this->request("select_keyword");
		$keyword					= $this->request("keyword");
		$page							= $this->request("page");
		$show_detail				= $this->request("show_detail");
		$sel_result					= $this->request("sel_result");
		$mode							= $this->request("mode");


		if(!strpos($_SESSION["quanxian"],"1002"))
		{
			throw new Lemon_ScriptException("해당 권한이 제한되었습니다");
			exit();
		}
		$pModel  = $this->getModel("ProcessModel");

		$bettingNo 	= $this->request("betting_no");
		$oper		= $this->request("oper");
		$check_date = $this->request("check_date");

		if (strtotime(date("Y-m-d H:i"))-strtotime($check_date)>0){
			throw new Lemon_ScriptException("시작된 경기가 포함된 배팅입니다.");
			exit();
		}

		if($oper=="race")
		{
			$url = "/game/betlist?perpage=".$perpage."&select_keyword=".$select_keyword."&keyword=".$keyword."&page=".$page."&show_detail=".$show_detail."&sel_result=".$sel_result."&mode=search";
			//"&active_bet=".$activeBet.;
		}

		$pModel->bettingCancelProcess($bettingNo);

		$this->alertRedirect("삭제 되었습니다", $url);
	}

	//▶ 팝업 베팅 목록
	public function popup_bet_listAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/game/popup_bet_list.html");

		$model 	= $this->getModel("GameModel");
		$cartModel = $this->getModel("CartModel");
		$memberModel = $this->getModel("MemberModel");
		$gameListModel = $this->getModel("GameListModel");

		$childSn				= $this->request("child_sn");
		$selectNo				= $this->request("select_no");
		$sel_result 		= $this->request("sel_result");
		$mode 					= $this->request("mode");
		$activeBet			= $this->request("active_bet");
		$perpage				= $this->request("page_size");
		$selectKeyword	= $this->request("select_keyword");
		$keyword				= $this->request("keyword");
		$showDetail 		= $this->request("show_detail");
		$bettingNo 			= $this->request("betting_no");

		if($childSn=="")
			exit();

		if($activeBet=='') 	$activeBet = 0;
		if($perpage=='') 		$perpage = 30;
		if($showDetail=='') $showDetail = 0;

		$where="";
		if($mode=="search")
		{
			switch($sel_result)
			{
				case 0: $where = " and a.result='0'"; break;
				case 1: $where = " and a.result='1'"; break;
				case 2: $where = " and a.result='2'"; break;
				case 9: $where=""; break;
			}

			if($keyword!="")
			{
				if($selectKeyword=="uid")							$where.=" and e.uid like('%".$keyword."%') ";
				else if($selectKeyword=="nick")				$where.=" and e.nick like('%".$keyword."%') ";
				else if($selectKeyword=="betting_no")	$where.=" and a.betting_no like('%".$keyword."%') ";
			}
		}

		$page_act = "perpage=".$perpage."&sel_result=".$sel_result."&mode=".$mode."&active_bet=".$activeBet."&select_keyword=".$selectKeyword."&keyword=".$keyword."&show_detail=".$showDetail."&child_sn=".$childSn."&select_no=".$selectNo;

		$total 			= $gameListModel->getGameSnBettingListTotal($where, $activeBet, $childSn, $selectNo);
		$pageMaker 	= $this->displayPage($total, $perpage, $page_act);
		$list 			= $gameListModel->getGameSnBettingList($where, $pageMaker->first, $pageMaker->listNum, $activeBet, $childSn, $selectNo);

		$this->view->assign("child_sn",$childSn);
		$this->view->assign("select_no",$selectNo);
		$this->view->assign("show_detail",$showDetail);
		$this->view->assign("select_keyword",$selectKeyword);
		$this->view->assign("keyword",$keyword);
		$this->view->assign("sel_result",$sel_result);
		$this->view->assign("active_bet",$activeBet);
		$this->view->assign("perpage",$perpage);
		$this->view->assign("betting_no",$bettingNo);
		$this->view->assign("list",$list);
		//$this->view->assign("sumList",$sumList);

		$this->display();
	}

	//▶ 유저의 배팅내역
	public function popup_betdetailAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/game/popup.bet_detail.html");

		$model 	= Lemon_Instance::getObject("GameModel", true);
		$cModel = Lemon_Instance::getObject("CartModel", true);

		$betting_no = $this->request("betting_no");
		$member_sn = $this->request("member_sn");

		$list = $cModel->getMemberBetDetailList($betting_no, $member_sn);

		$this->view->assign("list",$list);


		$this->display();
	}

	//▶ 게임 디테일 항목
	public function popup_gamedetailAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/game/popup.game_detail.html");

		$model 	= Lemon_Instance::getObject("GameModel", true);
		$cModel 	= Lemon_Instance::getObject("CartModel", true);


		$child_sn = $this->request("child_sn");


		$rs = $cModel->getBetByChildSn($child_sn);

		$home_team = $rs[0]["home_team"];
		$away_team = $rs[0]["away_team"];

		for( $i = 0; $i< sizeof($rs); ++$i )
		{
				$gameselect = $rs[$i]["select_no"];
				$game_type = $rs[$i]["game_type"];
				$money = $rs[$i]["bet_money"];

				if($game_type==1)
				{
					if($gameselect==1)
					{
						$home_bet_1=$home_bet_1+$money;
					}elseif($gameselect==2)
					{
						$away_bet_1=$away_bet_1+$money;
					}elseif($gameselect==3)
					{
						$draw_bet=$draw_bet_1+$money;
					}
				}elseif($game_type==2)
				{
					if($gameselect==1)
					{
						$home_bet_2=$home_bet_2+$money;
					}elseif($gameselect==2)
					{
						$away_bet_2=$away_bet_2+$money;
					}
				}elseif($game_type==4)
				{
					if($gameselect==1)
					{
						$home_bet_4=$home_bet_4+$money;
					}elseif($gameselect==2)
					{
						$away_bet_4=$away_bet_4+$money;
					}
				}
		}

		$line_1 = $home_bet_1 + $draw_bet + $away_bet_1;
		$line_2 = $home_bet_2 + $away_bet_2;
		$line_3 = $home_bet_3 + $away_bet_3;
		$line_4 = $home_bet_4 + $away_bet_4;

		$t_bet_1 = $home_bet_1 + $home_bet_2 + $home_bet_3 + $home_bet_4;
		$t_bet_2 = $away_bet_1 + $away_bet_2 + $away_bet_3 + $away_bet_4;

		$total = $line_1 + $line_2 + $line_3 + $line_4;

		$this->view->assign("home_team",$home_team);
		$this->view->assign("away_team",$away_team);

		$this->view->assign("home_bet_1",$home_bet_1);
		$this->view->assign("draw_bet",$draw_bet);
		$this->view->assign("away_bet_1",$away_bet_1);
		$this->view->assign("line_1",$line_1);

		$this->view->assign("home_bet_2",$home_bet_2);
		$this->view->assign("away_bet_2",$away_bet_2);
		$this->view->assign("line_2",$line_2);

		$this->view->assign("home_bet_4",$home_bet_4);
		$this->view->assign("away_bet_4",$away_bet_4);
		$this->view->assign("line_4",$line_2);


		$this->view->assign("t_bet_1",$t_bet_1);
		$this->view->assign("draw_bet",$draw_bet);
		$this->view->assign("t_bet_2",$t_bet_2);
		$this->view->assign("total",$total);

		$this->display();
	}

	//▶ 배당 수정
	function modifyrateAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/game/popup.modify_rate.html");
		$model = $this->getModel("GameModel");

		$idx 	= $this->request("idx");
		$gametype 	= $this->request("gametype");
		$mode 	= $this->request("mode");

		if($mode == "") {$mode = "add";}

		$item = $model->getChildRow($idx);
		$leagueName = $model->getRow('name', $model->db_qz.'league', 'sn='.$item[league_sn]);
		$item['league_name']=$leagueName['name'];

		$rs = $model->getSubChildRows($idx);
		if( sizeof($rs) > 0 )
		{
			$home_rate = $rs[0]['home_rate'];
			$draw_rate = $rs[0]['draw_rate'];
			$away_rate = $rs[0]['away_rate'];
		}

        // 핸디/언오버인 경우 다른 타입의 경기도 함께 보여준다 2015-09-30
        if($gametype == 2)
            $otherType = 4;
        else if($gametype == 4)
            $otherType = 2;
        else
            $otherType = 0;

        //
        if($otherType != 0) {
            $other = $model->getAnotherTypeChild($item[gameDate], $item[gameHour], $item[gameTime]
                                                , $item[league_sn], $item[home_team], $item[away_team], $otherType);
            if(sizeof($other) > 0)
            {
                $otherSn = $other[0]['sn'];
                $otherRs = $model->getSubChildRows($otherSn);
                if(sizeof($otherRs) > 0) {
                    $other_home_rate = $otherRs[0]['home_rate'];
                    $other_draw_rate = $otherRs[0]['draw_rate'];
                    $other_away_rate = $otherRs[0]['away_rate'];
                }
            }
        }

		$strMode	= "";
		$html 		= "";
		//$add		= "onkeyup='this.value=this.value.replace(/[^0-9.]/gi,\"\")'";
		$add = "";
		if($gametype==1)
		{
			if($mode=="update") {$strMode="disabled";}
			$html="<td bgcolor='#ffffff' align='left'>";
			$html=$html."&nbsp;&nbsp;승<input type='text' name='home_rate' size='4' value='".$home_rate."' ".$add.">";
			$html=$html."&nbsp;무<input type='text' name='draw_rate' size='4' value='".$draw_rate."' ".$add.">";
			$html=$html."&nbsp;패<input type='text' name='away_rate' size='4' value='".$away_rate."' ".$add.">";
			$html=$html."&nbsp;&nbsp;<FONT color=#006699>&#8226; 승/패경기일때는 무승부에 1.00 을 넣으세요 </font></td>" ;
		}
		elseif($gametype==2)
		{
			if($mode=="edit") {$strMode="disabled";}
			$html="<td bgcolor='#ffffff' align='left'>";
			$html=$html."&nbsp;&nbsp;홈팀<input type='text' name='home_rate' size='4' value='".$home_rate ."' ".$add.">";
			$html=$html."&nbsp;홈핸디<input type='text' name='draw_rate' size='4' value='".$draw_rate ."' >";
			$html=$html."&nbsp;원정팀<input type='text' name='away_rate' size='4' value='".$away_rate ."' ".$add.">";
			$html=$html."&nbsp;&nbsp;</td>";

            if($otherType != 0) {
                $otherHtml="<td bgcolor='#ffffff' align='left'>";
			    $otherHtml=$otherHtml."&nbsp;&nbsp;오버<input type='text' name='other_home_rate' size='4' value='".$other_home_rate."' ".$add.">";
			    $otherHtml=$otherHtml."&nbsp;기준점수<input type='text' name='other_draw_rate' size='4' value='".$other_draw_rate."' >";
			    $otherHtml=$otherHtml."&nbsp;언더<input type='text' name='other_away_rate' size='4' value='".$other_away_rate."' ".$add.">";
			    $otherHtml=$otherHtml."&nbsp;&nbsp;</td>";
            }
		}
		elseif($gametype==4)
		{
			if($mode=="edit") {$strMode="disabled";}
			$html="<td bgcolor='#ffffff' align='left'>";
			$html=$html."&nbsp;&nbsp;오버<input type='text' name='home_rate' size='4' value='".$home_rate."' ".$add.">";
			$html=$html."&nbsp;기준점수<input type='text' name='draw_rate' size='4' value='".$draw_rate."' >";
			$html=$html."&nbsp;언더<input type='text' name='away_rate' size='4' value='".$away_rate."' ".$add.">";
			$html=$html."&nbsp;&nbsp;</td>";

            if($otherType != 0) {
                $otherHtml="<td bgcolor='#ffffff' align='left'>";
			    $otherHtml=$otherHtml."&nbsp;&nbsp;홈팀<input type='text' name='other_home_rate' size='4' value='".$other_home_rate."' ".$add.">";
			    $otherHtml=$otherHtml."&nbsp;홈핸디<input type='text' name='other_draw_rate' size='4' value='".$other_draw_rate."' >";
			    $otherHtml=$otherHtml."&nbsp;원정팀<input type='text' name='other_away_rate' size='4' value='".$other_away_rate."' ".$add.">";
			    $otherHtml=$otherHtml."&nbsp;&nbsp;</td>";
            }
		}

		$this->view->assign("idx",$idx);

		$this->view->assign("mode",$mode);
		$this->view->assign("gametype",$gametype);
		$this->view->assign("item",$item);
		$this->view->assign("html",$html);

        $this->view->assign("otherSn", $otherSn);
        $this->view->assign("otherType", $otherType);
        $this->view->assign("otherHtml", $otherHtml);

		$this->view->assign("strMode",$strMode);



		$this->display();
	}

	//▶ 경기 배당수정 처리
	function rateProcessAction()
	{
		$model 		= $this->getModel("GameModel");
		$childIdx = $this->request("idx");
		$gametype	= $this->request("gametype");
		$home_rate 		= $this->request("home_rate");
		$draw_rate 		= $this->request("draw_rate");
		$away_rate 		= $this->request("away_rate");
		$gameDate 		= $this->request("gameDate");
		$gameHour 		= $this->request("gameHour");
		$gameTime 		= $this->request("gameTime");

        $otherSn = $this->request("otherSn");
        $otherType = $this->request("otherType");
        $other_home_rate = $this->request("other_home_rate");
        $other_draw_rate = $this->request("other_draw_rate");
        $other_away_rate = $this->request("other_away_rate");

		$model->modifyChildRate($childIdx,$gametype,$home_rate,$draw_rate,$away_rate);
		$model->modifyChildRate_Date($childIdx,$gameDate,$gameHour,$gameTime);

        if($otherSn != '' && $otherSn != 0) {
            $model->modifyChildRate($otherSn,$otherType,$other_home_rate,$other_draw_rate,$other_away_rate);
		    $model->modifyChildRate_Date($otherSn,$gameDate,$gameHour,$gameTime);
        }

		echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script>alert('  수정되었습니다   ');opener.document.location.reload(); self.close();</script>";
	}

}
?>
