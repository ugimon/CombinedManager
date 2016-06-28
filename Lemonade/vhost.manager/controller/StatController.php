<?php

class StatController extends WebServiceController
{

	//▶ 로그인 내역
	function adminlogAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/stat/admin_log.html");

		$model 	= $this->getModel("StatModel");
		$lModel 	= $this->getModel("LoginModel");

		$perpage = $this->request("perpage");
		$beginDate = $this->request("begin_date");
		$endDate = $this->request("end_date");

		if($perpage=='')		$perpage=10;
		if($beginDate!="") 	{$where = "and  login_date >='".$beginDate."'";}
		if($endDate!="")
		{
			if($where==""){$where="and login_date <='".$endDate."'";}
			$where=$where." and login_date <='".$endDate."'";
		}

		$page_act = "begin_date=".$beginDate."&end_date=".$endDate."&keyword=".$keyword."&perpage=".$perpage;
		$total 			= $lModel->getAdminLoginTotal($where);
		$pageMaker 	= $this->displayPage($total, 10, $page_act);
		$list 			= $lModel->getAdminLogList($where, $pageMaker->first, $pageMaker->listNum);

		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('list', $list);
		$this->display();
	}

	//▶ 사이트 정산
	function siteaccountAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/stat/site_accounting.html");

		$model 	= $this->getModel("StatModel");
		$mModel = $this->getModel("MoneyModel");
		$aModel = $this->getModel("AccountModel");

		$act = $this->request('act');

		if(isset($act)&&$act=="account")
		{
			$exchange_money	 = $this->request("exchange_money");
			$change_money	 = $this->request("change_money");
			$acc_bet		 = $this->request("acc_bet");
			$acc_bonus_money = $this->request("acc_bonus_money");
			$acc_partner	 = $this->request("acc_partner");
			$account_money	 = $this->request("account_money");
			$beginDate		 = $this->request("reg_date");
			$endDate		 = $this->request("objdate");

			$rs = $aModel->addSiteAccount($exchange_money, $change_money, $acc_bet, $acc_bonus_money, $acc_partner, $account_money, $beginDate, $endDate);

			if($rs>0) { throw new Lemon_ScriptException("정산 신청이 접수되였습니다!","","go","/stat/siteaccount"); }
			else { throw new Lemon_ScriptException("정산 신청이 실패하였습니다!","","go","/stat/siteaccount"); }
			exit;
		}

		$accountList = $aModel->accountSite();

		$total = $aModel->getSiteAccountTotal();
		$pageMaker = $this->displayPage($total, 10);
		$list = $aModel->getSiteAccountList($pageMaker->first, $pageMaker->listNum);

		$this->view->assign('list', $list);
		$this->view->assign('lastList', $accountList);
		$this->display();
	}

	//▶ 사이트 현황
	function siteAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/stat/site.html");

		$statModel = $this->getModel("StatModel");
		$partnerModel = $this->getModel("PartnerModel");

		$beginDate	= $this->request("begin_date");
		$endDate		= $this->request("end_date");
		$filterPartnerSn 	= $this->request('filter_partner_sn');
		$filter_logo 				= $this->request('filter_logo');

		//디폴트 날짜 = 1일부터 금일까지
		if($beginDate=='' or $endDate=='')
		{
			$beginDate 	= date("Y-m-")."01";
			$endDate 		= date("Y-m-d");
		}

		$list = $statModel->getMemberStatic('', $filterPartnerSn, $beginDate, $endDate, $filter_logo);

		$sumList = array();

		foreach($list as $key => $value)
		{
			$sumList['total_member_count'] 							+= $list[$key]['member_count'];
			$sumList['total_charge_member_count'] 			+= $list[$key]['charge_member_count'];
			$sumList['total_visit_member_count'] 				+= $list[$key]['visit_member_count'];
			$sumList['total_betting_member_count'] 			+= $list[$key]['betting_member_count'];
			$sumList['total_sum_betting']								+= $list[$key]['sum_betting'];
			$sumList['total_bet_count']									+= $list[$key]['bet_count'];
			$sumList['total_ing_game']									+= $list[$key]['ing_game'];
			$sumList['total_fin_game']									+= $list[$key]['fin_game'];
			$sumList['total_charge_count']							+= $list[$key]['charge_count'];
			$sumList['total_sum_charge']								+= $list[$key]['sum_charge'];
			$sumList['total_exchange_count']						+= $list[$key]['exchange_count'];
			$sumList['total_sum_exchange']							+= $list[$key]['sum_exchange'];
		}

		$partnerList = $partnerModel->getPartnerIdList($filter_logo);

		$logoModel = $this->getModel("LogoModel");
		$logoList = $logoModel->getList();
		$this->view->assign('logolist', $logoList);

		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('list', $list);
		$this->view->assign('sumList', $sumList);
		$this->view->assign('filter_logo', $filter_logo);
		$this->view->assign('filter_partner_sn', $filterPartnerSn);
		$this->view->assign('partner_list', $partnerList);
		$this->display();
	}

	//▶ 입/출금 통계
	//▶ 입/출금 통계

	//▶ 입/출금 통계
	function moneyAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/stat/money.html");

		$statModel = $this->getModel("StatModel");
		$partnerModel = $this->getModel("PartnerModel");

		$beginDate	= $this->request("begin_date");
		$endDate		= $this->request("end_date");
		$filterPartnerSn 	= $this->request('filter_partner_sn');
		$filter_logo 				= $this->request('filter_logo');

		//디폴트 날짜 = 1일부터 금일까지
		if($beginDate=='' or $endDate=='')
		{
			$beginDate 	= date("Y-m-")."01";
			$endDate 		= date("Y-m-d");
		}

		if($filterPartnerSn!="")

			$list = $partnerModel->getRecommendMoneyList($filterPartnerSn, $beginDate, $endDate, $filter_logo,$filterPartnerSn);
		else
		{

//		$list = $statModel->getMoneyList('', $filterPartnerSn, $beginDate, $endDate, $filter_logo);
			$list = $statModel->getMoneyList2('', $filterPartnerSn, $beginDate, $endDate, $filter_logo);
		}

		$sumList = array();

		foreach($list as $key => $value)
		{
			$sumList['sum_betting_ready_money']		+= $list[$key]['betting_ready_money'];
			$sumList['sum_exchange']							+= $list[$key]['exchange'];
			$sumList['sum_charge']								+= $list[$key]['charge'];
			$sumList['sum_benefit']								+= $list[$key]['benefit'];

			$sumList['rec_sum_benefit']							+= $list[$key]['rec_benefit'];
			$sumList['child_rec_sum_benefit']							+= $list[$key]['child_rec_benefit'];
			$sumList['total_rec_sum_benefit']							+= $list[$key]['total_rec_benefit'];

			$sumList['sum_admin_charge']					+= $list[$key]['admin_charge'];
			$sumList['sum_admin_exchange']				+= $list[$key]['admin_exchange'];
			$sumList['sum_admin_mileage_charge']	+= $list[$key]['admin_mileage_charge'];
			$sumList['sum_admin_mileage_exchange']+= $list[$key]['admin_mileage_exchange'];
		}

		$partnerList = $partnerModel->getPartnerIdList($filter_logo);

		$logoModel = $this->getModel("LogoModel");
		$logoList = $logoModel->getList();
		$this->view->assign('logolist', $logoList);

		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('list', $list);
		$this->view->assign('sumList', $sumList);
		$this->view->assign('filter_partner_sn', $filterPartnerSn);
		$this->view->assign('filter_logo', $filter_logo);
		$this->view->assign('partner_list', $partnerList);
		$this->display();
	}


	//▶ 팝업 입/출금 통계
	function popup_moneyAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/stat/popup_money.html");

		$statModel = $this->getModel("StatModel");
		$partnerModel = $this->getModel("PartnerModel");

		$date	= $this->request("date");
		if($date=="") exit;

		$beginDate	= $date;
		$endDate		= $date;
		$filterPartnerSn 	= $this->request('filter_partner_sn');

		if($filterPartnerSn!="")
			$list = $partnerModel->getRecommendMoneyList($filterPartnerSn, $beginDate, $endDate);
		else
			$list = $statModel->getMoneyList('', $filterPartnerSn, $beginDate, $endDate);

		$sumList = array();

		foreach($list as $key => $value)
		{
			$sumList['sum_betting'] 							+= $list[$key]['betting'];
			$sumList['sum_win_money']							+= $list[$key]['win_money'];
			$sumList['sum_lose_money']						+= $list[$key]['lose_money'];
			$sumList['sum_betting_benefit']				+= $list[$key]['betting_benefit'];
			$sumList['sum_betting_ready_money']		+= $list[$key]['betting_ready_money'];
			$sumList['sum_exchange']							+= $list[$key]['exchange'];
			$sumList['sum_charge']								+= $list[$key]['charge'];
			$sumList['sum_benefit']								+= $list[$key]['benefit'];
			$sumList['sum_admin_charge']					+= $list[$key]['admin_charge'];
			$sumList['sum_admin_exchange']				+= $list[$key]['admin_exchange'];
			$sumList['sum_admin_mileage_charge']	+= $list[$key]['admin_mileage_charge'];
			$sumList['sum_admin_mileage_exchange']+= $list[$key]['admin_mileage_exchange'];
		}

		$partnerList = $partnerModel->getPartnerIdList();

		$this->view->assign('date', $date);
		$this->view->assign('list', $list);
		$this->view->assign('sumList', $sumList);
		$this->view->assign('filter_partner_sn', $filterPartnerSn);
		$this->view->assign('partner_list', $partnerList);
		$this->display();
	}

	//▶ 베팅 통계
	function betAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/stat/bet.html");

		$stat_model = $this->getModel("StatModel");
		$league_model	= $this->getModel("LeagueModel");

		$filter_logo 				= $this->request('filter_logo');
		$filter_category		= $this->request("filter_category");
		$filter_league			= $this->request("filter_league");
		$begin_date			= $this->request("begin_date");
		$end_date				= $this->request("end_date");

		if($begin_date=="")
		{
			$begin_date=date("Y-m")."-01";
		}
		if($end_date=="")
		{
			$end_date=date("Y-m-d");
		}

		if($filter_category == "soccer_live")
		{
			$rs = $stat_model->getLiveBetList($filter_logo, $filter_league, $begin_date, $end_date);
		}
		else
		{
			$rs = $stat_model->getBetList($filter_logo, $filter_category, $filter_league, $begin_date, $end_date);
		}

		$list = array();
		$totalList = array();
		$j = 0;
		$list[$j]['regdate'] = $begin_date;
		$list[$j]['date_name']	=$stat_model->dateName($list[$j]['regdate']);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			//축구실시간을 위한 편집
			switch($rs[$i]["result"])
			{
				case "-1"				: $rs[$i]["result"] = '0'; break;
				case "WIN"			: $rs[$i]["result"] = '1'; break;
				case "LOS"			: $rs[$i]["result"] = '2'; break;
				case "DRAW"		: $rs[$i]["result"] = '4'; break;
				case "CANCEL"	: $rs[$i]["result"] = '4'; break;
			}


			if($list[$j]['regdate'] != $rs[$i]["regdate"])
			{
				$j++;
				$list[$j]['regdate'] = $rs[$i]["regdate"];
				$list[$j]['date_name'] = $stat_model->dateName($list[$j]['regdate']);
				$list[$j][$rs[$i]["result"]]['bet_money']		= $rs[$i]["total_betting_money"];
				$list[$j][$rs[$i]["result"]]['bet_cnt']				= $rs[$i]["total_betting_cnt"];
				$list[$j][$rs[$i]["result"]]['win_money']		= $rs[$i]["total_win_money"];
				$list[$j][$rs[$i]["result"]]['win_cnt']				= $rs[$i]["total_win_cnt"];
				$list[$j]['total_member_count'] += $rs[$i]["total_member_count"];
				$list[$j]['total_bet_money']							+= $rs[$i]["total_betting_money"];
				$list[$j]['total_bet_cnt']								+= $rs[$i]["total_betting_cnt"];
			}
			else
			{
				$list[$j][$rs[$i]["result"]]['bet_money']		= $rs[$i]["total_betting_money"];
				$list[$j][$rs[$i]["result"]]['bet_cnt']				= $rs[$i]["total_betting_cnt"];
				$list[$j][$rs[$i]["result"]]['win_money']		= $rs[$i]["total_win_money"];
				$list[$j][$rs[$i]["result"]]['win_cnt']				= $rs[$i]["total_win_cnt"];
				$list[$j]['total_member_count'] += $rs[$i]["total_member_count"];
				$list[$j]['total_bet_money']							+= $rs[$i]["total_betting_money"];
				$list[$j]['total_bet_cnt']								+= $rs[$i]["total_betting_cnt"];
			}

			$totalList["total_bet_money"] += $rs[$i]["total_betting_money"];
			$totalList["total_bet_cnt"] += $rs[$i]["total_betting_cnt"];
			$totalList["total_member_count"] += $rs[$i]["total_member_count"];

			if($rs[$i]["result"]=='0')
			{
				$totalList["total_bet_money_0"] 	+= $rs[$i]["total_betting_money"];
				$totalList["total_bet_cnt_0"]		+= $rs[$i]["total_betting_cnt"];
			}
			if($rs[$i]["result"]=='1')
			{
				$totalList["total_win_money_1"] 	+= $rs[$i]["total_win_money"];
				$totalList["total_win_cnt_1"]		+= $rs[$i]["total_win_cnt"];
			}
			if($rs[$i]["result"]=='2')
			{
				$totalList["total_lose_money"] 	+= $rs[$i]["total_betting_money"];
				$totalList["total_lose_cnt"]		+= $rs[$i]["total_win_cnt"];
			}

			if($rs[$i]["result"]=='4')
			{
				$totalList["total_win_money_4"] 	+= $rs[$i]["total_win_money"];
				$totalList["total_win_cnt_4"]		+= $rs[$i]["total_win_cnt"];
			}
		}

		$totalList["total_win_money"] = $totalList["total_bet_money"]-$totalList["total_win_money_1"]-$totalList["total_win_money_4"];



		$league_list = $league_model->getListAll();

		$this->view->assign('filter_logo', $filter_logo);
		$this->view->assign('filter_category', $filter_category);
		$this->view->assign('filter_league', $filter_league);
		$this->view->assign('league_list', $league_list);
		$this->view->assign('begin_date', $begin_date);
		$this->view->assign('end_date', $end_date);
		$this->view->assign('list', $list);
		$this->view->assign('totalList', $totalList);
		$this->display();
	}

	//▶ 팝업 머니내역
	function popup_moneyloglistAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/stat/popup.money_list.html");

		$model 	= $this->getModel("MoneyModel");

		$filterState 	= $this->request('filter_state');
		$beginDate = $endDate = $this->request('date');
		$filter_partner_sn =$this->request('filter_partner_sn');
		$flag =$this->request('flag');

		//if($perpage=='') $perpage = 10;


		if($filterState!='')				{$where.=" and a.state=".$filterState;}
		if($filter_partner_sn!='')	{$where.=" and b.recommend_sn='".$filter_partner_sn."'";}

		$list 			= $model->getMoneyLogList($where, '', '', '', $type, $beginDate, $endDate);

		$this->view->assign('date', $endDate);
		$this->view->assign('filter_state', $filterState);
		$this->view->assign('filter_partner_sn', $filter_partner_sn);
		$this->view->assign('list', $list);
		$this->view->assign('flag', $flag);

		$this->display();
	}

	//▶ 팝업 마일리지내역
	function popup_mileageloglistAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/stat/popup.mileage_list.html");

		$model 	= $this->getModel("MoneyModel");

		$filterState 	= $this->request('filter_state');
		$beginDate = $endDate = $this->request('date');
		$filter_partner_sn =$this->request('filter_partner_sn');
		$flag =$this->request('flag');

		if($filterState!='')			{$where.=" and a.state=".$filterState;}
		if($filter_partner_sn!='')	{$where.=" and b.recommend_sn='".$filter_partner_sn."'";}

		$list 			= $model->getMileageLogList($where, '', '', '', $type, $beginDate, $endDate);

		$this->view->assign('date', $endDate);
		$this->view->assign('filter_state', $filterState);
		$this->view->assign('filter_partner_sn', $filter_partner_sn);
		$this->view->assign('list', $list);
		$this->view->assign('flag', $flag);

		$this->display();
	}
}

?>
