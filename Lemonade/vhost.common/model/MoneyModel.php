<?php
/*
	Charge, Exchange, Logs
*/
class MoneyModel extends Lemon_Model
{
	//▶ 멤버의  환전한  총머니
	function getMemberExchangeMoney($memberSn)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$sql = "select sum(agree_amount) as total from ".$this->db_qz."exchange_log where state=1 and member_sn=".$memberSn;

		$rows	= $this->db->exeSql($sql);
		$amount = $rows[0]['total'];

		$sql = "select ifnull(balance, 0) as balance from ".$this->db_qz."exchange_sum_log where member_sn=".$memberSn;
		$rows	= $this->db->exeSql($sql);

		$amount = $amount + $rows[0]['balance'];

		return $amount;
	}

	//▶ 멤버의  충전한 총머니
	function getMemberChargeMoney($memberSn)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$sql = "select sum(agree_amount) as total from ".$this->db_qz."charge_log where state=1 and member_sn=".$memberSn;
		$rows	= $this->db->exeSql($sql);
		$amount = $rows[0]['total'];

		$sql = "select ifnull(balance, 0) as balance from ".$this->db_qz."charge_sum_log where member_sn=".$memberSn;
		$rows	= $this->db->exeSql($sql);

		$amount = $amount + $rows[0]['balance'];

		return $amount;
	}

	function getExchangeList($state="", $readCheck="", $where="", $page=0, $page_size=0)
	{
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		if($state!="")
			$where.= " and a.state=".$state;

		if($page_size > 0)
			$limit = " limit ".$page.",".$page_size;

		$sql = "select a.sn, a.regdate, a.operdate, a.member_sn, a.amount, a.agree_amount, a.before_money, a.after_money, a.bank, a.bank_account, a.bank_owner, a.state, a.logo,
						(select count(*) from ".$this->db_qz."exchange_log where state=1 and member_sn=a.member_sn and DATE_FORMAT(regdate,'%Y-%m-%d')=DATE_FORMAT(now(),'%Y-%m-%d')) as todaycount,
						(select count(*) from ".$this->db_qz."exchange_log where state=1 and member_sn=a.member_sn) as totalcount,
						(select count(*) from ".$this->db_qz."member_bank where member_sn=b.sn) as bank_count,
						b.uid, b.nick, b.g_money, b.bank_member,
						ifnull(c.rec_id, '무소속') as recommendId
						from ".$this->db_qz."exchange_log a,".$this->db_qz."member b left outer join ".$this->db_qz."recommend c on b.recommend_sn=c.idx
						where a.member_sn=b.sn".$where."
						order by a.regdate desc ".$limit;

		$rs = $this->db->exeSql($sql);

		if($readCheck!="non")
		{
			for($i=0; $i<sizeof($rs); ++$i)
			{
				$sql = "update ".$this->db_qz."exchange_log set is_read=1
								where sn=".$rs[$i]['sn'];
				$this->db->exeSql($sql);
			}
		}

		return $rs;
	}

	//▶ 환전 총합-전체
	function getExchangeTotal($state="", $where="")
	{
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		if($state!="") $where.= " and a.state=".$state;

		$sql = "select count(*) as cnt
						from ".$this->db_qz."exchange_log a,".$this->db_qz."member b left outer join ".$this->db_qz."recommend c on b.recommend_sn=c.idx
						where a.member_sn=b.sn ".$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 환전 상태값
	function getExchangeState($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select state from ".$this->db_qz."exchange_log where sn=".$sn;
		$rs	= $this->db->exeSql($sql);
		return $rs[0]['state'];
	}

	//▶ 충전 상태값
	function getChargeState($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select state from ".$this->db_qz."charge_log where sn=".$sn;
		$rs	= $this->db->exeSql($sql);
		return $rs[0]['state'];
	}

	//▶ 금일 충전한 총머니
	function getTodayCharge($memberSn)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$sql = "select sum(amount) as total from ".$this->db_qz."charge_log
					where state=1 and member_sn=".$memberSn;

		$rs	= $this->db->exeSql($sql);
		return $rs[0]['total'];
	}

	//▶ 금일 충전한 총머니
	function getTodayTotalCharge($where='')
	{
		$sql = "select ifnull(sum(amount),0) as total from ".$this->db_qz."charge_log
					where state=1 and DATE_FORMAT(operdate,'%Y-%m-%d')=DATE_FORMAT(now(),'%Y-%m-%d')".$where;

		$rs	= $this->db->exeSql($sql);
		return $rs[0]['total'];
	}

	//▶ 금일 환전한 총머니
	function getTodayTotalExchange($where='')
	{
		$sql = "select ifnull(sum(amount),0) as total from ".$this->db_qz."exchange_log
					where state=1 and DATE_FORMAT(operdate,'%Y-%m-%d')=DATE_FORMAT(now(),'%Y-%m-%d')".$where;

		$rs	= $this->db->exeSql($sql);
		return $rs[0]['total'];
	}

	//▶ 충전 목록-전체
	function getChargeList($state="", $readCheck="", $where="", $page=0, $page_size=0)
	{
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		if($state!="")
			$where.= " and a.state=".$state;

		if($page_size > 0)
			$limit = " limit ".$page.",".$page_size;

		$sql = "select a.sn, a.regdate, a.operdate, a.member_sn, a.amount, a.agree_amount, a.before_money, a.after_money, a.bonus,a.bank_owner, a.state, a.logo,
							(select count(*) from ".$this->db_qz."charge_log where state=1 and member_sn=a.member_sn and DATE_FORMAT(regdate,'%Y-%m-%d')=DATE_FORMAT(now(),'%Y-%m-%d')) as todaycount,
							(select count(*) from ".$this->db_qz."charge_log where state=1 and member_sn=a.member_sn) as totalcount,
							(select lev_name from ".$this->db_qz."level_config where logo='a.logo' and lev=(select mem_lev from ".$this->db_qz."member where sn=a.member_sn )) as mem_lev,
							b.uid, b.nick, b.g_money, b.bank_member, b.bank_name,
							ifnull(c.rec_id, '무소속') as recommendId
				from ".$this->db_qz."charge_log a,".$this->db_qz."member b left outer join ".$this->db_qz."recommend c on b.recommend_sn=c.idx
				where a.member_sn=b.sn".$where."
				order by a.regdate desc ".$limit;

		$rs = $this->db->exeSql($sql);


		if($readCheck!='non')
		{
			for($i=0; $i<sizeof($rs); ++$i)
			{
				$sql = "update ".$this->db_qz."charge_log set is_read=1
								where sn=".$rs[$i]['sn'];
				$this->db->exeSql($sql);
			}
		}


		return $rs;
	}

	//▶ 충전 총합-전체
	function getChargeTotal($state="", $where="")
	{
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		if($state!="")
		{
			$where.= " and a.state=".$state;
		}

		$sql = "select count(*) as cnt
						from ".$this->db_qz."charge_log a,".$this->db_qz."member b left outer join ".$this->db_qz."recommend c on b.recommend_sn=c.idx
						where a.member_sn=b.sn".$where;

		$rs = $this->db->exeSql($sql);

		return $rs[0]['cnt'];
	}




	//▶ 환전 목록-멤버
	function getMemberExchangeList($sn, $state="", $where="", $page=0, $page_size=0)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		$where.= " and a.member_sn=".$sn;

		return $this->getExchangeList($state, "", $where, $page, $page_size);
	}

	//▶ 환전 총합-멤버
	function getMemberExchangeTotal($sn, $state="", $where="")
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		$where.= " and a.member_sn=".$sn;

		return $this->getExchangeTotal($state, $where);
	}

	//▶ 충전 목록-멤버
	function getMemberChargeList($sn, $state="", $where="", $page=0, $page_size=0)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		$where.= " and a.member_sn=".$sn;

		return $this->getChargeList($state, "", $where, $page, $page_size);
	}

	//▶ 충전 총합-멤버
	function getMemberChargeTotal($sn, $state="", $where="")
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		$where.= " and a.member_sn=".$sn;

		return $this->getChargeTotal($state, $where);
	}

	//▶ 충전 데이터
	function getChargeRow($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select a.*, b.uid from ".$this->db_qz."charge_log a, ".$this->db_qz."member b where a.member_sn=b.sn and a.sn=".$sn;
		$rs = $this->db->exeSql($sql);
		return $rs[0];
	}

	//▶ 충전 데이터
	function getExchangeRow($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select a.*, b.uid from ".$this->db_qz."exchange_log a, ".$this->db_qz."member b where a.member_sn=b.sn and a.sn=".$sn;
		$rs = $this->db->exeSql($sql);
		return $rs[0];
	}

	//▶ 충전여부 확인
	function isCharged($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select state from ".$this->db_qz."charge_log
					where sn=".$sn;

		$rs = $this->db->exeSql($sql);

		return ($rs[0]['state']==1);
	}

	//▶ 머니로그 추가
	function addMoneyLog($memberSn, $amount, $before, $after, $state, $statusMessage, $memo="")
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}

		if($state==4)
		{
			$bettingNo=$statusMessage;
			$statusMessage = "당첨배당금[배팅번호:".$statusMessage."]";

			$sql = "insert into ".$this->db_qz."money_log(member_sn,amount,before_money,after_money,regdate,state, status_message, betting_no)
						values(".$memberSn.",".$amount.",".$before.",".$after.",now(),".$state.",'".$statusMessage."','".$bettingNo."')";
		}
		else if($state==5)
		{
			$bettingNo=$statusMessage;
			$statusMessage = "취소";

			$sql = "insert into ".$this->db_qz."money_log(member_sn,amount,before_money,after_money,regdate,state, status_message, betting_no)
						values(".$memberSn.",".$amount.",".$before.",".$after.",now(),".$state.",'".$statusMessage."','".$bettingNo."')";
		}
		else if($state==8)
		{
			$bettingNo=$statusMessage;
			$statusMessage = "경기재입력[배팅번호:".$statusMessage."]";

			$sql = "insert into ".$this->db_qz."money_log(member_sn,amount,before_money,after_money,regdate,state, status_message, betting_no)
						values(".$memberSn.",".$amount.",".$before.",".$after.",now(),".$state.",'".$statusMessage."','".$bettingNo."')";
		}
		else
		{
			$sql = "insert into ".$this->db_qz."money_log(member_sn,amount,before_money,after_money,regdate,state, status_message, log_memo)
							values(".$memberSn.",".$amount.",".$before.",".$after.",now(),".$state.",'".$statusMessage."','".$memo."')";
		}

		$this->db->exeSql($sql);
	}

	//▶ 마일리지 목록 총합
	function getMileageTotal($sn, $type)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($type))
		{
			exit;
		}

		$where="";
		if($type!='')
			$where=" and state=".$type;

		$sql = "select count(*) as cnt from ".$this->db_qz."mileage_log ";
		$sql.= "where member_sn='".$sn."'".$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 마일리지 목록
	function getMileageList($sn, $page, $page_size, $type='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($type))
		{
			exit;
		}

		$where="";
		if($type!='')
			$where=" and state=".$type;

		$sql = "select sn,member_sn,amount,state,status_message,regdate from ".$this->db_qz."mileage_log ";
		$sql.= "where member_sn='".$sn."'".$where." order by regdate desc limit ".$page.",".$page_size;

		return $this->db->exeSql($sql);
	}

	//▶ 마일리지 목록 총합
	function getMileageLogTotal($where='', $memberSn='', $type='', $beginDate='', $endDate='')
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($type))
		{
			exit;
		}

		$sql = "select count(*) as cnt
						from ".$this->db_qz."mileage_log a,".$this->db_qz."member b
						where a.member_sn=b.sn and b.mem_status<>'G' ".$where;

		if($type!='') 		$sql.=" and a.state=".$type;
		if($memberSn!='') $sql.=" and a.member_sn=".$memberSn;
		if($beginDate!=''&& $endDate!='') $sql.=" and a.regdate between '".$beginDate." 00:00:00' and '".$endDate." 23:59:59'";

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 마일리지 목록
	function getMileageLogList($where='', $memberSn='', $page=0, $page_size=0, $type='', $beginDate='', $endDate='')
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($type))
		{
			exit;
		}

		$sql = "select a.regdate as log_regdate, a.*, b.*, c.rec_id
						from ".$this->db_qz."mileage_log a,".$this->db_qz."member b left outer join ".$this->db_qz."recommend c on b.recommend_sn=c.Idx
						where a.member_sn=b.sn and b.mem_status<>'G' ".$where;

		if($type!='') 		$sql.=" and a.state=".$type;
		if($memberSn!='') $sql.=" and a.member_sn=".$memberSn;
		if($beginDate!=''&& $endDate!='') $sql.=" and a.regdate between '".$beginDate." 00:00:00' and '".$endDate." 23:59:59'";
		$sql.=" order by a.regdate desc";
		if($page_size>0) 	$sql.=" limit ".$page.",".$page_size;

		return $this->db->exeSql($sql);
	}

	//▶ 머니내역 목록 총합
	function getMoneyLogTotal($where='', $memberSn='', $type='', $beginDate='', $endDate='')
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($type))
		{
			exit;
		}

		$sql = "select count(*) as cnt
						from ".$this->db_qz."money_log a, ".$this->db_qz."member b
						where a.member_sn=b.sn and b.mem_status<>'G' ".$where;

		if($type!='') 		$sql.=" and a.state=".$type;
		if($memberSn!='') $sql.=" and a.member_sn=".$memberSn;
		if($beginDate!=''&& $endDate!='') $sql.=" and a.regdate between '".$beginDate."' and '".$endDate."'";

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 머니내역 목록
	function getMoneyLogList($where='', $memberSn='', $page=0, $page_size=0, $type='', $beginDate='', $endDate='')
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($type))
		{
			exit;
		}

		if($type!='') 		$where.=" and a.state=".$type;
		if($memberSn!='') $where.=" and a.member_sn=".$memberSn;
		$subParam="";
		if($beginDate!=''&& $endDate!='')
		{
			$where.=" and a.regdate between '".$beginDate." 00:00:00' and '".$endDate." 23:59:59'";
			$subParam.=" and z.regdate between '".$beginDate." 00:00:00' and '".$endDate." 23:59:59'";
		}
		$where.=" order by a.regdate desc";
		if($page_size>0) 	$where.=" limit ".$page.",".$page_size;

		$sql = "select (select count(*) from tb_charge_log z where z.member_sn=b.sn".$subParam.") as charge_cnt, a.regdate as log_regdate, a.*, b.*, c.rec_id
						from ".$this->db_qz."money_log a,".$this->db_qz."member b left outer join ".$this->db_qz."recommend c on b.recommend_sn=c.Idx
						where a.member_sn=b.sn and b.mem_status<>'G' ".$where;

		return $this->db->exeSql($sql);
	}

	function ajaxMileage2Cash($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$pModel = Lemon_Instance::getObject("ProcessModel",true);

		$sql = "select point from ".$this->db_qz."member where sn='".$sn."'";
		$rs = $this->db->exeSql($sql);

		if(sizeof($rs)>0)
		{
			$mileage = $rs[0]['point'];
			//$amount = ((int)($mileage/10000))*10000;
			$amount = $mileage;
			$pModel->modifyMoneyProcess($sn, $amount,'6','포인트전환');
			$rs = $pModel->modifyMileageProcess($sn, -$amount,'6','포인트전환');

			$sql = "select g_money, point from ".$this->db_qz."member where sn='".$sn."'";
			$rs = $this->db->exeSql($sql);

			echo(json_encode($rs[0]));
		}
	}

	function ajaxCash2Mileage($sn, $amount)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$process_model = Lemon_Instance::getObject("ProcessModel",true);

		$sql = "select g_money from ".$this->db_qz."member where sn='".$sn."'";
		$rows = $this->db->exeSql($sql);

		if(count($rows)>0)
		{
			$money = $rows[0]['g_money'];

			if($money >= $amount)
			{
				$process_model->modifyMoneyProcess($sn, -$amount, '13', '마일리지전환');
				$rs = $process_model->modifyMileageProcess($sn, $amount, '13', '마일리지전환', 100);

				$sql = "select g_money, point from ".$this->db_qz."member where sn='".$sn."'";
				$rs = $this->db->exeSql($sql);

				echo(json_encode($rs[0]));
			}
		}
	}

	function modifyHide($type/*0=charge, 1=exchange*/, $sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		if("charge"==$type)					{$table = $this->db_qz."charge_log";}
		else if("exchange"==$type)	{$table = $this->db_qz."exchange_log";}
		else							{return;}

		$sql = "update ".$table." set is_hidden=1 where sn=".$sn;
		return $this->db->exeSql($sql);
	}

	function totalmemberMoney()
	{
		$sql = "select sum(g_money) as total_money from ".$this->db_qz."member where mem_status<>'G'";
		$rs=$this->db->exeSql($sql);
		return $rs[0]["total_money"];
	}

	function totalmemberMileage()
	{
		$sql = "select sum(point) as total_point from ".$this->db_qz."member where mem_status<>'G'";
		$rs=$this->db->exeSql($sql);
		return $rs[0]["total_point"];
	}
}
?>
