<?php


class ChargeController extends WebServiceController
{
	//▶ 입금신청
	function listAction()
	{
		$this->commonDefine();
		
		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/charge/list.html");

		$model 	= $this->getModel("MoneyModel");
		$partnerModel = $this->getModel("PartnerModel");

		$act 			= $this->request('act');

		$perpage 					= $this->request('perpage');
		$keyword 					= $this->request('keyword');
		$filterPartnerSn  = $this->request('filter_partner_sn');
		$beginDate  			= $this->request('begin_date');
		$endDate 					= $this->request('end_date');

		$dateType  				= $this->request('date_type');
		$filter_logo 				= $this->request('filter_logo');

		if($perpage=='') $perpage = 30;

		if($filterPartnerSn!='')
		{
			$levelCode = sprintf("%04d", $filterPartnerSn);
			$where=" and b.level_code like('".$levelCode."%')";
		}
		if($keyword!="")
		{
			$field = $this->request('field');

			if($field=="uid")				{$where.=" and b.uid like('%".$keyword."%')";}
			else if($field=="nick")	{$where.=" and b.nick like('%".$keyword."%')";}
			else if($field=="bank_owner")	{$where.=" and a.bank_owner like('%".$keyword."%')";}
		}


		if($beginDate!="") {
			$where.= " and a.".$dateType.">='".$beginDate." 00:00:00'";
		}

		if($endDate!="")	 {
			$where.= " and a.".$dateType."<='".$endDate." 23:59:59'";
		}

		if($filter_logo!="") {
			$where .= " and a.logo='".$filter_logo."'";
		}

		$page_act = "perpage=".$perpage."&keyword=".$keyword."&begin_date=".$beginDate."&end_date=".$endDate."&field=".$field."&act=".$act."&date_type=".$dateType."&filter_partner_sn=".$filterPartnerSn."&filter_logo=".$filter_logo;

		$total 			= $model->getChargeTotal("0", $where);
		$pageMaker	= $this->displayPage($total, $perpage, $page_act);
		$list 			= $model->getChargeList("0", 1, $where, $pageMaker->first, $pageMaker->listNum);

		$partnerList = $partnerModel->getPartnerIdList();

		$this->view->assign('keyword', $keyword);
		$this->view->assign('field', $field);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('date_type', $dateType);
		$this->view->assign('filter_partner_sn', $filterPartnerSn);
		$this->view->assign('filter_logo', $filter_logo);
		$this->view->assign('list', $list);
		$this->view->assign('partner_list', $partnerList);

		$this->display();
	}

	//▶ 입금완료 리스트
	function finlistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/charge/fin_list.html");

		$keyword 					= $this->request('keyword');
		$perpage 					= $this->request('perpage');
		$filterPartnerSn  = $this->request('filter_partner_sn');
		$beginDate  			= $this->request("begin_date");
		$endDate 					= $this->request("end_date");
		$dateType  				= $this->request('date_type');

		$model = $this->getModel("MoneyModel");
		$partnerModel = $this->getModel("PartnerModel");

		if($perpage=='') $perpage = 30;

		$where = "";

		if($filterPartnerSn!='')
		{
			$levelCode = sprintf("%04d", $filterPartnerSn);
			$where=" and b.level_code like('".$levelCode."%')";
		}

		if($keyword!="")
		{
			$field = $this->request("field");

			if($field=="uid")							{$where.=" and b.uid like('%".$keyword."%')";}
			else if($field=="nick")				{$where.=" and b.nick like('%".$keyword."%')";}
			else if($field=="bank_owner")	{$where.=" and a.bank_owner like('%".$keyword."%')";}
		}

		$page_act = "perpage=".$perpage."&keyword=".$keyword."&begin_date=".$beginDate."&end_date=".$endDate."&field=".$field."&act=".$act."&date_type=".$dateType."&filter_partner_sn=".$filterPartnerSn;

		if($beginDate!="") {$where.= " and a.".$dateType.">='".$beginDate." 00:00:00'";}
		if($endDate!="")	 {$where.= " and a.".$dateType."<='".$endDate." 23:59:59'";}

		$total 				= $model->getChargeTotal("1", $where);
		$pageMaker		= $this->displayPage($total, $perpage, $page_act);
		$list 				= $model->getChargeList("1", "", $where, $pageMaker->first, $pageMaker->listNum);
		$partnerList = $partnerModel->getPartnerIdList();

		$this->view->assign('keyword', $keyword);
		$this->view->assign('field', $field);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('date_type', $dateType);
		$this->view->assign('filter_partner_sn', $filterPartnerSn);
		$this->view->assign('list', $list);
		$this->view->assign('partner_list', $partnerList);

		$this->display();
	}

	//▶ 입금완료 리스트
	function finlist_editAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/charge/fin_list_edit.html");

		$keyword 					= $this->request('keyword');
		$perpage 					= $this->request('perpage');
		$filterPartnerSn  = $this->request('filter_partner_sn');
		$beginDate  			= $this->request("begin_date");
		$endDate 					= $this->request("end_date");
		$dateType  				= $this->request('date_type');
		$filter_logo  = $this->request('filter_logo');
		$filter_state  = $this->request('filter_state');

		$model = $this->getModel("MoneyModel");
		$partnerModel = $this->getModel("PartnerModel");

		if($perpage=='') $perpage = 30;

		$where = "";

		if($filterPartnerSn!='')
		{
			$levelCode = sprintf("%04d", $filterPartnerSn);
			$where=" and b.level_code like('".$levelCode."%')";
		}

		if($filter_logo!='') {$where=" and a.logo='".$filter_logo."'";}

		if($filter_state!='') {$where=" and a.state='".$filter_state."'";}

		if($keyword!="")
		{
			$field = $this->request("field");

			if($field=="uid")							{$where.=" and b.uid like('%".$keyword."%')";}
			else if($field=="nick")				{$where.=" and b.nick like('%".$keyword."%')";}
			else if($field=="bank_owner")	{$where.=" and a.bank_owner like('%".$keyword."%')";}
		}

		$page_act = "perpage=".$perpage."&keyword=".$keyword."&begin_date=".$beginDate."&end_date=".$endDate."&field=".$field."&act=".$act."&date_type=".$dateType."&filter_partner_sn=".$filterPartnerSn."&filter_logo=".$filter_logo."&filter_state=".$filter_state;

		if($beginDate!="") {$where.= " and a.".$dateType.">='".$beginDate." 00:00:00'";}
		if($endDate!="")	 {$where.= " and a.".$dateType."<='".$endDate." 23:59:59'";}

		$total 				= $model->getChargeTotal("", $where);
		$pageMaker		= $this->displayPage($total, $perpage, $page_act);
		$list 				= $model->getChargeList("", "", $where, $pageMaker->first, $pageMaker->listNum);

		$partnerList = $partnerModel->getPartnerIdList();

		$logoModel = $this->getModel("LogoModel");
		$logoList = $logoModel->getList();
		$logos = $logoModel->getList();

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

		$this->view->assign('logolist', $logos);
		$this->view->assign('keyword', $keyword);
		$this->view->assign('field', $field);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('date_type', $dateType);
		$this->view->assign('filter_partner_sn', $filterPartnerSn);
		$this->view->assign('list', $list);
		$this->view->assign('partner_list', $partnerList);
		$this->view->assign('filter_logo', $filter_logo);
		$this->view->assign('filter_state', $filter_state);

		$this->display();
	}

	//▶ 입금승인
	function agreeAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/charge/agree.html");

		$model 	= $this->getModel("MoneyModel");
		$mModel = $this->getModel("MemberModel");
		$pModel = $this->getModel("ProcessModel");

		$mode		= $this->request("mode");
		$chargeSn	= $this->request("charge_sn");
		$bonus 		= $this->request("bonus");

		$rs = $model->getChargeRow($chargeSn);
		$amount 	= $rs['amount'];
		$memberSn 	= $rs['member_sn'];
		$uid		= $mModel->getMemberField($memberSn, 'uid');
		$bonus = str_replace(",","",$bonus);
		$maxBonus = ($amount*10)/100;

		$charge_list = $model->getRows("*", $model->db_qz."charge_log", "member_sn=".$memberSn);

		if($mode=="process")
		{
			$agreeAmount = $this->request("amount");
			$amount = str_replace(",","",$agreeAmount);

			if($bonus>$maxBonus)
			{
				throw new Lemon_ScriptException("","","script","alert('충전 금액의 10%이상 보너스를 지급할 수 없습니다.');opener.document.location.reload(); self.close();");
				exit;
			}

			if($model->isCharged($chargeSn))
			{
				throw new Lemon_ScriptException("","","script","alert('이미 충전 되였습니다.');opener.document.location.reload(); self.close();");
				exit;
			}

			$pModel->chargeProcess($chargeSn, $memberSn, $amount, $bonus);

			throw new Lemon_ScriptException("","","script","alert('처리 되였습니다.');opener.document.location.reload(); self.close();");
			exit;
		}

		$this->view->assign('chargeSn', $chargeSn);
		$this->view->assign('uid', $uid);
		$this->view->assign('amount', $amount);
		$this->view->assign('maxBonus', $maxBonus);
		$this->view->assign('first_charge', sizeof($charge_list));
		$this->display();
	}

	//▶ 입금 신청 삭제
	function delProcessAction()
	{
		$model = $this->getModel("ProcessModel");

		$sn	= $this->request("sn");
		$rs = $model->delchargeReq($sn);
		if($rs<=0)
		{
			throw new Lemon_ScriptException("이미 취소 되었습니다","","go","/charge/finlist_edit");
			exit;
		}
		throw new Lemon_ScriptException("처리 되였습니다.","","go","/charge/finlist_edit");
	}

	//▶ 취소
	function cancelProcessAction()
	{
		$pModel = $this->getModel("ProcessModel");

		$sn	= $this->request("sn");
		$rs = $pModel->chargeCancelProcess($sn);
		if($rs<=0)
		{
			throw new Lemon_ScriptException("","","script","alert('이미 취소 되었습니다.');opener.document.location.reload(); self.close();");
			exit;
		}
		throw new Lemon_ScriptException("","","script","alert('처리 되였습니다.');opener.document.location.reload(); self.close();");
	}

	//▶ 충전정보
	function popup_chargeAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/charge/popup.list.html");

		$model 	= $this->getModel("MoneyModel");
		$member_model = $this->getModel("MemberModel");

		$uid = $this->request('mem_id');
		$sn = $member_model->getSn($uid);

		if($uid!="")
		{
			$field = $this->request('field');
		}

		$date_id = $this->request('date_id');
		$seldate = $this->request('seldate');

		if($date_id!="")
		{
			$where.= " and a.".$seldate.">='".$date_id."'";
		}

		$date_id1 = $this->request('date_id1');
		if($date_id1!="")
		{
			$where.= " and a.".$seldate."<='".$date_id1."'";
		}

		$pageact="mem_id=".$uid."&field=".$field."&date_id1=".$date_id1."&date_id=".$date_id."&seldate=".$seldate;

		$total		= $model->getMemberChargeTotal($sn, "", $where);
		$pageMaker 	= $this->displayPage($total, 10, $pageact);
		$list 		= $model->getMemberChargeList($sn, "", $where, $pageMaker->first, $pageMaker->listNum);

		$this->view->assign('uid', $uid);
		$this->view->assign('field', $field);
		$this->view->assign('date_id1', $date_id1);
		$this->view->assign('date_id', $date_id);
		$this->view->assign('seldate', $seldate);
		$this->view->assign('list', $list);

		$this->display();
	}
}

?>
