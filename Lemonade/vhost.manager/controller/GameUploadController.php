<?
/*
* Index Controller
*/
class GameUploadController extends WebServiceController 
{

	var $commentListNum = 10;
	
	//▶ 인덱스
	public function indexAction()
	{
		$this->gamelistAction();
	}
	
	//▶ 게임목록
	public function gamelistAction()
	{
		$this->commonDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/game_list.html");
		
		$gameModel		= $this->getModel("GameModel");
		$cartModel 		= $this->getModel("CartModel");
		$leagueModel 	= $this->getModel("LeagueModel");
		
		$act  				= $this->request("act");
		$state  			= $this->request("state");
		$perpage			= $this->request("perpage");
		$specialType	= $this->request("special_type");
		$categoryName = $this->request("categoryName");
		$gameType 		= $this->request("game_type");
		
		$moneyOption = $this->request("money_option");
		$beginDate  		= $this->request('begin_date');
		$endDate 				= $this->request('end_date');
		$filterTeam			= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');
		
		if($act=="modify_state")
		{
			$childSn = $this->request('child_sn'); //경기인텍스
			$newState= $this->request('new_state');
			$gameModel->modifyChildStaus($childSn,$newState);
		}
		else if($act=="delete_game")
		{
			$childSn = $this->request('child_sn');
			$gameModel->delChild($childSn);
		}
		else if($act=='deadline_game')
		{
			$childSn = $this->request('child_sn');
			$gameModel->modifyGameTime($childSn);
		}
		
		if($perpage=='') $perpage = 30;	
		if($moneyOption=='') $moneyOption=0;
		
		$minBettingMoney='';
		if($moneyOption==0)		$minBettingMoney='';
		if($moneyOption==1)		$minBettingMoney=1;
		
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
			else if($filterTeamType=='home_team')
			{
				$homeTeam = Trim($filterTeam);
			}
			else if($filterTeamType=='away_team')
			{
				$awayTeam = Trim($filterTeam);
			}
		}
		if($beginDate=="" || $endDate=="")
		{
			$beginDate 	= date("Y-m-d");
			$endDate		= date("Y-m-d",strtotime ("+1 days"));
		}
		
		$page_act= "state=".$state."&game_type=".$gameType."&categoryName=".$categoryName."&special_type=".$specialType."&perpage=".$perpage."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_team=".$filterTeam."&filter_betting_total=".$filterBettingTotal."&money_option=".$moneyOption;
		
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
			
		$total 			= $gameModel->getListTotal($filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);
		$pageMaker 	= $this->displayPage($total, $perpage, $page_act);
		$list 			= $gameModel->getList($pageMaker->first, $pageMaker->listNum, $filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);
		
		
		$categoryList = $leagueModel->getCategoryList();
		
		$this->view->assign("special_type",$specialType);
		$this->view->assign("money_option",$moneyOption);
		$this->view->assign("gameType",$gameType);
		$this->view->assign("categoryName",$categoryName);
		$this->view->assign("categoryList",$categoryList);
		$this->view->assign("search",$search);
		$this->view->assign("state",$state);
		$this->view->assign("list",$list);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('filter_team', $filterTeam);
		$this->view->assign('filter_team_type', $filterTeamType);
		
		$this->display();
	}
	
	/*
	//▶ 경기결과 입력후 당첨자 현황
	public function popup_win_member_listAction()
	{		
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/gameUpload/popup_win_member_list.html");
		
		$gameListModel 	= $this->getModel("GameListModel");
		$processModel 	= $this->getModel("ProcessModel");
		
		$accountParam		 = $this->request("account_param");
		$gameSnListParam = $this->request("game_sn_list");
	
		if($accountParam!="")
			$accountList = unserialize(urldecode($accountParam));
		if($gameSnListParam!="")
			$gameSnList = unserialize(urldecode($gameSnListParam));
			
		$act			 			= $this->request("act");
		$selectKeyword	= $this->request("select_keyword");
		$keyword				= $this->request("keyword");
		$showDetail 		= $this->request("show_detail");
		
		if($showDetail=="") $showDetail = 0;
		
		if($keyword!="")
		{
			if($selectKeyword=="uid")							$where.=" and c.uid like('%".$keyword."%') ";
			else if($selectKeyword=="nick")				$where.=" and c.nick like('%".$keyword."%') ";
			else if($selectKeyword=="betting_no")	$where.=" and a.betting_no like('%".$keyword."%') ";
		}
		
		if($act=="account")
		{
			for($i=0; $i<sizeof($gameSnList); ++$i)
			{
				$childSn = $gameSnList[$i];
				if($childSn!="")
				{
					$processModel->accountMoneyProcess($childSn);
				}
			}
			
			throw new Lemon_ScriptException("","","script","alert('정산되었습니다.');opener.form1.account_param='';opener.document.location.reload();self.close();");
			exit();
		}

		$this->view->assign("game_sn_list", $gameSnListParam);
		$this->view->assign("account_param", $accountParam);
		$this->view->assign("account_list", $accountList);
		$this->view->assign("keyword", $keyword);
		$this->view->assign("show_detail", $showDetail);
		$this->view->assign("select_keyword", $selectKeyword);
		$this->display();
	}	
	*/
	
	//▶ 경기결과 입력후 당첨자 현황
	public function popup_win_member_listAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/gameUpload/popup_win_member_list.html");
		
		$gameListModel 	= $this->getModel("GameListModel");
		$processModel 	= $this->getModel("ProcessModel");
		
		
		$accountParam		 = $this->request("account_param");
		$gameSnListParam = $this->request("game_sn_list");
	
		if($accountParam!="")
			$accountList = unserialize(urldecode($accountParam));
		if($gameSnListParam!="")
			$gameSnList = unserialize(urldecode($gameSnListParam));
			
		$act			 			= $this->request("act");
		$selectKeyword	= $this->request("select_keyword");
		$keyword				= $this->request("keyword");
		$showDetail 		= $this->request("show_detail");
		$paramPage_act	= $this->request("param_page_act");
		
		
		if($showDetail=="") $showDetail = 0;
		
		if($keyword!="")
		{
			if($selectKeyword=="uid")							$where.=" and c.uid like('%".$keyword."%') ";
			else if($selectKeyword=="nick")				$where.=" and c.nick like('%".$keyword."%') ";
			else if($selectKeyword=="betting_no")	$where.=" and a.betting_no like('%".$keyword."%') ";
		}
		
		if($act=="account")
		{
			for($i=0; $i<sizeof($gameSnList); ++$i)
			{
				$childSn = $gameSnList[$i]["child_sn"];
				$homeScore = $gameSnList[$i]["home_score"];
				$awayScore = $gameSnList[$i]["away_score"];
				$gameCancel = $gameSnList[$i]["is_cancel"];
				if($childSn!="")
				{
					$processModel->resultGameProcess($childSn, $homeScore, $awayScore, $gameCancel);
				}
			}
			
			$script	= "alert('정산되었습니다.');opener.document.location='/gameUpload/result_list".$paramPage_act."'";

			throw new Lemon_ScriptException("","","script",$script.";self.close();");
			exit();
		}

		$this->view->assign("game_sn_list", $gameSnListParam);
		$this->view->assign("account_param", $accountParam);
		$this->view->assign("account_list", $accountList);
		$this->view->assign("keyword", $keyword);
		$this->view->assign("show_detail", $showDetail);
		$this->view->assign("select_keyword", $selectKeyword);
		$this->view->assign("param_page_act", $paramPage_act);
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
		$this->view->define("content","content/gameUpload/bet_list.html");
		
		$model 	= $this->getModel("GameModel");
		$cartModel = $this->getModel("CartModel");
		$memberModel = $this->getModel("MemberModel");
		
		$bettingNo		= $this->request("betting_no");
		$sel_result 	= $this->request("sel_result");
		$mode 			= $this->request("mode");
		$activeBet		= $this->request("active_bet");
		$perpage		= $this->request("perpage");
		$selectKeyword	= $this->request("select_keyword");
		$keyword		= $this->request("keyword");
		$showDetail = $this->request("show_detail");
		
		if($activeBet=='') 	{$activeBet = 0;}
		if($perpage=='') 		{$perpage = 30;}
		if($showDetail=='') {$showDetail = 0;}
		
		$where="";
		if($mode=="search")
		{
			switch($sel_result)
			{
				case 0: $where = " and result='0'"; break;
				case 1: $where = " and result='1'"; break;
				case 2: $where = " and result='2'"; break;
				case 9: $where=""; break;
			}
			if($selectKeyword=='uid' && $keyword!='')
			{
				$memberSn = $memberModel->getSn($keyword);
				if($memberSn!='')
					$where.=" and member_sn=".$memberSn." ";
			}
			else if($selectKeyword=='nick' && $keyword!='')
			{
				$member = $memberModel->getByName($keyword);
				if(sizeof($member)>0)
					$where.=" and member_sn=".$member['sn']." ";
			}
		}
		if(!is_null($bettingNo) && $bettingNo!="")
		{
			$where.= " and betting_no='".$bettingNo."'";
		}
		
		$page_act = "mode=".$mode."&sel_result=".$sel_result."&show_detail=".$showDetail."&active_bet=".$activeBet."&perpage=".$perpage;
		$total 		= $cartModel->getBettingListTotal($where);
		$pageMaker 	= $this->displayPage($total, $perpage, $page_act);
		$list 		= $cartModel->getBettingList($where, $pageMaker->first, $pageMaker->listNum, $activeBet);
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

		$this->display();
	}
	
	//▶ 게임마감
	/*
	public function result_listAction()
	{
		$this->commonDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/result_list.html");
		
		$model 				= $this->getModel("GameModel");
		$cartModel 			= $this->getModel("CartModel");
		$processModel = $this->getModel("ProcessModel");
		$leagueModel 	= $this->getModel("LeagueModel");
		
		$act  				= $this->request("act");
		$state  			= $this->request("state");
		$perpage			= $this->request("perpage");
		$specialType	= $this->request("special_type");
		$gameType			= $this->request("game_type");
		$categoryName	= $this->request("categoryName");
		$moneyOption 	= $this->request("money_option");
		$beginDate  	= $this->request('begin_date');
		$endDate 			= $this->request('end_date');
		$filterTeam		= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');
		
		if($perpage=='') $perpage = 30;
		
		if($moneyOption=='') $moneyOption=0;
		
		$minBettingMoney='';
		if($moneyOption==0)		$minBettingMoney='';
		if($moneyOption==1)		$minBettingMoney=1;
		
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
			else if($filterTeamType=='home_team')
			{
				$homeTeam = Trim($filterTeam);
			}
			else if($filterTeamType=='away_team')
			{
				$awayTeam = Trim($filterTeam);
			}
		}
		if($beginDate=="" || $endDate=="")
		{
			$beginDate 	= date("Y-m-d");
			$endDate		= date("Y-m-d",strtotime ("+1 days"));
		}
		$page_act= "filter_team=".$filterTeam."&state=".$state."&game_type=".$gameType."&categoryName=".$categoryName."&special_type=".$specialType."&perpage=".$perpage."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_betting_total=".$filterBettingTotal."&money_option=".$moneyOption;
		
		if($act=="modify")
		{
			$arrayChildSn 	= $this->request("y_id");
			$arrayHomeRate 	= $this->request("home_rate");
			$arrayDrawRate 	= $this->request("draw_rate");
			$arrayAwayRate 	= $this->request("away_rate");
			$arrayHomeScore	= $this->request("home_score");
			$arrayAwayScore	= $this->request("away_score");
			
			for($i=0;$i<sizeof($arrayChildSn);++$i)
			{
				$isCancel	 = $arrayCancel[$i];
				$childSn 	 = $arrayChildSn[$i];
				$homeRate	 = $arrayHomeRate[$childSn];
				$drawRate	 = $arrayDrawRate[$childSn];
				$awayRate	 = $arrayAwayRate[$childSn];
				$homeScore = $arrayHomeScore[$childSn];
				$awayScore = $arrayAwayScore[$childSn];
				
				if($homeScore!="" && $awayScore!="")
				{
					$set =	"home_score='".$homeScore."',";  
					$set.=	"away_score='".$awayScore."'";
				
					$where = " sn=".$childSn;
					$model->modifyChild($set, $where);
				}
				
				if($homeRate!="" && $awayRate!="")
				{
					$set="";
					$where="";
					$set = "home_rate = '".$homeRate."',";  
					$set.= "draw_rate = '".$drawRate."',";
					$set.= "away_rate = '".$awayRate."'";
					
					$where = " child_sn=".$childSn;
					$model->modifySubChild($set, $where);
				}
			}
		}
		//게임정산
		else if($act=="modify_result")
		{
			$arrayChildSn 	= $this->request("y_id");
			$arrayHomeScore	= $this->request("home_score");
			$arrayAwayScore	= $this->request("away_score");
			$arrayCancel		= $this->request("check_cancel");
			$arrayType			= $this->request("game_types");
			$arrayDrawRate 	= $this->request("draw_rate");
			
			//checkbox의 경우 체크된 항목만 넘어오지만 "text"와 그외의 속성들은 모든 배열값들이 넘어온다.
			//그래서 Key값을 배열이름으로 사용하여 해당되는 내용만 전송하게 한다.
			for($i=0;$i<sizeof($arrayChildSn);++$i)
			{
				$isCancel	 = $arrayCancel[$i];
				$childSn 	 = $arrayChildSn[$i];
				$gameType	 = $arrayType[$childSn];
				$homeScore = $arrayHomeScore[$childSn];
				$awayScore = $arrayAwayScore[$childSn];
				$drawRate	 = $arrayDrawRate[$childSn];
				
				if($isCancel==0 && ($homeScore=="" || $awayScore==""))
				{
					throw new Lemon_ScriptException("스코어가 등록되지 않아 중지합니다.");
					exit;
				}
				
				if($isCancel==1)
				{
					//취소일 경우 0:0으로 셋팅(의미는 없음)
					$homeScore = 0;
					$awayScore = 0;
					$winTeam = "Cancel";
					
					$set="";
					$where="";
					
					$set .=	"home_score=".$homeScore."," ;
					$set .=	"away_score=".$awayScore;
							
					if($gameType==1 or $gameType==4)	$set .= ",win_team='Cancel'";
					else if($gameType==2)							$set .= ",handi_winner = 'Cancel'";
								
					$where = " sn=".$childSn;
					
					
					$model->modifyChild($set, $where);
					$processModel->resultGameProcess($childSn, $homeScore, $awayScore, $winTeam, "Cancel");
				}
				else
				{
					if($gameType==1)
					{
						if($homeScore==$awayScore)
						{
							if($drawRate=="1.00")		$winTeam = 'Cancel';
							else										$winTeam = 'Draw';
						}
						else if($homeScore > $awayScore){$winTeam = 'Home';}
						else														{$winTeam = 'Away';}
					}
					else if($gameType==2)
					{
						if($homeScore+$drawRate > $awayScore) 			{$winTeam = 'Home';}
						else if($homeScore+$drawRate < $awayScore) 	{$winTeam = 'Away';}
						else																				{$winTeam = 'Cancel';}
						
					}
					else if($gameType==4)
					{
						if($homeScore+$awayScore > $drawRate) 			{$winTeam = 'Home';}
						else if($homeScore+$awayScore < $drawRate) 	{$winTeam = 'Away';}
						else																				{$winTeam = 'Cancel';}
					}
					
					$set="";
					$where="";
					
					$set .=	"home_score=".$homeScore."," ;
					$set .=	"away_score=".$awayScore;
							
					if($gameType==1 or $gameType==4)	$set .= ",win_team='".$winTeam."'";
					else if($gameType==2)							$set .= ",handi_winner='".$winTeam."'";
								
					$where = " sn=".$childSn;
							
					$model->modifyChild($set, $where);
					$processModel->resultGameProcess($childSn, $homeScore, $awayScore, $winTeam);
				}
			}
		}
		
		$categoryName = $this->request("categoryName");
		$gameType 		= $this->request("game_type");
			
		$total 				= $model->getListTotal($state, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam);
		$pageMaker 		= $this->displayPage($total, $perpage, $page_act);
		$list 				= $model->getList($pageMaker->first, $pageMaker->listNum, $state, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam);
		
		$categoryList = $leagueModel->getCategoryList();
		
		$this->view->assign("special_type",$specialType);
		$this->view->assign("money_option",$moneyOption);
		$this->view->assign("gameType",$gameType);
		$this->view->assign("categoryName",$categoryName);
		$this->view->assign("categoryList",$categoryList);
		$this->view->assign("state",$state);
		$this->view->assign("list",$list);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('filter_team', $filterTeam);
		$this->view->assign('filter_team_type', $filterTeamType);
		
		$this->display();
	}
	*/
	
	//▶ 게임마감
	public function result_listAction()
	{
		$this->commonDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/result_list.html");
		
		$model 				= $this->getModel("GameModel");
		$cartModel 		= $this->getModel("CartModel");
		$processModel = $this->getModel("ProcessModel");
		$leagueModel 	= $this->getModel("LeagueModel");
		
		$act  				= $this->request("act");
		$perpage			= $this->request("perpage");
		$specialType	= $this->request("special_type");
		$gameType			= $this->request("game_type");
		$categoryName	= $this->request("categoryName");
		$beginDate  	= $this->request('begin_date');
		$endDate 			= $this->request('end_date');
		$filterTeam		= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');
		$state				= $this->request('state');
		
		$currentPage	= !($this->request('page'))?'1':intval($this->request('page'));
		
		if($state=="") $state=22;
		
		if($state==21)
		{
			$bettingEnable=1;
			$filterState=2;
		}
		else if($state==22)
		{
			$bettingEnable=-1;
			$filterState=2;
		}
		else if($state==3)
		{
			$bettingEnable=1;
			$filterState=4;
		}
		
		//$state = 2; // 발매만 처리
		
		if($perpage=='') $perpage = 30;
		
		$minBettingMoney='';
		
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
			else if($filterTeamType=='home_team')
				$homeTeam = Trim($filterTeam);

			else if($filterTeamType=='away_team')
				$awayTeam = Trim($filterTeam);
		}
		if($beginDate=="" || $endDate=="")
		{
			$beginDate 	= date("Y-m-d",strtotime ("-1 days"));
			$endDate		= date("Y-m-d",strtotime ("+1 days"));
		}
		$page_act= "filter_team=".$filterTeam."&state=".$state."&game_type=".$gameType."&categoryName=".$categoryName."&special_type=".$specialType."&perpage=".$perpage."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_betting_total=".$filterBettingTotal;
		
		if($act=="modify")
		{
			$arrayChildSn 	= $this->request("y_id");
			$arrayHomeRate 	= $this->request("home_rate");
			$arrayDrawRate 	= $this->request("draw_rate");
			$arrayAwayRate 	= $this->request("away_rate");
			$arrayHomeScore	= $this->request("home_score");
			$arrayAwayScore	= $this->request("away_score");
			
			for($i=0;$i<sizeof($arrayChildSn);++$i)
			{
				$isCancel	 = $arrayCancel[$i];
				$childSn 	 = $arrayChildSn[$i];
				$homeRate	 = $arrayHomeRate[$childSn];
				$drawRate	 = $arrayDrawRate[$childSn];
				$awayRate	 = $arrayAwayRate[$childSn];
				$homeScore = $arrayHomeScore[$childSn];
				$awayScore = $arrayAwayScore[$childSn];
				
				if($homeScore!="" && $awayScore!="")
				{
					$set =	"home_score='".$homeScore."',";  
					$set.=	"away_score='".$awayScore."'";
				
					$where = " sn=".$childSn;
					$model->modifyChild($set, $where);
				}
				
				if($homeRate!="" && $awayRate!="")
				{
					$set="";
					$where="";
					$set = "home_rate = '".$homeRate."',";  
					$set.= "draw_rate = '".$drawRate."',";
					$set.= "away_rate = '".$awayRate."'";
					
					$where = " child_sn=".$childSn;
					$model->modifySubChild($set, $where);
				}
			}
		}
		/*
		//게임정산
		else if($act=="modify_result")
		{
			$arrayChildSn 	= $this->request("y_id");
			$arrayHomeScore	= $this->request("home_score");
			$arrayAwayScore	= $this->request("away_score");
			$arrayCancel		= $this->request("check_cancel");
			$arrayType			= $this->request("game_types");
			$arrayDrawRate 	= $this->request("draw_rate");
			
			//checkbox의 경우 체크된 항목만 넘어오지만 "text"와 그외의 속성들은 모든 배열값들이 넘어온다.
			//그래서 Key값을 배열이름으로 사용하여 해당되는 내용만 전송하게 한다.
			for($i=0;$i<sizeof($arrayChildSn);++$i)
			{
				$isCancel	 = $arrayCancel[$i];
				$childSn 	 = $arrayChildSn[$i];
				$homeScore = $arrayHomeScore[$childSn];
				$awayScore = $arrayAwayScore[$childSn];
				
				$childRs = $model->getChildRow($childSn, '*');
				if($childRs['kubun']==1)
				{
					throw new Lemon_ScriptException("이미 처리된 게임이 포함되어 있습니다.");
					exit;
				}
				
				if($isCancel==0 && ($homeScore=="" || $awayScore==""))
				{
					throw new Lemon_ScriptException("스코어가 등록되지 않아 중지합니다.");
					exit;
				}
			}
			
			$data = array();
			for($i=0;$i<sizeof($arrayChildSn);++$i)
			{
				$isCancel	 = $arrayCancel[$i];
				$childSn 	 = $arrayChildSn[$i];
				$homeScore = $arrayHomeScore[$childSn];
				$awayScore = $arrayAwayScore[$childSn];
				
				$temp = $processModel->resultPreviewProcess($childSn, $homeScore, $awayScore, $isCancel);
				
				if(sizeof($temp)>0)
				{
					if(sizeof($data)<=0)
						$data = $temp;
					else
						$data = array_merge($data, $temp);

					$gameSnList[] = array("child_sn" => $childSn, "home_score" => $homeScore, "away_score" => $awayScore, "is_cancel" => $isCancel);
				}
				//배팅내역이 없다면 바로 정산처리
				else
				{
					$processModel->resultGameProcess($childSn, $homeScore, $awayScore, $isCancel);
				}
			}// end of for
		}
		*/
		
		//게임정산
		else if($act=="modify_result")
		{
			$arrayChildSn 	= $this->request("y_id");
			$arrayHomeScore	= $this->request("home_score");
			$arrayAwayScore	= $this->request("away_score");
			$arrayCancel		= $this->request("check_cancel");
			$arrayType			= $this->request("game_types");
			$arrayDrawRate 	= $this->request("draw_rate");
			
			//checkbox의 경우 체크된 항목만 넘어오지만 "text"와 그외의 속성들은 모든 배열값들이 넘어온다.
			//그래서 Key값을 배열이름으로 사용하여 해당되는 내용만 전송하게 한다.
			for($i=0;$i<sizeof($arrayChildSn);++$i)
			{
				$isCancel	 = $arrayCancel[$i];
				$childSn 	 = $arrayChildSn[$i];
				$homeScore = $arrayHomeScore[$childSn];
				$awayScore = $arrayAwayScore[$childSn];
				
				$childRs = $model->getChildRow($childSn, '*');
				if($childRs['kubun']==1)
				{
					throw new Lemon_ScriptException("이미 처리된 게임이 포함되어 있습니다.");
					exit;
				}
				
				if($isCancel==0 && ($homeScore=="" || $awayScore==""))
				{
					throw new Lemon_ScriptException("스코어가 등록되지 않아 중지합니다.");
					exit;
				}
			}
			
			$data 				= array();
			$betData 			= array();
			
			for($i=0;$i<sizeof($arrayChildSn);++$i)
			{
				$isCancel	 = $arrayCancel[$i];
				$childSn 	 = $arrayChildSn[$i];
				$homeScore = $arrayHomeScore[$childSn];
				$awayScore = $arrayAwayScore[$childSn];
				
				$dataArray = $processModel->resultPreviewProcess($childSn, $homeScore, $awayScore, $isCancel, $betData);
				
				$list_temp 		= $dataArray["list"];
				$betData_temp = $dataArray["betData"];
				
				if(sizeof($list_temp)>0)
				{
					if(sizeof($data)<=0)
						$data = $list_temp;
					else
						$data = array_merge($data, $list_temp);
				}
					
				if(sizeof($betData)<=0)
					$betData = $betData_temp;
				else
					$betData = array_merge($betData, $betData_temp);
					
				$gameSnList[] = array("child_sn" => $childSn, "home_score" => $homeScore, "away_score" => $awayScore, "is_cancel" => $isCancel);

			}// end of for
		}
		
		$categoryName = $this->request("categoryName");
		$gameType 		= $this->request("game_type");

		$total 				= $model->getListTotal($filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);
		$pageMaker 		= $this->displayPage($total, $perpage, $page_act);
		$list 				= $model->getList($pageMaker->first, $pageMaker->listNum, $filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);
		$categoryList = $leagueModel->getCategoryList();
		
		$paramPage_act = "?page=".$currentPage."&".$page_act;
		
		$this->view->assign("special_type", $specialType);
		$this->view->assign("gameType", $gameType);
		$this->view->assign("categoryName", $categoryName);
		$this->view->assign("categoryList", $categoryList);
		$this->view->assign("state", $state);
		$this->view->assign("list", $list);
		$this->view->assign("param_page_act", $paramPage_act);
		/*
		if(sizeof($data)>0)
		{
			$this->view->assign("account_param", urlencode(serialize($data)));
			$this->view->assign("game_sn_list", urlencode(serialize($gameSnList)));
		}
		*/
		
		if($act=="modify_result")
		{
			$this->view->assign("account_param", urlencode(serialize($data)));
			$this->view->assign("game_sn_list", urlencode(serialize($gameSnList)));
		}
		
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('filter_team', $filterTeam);
		$this->view->assign('filter_team_type', $filterTeamType);
		
		$this->display();
	}
	
	//▶ 게임복사
	public function game_copy_listAction()
	{
		$this->popupDefine();
		//$this->commonDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/game_copy_list.html");
		
		$mode								= $this->request("mode");
		$keyword						= $this->request("keyword");
		$selector						= $this->request("selector");
		$keywordCategory		= $this->request("keyword_category");
		$checkboxes					= $this->request("checkboxes");
		$beginDate					= $this->request("begin_date");
		$endDate					= $this->request("end_date");
		
		if($beginDate=="") $beginDate=date("Y-m-d");
		if($endDate=="") 	$endDate	= date("Y-m-d",strtotime ("+1 days"));
		
		$handicapAwayRate  = $handicapHomeRate = $this->request("handicap_rate");
		$underoverAwayRate = $underoverHomeRate = $this->request("underover_rate");
		$specialHandicapAwayRate = $specialHandicapHomeRate = $this->request("special_handicap_rate");
		$specialUnderoverAwayRate = $specialUnderoverHomeRate = $this->request("special_underover_rate");
		$normal_special_away_rate = $normal_special_home_rate = $this->request("normal_special_rate");
		
		
		$gameListModel 				= $this->getModel("GameListModel");
		$gameModel 				= $this->getModel("GameModel");
		$leagueModel 				= $this->getModel("LeagueModel");
	
		if($mode=="copy")
		{
			$arrayChildSn 	= $this->request("child_sn");
			$arrayGameDate 	= $this->request("game_date");
			$arrayCategory 	= $this->request("category");
			$arrayLeagueSn 	= $this->request("league_sn");
			$arrayHomeTeam	= $this->request("home_team");
			$arrayAwayTeam	= $this->request("away_team");
			$arrayHomeRate	= $this->request("home_rate");
			$arrayDrawRate	= $this->request("draw_rate");
			$arrayAwayRate	= $this->request("away_rate");
			
			for($i=0;$i<sizeof($arrayChildSn);++$i)
			{
				$childSn 	  = $arrayChildSn[$i];
				$date	 	  = $arrayGameDate[$childSn];
				$category	  = $arrayCategory[$childSn];
				$leagueSn	  = $arrayLeagueSn[$childSn];
				$homeTeam	  = $arrayHomeTeam[$childSn];
				$awayTeam 	= $arrayAwayTeam[$childSn];
				$homeRate  = $arrayHomeRate[$childSn];
				$drawRate 	= $arrayDrawRate[$childSn];
				$awayRate 	= $arrayAwayRate[$childSn];
				
				$gameDate = substr($date, 0, 10);
				$gameHour = substr($date, 11, 2);
				$gameTime = substr($date, 14, 2);

				//일반,스페셜,멀티,고액,이벤트(4가지) 확인
				//0=일반, 1=스페셜, 2=멀티, 3=고액
				
				//핸디캡
				if($checkboxes[0]==1)
				{
					$gameModel->addChild('',$category,$leagueSn,$homeTeam,$awayTeam,$gameDate,$gameHour,$gameTime,'','null',2,0,$handicapHomeRate,'',$handicapAwayRate);
				}
				//언더오버
				if($checkboxes[1]==1)
				{				
					$gameModel->addChild('',$category,$leagueSn,$homeTeam,$awayTeam,$gameDate,$gameHour,$gameTime,'','null',4,0,$underoverHomeRate,'',$underoverAwayRate);
				}
				//핸디캡(스패셜)
				if($checkboxes[2]==1)
				{
					$gameModel->addChild('',$category,$leagueSn,$homeTeam,$awayTeam,$gameDate,$gameHour,$gameTime,'','null',2,1,$specialHandicapHomeRate,'',$specialHandicapAwayRate);
				}
				//언더오버(스패셜)
				if($checkboxes[3]==1)
				{
					$gameModel->addChild('',$category,$leagueSn,$homeTeam,$awayTeam,$gameDate,$gameHour,$gameTime,'','null',4,1,$specialUnderoverHomeRate,'',$specialUnderoverAwayRate);
				}
				//스페셜 - 스패셜
				if($checkboxes[4]==1)
				{
					$gameModel->addChild('',$category,$leagueSn,$homeTeam,$awayTeam,$gameDate,$gameHour,$gameTime,'','null',1,1,$normal_special_home_rate,'',$normal_special_away_rate);
				}
			}
			throw new Lemon_ScriptException("","","script","alert('복사되었습니다.');opener.document.location.reload(); self.close();");
		} // end of if($mode=="copy")
	
		if($selector=='league') {$keywordLeage = $keyword;}
		else if($selector=='home_team') {$keywordHomeTeam = $keyword;}
		else if($selector=='away_team') {$keywordAwayTeam = $keyword;}
		$list = $gameListModel->getCopyGameList($keywordCategory, $keywordLeage,  $keywordHomeTeam, $keywordAwayTeam, $beginDate, $endDate);
		$categoryList = $leagueModel->getCategoryList();
		
		$this->view->assign("begin_date",$beginDate);
		$this->view->assign("end_date",$endDate);
		$this->view->assign("keyword",$keyword);
		$this->view->assign("selector",$selector);
		$this->view->assign("keyword_category",$keywordCategory);
		$this->view->assign("category_list",$categoryList);
		$this->view->assign("list",$list);
		
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
		$this->view->define("content","content/gameUpload/popup.bet_detail.html");
		
		$model 	= Lemon_Instance::getObject("GameModel", true);
		$cartModel = Lemon_Instance::getObject("CartModel", true);
		
		$betting_no = $this->request("betting_no");
		$member_sn = $this->request("member_sn");
		
		$list = $cartModel->getMemberBetDetailList($betting_no, $member_sn);
		
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
		$this->view->define("content","content/gameUpload/popup.game_detail.html");
		
		$model 	= Lemon_Instance::getObject("GameModel", true);
		$cartModel 	= Lemon_Instance::getObject("CartModel", true);
		
		
		$child_sn = $this->request("child_sn");
		
		$rs = $cartModel->getBetByChildSn($child_sn);
		
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
				}
				elseif($gameselect==2)
				{
					$away_bet_1=$away_bet_1+$money;
				}
				elseif($gameselect==3)
				{
					$draw_bet=$draw_bet_1+$money;
				}
			}
			else if($game_type==2)
			{
				if($gameselect==1) 			{$home_bet_2=$home_bet_2+$money;}
				elseif($gameselect==2)	{$away_bet_2=$away_bet_2+$money;}
			}
			elseif($game_type==4)
			{
				if($gameselect==1)
				{
					$home_bet_4=$home_bet_4+$money;
				}
				elseif($gameselect==2)
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
	
	//▶ 새회차 등록
	public function popup_addparentAction()
	{
		$this->popupDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/popup.add_parent.html");
		
		$model  = $this->getModel("GameModel");
		
		$parentIdx 	= $this->request("intParentIdx");
		$gameType 	= $this->request("strGameType");
		$mode 		= $this->request("mode");
		
		if($parentIdx!="")
		{
			$item = $model->getParentRow($parentIdx);
			
			$minBetPrice = $item['intMinBetPrice'];
			$maxBetPrice = $item['intMaxBetPrice'];
			$maxBetPrice = 3000000; 
			$gameEndTime = $item['strGameEndTime'];
			$gameDate	 = substr($gameEndTime,0,10);
			$temp		 = explode(":",(substr($gameEndTime,11)));
			$gameHour	 = Trim($temp[0]);
			$gameMin	 = Trim($temp[1]);
		}
		else
		{
			$lastIdx 	 = $model->getLastParentIdx();
			$parentIdx 	 = $lastIdx+1;
			$minBetPrice = 5000;
			$maxBetPrice = 3000000; 
		}
		
		$hours = array();
		for($i=0; $i<24; ++$i)
		{
			if($i<10) 	{$hour = '0'.$i;}
			else		{$hour = $i;}
			$hours[] = $hour;
		}
		
		$mins = array();
		for($i=0; $i<60; ++$i)
		{
			if($i<10) 	$min = '0'.$i;
			else		$min = $i;
			
			$mins[] = $min;
		}
		
		$this->view->assign("minBetPrice",$minBetPrice);
		$this->view->assign("maxBetPrice",$maxBetPrice);
		$this->view->assign("gameDate",$gameDate);
		$this->view->assign("gameHour",$gameHour);
		$this->view->assign("gameMin",$gameMin);
		$this->view->assign("hours",$hours);
		$this->view->assign("mins" ,$mins);
		$this->view->assign("mode",$mode);
		$this->view->assign("gameType",$gameType);
		$this->view->assign("parentIdx",$parentIdx);
		
		$this->display();
	}
	
	//▶ 새회차 등록
	public function addparentProcessAction()
	{
		$model  = $this->getModel("GameModel");
		
		$parentIdx 			= $this->request("intParentIdx");
		$gameEndTime 		= $this->request("strGameEndTime1") . " " . $this->request("strGameEndTime2") . ":" . $this->request("strGameEndTime3");
		$strGameType 		= $this->request("strGameType");
		$intMinBetPrice 	= $this->request("intMinBetPrice");
		$intMinBetPrice		= str_replace(",","",$intMinBetPrice);
		$intMaxBetPrice 	= $this->request("intMaxBetPrice");
		$intMaxBetPrice 	= str_replace(",","",$intMaxBetPrice);
		$intOverPrice 		= $this->request("intOverPrice");
		$backUrl 			= $this->request("backUrl");
		
		if(!preg_match('/^\d*$/',$intMaxBetPrice))
		{
			throw new Lemon_ScriptException("최소 배팅금액에 문제가 있습니다.");
			exit;
		}
		if(!preg_match('/^\d*$/',$intMinBetPrice))
		{
			throw new Lemon_ScriptException("최대 배당금액에 문제가 있습니다.");			
			exit;
		}
		
		if(is_null($intOverPrice) or $intOverPrice=="") 	{$intOverPrice = 0;}
		if(is_null($intMaxBetPrice) or $intMaxBetPrice=="")	{$intMaxBetPrice = 0;}
		$mode = Trim($this->request("mode"));
	
		if($mode == "add")
		{
			$rs = $model->addParent($gameEndTime, $intMinBetPrice, $intMaxBetPrice, $intOverPrice);
			
			if($rs>0) 	{throw new Lemon_ScriptException("","","script","alert('새 회차가 등록 되었습니다');opener.document.location.reload(); self.close();");}
			else		{throw new Lemon_ScriptException("","","script","alert('추가 실패 하였습니다!');opener.document.location.reload(); self.close();");}		
		}
		else
		{
			$rs = $model->modifyParent($parentIdx, $gameEndTime, $intMinBetPrice, $intMaxBetPrice, $intOverPrice);
			if($rs>0)	{throw new Lemon_ScriptException("","","script","alert('수정되었습니다.');opener.document.location.reload(); self.close();");}
			else		{throw new Lemon_ScriptException("","","script","alert('수정 실패 하였습니다!');opener.document.location.reload(); self.close();");}
		}
	}
	
	//▶ 데이터 수집
	public function collect7mAction()
	{
		$this->popupDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/collect7m.html");
		
		$model 	= $this->getModel("GameModel");
		$cartModel = $this->getModel("CollectModel");
		$leagueModel = $this->getModel("LeagueModel");

		$bianliang1 = $this->request("bianliang1");
		if($bianliang1=="")	{$bianliang1="0.00";}
		
		$bianliang2 = $this->request("bianliang2");
		if($bianliang2=="")	{$bianliang2="0.00";}
		
		$bianliang3 = $this->request("bianliang3");
		if($bianliang3=="")	{$bianliang3="0.00";}
	
		
		$strTime1 	= $this->request("strtime1");
		if($strTime1=="") $strTime1=date('Y-m-d');
		$mode 		= $this->request("mode");
	
		if($mode=="collect")
		{
			//$url="http://1x2.7m.cn/data/index_kr.js";
			$url="http://1x2.7m.cn/data/company/kr/66.js";
			$domain = "1x2.7m.cn";			
			//$strst		= $this->get_fsock_data($domain,"/data/index_kr.js");
			$strst		= $this->get_fsock_data($domain,"/data/company/kr/66.js");
		
			$var1		= strpos($strst,"[")."<br>";
			$var2		= strrpos($strst,"]")."<br>";
			$var		= $var2-$var1."<br>";
			$content	= substr($strst,$var1+1,$var-1);
			$arrContent	= explode('"',$content);			
			$cartModel->del();
			$rs = $leagueModel->getListAll();
			
			$leagueList="";
			
			for($i=0; $i<sizeof($rs); ++$i)
			{
				$leagueList.= $rs[$i]["name"];
			}
			
			for($i=1;$i<sizeof($arrContent);$i=$i+2)
			{
				$arrSubContent = explode("|",$arrContent[$i]);
					
				$arrTempDate = explode("," ,$arrSubContent[1]);
				$gameDate=date("Y-m-d H:i:s",mktime(trim($arrTempDate[3])+1,trim($arrTempDate[4]),trim($arrTempDate[5]),trim($arrTempDate[1]),trim($arrTempDate[2]),$arrTempDate[0]));
		
				$b_rate1=0;
				$b_rate2=0;
				$b_rate3=0;
				$strHomeName="";
				if(Trim($arrSubContent[11])!="")	{$b_rate1=Trim($arrSubContent[11]);}
				if(Trim($arrSubContent[12])!="")	{$b_rate2=Trim($arrSubContent[12]);}
				if(Trim($arrSubContent[13])!="")	{$b_rate3=Trim($arrSubContent[13]);}
				if(Trim($arrSubContent[14])==1)		{$strHomeName="(N)";}
							
				$cartModel->add($gameDate, $arrSubContent, $b_rate1, $b_rate2, $b_rate3);
			}
		}
		else if($mode=="search")
		{
			$searchList = $cartModel->getList("game_date='".$strTime1."'");
					
			$strDay=date('w',strtotime($strTime1));
			switch($strDay)
			{
				case 0: $week = "(일)";break;
				case 1: $week = "(월)";break;
				case 2: $week = "(화)";break;
				case 3: $week = "(수)";break;
				case 4: $week = "(목)";break;
				case 5: $week = "(금)";break;
				case 6: $week = "(토)";break;	
			}
			$this->view->assign("searchList",$searchList);
			$this->view->assign("week",$week);
		}
		
		$lastCollectTime="";
		$rs = $cartModel->getLastDate("collect_date"); 
		$total = sizeof($rs);
		if($total>0) {$lastCollectTime = $rs[0]["collect_date"];}
		
		$list = $cartModel->getList("", "game_date");
	
		$this->view->assign("mode",$mode);
		$this->view->assign("bianliang1",$bianliang1);		
		$this->view->assign("bianliang2",$bianliang2);		
		$this->view->assign("bianliang3",$bianliang3);		
		$this->view->assign("total",$total);
		$this->view->assign("strTime1",$strTime1);
		$this->view->assign("lastCollectTime",$lastCollectTime);
		$this->view->assign("list",$list);
		$this->display();
	}
	
	//▶ 데이터 수집
	public function upload7mAction()
	{
		$this->popupDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/upload7m.html");
		$model 	= $this->getModel("GameModel");
		$leagueModel = $this->getModel("LeagueModel");
		
		$gameType    = $this->request("gametype");
		$idx         = $this->request("idx");
		$game_num    = $this->request("game_num");
		$kubun       = $this->request("kubun");
		$league_num  = $this->request("league_num");
		$league_name = $this->request("league_name");
		$game_date   = $this->request("game_date");
		$game_hours  = $this->request("game_hours");
		$game_minute = $this->request("game_minute");
		$game_second = $this->request("game_second");
		$team1_name  = $this->request("team1_name");
		$a_rate1     = $this->request("a_rate1");
		$a_rate2     = $this->request("a_rate2");
		$a_rate3     = $this->request("a_rate3");
		$b_rate1     = $this->request("b_rate1");
		$b_rate2     = $this->request("b_rate2");
		$b_rate3     = $this->request("b_rate3");
		$c_rate1     = $this->request("c_rate1");
		$c_rate2     = $this->request("c_rate2");
		$c_rate3     = $this->request("c_rate3");
		$team2_name  = $this->request("team2_name");
		$chk_idx     = $this->request("chk_idx");
		
		$arr_select_rate=array();
		$isrep=true;
		/*
		switch($gameType)
		{
			case 1: $type=1; $special=0; break;
			case 2: $type=2; $special=0; break;
			case 4: $type=4; $special=0; break;
			case 6: $type=1; $special=1; break;
			case 7: $type=2; $special=1; break;
			case 8: $type=4; $special=1; break;
		}
		*/

		echo var_dump($gameType).".</br>";
		
		for($i=0;$i<count($idx);$i++)
		{
			$arr_select_rate[$i] = $this->request("radio_".trim($idx[$i]));
		}
		
		for($i=0;$i<count($idx);$i++)
		{
			$idx_temp         = Trim($idx[$i]);
			$game_num_temp    = Trim($game_num[$i]);
			$league_num_temp  = Trim($league_num[$i]);
			$league_name_temp = Trim($league_name[$i]);
			$game_date_temp   = Trim($game_date[$i]);
			$game_hours_temp  = Trim($game_hours[$i]);
			$game_minute_temp = Trim($game_minute[$i]);
	
			$team1_name_temp  = Trim($team1_name[$i]);
			$team1_name_temp  = str_replace("'","",$team1_name_temp);
			$a_rate1_temp     = Trim($a_rate1[$i]);
			$a_rate2_temp     = Trim($a_rate2[$i]);
			$a_rate3_temp     = Trim($a_rate3[$i]);
			$b_rate1_temp     = Trim($b_rate1[$i]);
			$b_rate2_temp     = Trim($b_rate2[$i]);
			$b_rate3_temp     = Trim($b_rate3[$i]);
			$c_rate1_temp     = Trim($c_rate1[$i]);
			$c_rate2_temp     = Trim($c_rate2[$i]);
			$c_rate3_temp     = Trim($c_rate3[$i]);
			$team2_name_temp  = Trim($team2_name[$i]);
			$team2_name_temp  = str_replace("'","",$team2_name_temp);
	
			$select_rate_temp = Trim($arr_select_rate[$i]);
			
			if($select_rate_temp==1)
			{
				$rate1_temp = $a_rate1_temp;
				$rate2_temp = $a_rate2_temp;
				$rate3_temp = $a_rate3_temp;
			}
			else if($select_rate_temp==2)
			{
				$rate1_temp = $b_rate1_temp;
				$rate2_temp = $b_rate2_temp;
				$rate3_temp = $b_rate3_temp;
			}
			else if($select_rate_temp==3)
			{
				$rate1_temp = $c_rate1_temp;
				$rate2_temp = $c_rate2_temp;
				$rate3_temp = $c_rate3_temp;
			}

			$leagueSn = "";
			if($this->find_array_str($chk_idx, $idx_temp))
			{
				$where = "name='".$league_name_temp ."'";
				$rs = $leagueModel->getListAll($where);
				
				if(sizeof($rs) > 0)
				{
					$leagueName 	= $rs[0]["name"];
					$leagueSn 		= $rs[0]["sn"];
				}
				else
				{
					//등록된 리그가 없다면, 별칭을 검색한다.
					$where = "alias_name='".$league_name_temp ."'";
					$rs = $leagueModel->getListAll($where);
					if(sizeof($rs) > 0)
					{
						$leagueName 	= $rs[0]["name"];
						$leagueSn 		= $rs[0]["sn"];
					}
					else
					{
						$message .= "\\n".$league_name_temp;
					}
				}
				
				if($kubun=="") {$kubun='null';}
				
				
				if($leagueSn!="")
					if(in_array('0', $gameType))
					{
						$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,1,0,$rate1_temp,$rate2_temp,$rate3_temp);
						//$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,2,0,'1.85','','1.85');
						//$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,4,0,'1.85','','1.85');
					}
					if(in_array('2', $gameType))
					{
						$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,2,0,'1.86','','1.86');
						//$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,1,2,$rate1_temp,$rate2_temp,$rate3_temp);
						//$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,2,2,'1.85','','1.85');
						//$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,4,2,'1.85','','1.85');
					}
					if(in_array('4', $gameType))
					{
						$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,4,0,'1.86','','1.86');
						//$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,1,1,$rate1_temp,$rate2_temp,$rate3_temp);
						//$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,2,1,'1.85','','1.85');
						//$model->addChild($parentIdx,"축구",$leagueSn,$team1_name_temp,$team2_name_temp,$game_date_temp,$game_hours_temp,$game_minute_temp,'',$kubun,4,1,'1.85','','1.85');
					}
				}
			}
		
		if($message!="")
		{
			throw new Lemon_ScriptException("","","script","alert('등록되지 않은 리그".$message."');opener.document.location.reload();");		
		}
		
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
		$this->view->define("content","content/gameUpload/popup.modify_rate.html");
		$model = $this->getModel("GameModel");
		
		$idx 	= $this->request("idx");		
		$gametype 	= $this->request("gametype");
		$mode 	= $this->request("mode");
		
		if($mode == "") {$mode = "add";}
			
		$item = $model->getChildRow($idx);
		$leagueName = $model->getRow('name', $model->db_qz.'league', 'sn='.$item[league_sn]);
		$item['league_name']=$leagueName['name'];	
		
		$rs = $model->getSubChildRows($idx);
		if(sizeof($rs) > 0)
		{
			$home_rate = $rs[0]['home_rate'];
			$draw_rate = $rs[0]['draw_rate'];
			$away_rate = $rs[0]['away_rate'];
		}
        
        // 연동 설정 가져오기
        $gameIni = $model->getGameIni($idx);
        $otherGameIni = null;
        
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
                
                $otherGameIni = $model->getGameIni($otherSn);
            }
            else 
            {
                $otherType = 0;
            }
        }
        
        ///////////////////////////////////////////////////
        // 게임 연동 체크박스 부분 
        $htmlIni = "";
        if( sizeof($gameIni) > 0 )
        {
            $htmlIni=$htmlIni."<input type='checkbox' name='allow_rate_change' ";
            if( $gameIni['allow_rate_change'] == 'Y' )
            {
                $htmlIni=$htmlIni."checked='checked' ";
            }
            $htmlIni=$htmlIni."value='Y' >배당연동&nbsp;&nbsp;&nbsp;";
            $htmlIni=$htmlIni."<input type='checkbox' name='allow_betting_auto' ";
            if( $gameIni['allow_betting_auto'] == 'Y' )
            {
                $htmlIni=$htmlIni."checked='checked' ";
            }
            $htmlIni=$htmlIni."value='Y' >발매연동&nbsp;&nbsp;&nbsp;";
            $htmlIni=$htmlIni."<input type='checkbox' name='allow_magam_auto' ";
            if( $gameIni['allow_magam_auto'] == 'Y' )
            {
                $htmlIni=$htmlIni."checked='checked' ";
            }
            $htmlIni=$htmlIni."value='Y' >마감연동";
        }
        
        $otherHtmlIni = "";
        if( sizeof($otherGameIni) > 0 )
        {
            $otherHtmlIni=$otherHtmlIni."<input type='checkbox' name='other_allow_rate_change' ";
            if( $otherGameIni['allow_rate_change'] == 'Y' )
            {
                $otherHtmlIni=$otherHtmlIni."checked='checked' ";
            }
            $otherHtmlIni=$otherHtmlIni."value='Y' >배당연동&nbsp;&nbsp;&nbsp;";
            $otherHtmlIni=$otherHtmlIni."<input type='checkbox' name='other_allow_betting_auto' ";
            if( $otherGameIni['allow_betting_auto'] == 'Y' )
            {
                $otherHtmlIni=$otherHtmlIni."checked='checked' ";
            }
            $otherHtmlIni=$otherHtmlIni."value='Y' >발매연동&nbsp;&nbsp;&nbsp;";
            $otherHtmlIni=$otherHtmlIni."<input type='checkbox' name='other_allow_magam_auto' ";
            if( $otherGameIni['allow_magam_auto'] == 'Y' )
            {
                $otherHtmlIni=$otherHtmlIni."checked='checked' ";
            }
            $otherHtmlIni=$otherHtmlIni."value='Y' >마감연동";
        }
        
	
		$strMode	= "";
		$html 		= "";
		//$add		= "onkeyup='this.value=this.value.replace(/[^0-9.]/gi,\"\")'";
		$add		= "'";
		if($gametype==1)
		{
			if($mode=="update") {$strMode="disabled";}
			$html="<td bgcolor='#ffffff' align='left'>";
			$html=$html."&nbsp;&nbsp;승<input type='text' name='home_rate' size='4' value='".$home_rate."' ".$add.">";
			$html=$html."&nbsp;무<input type='text' name='draw_rate' size='4' value='".$draw_rate."' ".$add.">";
			$html=$html."&nbsp;패<input type='text' name='away_rate' size='4' value='".$away_rate."' ".$add.">";
			$html=$html."&nbsp;&nbsp;<FONT color=#006699>&#8226; 승/패경기일때는 무승부에 1.00 을 넣으세요 </font>" ;
            if( sizeof($gameIni) > 0 )
            {
                $html=$html."<br/><br/>&nbsp;&nbsp;".$htmlIni;
            }
            $html=$html."</td>";
		}
		elseif($gametype==2)
		{
			if($mode=="edit") {$strMode="disabled";}
			$html="<td bgcolor='#ffffff' align='left'>";
			$html=$html."&nbsp;&nbsp;홈팀<input type='text' name='home_rate' size='4' value='".$home_rate ."' ".$add.">";
			$html=$html."&nbsp;홈핸디<input type='text' name='draw_rate' size='4' value='".$draw_rate ."' >";
			$html=$html."&nbsp;원정팀<input type='text' name='away_rate' size='4' value='".$away_rate ."' ".$add.">";
			$html=$html."&nbsp;&nbsp;&nbsp;&nbsp;";
            $html=$html.$htmlIni;
            $html = $html."</td>";
            
            if($otherType != 0) {
                $otherHtml="<td bgcolor='#ffffff' align='left'>";
			    $otherHtml=$otherHtml."&nbsp;&nbsp;오버<input type='text' name='other_home_rate' size='4' value='".$other_home_rate."' ".$add.">";
			    $otherHtml=$otherHtml."&nbsp;기준점수<input type='text' name='other_draw_rate' size='4' value='".$other_draw_rate."' >";
			    $otherHtml=$otherHtml."&nbsp;언더<input type='text' name='other_away_rate' size='4' value='".$other_away_rate."' ".$add.">";
			    $otherHtml=$otherHtml."&nbsp;&nbsp;&nbsp;&nbsp;";
                $otherHtml=$otherHtml.$otherHtmlIni;
                $otherHtml = $otherHtml."</td>";
            }
		}
		elseif($gametype==4)
		{
			if($mode=="edit") {$strMode="disabled";}
			$html="<td bgcolor='#ffffff' align='left'>";
			$html=$html."&nbsp;&nbsp;오버<input type='text' name='home_rate' size='4' value='".$home_rate."' ".$add.">";
			$html=$html."&nbsp;기준점수<input type='text' name='draw_rate' size='4' value='".$draw_rate."' >";
			$html=$html."&nbsp;언더<input type='text' name='away_rate' size='4' value='".$away_rate."' ".$add.">";
			$html=$html."&nbsp;&nbsp;&nbsp;&nbsp;";
            $html=$html.$htmlIni;
            $html = $html."</td>";
            
            if($otherType != 0) {
                $otherHtml="<td bgcolor='#ffffff' align='left'>";
			    $otherHtml=$otherHtml."&nbsp;&nbsp;홈팀<input type='text' name='other_home_rate' size='4' value='".$other_home_rate."' ".$add.">";
			    $otherHtml=$otherHtml."&nbsp;홈핸디<input type='text' name='other_draw_rate' size='4' value='".$other_draw_rate."' >";
			    $otherHtml=$otherHtml."&nbsp;원정팀<input type='text' name='other_away_rate' size='4' value='".$other_away_rate."' ".$add.">";
			    $otherHtml=$otherHtml."&nbsp;&nbsp;&nbsp;&nbsp;";
                $otherHtml=$otherHtml.$otherHtmlIni;
                $otherHtml = $otherHtml."</td>";
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
        
        $this->view->assign("gameIni", $gameIni);
        $this->view->assign("otherGameIni", $otherGameIni);
		
		$this->view->assign("strMode",$strMode);
	
		
		
		$this->display();
	}
	
	//▶ 경기 배당수정 처리 
	function rateProcessAction()	
	{
		$model 		= $this->getModel("GameModel");
		$child_sn = $this->request("idx");				
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
	    
        // 연동 설정부분
        $gameIni = $model->getGameIni($child_sn);
        $otherGameIni = $model->getGameIni($otherSn);
        
		$model->modifyChildRate($child_sn,$gametype,$home_rate,$draw_rate,$away_rate);		
		$model->modifyChildRate_Date($child_sn,$gameDate,$gameHour,$gameTime);
        
        if( sizeof($gameIni) > 0 )
        {
            $allow_rate_change = $this->request("allow_rate_change", 'N');
            if( $allow_rate_change == '' ) 
            {
                $allow_rate_change = 'N';
            }
            $allow_betting_auto = $this->request("allow_betting_auto", 'N');
            if( $allow_betting_auto == '' ) 
            {
                $allow_betting_auto = 'N';
            }
            $allow_magam_auto = $this->request("allow_magam_auto", 'N');
            if( $allow_magam_auto == '' ) 
            {
                $allow_magam_auto = 'N';
            }
            $allow_base_change = $this->request("allow_base_change", 'N');
            if( $allow_base_change == '' ) 
            {
                $allow_base_change = 'N';
            }
            $model->modifyGameIni($child_sn, $allow_rate_change, $allow_betting_auto, $allow_magam_auto, $allow_base_change, $gameIni['add_home_rate'], $gameIni['add_draw_rate'], $gameIni['add_away_rate']);
        }
        
        if($otherSn != '' && $otherSn != 0) {
            $model->modifyChildRate($otherSn,$otherType,$other_home_rate,$other_draw_rate,$other_away_rate);		
		    $model->modifyChildRate_Date($otherSn,$gameDate,$gameHour,$gameTime);
            
            if( sizeof($otherGameIni) > 0 )
            {
                $allow_rate_change = $this->request("other_allow_rate_change", 'N');
                if( $allow_rate_change == '' ) 
                {
                    $allow_rate_change = 'N';
                }
                $allow_betting_auto = $this->request("other_allow_betting_auto", 'N');
                if( $allow_betting_auto == '' ) 
                {
                    $allow_betting_auto = 'N';
                }
                $allow_magam_auto = $this->request("other_allow_magam_auto", 'N');
                if( $allow_magam_auto == '' ) 
                {
                    $allow_magam_auto = 'N';
                }
                $allow_base_change = $this->request("other_allow_base_change", 'N');
                if( $allow_base_change == '' ) 
                {
                    $allow_base_change = 'N';
                }
                $model->modifyGameIni($otherSn, $allow_rate_change, $allow_betting_auto, $allow_magam_auto, $allow_base_change, $otherGameIni['add_home_rate'], $otherGameIni['add_draw_rate'], $otherGameIni['add_away_rate']);
            }
        }
        
        
		
		throw new Lemon_ScriptException("","","script","alert('수정되었습니다.');opener.document.location.reload(); self.close();");		
	}
	
	
	//▶ 게임결과 입력 및 수정
	function popup_modifyresultAction()
	{
		$this->popupDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/popup.modify_result.html");
		$model 	= $this->getModel("GameModel");
		$leagueModel = $this->getModel("LeagueModel");
		
		$childSn 	= $this->request("idx");
		$pidx 	 	= $this->request("pidx");
		$mode 	 	= $this->request("mode");
		$category = $this->request("category");
		$result = $this->request("result");
		
		$categoryList = $leagueModel->getCategoryList();
	
		if ($mode=="edit")
		{
			$item = $model->getChildJoinSubChild($childSn);
			$leagueList = $leagueModel->getListAll();
		}
		
		$hours = array();
		for($i=0; $i<24; ++$i)
		{
			if($i<10) 	{$hour = '0'.$i;}
			else				{$hour = $i;}
			$hours[] = $hour;
		}
		
		$mins = array();
		for($i=0; $i<60; ++$i)
		{
			if($i<10) $min='0'.$i;
			else	$min=$i;
			
			$mins[] = $min;
		}
		
		$this->view->assign("hours",$hours);
		$this->view->assign("mins",$mins);
		$this->view->assign("idx",$childSn);
		$this->view->assign("pidx",$pidx);
		$this->view->assign("mode",$mode);
		$this->view->assign("item",$item[0]);
		$this->view->assign("league_list",$leagueList);
		$this->view->assign("category_list",$categoryList);
		$this->view->assign("result",$result);
		$this->display();
	}
	
	//▶ 엑셀 업로드
	function popup_exceluploadAction()
	{
		$this->popupDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/popup.excel_upload.html");
		
		header("Content-Type:text/html;charset=UTF-8");
		
		$model = $this->getModel("GameModel");
		$leagueModel = $this->getModel("LeagueModel");
		
		//참고 http://shonm.tistory.com/category/PHP/PHP%20%EC%97%91%EC%85%80%20%ED%8C%8C%EC%9D%BC%20%EC%9D%BD%EA%B8%B0
		
		$mode = $this->request("mode");		
		
		
		
		if($mode == "execl_collect")
		{
			include_once("include/excel_reader.php");			
			
			$conf = Lemon_Configure::readConfig('config');
			if($conf['site']!='')
			{
				$upload_dir	 = $conf['site']['local_upload_url']."/upload/excel/";
			}
		
			$tmp_name = $_FILES["fileUpload"]["tmp_name"]; // 임시파일명
		 	$name = $_FILES["fileUpload"]["name"];  // 파일명
		 	
		 	$upload_file = $upload_dir.$name;
		 	
		// 	echo "upload_file: ".$upload_file."<br/>";
		 	
 			if( move_uploaded_file($tmp_name, $upload_file) )
 			{
 				echo "파일업로드 성공"."<br/>";
 			}
 			else
 			{
 				throw new Lemon_ScriptException("파일업로드가 실패하였습니다.");			
 				exit;
 			}
  
			
			$handle = new Spreadsheet_Excel_Reader();		
			$handle->setOutputEncoding('utf-8');
			
			$handle->read($upload_file);
		
			$gamearray = array();
	
			for( $k=0; $k < sizeof($handle->sheets); ++$k )
			{ 
				for($i=0; $i <= $handle->sheets[0]['numRows']; $i++)
				{
					//엑셀 첫 열이 데이터가 아니라 구분 이므로 0번째 index 를 건너뛰고 읽음
			//		if($i==1) continue;
					
					$kind=''; $game_type=''; $game_date=''; $gameHour=''; $gameTime=''; $league_name=''; $home_team=''; $away_team=''; $home_rate=''; $draw_rate=''; $away_rate='';
					
					for ($j=1; $j<=$handle->sheets[$k]['numCols']; $j++)
					{
						switch( $j )
						{
						case 1: $kind = $handle->sheets[$k]['cells'][$i][$j]; break; // 게임옵션												
						case 2: $game_type = $handle->sheets[$k]['cells'][$i][$j]; break; // 게임방식												
						case 3: $game_date = $handle->sheets[$k]['cells'][$i][$j]; break; // 일자												
						case 4: $gameHour = $handle->sheets[$k]['cells'][$i][$j]; break; // 시간												
						case 5: $gameTime = $handle->sheets[$k]['cells'][$i][$j]; break; // 분
						case 6: $league_name = $handle->sheets[$k]['cells'][$i][$j]; break; // 리그명
						case 7: $home_team = $handle->sheets[$k]['cells'][$i][$j]; break; // 홈팀			
						case 8: $away_team = $handle->sheets[$k]['cells'][$i][$j]; break; // 원정팀																																
						case 9: $home_rate = $handle->sheets[$k]['cells'][$i][$j]; break; //  홈팀 승률																																
						case 10: $draw_rate = $handle->sheets[$k]['cells'][$i][$j]; break; //  무승부 승률
						case 11: $away_rate = $handle->sheets[$k]['cells'][$i][$j]; break; //  원정팀 승률																																			
						}
					}		
					
					if( $game_type == '' || $game_date == '' || $game_date == 0 || $gameHour == '' || $gameTime == '' )
					{
						continue;
					}
					
					$kind 				= trim($kind);					
					$game_type 		= trim($game_type);
					$game_date		= trim($game_date);
					$gameHour 		= trim($gameHour);
					$gameTime 		= trim($gameTime);
					$league_name 	= trim($league_name); 
					$home_team 		= trim($home_team); 
					$away_team 		= trim($away_team); 
					$home_rate 		= trim($home_rate); 
					$draw_rate 		= trim($draw_rate); 
					$away_rate 		= trim($away_rate);
					
					
					if( !is_null($league_name) || $league_name == '')
					{
						$rs = $leagueModel->getListByName($league_name);
						$cate_name = $rs[0]['kind'];
					}
					
					$count = sizeof($gamearray);
					
					$gamearray[$count]['cate_name'] 	= $cate_name;
					$gamearray[$count]['kind'] 				= $kind;
					$gamearray[$count]['game_type'] 	= $game_type;
					$gamearray[$count]['game_date'] 	= $game_date;
					$gamearray[$count]['gameHour'] 		= $gameHour;
					$gamearray[$count]['gameTime'] 		= $gameTime;
					$gamearray[$count]['league_name']	= $league_name;
					$gamearray[$count]['home_team']		= $home_team;
					$gamearray[$count]['away_team']		= $away_team;
					$gamearray[$count]['home_rate']		= $home_rate;
					$gamearray[$count]['draw_rate']		= $draw_rate;
					$gamearray[$count]['away_rate']		= $away_rate;
					
					$rs = $leagueModel->getLeagueSnByName( $league_name );
					$leagueSn = $rs['sn'];
					
					$type = 0;
					$is_specified_special=0;
												
					if($game_type == "승무패" || $game_type == "승패"){$type = 1;}
					else if($game_type == "핸디캡"){$type = 2;}
					else if($game_type == "홀 짝" || $game_type == "홀짝" ){$type = 3;}
					else if($game_type == "언더오버" || $game_type == "하이로우" || $game_type == "언오버" ){$type = 4;}  
					else if($game_type == "승스페")
					{
						$type = 1; 
						$is_specified_special=1;

						if( false!=strstr($league_name, "득점/무득점"))
						{
							$home_team =$home_team."[득점]";
							$Context = "[무득점]";
							$away_team = $Context.$away_team;
						}
						
					}  
					
					if($kind == '일반')					{ $special = 0;}
					else if( $kind == '스페셜')	{ $special = 1;}
					else if( $kind == '멀티')		{ $special = 2;}
					else if( $kind == '고액')		{ $special = 3;}
					else if( $kind == '이벤트')	{ $special = 4;}
					else if( $kind == '사다리')	{ $special = 5;}
					
					$kubun = 'null';
				
					$rs = $model->addChild(0,$cate_name,$leagueSn,$home_team,$away_team,$game_date,$gameHour,$gameTime,'',$kubun,$type,$special,$home_rate,$draw_rate,$away_rate,$is_specified_special);
					if($rs <=0)
					{
						throw new Lemon_ScriptException("","","script","alert('입력실패.');opener.document.location.reload(); self.close();");			
					}
				}
			}
			
			throw new Lemon_ScriptException("","","script","alert('입력되었습니다.');opener.document.location.reload(); self.close();");			
		}		
	
		
		$this->view->assign("gamearray", $gamearray);

		$this->display();
	}
	
	//▶ 게임 수정
	function modifyProcessAction()
	{
		$view			= $this->request("view");
		$childSn	= $this->request("idx");
		$pidx			= $this->request("pidx");
		$auto			= $this->request("auto");
		$bet_type	= $this->request("bet_type");
		$mode 		= Trim($this->request("mode"));
		$result_state 		= Trim($this->request("result_state"));
		
		$leagueModel 	= $this->getModel("LeagueModel");
		$gModel 	= $this->getModel("GameModel");
		$cartModel 	= $this->getModel("CartModel");
		$pModel		= $this->getModel("ProcessModel");
		
		//결과입력이 없을 경우
		if($view == "")
		{
			$currentGame = $gModel->getChildRow($childSn);
			
			$type 	 = $this->request("game_type");
			$special = $this->request("special_type");
			
			$gameDate = $this->request("GameDate");
			$gameHour = $this->request("gameHour");
			$gameTime = $this->request("gameTime");
			$leagueSn = Trim($this->request("strLeagueName"));
			$homeTeam = $this->request("HomeTeam");
			$awayTeam = $this->request("AwayTeam");
			$notice 	= "";//str_replace("<br>",chr(13),$this->request("notice"));
			$play 		= $this->request("play");
			$league_kind = $leagueModel->getLeagueField($leagueSn, 'kind');
			$category	= Trim($this->request("category"));
			
			if($result_state==1)
			{
				$homeScore = $this->request("winPoint");
				$awayScore = $this->request("winPoint2");
			}
	
			$set .= "league_sn = '" . $leagueSn . "',";
			$set .=	"home_team = '" . $homeTeam . "',";  
			$set .=	"away_team = '" . $awayTeam . "',";
			$set .=	"gameDate = '" . $gameDate . "',";
			$set .=	"gameHour = '" . $gameHour . "',";
			$set .= "gameTime = '" . $gameTime . "',";
			$set .= "sport_name = '" . $category . "',";
			if($result_state==1)
			{
				$set .= "home_score = '" . $homeScore . "',";
				$set .= "away_score = '" . $awayScore . "',";
			}
			$set .= "notice = '" .$notice . "' ";
			
			//대분류, 종류 수정시에는 해당되는 게임만 수정해야 한다.
			if($currentGame['type'] != $type || $currentGame['special'] != $special)
			{
				if($gModel->isGameExist($type, 
																$special, 
																$currentGame['home_team'],
																$currentGame['away_team'],
																$currentGame['gameDate'],
																$currentGame['gameHour'],
																$currentGame['gameTime']) > 0)
				{
					throw new Lemon_ScriptException("","","script","alert('게임이 이미 존재합니다.');opener.document.location.reload(); self.close();");
					exit;
				}
				
				$gModel->modifyGameType($childSn, $special, $type);
			}
			
			$where = " sn=".$childSn;
			$gModel->modifyChild($set,$where);
			
			/*
			$item = $gModel->getSameChild($currentGame['gameDate'], $currentGame['gameHour'], $currentGame['gameTime'], $currentGame['league_sn'], $currentGame['home_team'], $currentGame['away_team']);
			
			for($i=0; $i<sizeof($item); ++$i)
			{
				$where = " sn=".$item[$i]['sn'];
				$gModel->modifyChild($set,$where);
			}
			*/
		}
		//결과입력시
		else
		{
			/*
			if($auto=="0") 	{$winTeam = Trim($this->request("winTeamauto"));}
			else 						{$winTeam = Trim($this->request("winTeam"));}
			
			$gameCancel="";
			if($winTeam=="Cancel")
			{
				$homeScore 	= 0;
				$awayScore	= 0;
				$gameCancel = "Cancel";
			}
			else
			{
				$homeScore = $this->request("winPoint");
				$awayScore = $this->request("winPoint2");
			}
			
			$set.= "home_score=".$homeScore."," ;
			$set.= "away_score=".$awayScore.",";
			
			if($bet_type==1 or $bet_type==4) 	$set .= "win_team='".$winTeam."'";
			else if($bet_type==2)							$set .= "handi_winner = '".$winTeam."'";
				
			$where = " sn=".$childSn;
			
			$gModel->modifyChild($set, $where);
			$pModel->resultGameProcess($childSn, $homeScore, $awayScore, $winTeam, $gameCancel);
			*/
			if($auto=="0") 	{$winTeam = Trim($this->request("winTeamauto"));}
			else 						{$winTeam = Trim($this->request("winTeam"));}
			
			$gameCancel="";
			if($winTeam=="Cancel")
			{
				$gameCancel="1";
			}
			else
			{
				$homeScore = $this->request("winPoint");
				$awayScore = $this->request("winPoint2");
			}

			$pModel->resultGameProcess($childSn, $homeScore, $awayScore, $gameCancel);
		}
			
		throw new Lemon_ScriptException("","","script","alert('수정되었습니다.');opener.document.location.reload(); self.close();");							
		
	}
	
	//▶ 게임 결과 처리
	function resultmoneyProcessAction()
	{
		$childSn	= $this->request("idx");    //'경기인텍스
		$type		= $this->request("type");
		
		$pModel 	= $this->getModel("ProcessModel");
		$commonModel 	= $this->getModel("CommonModel");

		
		$rs = $pModel->resultMoneyProcess($childSn);
		
		if($rs==-1)	{$msg = "[수정]에서 게임결과를 입력후 누르세요";}
		else		{$msg = "배당지급이 완료되었습니다.";}

		$url = "/gameUpload/gamelist?type=".$type;	
		
		$commonModel->alertGo($msg, $url);		
		
	}
	
	//▶ 경기 결과 취소 
	public function cancel_resultProcessAction()
	{
		$childIdx 	= $this->request("idx"); 	//경기인텍스
		$type 	= $this->request("type");
		
		$state						=$this->request("state");
		$gameType					=$this->request("game_type");
		$categoryName			=$this->request("categoryName");
		$special_type			=$this->request("special_type");
		$perpage					=$this->request("perpage");
		$begin_date				=$this->request("begin_date");
		$end_date					=$this->request("end_date");
		$filter_team_type	=$this->request("filter_team_type");
		$filter_team			=$this->request("filter_team");
		$money_option			=$this->request("money_option");
		$page							=$this->request("page");
		
		$param="&state=".$state."&game_type=".$gameType."&categoryName=".$categoryName."&special_type=".$special_type."&perpage=".$perpage."&begin_date=".$begin_date."&end_date=".$end_date."&filter_team_type=".$filter_team_type."&filter_team=".$filter_team."&money_option=".$money_option."&page=".$page;
		
		$model = $this->getModel("GameModel");
		$commonModel 	= $this->getModel("CommonModel");
		
		$rs = $model->cancelResultChild($childIdx);
		if($rs==-1)
		{
			$msg = "게임결과가 입력되지 않았습니다.  게임결과 등록후 배당지급하세요.";
			$url = "/gameUpload/gamelist?&bet=0";	
			$commonModel->alertGo($msg, $url.$param);
		}
		else
		{
			$msg = "배당지급이 취소 되었습니다.";
			$url = "/gameUpload/gamelist?search=search&bet=1";
			$commonModel->alertGo($msg, $url.$param);
		}
	}
	
	//▶  경기업로드 
	function popup_gameuploadAction()
	{
		$this->popupDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/popup.game_upload.html");
		$model = $this->getModel("GameModel");
		$leagueModel = $this->getModel("LeagueModel");
		
		// 경기종류
		$pidx = $this->request("pidx");
		
		$categoryList = $leagueModel->getCategoryList();
		
		$gameHour = "<select name='gameHour[]' id='game_hour'>";
		for($i=0;$i<24;$i++)
		{
			$j=$i;
			if($j<10) {$j="0".$j;}
			$gameHour=$gameHour."<option value='".$j."'>".$j."</option>";
		}
		$gameHour = $gameHour . "</select>";		
		
		$gameTime = "<select name='gameTime[]' id='game_time'>";
		for($i=0;$i<60;$i++)
		{
			$j=$i;
			if($j<10)	{$j="0".$j;}
			$gameTime=$gameTime."<option value='".$j."'>".$j."</option>";
		}
		$gameTime = $gameTime . "</select>";

		$leagueList = $leagueModel->getListAll($where);

		$this->view->assign("pidx", $pidx);
		$this->view->assign("kind", $kind);
		$this->view->assign("category_list", $categoryList);
		$this->view->assign("league_list", $leagueList);
		$this->view->assign("gameType", $gameType);
		$this->view->assign("gameHour", $gameHour);
		$this->view->assign("gameTime", $gameTime);
		$this->view->assign("league", $league);
		
		$this->display();
	}
	
	
	//▶ 게임 업로드 처리
	function gameuploadProcessAction()
	{
		$this->popupDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/popup.game_upload_result.html");
		
		$leagueModel = $this->getModel("LeagueModel");
		$gmodel = $this->getModel("GameModel");
		
		$intParentIdx		= $this->request("pidx");
		$kind_arr			= $this->request("kind");
		$kubun				= $this->request("kubun");
		$gameType_arr		= $this->request("gametype");
		$league_arr			= $this->request("league");
		$gameDate_arr		= $this->request("gameDate");
		$gameHour_arr		= $this->request("gameHour");
		$gameTime_arr		= $this->request("gameTime");
		$HomeTeam_arr		= $this->request("HomeTeam");
		$AwayTeam_arr		= $this->request("AwayTeam");
		$type_a_arr			= $this->request("type_a");
		$type_b_arr			= $this->request("type_b");
		$type_c_arr			= $this->request("type_c");
		$isEvents			= $this->request("is_event");
		$specialTypeArray	= $this->request("special_type");
		
		$strrep	=	true;
		
		for($i=0;$i<count($kind_arr);$i++)
		{
			$kind 		 	 = $kind_arr[$i];
			$gameType 	 = trim($gameType_arr[$i]);
			$leagueSn 	 = trim($league_arr[$i]);
			$gameDate 	 = trim($gameDate_arr[$i]);
			$gameHour 	 = trim($gameHour_arr[$i]);
			$gameTime 	 = trim($gameTime_arr[$i]);
			$HomeTeam 	 = trim($HomeTeam_arr[$i]);
			$HomeTeam	 	 = str_replace("'","&#039;",$HomeTeam);
			$AwayTeam 	 = trim($AwayTeam_arr[$i]);
			$AwayTeam 	 = str_replace("'","&#039;",$AwayTeam);
			$homeRate 	 = trim($type_a_arr[$i]);
			$drawRate 	 = trim($type_b_arr[$i]);
			$awayRate	 	 = trim($type_c_arr[$i]);
			$specialType = trim($specialTypeArray[$i]);
			$is_specified_special = 0;
			
			if($gameType==5)
			{
				$gameType = 1;
				$is_specified_special = 1;
			}
			
			if($gameType==1 && ($drawRate=="1.00" || $drawRate=="1.0" || $drawRate=="1"))
				$drawRate="1.00";
	
			$LeagueName	 = '';
			$type		 = '';
			
			$rs = $leagueModel->getListBySn( $leagueSn );
			if( sizeof($rs) <= 0 )
			{
				echo "인덱스번호[" .$leagueSn. "] 에 해당되는 리그정보가 디비에 없습니다";
				exit;				
			}
			else
			{
				$LeagueName = $rs["name"];
				if( $is_specified_special == 1)
				{
					if( false!=strstr($LeagueName, "득점/무득점"))
					{
						$HomeTeam =$HomeTeam."[득점]";
						$Context = "[무득점]";
						$AwayTeam = $Context.$AwayTeam;
					}
				}
			}			
			
			if($kubun=="") $kubun = 'null';
		
			$gmodel->addChild($intParentIdx,$kind,$leagueSn,$HomeTeam,$AwayTeam,$gameDate,$gameHour,$gameTime,'',$kubun,$gameType,$specialType,$homeRate,$drawRate,$awayRate, $is_specified_special);
		}
		
		$this->display();
	}
	
	//▶ 게임 발매 수정 
	function modifyStausProcessAction()
	{
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		
		$childSn 			= $this->request('child_sn');    //경기인텍스				
		$state 				= $this->request('state');
		$state  			= $this->request("state");
		$perpage			= $this->request("perpage");
		$specialType	= $this->request("special_type");
		$categoryName = $this->request("categoryName");
		$gameType 		= $this->request("game_type");
		
		$moneyOption 	= $this->request("money_option");
		$beginDate  	= $this->request('begin_date');
		$endDate 			= $this->request('end_date');
		$filterTeam			= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');

		$model = $this->getModel("GameModel");
		$model->modifyChildStaus($childSn,$state);
		
		$param="state=".$state."&game_type=".$gameType."&categoryName=".$categoryName."&special_type=".$specialType."&perpage=".$perpage."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_team=".$filterTeam."&money_option=".$moneyOption;
		
		$url = "/gameUpload/gamelist?".$param;
		throw new Lemon_ScriptException("변경되었습니다.","","go","/gameUpload/");
	}
	
	
	function get_fsock_data($domain,$url,$port=80,$timeout=30)
	{
		$fp = fsockopen($domain,$port,$errstr,$timeout) or die($errstr);
		if($fp)
		{
			echo "#### 포트 연결 성공 ####";
		 	echo "<br>";
		 
			$out = "GET $url HTTP/1.1\r\n";
			$out .= "Host: $domain\r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp,$out);
			$res = '';
			while(!feof($fp)){
				$res .= fgets($fp,128);
			}			
			fclose($fp);
			$pattern = '/HTTP\/1\.\d\s(\d+)/';
			if( preg_match($pattern,$res,$matches)&& $matches[1] == 200){
				$data_arr = explode("\r\n\r\n", $res);				
				
				$data = $data_arr[1];
				$enc = mb_detect_encoding($data,array('EUC-KR','UTF-8','shift_jis','CN-GB'));
				
				if( $enc != 'UTF-8') {
					$data = iconv($enc,'UTF-8',$data);
				}
				return $data;				
			}
		}
		else
		{
			echo "#### 포트 연결 실패 ####";
		 	echo "<br>";
		}
		
		return false;
	}
	
	
	function readFile($filename, $count = 2000, $tag = "\r\n") 
	{
		$content = "";
		$_current = "";
		$step= 1;
		$tagLen = strlen($tag);
		$start = 0;
		$i = 0;
		$handle = fopen($filename,'r');

		$content = stream_get_contents($handle);
		
		fclose($handle);
		
		return $content;
	}
	
	function find_array_str($arr,$str)
	{
		$flag=false;
		for($i=0;$i<count($arr);$i++)
		{
			if($arr[$i]==$str)
			{
				$flag=true;
				break;
			}
		}
		return $flag;
	}
    
    //▶ 연동게임목록
	public function remote_listAction()
	{
		$this->commonDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/gameUpload/remote_list.html");
		
		$gameModel		= $this->getModel("GameModel");
		$cartModel 		= $this->getModel("CartModel");
		$leagueModel 	= $this->getModel("LeagueModel");
		
		$act  				= $this->request("act");
		$state  			= $this->request("state");
		$perpage			= $this->request("perpage");
		$specialType	= $this->request("special_type");
		$categoryName = $this->request("categoryName");
		$gameType 		= $this->request("game_type");
		
		$moneyOption = $this->request("money_option");
		$beginDate  		= $this->request('begin_date');
		$endDate 				= $this->request('end_date');
		$filterTeam			= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');
		
		if($act=="modify_state")
		{
			$childSn = $this->request('child_sn'); //경기인텍스
			$newState= $this->request('new_state');
			$gameModel->modifyChildStaus($childSn,$newState);
		}
		else if($act=="delete_game")
		{
			$childSn = $this->request('child_sn');
			$gameModel->delChild($childSn);
		}
		else if($act=='deadline_game')
		{
			$childSn = $this->request('child_sn');
			$gameModel->modifyGameTime($childSn);
		}
		
		if($perpage=='') $perpage = 30;	
		if($moneyOption=='') $moneyOption=0;
		
		$minBettingMoney='';
		if($moneyOption==0)		$minBettingMoney='';
		if($moneyOption==1)		$minBettingMoney=1;
		
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
			else if($filterTeamType=='home_team')
			{
				$homeTeam = Trim($filterTeam);
			}
			else if($filterTeamType=='away_team')
			{
				$awayTeam = Trim($filterTeam);
			}
		}
		if($beginDate=="" || $endDate=="")
		{
			$beginDate 	= date("Y-m-d");
			$endDate		= date("Y-m-d",strtotime ("+1 days"));
		}
		
		$page_act= "state=".$state."&game_type=".$gameType."&categoryName=".$categoryName."&special_type=".$specialType."&perpage=".$perpage."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_team=".$filterTeam."&filter_betting_total=".$filterBettingTotal."&money_option=".$moneyOption;
		
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
			
		$total 			= $gameModel->getListTotal($filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);
		$pageMaker 	= $this->displayPage($total, $perpage, $page_act);
		$list 			= $gameModel->getList($pageMaker->first, $pageMaker->listNum, $filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable);
		
        
        // 연동 옵션을 가져온다
        if( sizeof($list) > 0 )
        {
            foreach( $list as &$item )
            {
                $gameIni = $gameModel->getGameIni($item['child_sn']);
                if( sizeof($gameIni) > 0)
                {
                    $item['allow_rate_change'] = $gameIni['allow_rate_change'];
                    $item['allow_betting_auto'] = $gameIni['allow_betting_auto'];
                    $item['allow_magam_auto'] = $gameIni['allow_magam_auto'];
                    $item['allow_base_change'] = $gameIni['allow_base_change'];
                    $item['add_home_rate'] = $gameIni['add_home_rate'];
                    $item['add_draw_rate'] = $gameIni['add_draw_rate'];
                    $item['add_away_rate'] = $gameIni['add_away_rate'];
                    $item['edited'] = $gameIni['edited'];
                }
            }
        }
        
		
		$categoryList = $leagueModel->getCategoryList();
		
		$this->view->assign("special_type",$specialType);
		$this->view->assign("money_option",$moneyOption);
		$this->view->assign("gameType",$gameType);
		$this->view->assign("categoryName",$categoryName);
		$this->view->assign("categoryList",$categoryList);
		$this->view->assign("search",$search);
		$this->view->assign("state",$state);
		$this->view->assign("list",$list);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('filter_team', $filterTeam);
		$this->view->assign('filter_team_type', $filterTeamType);
		
		$this->display();
	}
    
    public function remote_modifyAction()
    {
        $gameModel = $this->getModel("GameModel");
        $child_sn = $this->request("child_sn");
        
        $add_home_rate = $this->request("add_home_rate");
        $add_draw_rate = $this->request("add_draw_rate");
        $add_away_rate = $this->request("add_away_rate");
        
        $allow_rate_change = $this->request("allow_rate_change");
        $allow_betting_auto = $this->request("allow_betting_auto");
        $allow_magam_auto = $this->request("allow_magam_auto");
        $allow_base_change = $this->request("allow_base_change");
        
        $state						=$this->request("state");
		$gameType					=$this->request("game_type");
		$categoryName			=$this->request("categoryName");
		$special_type			=$this->request("special_type");
		$perpage					=$this->request("perpage");
		$begin_date				=$this->request("begin_date");
		$end_date					=$this->request("end_date");
		$filter_team_type	=$this->request("filter_team_type");
		$filter_team			=$this->request("filter_team");
		$money_option			=$this->request("money_option");
		$page							=$this->request("page");
		
		$param="&state=".$state."&game_type=".$gameType."&categoryName=".$categoryName."&special_type=".$special_type."&perpage=".$perpage."&begin_date=".$begin_date."&end_date=".$end_date."&filter_team_type=".$filter_team_type."&filter_team=".$filter_team."&money_option=".$money_option."&page=".$page;
		
		$commonModel 	= $this->getModel("CommonModel");
        
        if( sizeof($child_sn) > 0 )
        {
            foreach($child_sn as $idx)
            {
                // 기존 설정
                $orignalIni = $gameModel->getGameIni($idx);
            
                if( $orignalIni['allow_rate_change'] != $allow_rate_change[$idx] 
                    || $orignalIni['allow_betting_auto'] != $allow_betting_auto[$idx] 
                    || $orignalIni['allow_magam_auto'] != $allow_magam_auto[$idx] 
                    || $orignalIni['allow_base_change'] != $allow_base_change[$idx] 
                    || $orignalIni['add_home_rate'] != $add_home_rate[$idx] 
                    || $orignalIni['add_draw_rate'] != $add_draw_rate[$idx] 
                    || $orignalIni['add_away_rate'] != $add_away_rate[$idx] )
                {
                    // 변경된 환수율을 적용하여 배당 설정
                    $subchild = $gameModel->getSubChildRow($idx);
                    //$home_rate = $subchild['home_rate']; // - ( $orignalIni['add_home_rate'] - $add_home_rate[$idx]);
                    //$away_rate = $subchild['away_rate']; // - ( $orignalIni['add_away_rate'] - $add_away_rate[$idx]);
                    //$draw_rate = $subchild['draw_rate']; // - ( $orignalIni['add_draw_rate'] - $add_draw_rate[$idx]);
                
                    // 마지막 로그에 환수율 적용
                    $lastLog = $gameModel->getLastSubChildLog($orignalIni['orignal_sn']);
                    $home_rate = $lastLog['home_rate'] + $add_home_rate[$idx];
                    $away_rate = $lastLog['away_rate'] + $add_away_rate[$idx];
                    if( $lastLog['draw_rate'] == 1 )
                    {
                        $draw_rate = $lastLog['draw_rate'];
                    }
                    else
                    {
                        if( $allow_base_change == 'Y')
                        {
                        	$draw_rate = $lastLog['draw_rate'] + $add_draw_rate[$idx];
                        }
                        else
                        {
                        	$draw_rate = $subchild['draw_rate'] - ( $orignalIni['add_draw_rate'] - $add_draw_rate[$idx] );
                        }
                    }
                
                    // 변경 배당으로 수정
                    $set = "home_rate = '$home_rate', away_rate = '$away_rate', draw_rate = '$draw_rate' ";
                    $where = "child_sn = $idx ";
                    $gameModel->modifySubChild($set, $where);
            
                    // 새로운 설정 저장 , 수정됨 체크
                    $gameModel->modifyGameIniEdited($idx, $allow_rate_change[$idx], $allow_betting_auto[$idx], $allow_magam_auto[$idx]
                        , $allow_base_change[$idx], $add_home_rate[$idx], $add_draw_rate[$idx], $add_away_rate[$idx] );
                }
            
            }
        }
        
        $msg = "수정되었습니다.";
		$url = "/gameUpload/remote_list?";
		$commonModel->alertGo($msg, $url.$param);
    }
}
?>
