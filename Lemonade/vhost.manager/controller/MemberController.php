<?php


class MemberController extends WebServiceController
{
	//▶ 상세정보
	function rollingViewAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}

		$this->view->define("content","content/member/rollingView.html");

		$model 	= Lemon_Instance::getObject("MemberModel", true);
		$partnerModel = $this->getModel("PartnerModel");
		$mModel = $this->getModel("MemoModel");
		$eModel = $this->getModel("EtcModel");
		$monModel 	= $this->getModel("MoneyModel");
		$cModel = $this->getModel("CartModel");

		$memberSn 	= $this->request("idx");

		$memoList 		= $mModel->getMemberNote($memberSn);
		$list 			= $model->member($memberSn);

		$levelList		= $eModel->getLevel();
		$country_code	= $eModel->getNationByIp($list['mem_ip']);
		$partnerList = $partnerModel->getPartnerIdList($list['logo']);
		$joiner_sn = $model->getJoiner($memberSn);

		if($joiner_sn!="")
		{
			$joiner = $model->getBySn($joiner_sn);
		}
		$joiners = $model->getJoiners($memberSn);

		$chargeList	= $monModel->getMemberChargeList($memberSn, "1", $where, 0, 1);
		$exchangeList	= $monModel->getMemberExchangeList($memberSn, "1", $where, 0, 2);

		if($exchangeList[0]['operdate'] < $chargeList[0]['operdate'])
		{
			// 입금 이후, 출금이 없는 경우
			$nCase = "1";
			$bDate = $chargeList[0]['operdate'];
		}

		else if($exchangeList[0]['operdate'] > $chargeList[0]['operdate'])
		{
			// 입금 이후, 출금이 있는 경우
			$nCase = "2";
			$bDate = $chargeList[0]['operdate'];
		}

		else
		{
			$nCase = "3";
		}

		echo $bDate;

		$page_act = "mem_sn=".$memberSn;
		$total 		= $cModel->getMemberTotalCartCount($memberSn, '', '', $cWhere);  // 전체 카트내역
		$pageMaker	= $this->displayPage($total, 10, $page_act);
		$betList = $cModel->getMemberBetList2($memberSn, $pageMaker->first, $pageMaker->listNum, $bDate, date("Y-m-d H:i:s",time()), '', $cWhere);

		$totalBet = 0;		// 총 롤링 금액
		$sportsBet = 0;		// 스포츠 롤링 금액
		$liveBet = 0;		// 실시간 롤링 금액
		$sadariBet = 0;		// 사다리 롤링 금액
		$powerBet = 0;		// 파워볼 롤링 금액

		$totalBetCount = 0;
		$sportsBetCount = 0;
		$liveBetCount = 0;
		$sadariBetCount = 0;
		$powerBetCount = 0;

		$isContinueSadariBet = 0;	// 사다리 조합배팅 연속회수

		echo sizeof($betList);

		for($i=0; $i<sizeof($betList); $i++)
		{
		//	echo $betList[$i]['item'][0]['league_name']."<br>";

			$totalBet = $totalBet + $betList[$i]['betting_money'];
			$totalBetCount++;

			if(strpos($betList[$i]['item'][0]['league_name'], "사다리"))
			{
				$sadariBet = $sadariBet + $betList[$i]['betting_money'];
				$sadariBetCount++;
			}

			else if(strpos($betList[$i]['item'][0]['league_name'], "파워볼") || strpos($betList[$i]['item'][0]['league_name'], "일반볼합"))
			{
				echo "a";

				$powerBet = $powerBet + $betList[$i]['betting_money'];
				$powerBetCount++;
			}

			else if(strpos($betList[$i]['item'][0]['league_name'], "실시간"))
			{
				$liveBet = $liveBet + $betList[$i]['betting_money'];
				$liveBetCount++;
			}

//			print_r ($betList);
		}

		$this->view->assign('betList', $betList);

		$this->view->assign('totalBet', $totalBet);
		$this->view->assign('liveBet', $liveBet);
		$this->view->assign('sadariBet', $sadariBet);
		$this->view->assign('powerBet', $powerBet);

		$this->view->assign('totalBetCount', $totalBetCount);
		$this->view->assign('liveBetCount', $liveBetCount);
		$this->view->assign('sadariBetCount', $sadariBetCount);
		$this->view->assign('powerBetCount', $powerBetCount);

		$this->view->assign('rollingMoney', $chargeList['amount']);
		$this->view->assign('ncase', $nCase);

		$this->view->assign('chargeList', $chargeList);
		$this->view->assign('exchangeList', $exchangeList);

		$this->view->assign('memberSn', $memberSn);
		$this->view->assign('list', $list);
		$this->view->assign('quanxian', $_SESSION["quanxian"]);
		$this->view->assign('memoList', $memoList);
		$this->view->assign('levelList', $levelList);
		$this->view->assign('joiner', $joiner);
		$this->view->assign('joiners', $joiners);
		$this->view->assign('country_code', $country_code);
		$this->view->assign('partner_list', $partnerList);

		$this->display();
	}

	//▶ 회원목록
	function listAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/list.html");

		$model  = $this->getModel("MemberModel");
		$partnerModel = $this->getModel("PartnerModel");
		$configModel = $this->getModel("ConfigModel");
		$eModel = $this->getModel("EtcModel");
		$logoModel = $this->getModel("LogoModel");

		$act 				= $this->request("act");
		$perpage 		= $this->request("perpage");
		$sortField 	= $this->request("sort_field");
		$sortType 	= $this->request("sort_type");
		$field 			= $this->request("field");
		$keyword		= $this->request("keyword");
		$filterPartnerSn 	 = $this->request('filter_partner_sn');
		$filterMemberState = $this->request('filter_state');
		$filterMemberLevel = $this->request('filter_level');
		$filterDomain			 = $this->request('filter_domain');
		$_page						 = $this->request("page");
		$filter_logo 				= $this->request('filter_logo');

		$params = "page=".$_page."&perpage=".$perpage."&sort_field=".$sortField."&sort_type=".$sortType;
		$params.= "&field=".$field."&keyword=".$keyword."&filter_partner_sn=".$filterPartnerSn;
		$params.= "&filter_state=".$filterMemberState."&filter_level=".$filterMemberLevel."&filter_domain=".$filterDomain;

		if($perpage=='')  $perpage = 30;
		if($sortType=='')	$sortType = "desc";

		$logoList = $logoModel->getList();

		if($act=="stop")
		{
			$arrmemidx = $this->request("y_id");
			$str="";
			for($i=0;$i<count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}
			$str = substr($str,0,strlen($str)-1);

			$rs = $model->modifyStatus($str, "stop");

			if($rs>0)	{ throw new Lemon_ScriptException("선택 회원이 차단/정지되었습니다.", "", "go", "/member/list?".$params); }
			else			{ throw new Lemon_ScriptException("정상처리되지 못했습니다.", "", "back", ""); }
		}
		else if($act=="bad")
		{
			$arrmemidx = $this->request("y_id");

			$str="";
			for($i=0;$i<count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}
			$str=substr($str,0,strlen($str)-1);

			$rs = $model->modifyStatus($str, "bad");

			if($rs>0) 	{throw new Lemon_ScriptException("선택 회원이 불량회원처리 되었습니다.", "", "go", "/member/list");}
			else		{throw new Lemon_ScriptException("취소하였습니다.", "", "go", "/member/list");}
		}
		else if( $act=="delete")
		{
			$arrmemidx = $this->request("y_id");
			$str="";
			for($i=0;$i<count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}

			$str=substr($str,0,strlen($str)-1);

			$rs = $model->modifyStatus($str, "delete");

			if($rs>0)	{throw new Lemon_ScriptException("선택 회원이 탈퇴 처리 되었습니다.", "", "go", "/member/list?".$params);}
			else		{throw new Lemon_ScriptException("정상처리되지 못했습니다.", "", "back", "");}
		}
		else if($act == "levelup")
		{
			$filterMemberState='0001';
			$configModel->modifyAlramFlag("new_member", "off");
		}
		else if($act == "modify_level")
		{
			$targetMemberSn = $this->request("modify_member_sn");
			$newLevel				= $this->request("modify_level");
			if($targetMemberSn!="" && $newLevel!="")
			{
				$set = " mem_lev=".$newLevel;
				$model->modifyByParam($targetMemberSn, $set);
			}
		}
		else if($act == "modify_state")
		{
			$targetMemberSn = $this->request("modify_member_sn");
			$newState				= $this->request("modify_state");
			if($targetMemberSn!="" && $newState!="")
			{
				$set = " mem_status='".$newState."'";
				$model->modifyByParam($targetMemberSn, $set);
			}
		}
		else if($act == "modify_domain")
		{
			$targetMemberSn = $this->request("modify_member_sn");
			$newDomain				= $this->request("modify_domain");
			if($targetMemberSn!="" && $newDomain!="")
			{
				$set = " permit_domain='".$newDomain."'";
				$model->modifyByParam($targetMemberSn, $set);
			}
		}
		else if($act == "modify")
		{
			$targetMemberSn 		= $this->request("modify_member_sn");
			$modifyBank					= $this->request("modify_bank_name");
			$modifyBankAccount 	= $this->request("modify_bank_account");
			$modifyBankMember 	= $this->request("modify_bank_member");
			$modifyPhone			 	= $this->request("modify_phone");
			$boardAuth				 	= $this->request("modify_board_auth");

			if($targetMemberSn!="")
			{
				$set = " bank_name='".$modifyBank."',bank_account='".$modifyBankAccount."',bank_member='".$modifyBankMember."',phone='".$modifyPhone."', memo='".$boardAuth."'";
				$model->modifyByParam($targetMemberSn, $set);
			}
		}

		if($keyword!="")
		{
			if($field=="mem_id") 		{ $where.=" and a.uid like '%".$keyword."%'";}
			elseif($field=="nick")	{ $where.=" and a.nick like '%".$keyword."%'";}
			elseif($field=="join_recommend")
			{
				$joinRecommendNick=$keyword;
			}
			elseif($field=="mem_ip")			{$where.=" and a.mem_ip like('%".$keyword."%') ";}
			elseif($field=="bank_member")	{$where.=" and a.bank_member like '%".$keyword."%'";}
		}

		if($filterPartnerSn!='')
		{
			$levelCode = sprintf("%04d", $filterPartnerSn);
			$where=" and a.level_code like('".$levelCode."%')";
		}

		$flag=0;
		if($filterMemberState!='' && strpos($filterMemberState, '1')!==false)
		{
			$where.=" and (";

			if(substr($filterMemberState,0,1)=="1")
			{
				if($flag==1)	{$where.=" or a.mem_status='S' ";}
				else 					{$where.=" a.mem_status='S' "; $flag=1;}
			}
			if(substr($filterMemberState,1,1)=="1")
			{
				if($flag==1)	{$where.=" or a.mem_status='N' ";}
				else 					{$where.=" a.mem_status='N' "; $flag=1;}
			}
			if(substr($filterMemberState,2,1)=="1")
			{
				if($flag==1)	{$where.=" or a.mem_status='G' ";}
				else 					{$where.=" a.mem_status='G' "; $flag=1;}
			}
			if(substr($filterMemberState,3,1)=="1")
			{
				if($flag==1)	{$where.=" or a.mem_status='W' ";}
				else 					{$where.=" a.mem_status='W' "; $flag=1;}
			}
			$where.=" )";
		}

		//$domainList	 = $configModel->getDomainList();
		$flag=0;
		if($filterDomain!='' && strpos($filterDomain, '1')!==false)
		{
			$where.=" and (";

			for($i=0; $i<sizeof($domainList); ++$i)
			{
				if(substr($filterDomain,$i,1)=="1")
				{
					if($flag==1)	{$where.=" or a.login_domain like '%".$domainList[$i]['url']."'";}
					else 					{$where.=" a.login_domain like '%".$domainList[$i]['url']."'"; $flag=1;}
				}
			}

			$where.=" )";
		}

		$configRows  = $configModel->getLevelConfigRows();

		$flag=0;
		if($filterMemberLevel!='' && strpos($filterMemberLevel, '1')!==false)
		{
			$where.=" and (";
			for($i=0; $i<sizeof($configRows); ++$i)
			{
				if(substr($filterMemberLevel,$i,1)=="1")
				{
					if($flag==1)	{$where.=" or a.mem_lev=".$configRows[$i]['lev'];}
					else					{$where.=" a.mem_lev=".$configRows[$i]['lev']; $flag=1;}
				}
			}
			$where.=" )";
		}
		$orderby='';

		if($sortField=="regdate")						$orderby=" order by regdate ".$sortType;
		else if($sortField=="virtual_money")	$orderby=" order by virtual_money ".$sortType;
		else if($sortField=="g_money")			$orderby=" order by g_money ".$sortType;
		else if($sortField=="benefit")				$orderby=" order by ((old_charge_sum+charge_sum)-(old_exchange_sum+exchange_sum)) ".$sortType;
		else if($sortField=="visit_count")			$orderby=" order by visit_count ".$sortType;
		else if($sortField=="recent_visit")			$orderby=" order by last_date ".$sortType;

		$page_act = "act=".$act."&perpage=".$perpage."&sort_field=".$sortField."&sort_type=".$sortType;
		$page_act.= "&field=".$field."&keyword=".$keyword."&filter_partner_sn=".$filterPartnerSn;
		$page_act.= "&filter_state=".$filterMemberState."&filter_level=".$filterMemberLevel."&filter_domain=".$filterDomain."&filter_logo=".$filter_logo;;

		$total 			= $model->getTotal($where, $joinRecommendNick, $filter_logo);
		$pageMaker 	= $this->displayPage($total, 10, $page_act);
		$list 			= $model->getList($where, $pageMaker->first, $pageMaker->listNum, $orderby, $joinRecommendNick, $filter_logo);


		for($i=0; $i<sizeof($list); ++$i)
		{
			$rs = $eModel->getLevel2($list[$i]['logo']);

			$list[$i]['level_type'] = $rs;
			$list[$i]['permit_domain_list'] = $domainList;
		}

		for($i=0; $i<sizeof($list); ++$i)
		{
			for($j=0; $j<sizeof($logoList); ++$j)
			{
				if($logoList[$j]['name'] == $list[$i]['logo'])
				{
					$list[$i]['logo_name'] = $logoList[$j]['name'];
					$list[$i]['logo_nick'] = $logoList[$j]['nick'];
					$list[$i]['logo_color'] = $logoList[$j]['color'];
				}
			}
		}

		$partnerList	= $partnerModel->getPartnerIdList();

		$this->view->assign('quanxian', $_SESSION["quanxian"]);
		$this->view->assign('filter_logo', $filter_logo);
		$this->view->assign('keyword', $keyword);
		$this->view->assign('field', $field);
		$this->view->assign('sort_field', $sortField);
		$this->view->assign('sort_type', $sortType);
		$this->view->assign('filter_member_state', $filterMemberState);
		$this->view->assign('filter_level', $filterMemberLevel);
		$this->view->assign('filter_domain', $filterDomain);
		$this->view->assign('filter_partner_sn', $filterPartnerSn);
		$this->view->assign('list', $list);
		$this->view->assign('logolist', $logoList);
		//$this->view->assign('domain_list', $domainList);
		$this->view->assign('partner_list', $partnerList);
		$this->view->assign('config_rows', $configRows);
		$this->view->assign('level_list', $levelList);
		//echo $_page;
		$this->view->assign('current_page', $_page);

		$this->display();
	}

	//▶ 불량회원
	function badlistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/bad_list.html");

		$model 	= Lemon_Instance::getObject("MemberModel", true);
		$eModel = $this->getModel("EtcModel");

		$nname 	= $this->request("username");
		$mode	= $this->request("mode");

		if(!is_null($nname) && $nname!="")
		{
			$where =" and mem_id like '%".$nname."%'";
		}

		if($mode == "change")
		{
			$idx = $this->request("idx");
			$model->modifyStatus($idx,"good");

			throw new Lemon_ScriptException("전환 되였습니다.", "", "go", "/member/badlist");
		}

		if($mode=="allchange")
		{
			$arrmemidx = $this->request("y_id");

			$str="";
			for($i=0;$i<count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}
			$str = substr($str,0,strlen($str)-1);
			$model->modifyStatus($str, "good");

			throw new Lemon_ScriptException("", "", "go", "/member/badlist");

		}

		// level Name
		$arr_mem_lev = array();
		$rs = $eModel->getLevel();
		for( $i=0; $i < sizeof($rs); ++$i )
		{
			$level = $rs[$i]['lev'];
			$levelName = $rs[$i]['lev_name'];
			$arr_mem_lev[ $level ] = $levelName;
		}

		$total = $model->getBadTotal($where);

		$pageMaker = $this->displayPage($total, 10);
		$list = $model->getBadList($where, $pageMaker->first, $pageMaker->listNum);

		$this->view->assign('nname', $nname);
		$this->view->assign('quanxian', $_SESSION["quanxian"]);
		$this->view->assign('arr_mem_lev', $arr_mem_lev);
		$this->view->assign('str', $str);
		$this->view->assign('field', $field);
		$this->view->assign('list', $list);

		$this->display();
	}

	//▶ 접속기록
	function loginlistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/login_list.html");

		$model				= $this->getModel("MemberModel");
		$eModel				= $this->getModel("EtcModel");
		$loginModel		= $this->getModel("LoginModel");
		$partnerModel = $this->getModel("PartnerModel");
		$configModel	= $this->getModel("ConfigModel");

		$act = $this->request("act");
		$perpage = $this->request("perpage");
		$filter_logo 				= $this->request('filter_logo');

		if($perpage=='') $perpage=30;

		if($act=="delete_user")
		{
			$arrmemidx = $this->request("y_id");

			$str="";
			for($i=0;$i<count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}
			$str=substr($str,0,strlen($str)-1);

			$loginModel->del($str);
		}
		else if($act=="deleteone")
		{
			$idx = $this->request("idx");

			$loginModel->del($idx);
		}
		else if($act=="recent3hour")
		{
			$where = " and b.visit_date between date_add(now(), interval -3 hour) and now() ";
		}

		$filter_partnerIdx					= $this->request("partner_idx");
		$filter_domainName					= $this->request("domain_name");
		$isLogin_fail								= $this->request("isLogin_fail");

		if($filter_partnerIdx!="") {
			$where.=" and a.recommend_sn='".$filter_partnerIdx."'";
		}
		if($filter_domainName!="") {
			$where.=" and a.login_domain like '%".$filter_domainName."'";
		}
		if($isLogin_fail=="on") {
			$where.=" and b.status='1'";
		}

		$field 		= Trim($this->request("field"));
		$keyword 	= $this->request("keyword");

		if($field=="member_id") 				{$where.=" and b.member_id like '%".$keyword."%'";}
		else if($field=="nick") 				{$where.=" and a.nick like '%".$keyword."%'";}
		else if($field=="bank_member") 	{$where.=" and a.bank_member like '%".$keyword."%'";}
		else if($field=="visit_ip")			{$where.=" and b.visit_ip like('%".$keyword."%') ";}

		if($filter_logo!="")
		{
			$where.=" and a.logo='".$filter_logo."'";
		}

		// level Name
		$arr_mem_lev = array();
		$rs = $eModel->getLevel();
		for( $i=0; $i < sizeof($rs); ++$i )
		{
			$level = $rs[$i]['lev'];
			$levelName = $rs[$i]['lev_name'];
			$arr_mem_lev[ $level ] = $levelName;
		}

		$page_act = "act=".$act."&field=".$field."&keyword=".$keyword."&perpage=".$perpage."&partner_idx=".$filter_partnerIdx."&domain_name=".$filter_domainName."&isDuplication_connection=".$isDuplication_connection."&isLogin_fail=".$isLogin_fail."&filter_logo=".$filter_logo;
		$total 		= $loginModel->getTotal($where);
		$pageMaker 	= $this->displayPage($total, $perpage, $page_act);
		$partnerList	= $partnerModel->getPartnerIdList();
		$domainList	 = $configModel->getDomainList();
		$list 		= $loginModel->getList($where, $pageMaker->first, $pageMaker->listNum);

		$this->view->assign('nname', $nname);
		$this->view->assign('quanxian', $_SESSION["quanxian"]);
		$this->view->assign('arr_mem_lev', $arr_mem_lev);
		$this->view->assign('keyword', $keyword);
		$this->view->assign('field', $field);
		$this->view->assign('partnerList', $partnerList);
		$this->view->assign('domainList', $domainList);
		$this->view->assign('filter_partner_idx', $filter_partnerIdx);
		$this->view->assign('filter_domain_name', $filter_domainName);
		$this->view->assign('isDuplication_connection', $isDuplication_connection);
		$this->view->assign('isLogin_fail', $isLogin_fail);
		$this->view->assign('list', $list);
		$this->view->assign('filter_logo', $filter_logo);

		$this->display();
	}

	//▶ 레벨관리
	function levelConfigAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/level_config.html");

		$model 	= Lemon_Instance::getObject("MemberModel", true);
		$cModel 	= Lemon_Instance::getObject("ConfigModel", true);
		$eModel 	= Lemon_Instance::getObject("EtcModel", true);

		$strvalue = $this->request("strid");

		if($strvalue!="")
		{
			$strvarr = explode(",",$strvalue);
			for($i=0;$i<count($strvarr);$i++)
			{
				$lev_name 		= $this->request("lev_name".$strvarr[$i]);
				$lev_min_money 	= str_replace(",","",$this->request("lev_min_money".$strvarr[$i]));
				$lev_max_money 	= str_replace(",","",$this->request("lev_max_money".$strvarr[$i]));
				$lev_max_bouns 	= str_replace(",","",$this->request("lev_max_bouns".$strvarr[$i]));

				$eModel->modifyLevelConfig($strvarr[$i], $lev_name, $lev_min_money, $lev_max_money, $lev_max_bouns);
			}
			throw new Lemon_ScriptException("수정완료");
		}

		$list = $cModel->getLevelConfigRows();

		for($i=0; $i<sizeof($list); ++$i)
		{
			$ids .= $list[$i]['id'];
		}
		if(strlen($ids)>0)
			$ids = substr($ids, 0, strlen($ids)-1);

		$this->view->assign('list',$list);
		$this->view->assign('ids', $ids);

		$this->display();
	}

	//▶ 회원등록
	function addAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/add.html");

		$memberModel 	= Lemon_Instance::getObject("MemberModel", true);

		$act	= $this->request('act');

		if($act=="add")
		{
			$logo	= $this->post("logo");
			$uid	= $this->post("mem_id");
			$nick	= $this->post("nick");
			$name	= $this->post("name");
			$pwd	= $this->post("psw");
			$phone	= $this->post("phone");
			$mail	= $this->post("mail");

			if($logo=='')
			{
				$logo='totobang';
			}

			if($memberModel->isMember($uid))
			{
				throw new Lemon_ScriptException("이미 가입된 아이디 입니다.");
				exit;
			}

			$rs = $memberModel->getByName($nick);
			if(sizeof($rs)>0)
			{
				throw new Lemon_ScriptException("이미 가입된 닉네임  입니다.");
				exit;
			}

			$rs = $memberModel->joinAdd($uid, $pwd, "1234", $nick, $name, $phone, 0, "G", "", "", "", "", "", "127.0.0.1", $logo);

			if($rs>0)
				throw new Lemon_ScriptException("새로운 회원이 등록 되였습니다.","","go","/member/add");
			else
				throw new Lemon_ScriptException("회원 등록에 실패 하였습니다.","","go","/member/add");
		}

		$this->display();
	}

	//▶ 상세정보
	function popup_detailAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}

		$this->view->define("content","content/member/popup.detail.html");

		$model 	= Lemon_Instance::getObject("MemberModel", true);
		$partnerModel = $this->getModel("PartnerModel");
		$mModel = $this->getModel("MemoModel");
		$eModel = $this->getModel("EtcModel");

		$memberSn 	= $this->request("idx");
		$mode		= $this->request("mode");

		if($mode == "delete_note" )
		{
			$noteSn = $this->request("note_sn");

			$model->delNote($noteSn);

			$url = "/member/popup_detail?idx=".$memberSn;
			throw new Lemon_ScriptException("삭제 되였습니다.","","go",$url);
			exit;
		}
		if($mode == "deleteuser" )
		{
			$model->del($memberSn);
		}

		if($mode=="modify")
		{
			$memid				= $this->request("memid");
			$urlidx				= $this->request("urlidx");
			$pwd					= $this->request("pwd");
			$bank_name		= trim($this->request("bank_name"));
			$bank_member	= trim($this->request("bank_member"));
			$bank_count		= trim($this->request("bank_count"));
			$mem_lev			= trim($this->request("mem_lev"));
			$email				= trim($this->request("email"));
			$phone				= $this->request("phone");
			$memo					= $this->request("memo");
			$memberStatus	= $this->request("member_status");
			$exchangePwd	= $this->request("exchange_pass");
			$recommendSn	= $this->request("recommend_sn");

			if($pwd=="default")
			{
				$pwd="";
				$where="";
			}
			else
			{
				$where="upass='".$pwd."',";
			}

			$model->modify($where, $bank_name, $bank_count, $bank_member, $chu_member, $mem_lev, $email, $phone, $memo, $memid, $memberStatus, $exchangePwd, $recommendSn);

			$url = "/member/popup_detail?idx=".$urlidx;
			throw new Lemon_ScriptException("수정 되었습니다.","","go",$url);

			exit;
		} // end of 'add'


		$memoList 		= $mModel->getMemberNote($memberSn);
		$list 			= $model->member($memberSn);

		$levelList		= $eModel->getLevel2($list['logo']);
		$country_code	= $eModel->getNationByIp($list['mem_ip']);
		$partnerList = $partnerModel->getPartnerIdList($list['logo']);
		$joiner_sn = $model->getJoiner($memberSn);

		if($joiner_sn!="")
		{
			$joiner = $model->getBySn($joiner_sn);
		}
		$joiners = $model->getJoiners($memberSn);


		$this->view->assign('memberSn', $memberSn);
		$this->view->assign('list', $list);
		$this->view->assign('quanxian', $_SESSION["quanxian"]);
		$this->view->assign('memoList', $memoList);
		$this->view->assign('levelList', $levelList);
		$this->view->assign('joiner', $joiner);
		$this->view->assign('joiners', $joiners);
		$this->view->assign('country_code', $country_code);
		$this->view->assign('partner_list', $partnerList);

		$this->display();
	}


	function popup_joinersAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/popup.joiners.html");

		$model 	= Lemon_Instance::getObject("MemberModel", true);
		$memberSn 	= $this->request("sn");

		$joiners = $model->getJoiners($memberSn);

		$this->view->assign('joiners', $joiners);

		$this->display();
	}

	//▶ 베팅정보
	function popup_betAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/popup.bet_list.html");

		$model = $this->getModel("CartModel");

		$mem_sn	= $this->request("mem_sn");
		$betting_no 	= $this->request("betting_no");

		if($betting_no!="")
		{
			$where = " and betting_no='".$betting_no."'";
		}

		$page_act = "mem_sn=".$mem_sn;

		$total 		= $model->getMemberTotalCartCount($mem_sn, '', '', $where);  // 전체 카트내역
		$pageMaker	= $this->displayPage($total, 10, $page_act);
		$list = $model->getMemberBetList($mem_sn, $pageMaker->first, $pageMaker->listNum, '', '', '', $where);


		$this->view->assign('betting_no', $betting_no);
		$this->view->assign('mem_sn', $mem_sn);
		$this->view->assign('list', $list);

		$this->display();
	}

	function popup_live_game_betting_listAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/popup_live_game_betting_list.html");

		$model = Lemon_Instance::getObject("CartModel",true);

		$member_sn 	= $this->request("mem_sn");
		$betting_no 	= $this->request("betting_no");
		$perpage		= $this->request("perpage");

		if($perpage=='') 		{$perpage = 10;}

		if($betting_no!="")
		{
			$where = " and betting_no='".$betting_no."'";
		}
		$page_act = "member_sn=".$member_sn."&betting_no=".$betting_no."&perpage=".$perpage;

		$total 		= $model->getLiveGameMemberBettingListTotal($member_sn, '', '', $where);  // 전체 카트내역
		$pageMaker	= $this->displayPage($total, $perpage, $page_act);
		$list = $model->getLiveGameMemberBettingList($member_sn, $pageMaker->first, $pageMaker->listNum, '', '', '', $where);

		$this->view->assign('perpage', $perpage);
		$this->view->assign('member_sn', $member_sn);
		$this->view->assign('betting_no', $betting_no);
		$this->view->assign('list', $list);


		$this->display();
	}

	//▶ 회원 계좌 목록
	function popup_bank_accountAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/etc/bank_account_list.html");

		$memberModel = $this->getModel("MemberModel");

		$memberSn	= $this->request("member_sn");

		$list = $memberModel->getMemberBankList($memberSn);

		$this->view->assign('list', $list);
		$this->display();
	}

	//▶ 로그인정보
	function popup_loginlistAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/popup.login_list.html");
		$model 	= $this->getModel("StatModel");
		$mModel = $this->getModel("MemberModel");
		$eModel 	= $this->getModel("EtcModel");
		$loginModel 	= $this->getModel("LoginModel");

		$act = $this->request("act");

		if($act=="delete_user")
		{
			$arrmemidx = $this->request("y_id");
			$str="";
			for($i=0;$i<count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}
			$str = substr($str,0,strlen($str)-1);

			$loginModel->del($str);
		}
		if($act=="deleteone")
		{
			$idx = $this->request("idx");

			$loginModel->del($idx);
		}

		$field	= $this->request("field");
		$str		= $this->request("username");

		if($field=="member_id")
		{
			$where = " and b.member_id like '%".$str."%'";
		}
		else if($field=="visit_ip")
		{
			$where = " and b.visit_ip ='".$str."' ";
		}

		$total 		= $loginModel->getTotal($where);
		$pageMaker 	= $this->displayPage($total, 10);
		$list 			= $loginModel->getList($where, $pageMaker->first, $pageMaker->listNum);

		$rs = $eModel->getLevel();
		$levelList = array();
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$level = $rs[$i]['lev'];
			$lev_name = $rs[$i]['lev_name'];
			$levelList[$level] = $lev_name;
		}

		$this->view->assign('str', $str);
		$this->view->assign('field', $field);
		$this->view->assign('levelList', $levelList);
		$this->view->assign('list', $list);
		$this->display();
	}

	//▶ 회원메모쓰기
	function popup_notewriteAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/popup.note_write.html");

		$model 	= Lemon_Instance::getObject("MemberModel", true);

		$act 			= $this->request('act');
		$mem_idx		= $this->request("idx");
		$context_idx	= $this->request("context_idx");

		if($act=="send")
		{
			$regdate 	= $this->request("regdate");
			$content	= htmlspecialchars($this->request("content"));

			if($regdate == "")
			{
				$model->writeNote($mem_idx, $content);
				throw new Lemon_ScriptException("","","script","alert('글쓰기가 성공 하였습니다.'); self.close();");
			}
			else
			{
				$model->modifyNote($context_idx);
				throw new Lemon_ScriptException("","","script","alert('수정되었습니다.'); self.close();");
			}
		}
		if( $act== "view" )
		{
			$memo = $model->getNote($context_idx);
			$this->view->assign('memo', $memo);
		}

		$this->view->assign('content', $content);
		$this->view->assign('regdate', $regdate);
		$this->view->assign('mem_idx', $mem_idx);
		$this->view->assign('context_idx', $context_idx);
		$this->display();
	}

	//▶ 회원메모쓰기
	function noteProcessAction()
	{
		$memberModel = Lemon_Instance::getObject("MemberModel", true);

		$act = $this->request("act");
		$memberSn = $this->request("member_sn");

		if($act=='add')
		{
			$content = htmlspecialchars($this->request("content"));
			$memberModel->writeNote($memberSn, $content);
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=/member/popup_detail?idx=".$memberSn."'>";
		}
		else if($act=='modify')
		{
			$noteSn = $this->request("note_sn");
			$content = htmlspecialchars($this->request("content"));
			$memberModel->modifyNote($noteSn, $content);
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=/member/popup_detail?idx=".$memberSn."'>";
		}
		else if($act=='delete')
		{
			$noteSn = $this->request("note_sn");
			$memberModel->delNote($noteSn);


			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=/member/popup_detail?idx=".$memberSn."'>";
		}
	}

	//▶ 회원머니수정
	function popup_moneychangeAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/member/popup_moneychange.html");

		$model 		= Lemon_Instance::getObject("MemberModel", true);
		$moneyModel = Lemon_Instance::getObject("MoneyModel", true);
		$pModel		= Lemon_Instance::getObject("ProcessModel", true);

		$idx 	= $this->request('idx');
		$act 	= $this->request('act');
		$mode 	= $this->request('mode');

		if(!strpos($_SESSION["quanxian"],"1001"))
		{
			throw new Lemon_ScriptException("","","script","alert('사용하고 있는 관리자 계정은 권한이 없습니다.  관리자와 연락하여 주십시오.'); window.close();");
			exit();
		}

		$field = "sn";
		$rs = $model->getMemberField($idx, $field, "");

		if($rs<=0)
		{
			throw new Lemon_ScriptException("","","script","alert('존재하지 않는 회원입니다.'); window.close();");
			exit();
		}

		if($mode=="add")
		{
			$type 		= $this->request("type");
			$radio 		= $this->request("radio");
			$memo 		= $this->request("memo");
			$g_money 	= $this->request("g_money");
			$g_money 	= str_replace(",","",$g_money);

			//증가
			if($radio == 0)
			{
				if($type=="money")
					$pModel->modifyMoneyProcess($idx, $g_money, "7", "관리자수정", $memo);
				else if($type=="virtual_money")
					$pModel->modifyVirtualMoneyProcess($idx, $g_money, "7", "관리자수정", $memo);
				else if($type=="mileage")
					$pModel->modifyMileageProcess($idx, $g_money, "7", "관리자수정", 0, '', $memo);
			}
			//차감
			else
			{
				if($type=="money")
					$pModel->modifyMoneyProcess($idx, -$g_money, "7", "관리자수정", $memo);
				else if($type=="virtual_money")
					$pModel->modifyVirtualMoneyProcess($idx, -$g_money, "7", "관리자수정", $memo);
				else if($type=="mileage")
					$pModel->modifyMileageProcess($idx, -$g_money, "7", "관리자수정", 0, '', $memo);
			}

			throw new Lemon_ScriptException("","","script","alert('처리되었습니다 '); opener.document.location.reload(); self.close();");

		}
		else
		{
			$field = "*";
			$where = "sn= '".$idx."'";
			$rs = $model->getMemberRows( $field, $where );

			$mem_id = 	$rs[0]["uid"];
			$mem_idx = 	$rs[0]["sn"];
			$cash = $rs[0]["g_money"];
			$strshow="회원 보유머니 수정";
			$point = $rs[0]["point"];
			$virtual_money = $rs[0]["virtual_money"];
		}

		$this->view->assign('strshow',$strshow);
		$this->view->assign('idx', $idx);
		$this->view->assign('act', $act);
		$this->view->assign('cash', $cash);
		$this->view->assign('point', $point);
		$this->view->assign('virtual_money', $virtual_money);
		$this->view->assign('mem_id', $mem_id);


		$this->display();
	}

	function issue_virtual_moneyAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}

		$this->view->define("content","content/member/popup_issue_virtual_money.html");

		$memberModel 		= $this->getModel("MemberModel");
		$configModel 	= $this->getModel("ConfigModel");

		$act = $this->request('act');

		if($act=="send")
		{
			$sent = 0;
			$filter_logo = $this->request("filter_logo");
			$toid_level = $this->request("toid_level");
			$amount	= $this->request("amount");
			$amount = str_replace(",","",$amount);

			$where=" mem_status!='S' ";

			if($filter_logo!="")
			{
				$where.=" and logo='".$filter_logo."'";
			}

			if($toid_level!="")
			{
				$where.=" and mem_lev=".$toid_level;
			}

			$sent = $memberModel->issueVirtualMoney($amount, $where);

			if(sizeof($sent) > 0)
				throw new Lemon_ScriptException("","","script","alert('지급되었습니다.');self.close();");
		}

		$levelList 		= $configModel->getLevelConfigRows("lev,lev_name");

		$this->view->assign("level_list", $levelList);
		$this->display();
	}
}

?>
