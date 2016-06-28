<?php
class MemberModel extends Lemon_Model
{
	//▶ 필드 데이터
	function getMemberField($sn, $field, $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$where = "sn=".$sn;

		if($addWhere!='') {
			$where .=' and '.$addWhere;
		}

		$rs = $this->getRow($field, $this->db_qz.'member', $where);

		return $rs[$field];
	}

	//▶ 필드 데이터
	function getMemberRow($sn, $field='*', $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$where = "sn=".$sn;

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRow($field, $this->db_qz.'member', $where);
	}

	//▶ 필드 데이터's
	function getMemberRows($field, $addWhere='')
	{
		$where = "1=1";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRows($field, $this->db_qz.'member', $where);
	}

	//▶ 필드 데이터
	function getMemberRowById($uid, $field, $addWhere='')
	{
		$where = "uid='".$uid."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'member', $where);
		return $rs[$field];
	}

	//▶ 필드 데이터
	function getMemberRowByNick($nick, $field, $addWhere='')
	{
		$where = "nick='".$nick."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'member', $where);

		return $rs[$field];
	}

	//▶ 필드 데이터
	function getMemberRowByBankOwner($bankOwner, $field, $addWhere='')
	{
		$where = "bank_member='".$bankOwner."' and logo='".$this->logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'member', $where);
		return $rs[$field];
	}

	//▶ 정보 by ID
	function getById($id)
	{
		return $this->getRow('*', $this->db_qz.'member', "uid='".$id."'");
	}

	//▶ 정보 by Sn
	function getBySn($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		return $this->getRow('*', $this->db_qz.'member', "sn=".$sn);
	}

	//▶ 정보 by Name
	function getByName($name)
	{
		return $this->getRow('*', $this->db_qz.'member', "nick='".$name."'");
	}

	//▶ 정보 by phone_num
	function getByPhone_num($phone_num)
	{
		return $this->getRow('*', $this->db_qz.'member', "phone='".$phone_num);
	}

	function getSn($uid)
	{
		$rs = $this->getRow('sn', $this->db_qz.'member', "uid='".$uid."'");
		return $rs['sn'];
	}

	//▶ 총합
	function getTotal($where, $joinRecommendNick='', $logo='')
	{
		if($logo!='') $logo = " and a.logo='".$logo."'";
		if($joinRecommendNick!='')
		{
			$where.= " and a.sn in (select member_sn from ".$this->db_qz."join_recommend where recommend_sn in (select sn from ".$this->db_qz."member where nick like('%".$joinRecommendNick."%')))";
		}

		$sql = "select count(*) as cnt from ".$this->db_qz."member a where 1=1 ".$logo.$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 회원여부
	function isMember($id)
	{
		$sql = "select count(*) as cnt from ".$this->db_qz."member
						where uid='".$id."'";

		$rs = $this->db->exeSql($sql);

		return ($rs[0]['cnt']>0);
	}

	//▶ 회원목록
	function getList($where, $page, $page_size, $orderby='', $joinRecommendNick='', $logo='')
	{
		if($logo!='') $logo = " and a.logo='".$logo."'";

		if($joinRecommendNick!='')
		{
			$where.= " and a.sn in (select member_sn from ".$this->db_qz."join_recommend where recommend_sn in (select sn from ".$this->db_qz."member where nick like('%".$joinRecommendNick."%')))";
		}

		if($orderby=='')
			$orderby=" order by a.regdate desc,a.mem_status desc,a.sn";

		$mModel 	= Lemon_Instance::getObject("MemoModel",true);
		$eModel 	= Lemon_Instance::getObject("EtcModel",true);
		$cModel 	= Lemon_Instance::getObject("CartModel",true);
		$msModel 	= Lemon_Instance::getObject("MoneyModel",true);

		// 기존의회원수익(benefit) 쿼리 :
		//ifnull((select sum(amount) from ".$this->db_qz."charge_log where member_sn=a.sn and state=1), 0)-ifnull((select sum(amount) from ".$this->db_qz."exchange_log where member_sn=a.sn and state=1), 0)-ifnull(a.g_money, 0)-ifnull((select sum(betting_money) from ".$this->db_qz."total_cart where member_sn=a.sn), 0) as benefit,
/*
		$sql = "select a.*, b.Idx, b.rec_id, b.rec_name,
								(select ifnull(sum(amount),0) from ".$this->db_qz."charge_log where member_sn=a.sn and state=1) as charge_sum,
								(select ifnull(sum(amount),0) from ".$this->db_qz."exchange_log where member_sn=a.sn and state=1) as exchange_sum,
								ifnull((select balance from ".$this->db_qz."charge_sum_log where member_sn=a.sn),0) as old_charge_sum,
								ifnull((select balance from ".$this->db_qz."exchange_sum_log where member_sn=a.sn),0) as old_exchange_sum,
								(select ifnull(sum(betting_money),0) from ".$this->db_qz."total_cart where member_sn=a.sn) as bet_total,
								(select count(*) from ".$this->db_qz."visit where member_id=a.uid) as visit_count
						from ".$this->db_qz."member a LEFT OUTER JOIN ".$this->db_qz."recommend b on a.recommend_sn=b.idx
						where a.sn>0".$logo.$where.$orderby."
						limit ".$page.",".$page_size;
	*/
	$sql = "select a.*, b.Idx, b.rec_id, b.rec_name,
						(select ifnull(sum(amount),0) from ".$this->db_qz."charge_log where member_sn=a.sn and state=1) as charge_sum,
						(select ifnull(sum(amount),0) from ".$this->db_qz."exchange_log where member_sn=a.sn and state=1) as exchange_sum,
						(select ifnull(sum(betting_money),0) from ".$this->db_qz."total_cart where member_sn=a.sn) as bet_total,
						(select count(*) from ".$this->db_qz."visit where member_id=a.uid and status = 0) as visit_count
					from ".$this->db_qz."member a LEFT OUTER JOIN ".$this->db_qz."recommend b on a.recommend_sn=b.idx
					where a.sn>0".$logo.$where.$orderby."
					limit ".$page.",".$page_size;

		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$ip	= $rs[$i]['reg_ip'];
			$rs[$i]['country_code'] = $eModel->getNationByIp($ip);

			// 가입 추천인 정보
			$sql = "select sn, nick, bank_member from ".$this->db_qz."member
							where sn=(select recommend_sn from ".$this->db_qz."join_recommend where member_sn=".$rs[$i]['sn'].")";
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['join_recommend_sn'] = $rsi[0]['sn'];
			$rs[$i]['join_recommend_nick'] = $rsi[0]['nick'];
			$rs[$i]['join_recommend_bank_member'] = $rsi[0]['bank_member'];
			if(($i+1)%2==1){
				$rs[$i]['bgColor']="#ffffcd";
			}

			$sql = "select ifnull(balance,0) as balance from ".$this->db_qz."charge_sum_log where member_sn=".$rs[$i]['sn'];
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['charge_sum'] = $rs[$i]['charge_sum'] + $rsi[0]['balance'];

			$sql = "select ifnull(balance,0) as balance from ".$this->db_qz."exchange_sum_log where member_sn=".$rs[$i]['sn'];
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['exchange_sum'] = $rs[$i]['exchange_sum'] + $rsi[0]['balance'];

			$rs[$i]['benefit'] = $rs[$i]['charge_sum'] - $rs[$i]['exchange_sum'];
		}

		return $rs;
	}

	//▶ 파티너 아이디
	function getPartnerList($id, $where="")
	{
		$sql = "select idx
						from tb_recommend
						where rec_id='".$id."' and logo='".$this->logo."'".$where;

		return $this->db->exeSql($sql);
	}
	//▶ 파트너 정보
	function getPartnerInfo($id)
	{
		$sql = "select idx ,default_rate,nc_rate,wb_rate,sb_rate ,rec_name
						from tb_recommend
						where rec_id='".$id."' ";
		$rs = $this->db->exeSql($sql);

		return $rs;
	}

	//▶ 신규회원 레벨업
	function NewMember_LevelUp()
	{
		$sql = "select sn
				from ".$this->db_qz."member where logo='".$this->logo."'  and mem_status='W' ";
		$rs = $this->db->exeSql($sql);

		for( $i = 0; $i < sizeof($rs); ++$i )
		{
			$this->modifyStatus($rs[$i]['sn'], 'good');
		}
	}

	//▶ 상태설정
	function modifyStatus($sn, $flag)
	{
		if($flag=="stop")
		{
			$sql = "update ".$this->db_qz."member
							set mem_status='S' where sn in(".$sn.")";
		}
		else if($flag=="good")
		{
			$sql = "update ".$this->db_qz."member
							set mem_status='N' where sn in(".$sn.")";
		}
		else if($flag=="bad")
		{
			$sql = "update ".$this->db_qz."member
							set mem_status='B' where sn in(".$sn.")";
		}
		else if($flag=="delete")
		{
			$sql = "update ".$this->db_qz."member
							set mem_status='D' where sn in(".$sn.")";
		}

		return $this->db->exeSql($sql);
	}

	function modifyByParam($memberSn, $set)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$sql = "update ".$this->db_qz."member set ".$set." where sn=".$memberSn;
		return $this->db->exeSql($sql);
	}

	//▶ 회원가입
	function joinAdd($uid, $pwd, $exchangePwd, $nick, $name, $phone, $freeMoney, $state, $joinerSn, $partnerSn, $bank_name, $bank_account, $bank_member, $ip, $logo='totobang')
	{
		if($partnerSn!="")
		{
			$levelModel 	= Lemon_Instance::getObject("LevelCodeModel",true);
			$partnerModel = Lemon_Instance::getObject("PartnerModel",true);
			$rs = $partnerModel->getPartnerRow($partnerSn);
			if(sizeof($rs) > 0)
			{
				if($rs[0]['rec_lev']==2)
				{
					$rollingSn = $rs[0]['Idx'];
					$rsi = $partnerModel->getPartnerRow($rs[0]['parent_sn']);
					if(sizeof($rsi) > 0)
					{
						$partnerSn = $rsi[0]['Idx'];
					}
				}
			}

			$levelCode 	= $levelModel->makeMemberLevelCode($partnerSn);
		}

		$sql = "insert into ".$this->db_qz."member(uid,upass,exchange_pass,nick,name,recommend_sn,rolling_sn,level_code,
						phone,point,email,mem_status,last_date,bank_name,bank_account,bank_member,reg_ip,mem_ip,regdate,reg_domain, logo)
		 				values(";

		$sql.= "'".$uid."',";
		$sql.= "'".trim($pwd)."',";
		$sql.= "'".$exchangePwd."',";
		$sql.= "'".$nick."',";
		$sql.= "'".$name."',";
		$sql.= "'".$partnerSn."',";
		$sql.= "'".$rollingSn."',";
		$sql.= "'".$levelCode."',";
		$sql.= "'".$phone."',";
		$sql.= "'".$freeMoney."',";
		$sql.= "'".$email."',";
		$sql.= "'".$state."',";
		$sql.= "now(),";
		$sql.= "'".$bank_name."',";
		$sql.= "'".$bank_account."',";
		$sql.= "'".$bank_member."',";
		$sql.= "'".$ip."',";
		$sql.= "'".$ip."',";
		$sql.= "now(),";
		$sql.= "'".$_SERVER['HTTP_HOST']."',";
	  $sql.= "'".$logo."')";
	  $memberSn = $this->db->exeSql($sql);

	  if($memberSn<=0) return 0;

	  //추천인 등록
	  $partnerModel = Lemon_Instance::getObject("PartnerModel",true);
	  $rs = $partnerModel->addRecommend($memberSn, $joinerSn);

	  return $rs;
	}

	//▶ 회원정보, 추천인 포함
	function member($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$mModel = Lemon_Instance::getObject("MoneyModel",true);
		$cModel = Lemon_Instance::getObject("CartModel",true);

		$sql = "select a.*,b.rec_id
						from ".$this->db_qz."member a left outer join ".$this->db_qz."recommend b on a.recommend_sn=b.idx
						where a.sn=".$sn;
		$rs = $this->db->exeSql($sql);

		$rs[0]['charge_cnt'] = $mModel->getMemberChargeTotal($rs[0]['sn'], "1");
		$rs[0]['exchange_cnt'] = $mModel->getMemberExchangeTotal($rs[0]['sn'], "1");

		$rs[0]['charge_money'] = $mModel->getMemberChargeMoney($rs[0]['sn'] );
		$rs[0]['exchange_money'] = $mModel->getMemberExchangeMoney($rs[0]['sn'] );
		$rs[0]['benefit'] = $rs[0]['charge_money']-$rs[0]['exchange_money'];

		$rs[0]['bet_money'] = $cModel->getMembeRTotalBetMoney($rs[0]['sn']);

		return $rs[0];
	}

	//▶ 정보수정
	function modify($where, $bank_name, $bank_account, $bank_member, $recommend_sn, $mem_lev, $email, $phone, $memo, $uid, $memberStatus, $exchangePwd, $recommendSn='')
	{
		//뱅킹 정보 변경시 업데이트를 위해 데이터 변경을 확인한다.
		$rs = $this->getById($uid);
		if(Trim($rs['bank_name'])!=Trim($bank_name) ||
			 Trim($rs['bank_account'])!=Trim($bank_account) ||
			 Trim($rs['bank_member'])!=Trim($bank_member) )
		{
			$sql = "insert into ".$this->db_qz."member_bank(member_sn, bank_name, bank_account, bank_member, regdate, logo)
							values(".$rs['sn'].",'".$bank_name."','".$bank_account."','".$bank_member."', now(),'".$this->logo."')";

			$this->db->exeSql($sql);
		}

		$set="";
		if($recommendSn!="")
		{
			$levelModel = Lemon_Instance::getObject("LevelCodeModel",true);
			$levelCode = $levelModel->makeMemberLevelCode($recommendSn);
			$set.="recommend_sn=".$recommendSn.", level_code='".$levelCode."',";
		}

		$sql = "update ".$this->db_qz."member
						set ".$where." bank_name='".$bank_name."', bank_account='".$bank_account."',bank_member='".$bank_member."',
						recommend_sn='".$recommend_sn."',mem_lev='".$mem_lev."',email='".$email."',phone='".$phone."',".$set."
						memo='".$memo."', mem_status='".$memberStatus."', exchange_pass='".$exchangePwd."' where uid='".$uid."'";

		return $this->db->exeSql($sql);
	}

	function modifyMember($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$data['upass'] 		= md5($this->req->request('newpass'));
		$data['sms_safedomain'] = $this->req->request('sms_safedomain');
		$data['sms_event'] 		= $this->req->request('sms_event');
		$data['sms_betting_ok'] = $this->req->request('sms_betting_ok');

		$where = "sn=".$sn;

		$this->db->setUpdate($this->db_qz.'member', $data, $where);

		$this->db->exeSql();
	}

	//▶ 회원 메모쓰기
	function writeNote($sn, $content)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "insert into ".$this->db_qz."member_note(member_sn,regdate,memo) values ";
		$sql.= "(".$sn.",now(),'".$content."')";
		return $this->db->exeSql($sql);
	}

	//▶ 회원 메모수정
	function modifyNote($noteSn, $content)
	{
		if( !$this->is_integer_mysql_parameter($noteSn))
		{
			exit;
		}

		$sql = "update ".$this->db_qz."member_note
						set memo= '".$content."' where sn=".$noteSn;

		return $this->db->exeSql($sql);
	}

	//▶ 회원 메모보기
	function getNote($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select *
				from ".$this->db_qz."member_note
					where sn='".$sn."'";
		$rs = $this->db->exeSql($sql);
		return $rs[0];
	}

	//▶ 관리자 메모삭제
	function delNote($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "delete from ".$this->db_qz."member_note where sn = ".$sn;
		$this->db->exeSql($sql);
	}

	function getMember_Export()
	{
		$sql = "
			select *,
	 			(select rec_id from ".$this->db_qz."recommend where idx=recommend_sn) as recommend_id,
	 			(select sum(agree_amount) from ".$this->db_qz."charge_log where member_sn=a.sn) as tot_charge,
	 			(select sum(agree_amount) from ".$this->db_qz."exchange_log where member_sn=a.sn) as tot_exchange
			from ".$this->db_qz."member a order by regdate desc";

		$rows = $this->db->exeSql($sql);

		for($i=0; $i<count($rows); ++$i)
		{
			$logo = $rows[$i]['logo'];
			if($logo=="totobang")
		  {
		  	$rows[$i]['logo'] = "K";
		  }
		  else if($logo=="orange")
		  {
	    	$rows[$i]['logo'] = "M";
	    }
	    else if($logo=="apple")
	    {
	    	$rows[$i]['logo'] = "S";
	    }
		}

		return $rows;
	}

	//▶ 필드 데이터
	function getMemberBankList($memberSn)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$sql = "select * from ".$this->db_qz."member_bank
						where member_sn=".$memberSn." and logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	//하루 배팅취소 횟수업데이트
	function setBet_cancel_cnt($sn, $bet_cancel_cnt)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "update ".$this->db_qz."member set bet_cancel_flag='".date('Y-m-d')."_".$bet_cancel_cnt."'	where sn=".$sn." and logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 추천인 정보
	function getJoiner($member_sn)
	{
		if( !$this->is_integer_mysql_parameter($member_sn))
		{
			exit;
		}

		$sql = "select recommend_sn
						from ".$this->db_qz."join_recommend
						where member_sn=".$member_sn;
		$rs = $this->db->exeSql($sql);

		return $rs[0]['recommend_sn'];
	}

	//▶ 내가 추천한 유저의 정보
	function getJoiners($member_sn)
	{
		if( !$this->is_integer_mysql_parameter($member_sn))
		{
			exit;
		}

		$sql = "select b.*
						from ".$this->db_qz."join_recommend a, ".$this->db_qz."member b
						where a.member_sn=b.sn and a.recommend_sn=".$member_sn;
		$rs = $this->db->exeSql($sql);

		return $rs;
	}

	//▶ 현재 게임중인지 판단, 베팅안됨
	function getServiceID($uid)
	{
		$sql = "select nServerID
						from ".$this->db_qz."godoriuser
						where szID='".$uid."'";
		$rs = $this->db->exeSql($sql);

		if(count($rs)<=0)
			return -1;

		return $rs[0]['nServerID'];
	}

	function issueVirtualMoney($amount, $where)
	{
		$data['virtual_money'] = $amount;

		$this->db->setUpdate($this->db_qz."member", $data, $where);
		$rs = $this->db->exeSql();
	}

	function virtual_money_top_ranker($logo)
	{
		$sql = "select * from tb_member
						where mem_status='N' and logo='".$logo."' order by virtual_money desc limit 0, 15";
		$rows = $this->db->exeSql($sql);

		return $rows;
	}
}
?>
