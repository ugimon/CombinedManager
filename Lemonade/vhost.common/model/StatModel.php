<?php
class StatModel extends Lemon_Model
{
	//▶ 유저 통계
	function getMemberStatic($parentSn='', $partnerSn='', $beginDate='', $endDate='', $logo='')
	{
		$array = array();
		$param_logo = $logo;

		if($beginDate!='' && $endDate!='')
		{
			$sql = "select datediff(date('".$endDate."'),date('".$beginDate."'))+1 as sumday";

			$rs = $this->db->exeSql($sql);
			$sumday = $rs[0]["sumday"];

			for($i=$sumday; $i>0; --$i)
			{
				$item = array();
				$interval = $i-1;
				$sql = "select date(date_sub(date('".$endDate."'),interval ".$interval." day)) as current";
				$rsi = $this->db->exeSql($sql);

				$item[$i]['current_date']				=$currentDate = $rsi[0]["current"];
				$item[$i]['current_date_name']	=$this->dateName($currentDate);
				$where=" and date(regdate)=date('".$currentDate."')";

				$partnerWhere="";
				if($partnerSn!='')
				{
					$partnerWhere=" and recommend_sn='".$partnerSn."'";
				}

				//유져수
				if($logo!='')
					$logo=" and logo='".$param_logo."'";
				$sql = "select count(*) as cnt from tb_member where mem_status!='G'".$logo.$where.$partnerWhere;
				$rs = $this->db->exeSql($sql);
				$item[$i]['member_count'] = $rs[0]['cnt'];

				//입금유져수
				if($logo!='') $logo = " and a.logo='".$param_logo."'";
				$sql = "select count(distinct a.sn) as cnt from tb_member a inner join tb_charge_log b on a.sn=b.member_sn where a.mem_status!='G'".$logo;
				$sql.= " and b.regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'".$partnerWhere;
				$rs = $this->db->exeSql($sql);
				$item[$i]['charge_member_count'] = $rs[0]['cnt'];

				//접속유져수
				$sql = "select count(distinct a.sn) as cnt from tb_member a inner join tb_visit b on a.uid=b.member_id where a.mem_status!='G'".$logo;
				$sql.= " and b.visit_date between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'".$partnerWhere;
				$rs = $this->db->exeSql($sql);
				$item[$i]['visit_member_count'] = $rs[0]['cnt'];

				//배팅유져수
				$sql = "select count(distinct b.member_sn) as cnt
								from tb_member a inner join tb_total_cart b on a.sn=b.member_sn where a.mem_status!='G' ".$logo;
				$sql.= " and b.regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'".$partnerWhere;
				$rs = $this->db->exeSql($sql);
				$item[$i]['betting_member_count'] = $rs[0]['cnt'];

				//총 베팅금액, 베팅횟수
				$sql = "select sum(betting_money) as sum_bet,
									count(betting_no) as countbet
								from tb_total_cart a inner join tb_member b on a.member_sn=b.sn
								where a.is_account=1 and ";
				$sql.= " a.regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'".$logo.$partnerWhere;
				$rs = $this->db->exeSql($sql);
				$item[$i]['sum_betting']	= $rs[0]['sum_bet'];
				$item[$i]['bet_count']	  = $rs[0]['countbet'];

				/*
				//진행중, 마감 게임
				$sql = "select count(sn) sumrace, kubun
						from tb_child
							where kubun is not null group by kubun and date(gamedate)=date('".$currentDate."')";
				$rs = $this->db->exeSql($sql);

				if($rs[0]['kubun']==0)
					$item[$i]['ing_game'] = $rs[0]['sumrace'];
				else
					$item[$i]['fin_game'] = $rs[0]['sumrace'];
				*/

				//환전 횟수, 총합
				$sql="select count(a.sn) as countexchange, sum(agree_amount) as sumexchange
								from tb_exchange_log a inner join tb_member b on a.member_sn=b.sn";
				$sql.=" where state = 1 and b.mem_status!='G' and a.regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'".$logo.$partnerWhere;
				$rs = $this->db->exeSql($sql);

				$item[$i]['exchange_count'] = $rs[0]['countexchange'];
				$item[$i]['sum_exchange'] = $rs[0]['sumexchange'];

				//충전 횟수, 총합
				$sql="select count(a.sn) as countcharge, sum(agree_amount) as sumcharge
								from tb_charge_log a inner join tb_member b on a.member_sn=b.sn";
				$sql.=" where state = 1 and b.mem_status!='G' and a.regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'".$logo.$partnerWhere;

				$rs = $this->db->exeSql($sql);

				$item[$i]['charge_count'] = $rs[0]['countcharge'];
				$item[$i]['sum_charge'] = $rs[0]['sumcharge'];

				$array[] = $item[$i];
			}
		}



		return $array;
	}


	function getPartner($uid)
	{
		$sql = "select idx from tb_recommend where rec_id='".$uid."' and logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 입출금 통계
	function getMoneyList($parentSn='', $partnerSn='', $beginDate='', $endDate='', $logo='')
	{
		$list = array();

		$param_logo = $logo;
		if($logo!='') $logo = " and logo='".$param_logo."'";

		$cartModel = Lemon_Instance::getObject("CartModel",true);

		if($beginDate!="" && $endDate!="")
		{
			$sql = "select datediff(date('".$endDate."'),date('".$beginDate."'))+1 as sumday";

			$rs = $this->db->exeSql($sql);
			$sumday = $rs[0]["sumday"];

			$item = array();
			for($i=$sumday; $i>0; --$i)
			{
				$interval = $i-1;
				$sql = "select date(date_sub(date('".$endDate."'),interval ".$interval." day)) as current";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['current_date'] = $currentDate = $rsi[0]["current"];
				$item[$i]['current_date_name']	=$this->dateName($currentDate);

				// 배팅대기
				$rsi = $cartModel->getTotalBetMoney($partnerSn,'','',$currentDate, $currentDate, 0, $param_logo);
				$item[$i]['betting_ready_money'] = $rsi['total_betting'];

				// 출금총액
				$sql = "select count(*) as count_exchange, (case when sum(agree_amount) is null then 0 else sum(agree_amount) end) as sum_exchange
								from tb_exchange_log where regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59' and state=1".$logo;
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['count_exchange'] = $rsi[0]["count_exchange"];
				$item[$i]['exchange'] 	= $rsi[0]["sum_exchange"];

				// 입금총액
				$sql = "select count(*) as count_charge, (case when sum(agree_amount) is null then 0 else sum(agree_amount) end) as sum_charge
								from tb_charge_log where regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59' and state=1".$logo;
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['count_charge'] 	= $rsi[0]["count_charge"];
				$item[$i]['charge'] 		= $rsi[0]["sum_charge"];

				// 수익
				$item[$i]['benefit'] = $item[$i]['charge']-$item[$i]['exchange'];

				// 관리자 입금
				$sql = "select ifnull(sum(amount),0) as sum_admin_charge from tb_money_log
								where state=7 and amount>0 and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59' and (select mem_status from tb_member where sn=member_sn ".$logo.")<>'G' ";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";

				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_charge'] = $rsi[0]['sum_admin_charge'];

				// 관리자 출금
				$sql = "select ifnull(sum(amount),0) as sum_admin_exchange from tb_money_log
								where state=7 and amount<0 and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59' and (select mem_status from tb_member where sn=member_sn ".$logo.")<>'G'";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_exchange'] = $rsi[0]['sum_admin_exchange'];

				// 포인트 입금
				$sql = "select ifnull(sum(amount),0) as sum_admin_charge from tb_mileage_log
								where amount>0
								and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'
								and member_sn in(select sn from tb_member where mem_status!='G'".$logo.")";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_mileage_charge'] = $rsi[0]['sum_admin_charge'];

				// 포인트 출금
				$sql = "select ifnull(sum(amount),0) as sum_admin_exchange from tb_mileage_log
								where amount<0
								and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'
								and member_sn in(select sn from tb_member where mem_status!='G'".$logo.")";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_mileage_exchange'] = $rsi[0]['sum_admin_exchange'];

				$list[] = $item[$i];
			}
		}

		return $list;
	}

	//▶ 입출금 통계
	function getMoneyList2($parentSn='', $partnerSn='', $beginDate='', $endDate='', $logo='')
	{
		$list = array();

		$logoModel = Lemon_Instance::getObject("LogoModel",true);
		$logoList = $logoModel->getList();

		$flag=0;
		if($logo!='')
		{
			$where=" and (";
			for($i=1; $i<=sizeof($logoList); ++$i)
			{
				if(substr($logo, $i ,1) == "1")
				{
					if($flag==1)	{$where.=" or logo='".$logoList[$i-1]['name']."'";}
					else					{$where.=" logo='".$logoList[$i-1]['name']."'"; $flag=1;}
				}
			}
			$where.=" )";
		}

		$logo = $where;

		$cartModel = Lemon_Instance::getObject("CartModel",true);

		if($beginDate!="" && $endDate!="")
		{
			$sql = "select datediff(date('".$endDate."'),date('".$beginDate."'))+1 as sumday";

			$rs = $this->db->exeSql($sql);
			$sumday = $rs[0]["sumday"];

			$item = array();
			for($i=$sumday; $i>0; --$i)
			{
				$interval = $i-1;
				$sql = "select date(date_sub(date('".$endDate."'),interval ".$interval." day)) as current";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['current_date'] = $currentDate = $rsi[0]["current"];
				$item[$i]['current_date_name']	=$this->dateName($currentDate);

				// 배팅대기
				$rsi = $cartModel->getTotalBetMoney($partnerSn,'','',$currentDate, $currentDate, 0, $param_logo);
				$item[$i]['betting_ready_money'] = $rsi['total_betting'];

				// 출금총액
				$sql = "select count(*) as count_exchange, (case when sum(agree_amount) is null then 0 else sum(agree_amount) end) as sum_exchange
								from tb_exchange_log where regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59' and state=1".$logo;
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['count_exchange'] = $rsi[0]["count_exchange"];
				$item[$i]['exchange'] 	= $rsi[0]["sum_exchange"];

				// 입금총액
				$sql = "select count(*) as count_charge, (case when sum(agree_amount) is null then 0 else sum(agree_amount) end) as sum_charge
								from tb_charge_log where regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59' and state=1".$logo;
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['count_charge'] 	= $rsi[0]["count_charge"];
				$item[$i]['charge'] 		= $rsi[0]["sum_charge"];

				// 수익
				$item[$i]['benefit'] = $item[$i]['charge']-$item[$i]['exchange'];

				// 관리자 입금
				$sql = "select ifnull(sum(amount),0) as sum_admin_charge from tb_money_log
								where state=7 and amount>0 and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59' and (select mem_status from tb_member where sn=member_sn ".$logo.")<>'G' ";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";

				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_charge'] = $rsi[0]['sum_admin_charge'];

				// 관리자 출금
				$sql = "select ifnull(sum(amount),0) as sum_admin_exchange from tb_money_log
								where state=7 and amount<0 and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59' and (select mem_status from tb_member where sn=member_sn ".$logo.")<>'G'";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_exchange'] = $rsi[0]['sum_admin_exchange'];

				// 포인트 입금
				$sql = "select ifnull(sum(amount),0) as sum_admin_charge from tb_mileage_log
								where amount>0
								and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'
								and member_sn in(select sn from tb_member where mem_status!='G'".$logo.")";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_mileage_charge'] = $rsi[0]['sum_admin_charge'];

				// 포인트 출금
				$sql = "select ifnull(sum(amount),0) as sum_admin_exchange from tb_mileage_log
								where amount<0
								and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'
								and member_sn in(select sn from tb_member where mem_status!='G'".$logo.")";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where recommend_sn=".$partnerSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_mileage_exchange'] = $rsi[0]['sum_admin_exchange'];

				$list[] = $item[$i];
			}
		}

		return $list;
	}

	//▶ 롤링 입출금 통계
	function getRollingMoneyList($parentSn='', $rollingSn='', $beginDate='', $endDate='')
	{
		$list = array();

		$cartModel = Lemon_Instance::getObject("CartModel",true);

		if($beginDate!="" && $endDate!="")
		{
			$sql = "select datediff(date('".$endDate."'),date('".$beginDate."'))+1 as sumday";

			$rs = $this->db->exeSql($sql);
			$sumday = $rs[0]["sumday"];

			$item = array();
			for($i=$sumday; $i>0; --$i)
			{
				$interval = $i-1;
				$sql = "select date(date_sub(date('".$endDate."'),interval ".$interval." day)) as current";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['current_date'] = $currentDate = $rsi[0]["current"];

				// 총배팅
				$rsi = $cartModel->getRollingTotalBetMoney($rollingSn,'','',$currentDate, $currentDate);
				$item[$i]['betting'] = $rsi['total_betting'];

				// 총당첨
				$item[$i]['win_money'] = $rsi['total_result'];

				// 미당첨
				$rsi = $cartModel->getRollingTotalBetMoney($rollingSn,'','',$currentDate, $currentDate, 2);
				$item[$i]['lose_money'] = $rsi['total_betting'];

				// 배팅수익
				$item[$i]['betting_benefit'] = $item[$i]['lose_money']-$item[$i]['win_money'];

				// 배팅대기
				$rsi = $cartModel->getRollingTotalBetMoney($rollingSn,'','',$currentDate, $currentDate, 0);
				$item[$i]['betting_ready_money'] = $rsi['total_betting'];

				// 출금총액
				$sql = "select count(*) as count_exchange, (case when sum(agree_amount) is null then 0 else sum(agree_amount) end) as sum_exchange
								from tb_exchange_log where logo='".$this->logo."' and date(regdate)=date('".$currentDate."') and state=1";
				if($rollingSn!='')
					$sql.= " and member_sn in(select sn from tb_member where rolling_sn=".$rollingSn.") ";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['count_exchange'] = $rsi[0]["count_exchange"];
				$item[$i]['exchange'] 	= $rsi[0]["sum_exchange"];

				// 입금총액
				$sql = "select count(*) as count_charge, (case when sum(agree_amount) is null then 0 else sum(agree_amount) end) as sum_charge
								from tb_charge_log where logo='".$this->logo."' and date(regdate)=date('".$currentDate."') and state=1";
				if($rollingSn!='')
					$sql.= " and member_sn in(select sn from tb_member where rolling_sn=".$rollingSn.") ";

				$rsi = $this->db->exeSql($sql);

				$item[$i]['count_charge'] 	= $rsi[0]["count_charge"];
				$item[$i]['charge'] 		= $rsi[0]["sum_charge"];

				// 수익
				$item[$i]['benefit'] = $item[$i]['charge']-$item[$i]['exchange'];

				// 관리자 입금
				$sql = "select ifnull(sum(amount),0) as sum_admin_charge from tb_money_log
								where state=7 and amount>0 and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'";
				if($rollingSn!='')
					$sql.= " and member_sn in(select sn from tb_member where rolling_sn=".$rollingSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_charge'] = $rsi[0]['sum_admin_charge'];

				// 관리자 출금
				$sql = "select ifnull(sum(amount),0) as sum_admin_exchange from tb_money_log
								where state=7 and amount<0 and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where rolling_sn=".$rollingSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_exchange'] = $rsi[0]['sum_admin_exchange'];

				// 포인트 입금
				$sql = "select ifnull(sum(amount),0) as sum_admin_charge from tb_mileage_log
								where amount>0
								and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'
								and member_sn in(select sn from tb_member where mem_status!='G')";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where rolling_sn=".$rollingSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_mileage_charge'] = $rsi[0]['sum_admin_charge'];

				// 포인트 출금
				$sql = "select ifnull(sum(amount),0) as sum_admin_exchange from tb_mileage_log
								where amount<0
								and regdate between '".$currentDate." 00:00:00' and '".$currentDate." 23:59:59'
								and member_sn in(select sn from tb_member where mem_status!='G')";
				if($partnerSn!='')
					$sql.= " and member_sn in(select sn from tb_member where rolling_sn=".$rollingSn.") ";
				$rsi = $this->db->exeSql($sql);
				$item[$i]['admin_mileage_exchange'] = $rsi[0]['sum_admin_exchange'];

				$list[] = $item[$i];
			}
		}

		return $list;
	}

	//배팅통계
	function getBetList($filter_logo='', $filter_category='', $filter_league='', $begin_date, $end_date)
	{
		$where = "";

		if($filter_logo!='')
		{
			$where .= " and a.logo='".$filter_logo."'";
		}

		if($filter_category!='')
		{
			switch($filter_category)
			{
				case "normal"			:$where .= " and d.special='0'";break;
				case "special"			:$where .= " and d.special='1'";break;
				case "live"				:$where .= " and d.special='2'";break;
				case "ladder"			:$where .= " and d.special='5'";break;
				case "powerball"	:$where .= " and d.special='6'";break;
                case "dalpang"          :$where .= " and d.special='7'";break;
			}
		}

		if($filter_league!="")
		{
			$where .= " and d.league_sn='".$filter_league."'";
		}

		$sql = "select 	date(a.regdate) as regdate, a.result,
										sum(a.betting_money) as total_betting_money,
										count(a.betting_money) as total_betting_cnt,
										sum(a.result_money) as total_win_money,
										count(a.result_money) as total_win_cnt
					 	from tb_total_cart a,
					 	(select x.sub_child_sn, x.betting_no
					 		from tb_total_betting x, tb_total_cart y, tb_member z where x.betting_no=y.betting_no and y.member_sn=z.sn
					 		and z.mem_status!='G'
							and y.regdate between '".$begin_date." 00:00:00' and '".$end_date." 23:59:59' group by betting_no) b
						inner join tb_subchild c on b.sub_child_sn=c.sn, tb_child d
					 	where a.betting_no=b.betting_no and c.child_sn=d.sn and a.is_account=1".$where."
					 	group by date(a.regdate), a.result";

		$rows = $this->db->exeSql($sql);

		// Query 는 결과값에 의해 group을 하기 때문에
		// 배팅유저수를 구하기 위해서는 중복된 값을 제거해야 할 필요가 있었다.
		$date_array = array();
		for( $i=0; $i < count($rows); ++$i)
		{
			if( false===array_search($rows[$i]['regdate'], $date_array))
			{
				$date_array[] = $rows[$i]['regdate'];
			}
		}

		$used_array = array();
		for( $i=0; $i < count($rows); ++$i)
		{
			$src_date = $rows[$i]['regdate'];

			if( false!==array_search($src_date, $used_array))
			{
				continue;
			}
			$used_array[] = $src_date;

			for($j=0; $j < count($date_array); ++$j)
			{
				$dst_date = $date_array[$j];

				if($src_date==$dst_date)
				{
					$sql = "select count(distinct(a.member_sn)) as cnt
										from tb_total_cart a, tb_total_betting b, tb_subchild c, tb_child d, tb_member e
									where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn and a.member_sn=e.sn
										and e.mem_status<>'G'
										and a.regdate between '".$src_date." 00:00:00' and '".$src_date." 23:59:59'".$where;
					$betting_rows = $this->db->exeSql($sql);
					$rows[$i]['total_member_count'] = $betting_rows[0]['cnt'];
					break;
				}
			}
		}

		return $rows;
	}

	//축구실시간 배팅통계
	function getLiveBetList($filter_logo='', $filter_league='', $begin_date, $end_date)
	{
		$where = "";

		$sql = "select date(a.reg_time) as regdate, a.betting_result as result, sum(a.betting_money) as total_betting_money,
								count(a.betting_money) as total_betting_cnt, sum(a.prize) as total_win_money, count(a.prize) as total_win_cnt,
								count(distinct(a.member_sn)) as total_member_count
					 	from tb_live_betting a, tb_live_game b
					 	where a.live_sn=b.sn and (select mem_status from tb_member c where c.sn=a.member_sn) <> 'G'
					 			and date(a.reg_time)>='".$begin_date."' and date(a.reg_time)<='".$end_date."'";

		if($filter_logo!='')
		{
			$where .= " and a.logo='".$filter_logo."'";
		}

		if($filter_league!="")
		{
			$where .= " and b.league_sn='".$filter_league."'";
		}

		$groupby = " group by date(a.reg_time), a.betting_result";

		$sql = $sql.$where.$groupby;
		$rs = $this->db->exeSql($sql);

		return $rs;
	}

	//▶ 회원베팅총합
	function getMemberBetTotal($sn, $where)
	{
		$sql = "select count(*) as cnt
				from tb_total_cart
					where  member_sn ='".$sn."' and logo='".$this->logo."' and kubun ='Y' ".$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 회원베팅정보
	function getMemberBetList($sn, $where, $page, $page_size)
	{
		$sql = "select a.uid,a.nick,b.betting_no,b.betting_cnt,b.before_money,b.betting_money,b.result_rate,b.result_money,b.regdate,b.operdate,b.result,b.bonus
				from tb_member a, tb_total_cart b
					where b.member_sn='".$sn."' and kubun='Y' ".$where." and b.logo='".$this->logo."' and a.sn=b.member_sn
						order by regdate desc limit ".$page.",".$page_size;
		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]['betting_no'];
			$sql = "select a.select_no,a.home_rate,a.away_rate,a.draw_rate,a.game_type,a.result,b.name, c.home_team,c.home_score,c.away_team,c.away_score,
					c.gameDate,c.gameHour,c.gameTime
					from tb_total_betting a, tb_league b, tb_child c
						where a.betting_no='".$bettingNo."' and c.league_sn=b.sn and a.sub_child_sn=c.sn";

			$rsi = $this->db->exeSql($sql);
			$rs[$i]['item'] = $rsi;

			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$rs[$i]['item'][$j]['date'] = substr($rsi[$j]['gameDate'],5,2)."/".  substr($rsi[$j]['gameDate'],8,2) ." ". $rsi[$j]['gameHour'] .":". $rsi[$j]['gameTime'];
			}

		}
		return $rs;
	}
}

?>
