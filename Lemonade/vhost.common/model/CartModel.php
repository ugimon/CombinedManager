<?php

class CartModel extends Lemon_Model
{

	//▶ 베팅 목록
	function getBetByChildSn($child_sn)
	{
		if( !$this->is_integer_mysql_parameter($child_sn))
		{
			exit;
		}

		$sql = "select c.select_no, c.game_type, c.bet_money, a.home_team, a.away_team
						from tb_child a, tb_subchild b, tb_total_betting c, tb_total_cart d
					where a.sn=b.child_sn and b.sn=c.sub_child_sn and c.betting_no=d.betting_no and
						logo='".$this->logo."' and d.kubun='Y' and a.sn =".$child_sn;

		return $this->db->exeSql($sql);
	}


	function getMemberBetDetailList($betting_no, $member_sn)
	{
		if( !$this->is_integer_mysql_parameter($betting_no))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($member_sn))
		{
			exit;
		}

		$sql = "select a.sn as total_betting_sn, a.sub_child_sn,a.select_no,a.select_rate,a.game_type,a.result,b.win_team,
						b.sn as child_sn, b.home_team,b.away_team,b.home_score,b.away_score,b.special,b.gameDate,b.gameHour,b.gameTime,
						c.name as league_name,c.lg_img as league_image, d.win,a.home_rate,a.away_rate,a.draw_rate
						from tb_total_betting a, tb_child b, tb_league c, tb_subchild d, tb_total_cart e
						where a.betting_no='".$betting_no."' and a.sub_child_sn=d.sn and b.league_sn=c.sn and b.sn=d.child_sn and a.betting_no = e.betting_no and e.member_sn = ".$member_sn;

		$rs = $this->db->exeSql($sql);

		for($i = 0; $i < sizeof($rs); ++$i)
		{
			$gameDate = trim($rs[$i]['gameDate']);
			$gameHour = trim($rs[$i]['gameHour']);
			$gameTime = trim($rs[$i]['gameTime']);

			$strDay = date('w',strtotime($gameDate));
			switch($strDay)
			{
			case 0: $Weekday_name = "(일)"; break;
			case 1: $Weekday_name = "(월)"; break;
			case 2: $Weekday_name = "(화)"; break;
			case 3: $Weekday_name = "(수)"; break;
			case 4: $Weekday_name = "(목)"; break;
			case 5: $Weekday_name = "(금)"; break;
			case 6: $Weekday_name = "(토)"; break;
			}
			$g_date = substr($gameDate,5,2)."/".  substr($gameDate,8,2) . $Weekday_name ." ". $gameHour .":". $gameTime;
			$rs[$i]['g_date'] = $g_date;
		}

		return $rs;
	}

	//▶ 카트 마지막 인덱스
	function getLastCartIndex()
	{
		$sql = "select max(sn) as last_sn from tb_total_cart";

		$rs = $this->db->exeSql($sql);
		return $rs[0]['last_sn'];
	}

	//▶ 배팅 총머니

	function getTotalBetMoney($partnertSn='',$childSn='',$memberSn='', $beginDate='', $endDate='', $result='', $logo='')
	{
		if( !$this->is_integer_mysql_parameter($partnertSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		if($logo!='') $logo = " and logo='".$logo."'";

		$sql = "select
							ifnull(sum(betting_money),0) as total_betting,
							ifnull(sum(result_money),0) as total_result,
							count(betting_money) as betting_count
						from tb_total_cart
						where kubun='Y' and is_account=1".$logo;

		if($childSn!='')
		{
			$sql.=" and betting_no in
							(select distinct(betting_no) from tb_total_betting where sub_child_sn =
							(select sn from tb_subchild where child_sn=".$childSn."))";
		}
		if($memberSn!='')	$sql.=" and member_sn=".$memberSn;
		if($beginDate!='' && $endDate!='')
		{
			$beginDate.= " 00:00:00";
			$endDate	.= " 23:59:59";
			$sql.=" and regdate between '".$beginDate."' and '".$endDate."'";
		}
		if($result===2) $sql.=" and result=2";
		else if($result===1) 	$sql.=" and result=1";
		else if($result===0) 	$sql.=" and result=0";
		if($partnertSn!='') 	$sql.=" and partner_sn=".$partnertSn;

		$rs = $this->db->exeSql($sql);

		return $rs[0];
	}

	//▶ 롤링 배팅 총머니
	function getRollingTotalBetMoney($rollingSn='',$beginDate='', $endDate='', $result='')
	{
		$sql = "select
							ifnull(sum(betting_money),0) as total_betting,
							ifnull(sum(result_money),0) as total_result,
							count(betting_money) as betting_count
						from tb_total_cart
						where logo='".$this->logo."' and kubun='Y' and is_account=1";

		if($beginDate!='' && $endDate!='')
		{
			$beginDate.= " 00:00:00";
			$endDate	.= " 23:59:59";
			$sql.=" and regdate between '".$beginDate."' and '".$endDate."'";
		}
		if($result===2) $sql.=" and result=2";
		else if($result===1) 	$sql.=" and result=1";
		else if($result===0) 	$sql.=" and result=0";

		if($rollingSn!='') 	$sql.=" and rolling_sn=".$rollingSn;

		$rs = $this->db->exeSql($sql);

		return $rs[0];
	}

	//▶ 승,무,패 별 매팅 금액 - 낙첨 금액 제외
	function getTeamTotalBetMoney($childSn)
	{
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}

		$item = array();

		//승 총배팅 금액
		$sql = "select 	ifnull(sum(a.betting_money),0) as total_betting, count(a.betting_money) as cnt
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn
						and a.kubun='Y' and d.sn=".$childSn." and b.select_no=1
						and a.is_account=1";

		$rs = $this->db->exeSql($sql);
		$item['home_total_betting'] = $rs[0]['total_betting'];
		$item['home_count'] = $rs[0]['cnt'];

		//무 총배팅 금액
		$sql = "select 	ifnull(sum(a.betting_money),0) as total_betting, count(a.betting_money) as cnt
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn
						and a.kubun='Y' and d.sn=".$childSn." and b.select_no=3
						and a.is_account=1";
		$rs = $this->db->exeSql($sql);
		$item['draw_total_betting'] = $rs[0]['total_betting'];
		$item['draw_count'] = $rs[0]['cnt'];

		//패 총배팅 금액
		$sql = "select 	ifnull(sum(a.betting_money),0) as total_betting, count(a.betting_money) as cnt
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn
						and a.kubun='Y' and d.sn=".$childSn." and b.select_no=2
						and a.is_account=1";
		$rs = $this->db->exeSql($sql);
		$item['away_total_betting'] = $rs[0]['total_betting'];
		$item['away_count'] = $rs[0]['cnt'];

		//승 총배팅 금액 - 낙첨제외
		$sql = "select 	ifnull(sum(a.betting_money),0) as total_betting
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn
						and a.kubun='Y' and d.sn=".$childSn." and b.select_no=1 and a.result=0
						and a.is_account=1";
		$rs = $this->db->exeSql($sql);
		$item['active_home_total_betting'] = $rs[0]['total_betting'];

		//무 총배팅 금액
		$sql = "select 	ifnull(sum(a.betting_money),0) as total_betting
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn
						and a.kubun='Y' and d.sn=".$childSn." and b.select_no=3  and a.result=0
						and a.is_account=1";
		$rs = $this->db->exeSql($sql);
		$item['active_draw_total_betting'] = $rs[0]['total_betting'];

		//패 총배팅 금액
		$sql = "select 	ifnull(sum(a.betting_money),0) as total_betting
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn
						and a.kubun='Y' and d.sn=".$childSn." and b.select_no=2  and a.result=0
						and a.is_account=1";
		$rs = $this->db->exeSql($sql);
		$item['active_away_total_betting'] = $rs[0]['total_betting'];

		return $item;
	}

	public function calcResultRate($betting_no)
	{
		if( !$this->is_integer_mysql_parameter($betting_no))
		{
			exit;
		}

		$sql = "select select_rate, result
						from tb_total_betting
						where betting_no='".$betting_no."'";

		$rs = $this->db->exeSql($sql);

		$result_rate = 1;
		for( $i=0; $i<sizeof($rs); ++$i)
		{
			if($rs[$i]['result']!=4)
			{
				$result_rate = $result_rate*$rs[$i]['select_rate'];
			}
			else
				$result_rate *=1;
		}
		return bcmul($result_rate, 1, 2);
	}

	//▶ 멤버의 배팅목록
	public function getMemberBetList($memberSn, $page=0, $page_size=0, $beginDate='', $endDate='', $orderby='', $where='')
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$mModel = Lemon_Instance::getObject("MemberModel",true);
		$pModel = Lemon_Instance::getObject("PartnerModel",true);

		$sql = "select *
						from tb_total_cart
						where member_sn ='".$memberSn."' and kubun ='Y' ".$where;

		//Date
		if($beginDate!="" && $endDate!="") 	{$sql.=" and (bet_date between '".$beginDate."' and '".$endDate."') ";}

		//order by, limit
		$sql.=  " order by betting_no desc";
		if($page_size > 0)					{$sql.= " limit ".$page.",".$page_size;}

		//excute
		$rs = $this->db->exeSql($sql);

		$itemList = array();


		$addWhere = " sn = ".$memberSn;
		$rsm = $mModel->getMemberRows('*', $addWhere);

		$recommend_sn =  $rsm[0]['recommend_sn'];
		if( $recommend_sn != '' )
			$rec_name = $pModel->getPartnerField( $recommend_sn, 'rec_name');

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]["betting_no"];
			$rs[$i]['result_rate'] = $this->calcResultRate($bettingNo);
			$event = $rs[$i]["event"];

			$sql = "select a.sub_child_sn,a.select_no,a.home_rate,a.away_rate,a.draw_rate,a.select_rate,a.game_type,a.result,
								b.sn as child_sn, b.home_team,b.away_team,b.home_score,b.away_score,b.special,b.gameDate,b.gameHour,b.gameTime,
								c.name as league_name,c.lg_img as league_image, d.win
							from tb_total_betting a, tb_child b, tb_league c, tb_subchild d
							where a.betting_no='".$bettingNo."' and a.sub_child_sn=d.sn and b.league_sn=c.sn and b.sn=d.child_sn";

			if($orderby!='') {$sql.=" order by ".$orderby;}

			$rsi = $this->db->exeSql($sql);

			$itemList[$bettingNo] = $rs[$i];
			$itemList[$bettingNo]['member'] = $rsm[0];
			$itemList[$bettingNo]['rec_name'] = $rec_name;

			$index = 0;

			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$itemList[$bettingNo]['win_money'] = (int)($rs[$i]['betting_money']*$rs[$i]['result_rate']);
				$itemList[$bettingNo]['folder_bonus']=0;

				$gameDate = trim($rsi[$j]['gameDate']);
				$gameHour = trim($rsi[$j]['gameHour']);
				$gameTime = trim($rsi[$j]['gameTime']);

				$strDay = date('w',strtotime($gameDate));
				switch($strDay)
				{
				case 0: $Weekday_name = "(일)"; break;
				case 1: $Weekday_name = "(월)"; break;
				case 2: $Weekday_name = "(화)"; break;
				case 3: $Weekday_name = "(수)"; break;
				case 4: $Weekday_name = "(목)"; break;
				case 5: $Weekday_name = "(금)"; break;
				case 6: $Weekday_name = "(토)"; break;
				}
				$g_date = substr($gameDate,5,2)."/".  substr($gameDate,8,2) . $Weekday_name ." ". $gameHour .":". $gameTime;
				$rsi[$j]['g_date'] = $g_date;

				//폴더보너스 체크시 적용
				if($event==0)
				{
					$cModel = Lemon_Instance::getObject("ConfigModel",true);

					$level	= $mModel->getMemberField($memberSn,'mem_lev');
					$field  = $cModel->getLevelConfigRow($level);
					$folderBonuses = explode(":", $field['lev_folder_bonus']);

					$bonusRate=0;
					switch($rs[$i]['betting_cnt'])
					{
						case 3:  $bonusRate=$folderBonuses[0]; break;
						case 4:  $bonusRate=$folderBonuses[1]; break;
						case 5:  $bonusRate=$folderBonuses[2]; break;
						case 6:  $bonusRate=$folderBonuses[3]; break;
						case 7:  $bonusRate=$folderBonuses[4]; break;
						case 8:  $bonusRate=$folderBonuses[5]; break;
						case 9:  $bonusRate=$folderBonuses[6]; break;
						case 10: $bonusRate=$folderBonuses[7]; break;
					}

					$itemList[$bettingNo]['bonus_rate'] = $bonusRate;
					$itemList[$bettingNo]['folder_bonus'] = (int)($itemList[$bettingNo]['win_money']*$bonusRate/100);
				}
				else if($event==1)
				{
					$cModel = Lemon_Instance::getObject("ConfigModel",true);

					$folderBonuses = $cModel->getEventConfigRow();

					$amount=0;
					switch($rs[$i]['betting_cnt'])
					{
						case 1:  $amount=$folderBonuses['bonus1']; break;
						case 2:  $amount=$folderBonuses['bonus2']; break;
						case 3:  $amount=$folderBonuses['bonus3']; break;
						case 4:  $amount=$folderBonuses['bonus4']; break;
						case 5:  $amount=$folderBonuses['bonus5']; break;
						case 6:  $amount=$folderBonuses['bonus6']; break;
						case 7:  $amount=$folderBonuses['bonus7']; break;
						case 8:  $amount=$folderBonuses['bonus8']; break;
						case 9:  $amount=$folderBonuses['bonus9']; break;
						case 10: $amount=$folderBonuses['bonus10']; break;
					}

					$itemList[$bettingNo]['folder_bonus'] = $amount;
				}

				$rsi[$j]['index'] = $index;
				$itemList[$bettingNo]['item'][] = $rsi[$j];
				++$index;
			//	$itemList[$bettingNo]['item'][] = $rsi[$j];

			} // end of 2nd for
		} // end of 1st for

		return $itemList;
	}

	//▶ 멤버의 배팅목록 (뷰 용 by. sp)
	public function getMemberBetList2($memberSn, $page=0, $page_size=0, $beginDate='', $endDate='', $orderby='', $where='')
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$mModel = Lemon_Instance::getObject("MemberModel",true);
		$pModel = Lemon_Instance::getObject("PartnerModel",true);

		$sql = "select *
						from tb_total_cart
						where member_sn ='".$memberSn."' and kubun ='Y' ".$where;


		//Date
		if($beginDate!="" && $endDate!="") 	{$sql.=" and (bet_date between '".$beginDate."' and '".$endDate."') ";}

		//order by, limit
		$sql.=  " order by betting_no desc";

		//excute
		$rs = $this->db->exeSql($sql);

		$itemList = array();


		$addWhere = " sn = ".$memberSn;
		$rsm = $mModel->getMemberRows('*', $addWhere);

		$recommend_sn =  $rsm[0]['recommend_sn'];
		if( $recommend_sn != '' )
			$rec_name = $pModel->getPartnerField( $recommend_sn, 'rec_name');

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]["betting_no"];
			$rs[$i]['result_rate'] = $this->calcResultRate($bettingNo);
			$event = $rs[$i]["event"];

			$sql = "select a.sub_child_sn,a.select_no,a.home_rate,a.away_rate,a.draw_rate,a.select_rate,a.game_type,a.result,
								b.sn as child_sn, b.home_team,b.away_team,b.home_score,b.away_score,b.special,b.gameDate,b.gameHour,b.gameTime,
								c.name as league_name,c.lg_img as league_image, d.win
							from tb_total_betting a, tb_child b, tb_league c, tb_subchild d
							where a.betting_no='".$bettingNo."' and a.sub_child_sn=d.sn and b.league_sn=c.sn and b.sn=d.child_sn";

			if($orderby!='') {$sql.=" order by ".$orderby;}

			$rsi = $this->db->exeSql($sql);

			$itemList[$i] = $rs[$i];
			$itemList[$i]['member'] = $rsm[0];
			$itemList[$i]['rec_name'] = $rec_name;

			$index = 0;

			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$itemList[$i]['win_money'] = (int)($rs[$i]['betting_money']*$rs[$i]['result_rate']);
				$itemList[$i]['folder_bonus']=0;

				$gameDate = trim($rsi[$j]['gameDate']);
				$gameHour = trim($rsi[$j]['gameHour']);
				$gameTime = trim($rsi[$j]['gameTime']);

				$strDay = date('w',strtotime($gameDate));
				switch($strDay)
				{
				case 0: $Weekday_name = "(일)"; break;
				case 1: $Weekday_name = "(월)"; break;
				case 2: $Weekday_name = "(화)"; break;
				case 3: $Weekday_name = "(수)"; break;
				case 4: $Weekday_name = "(목)"; break;
				case 5: $Weekday_name = "(금)"; break;
				case 6: $Weekday_name = "(토)"; break;
				}
				$g_date = substr($gameDate,5,2)."/".  substr($gameDate,8,2) . $Weekday_name ." ". $gameHour .":". $gameTime;
				$rsi[$j]['g_date'] = $g_date;

				//폴더보너스 체크시 적용
				if($event==0)
				{
					$cModel = Lemon_Instance::getObject("ConfigModel",true);

					$level	= $mModel->getMemberField($memberSn,'mem_lev');
					$field  = $cModel->getLevelConfigRow($level);
					$folderBonuses = explode(":", $field['lev_folder_bonus']);

					$bonusRate=0;
					switch($rs[$i]['betting_cnt'])
					{
						case 3:  $bonusRate=$folderBonuses[0]; break;
						case 4:  $bonusRate=$folderBonuses[1]; break;
						case 5:  $bonusRate=$folderBonuses[2]; break;
						case 6:  $bonusRate=$folderBonuses[3]; break;
						case 7:  $bonusRate=$folderBonuses[4]; break;
						case 8:  $bonusRate=$folderBonuses[5]; break;
						case 9:  $bonusRate=$folderBonuses[6]; break;
						case 10: $bonusRate=$folderBonuses[7]; break;
					}

					$itemList[$i]['bonus_rate'] = $bonusRate;
					$itemList[$i]['folder_bonus'] = (int)($itemList[$i]['win_money']*$bonusRate/100);
				}
				else if($event==1)
				{
					$cModel = Lemon_Instance::getObject("ConfigModel",true);

					$folderBonuses = $cModel->getEventConfigRow();

					$amount=0;
					switch($rs[$i]['betting_cnt'])
					{
						case 1:  $amount=$folderBonuses['bonus1']; break;
						case 2:  $amount=$folderBonuses['bonus2']; break;
						case 3:  $amount=$folderBonuses['bonus3']; break;
						case 4:  $amount=$folderBonuses['bonus4']; break;
						case 5:  $amount=$folderBonuses['bonus5']; break;
						case 6:  $amount=$folderBonuses['bonus6']; break;
						case 7:  $amount=$folderBonuses['bonus7']; break;
						case 8:  $amount=$folderBonuses['bonus8']; break;
						case 9:  $amount=$folderBonuses['bonus9']; break;
						case 10: $amount=$folderBonuses['bonus10']; break;
					}

					$itemList[$i]['folder_bonus'] = $amount;
				}

				$rsi[$j]['index'] = $index;
				$itemList[$i]['item'][] = $rsi[$j];
				++$index;
			//	$itemList[$bettingNo]['item'][] = $rsi[$j];

			} // end of 2nd for
		} // end of 1st for

		return $itemList;
	}
	// 총판/하부총판별 배팅한카트횟수
	function getRecommendTotalCartCount($partner_sn, $startDate="", $endDate="",$where="",$child_sn)
	{
		if( !$this->is_integer_mysql_parameter($partner_sn))
		{
			exit;
		}

		$whereStr=" and c.kubun ='Y' ";
		if( $startDate!="" && $endDate!="")
		{
			$whereStr.= " and (c.bet_date between '".$startDate."' and '".$endDate."')";
		}



		 if($child_sn>0 ){ //해당총판만
			//$sql = "select count(1) as cnt from tb_member 	where recommend_sn = '".$child_sn."' ".$where;
			if($partner_sn == $child_sn)
				$sql="select count(1) as cnt from tb_total_cart c, tb_member a ,tb_recommend b where c.member_sn=a.sn and a.recommend_sn=b.idx and  b.idx=".$partner_sn .$whereStr." ".$where ;
			else
				$sql="select count(1) as cnt from tb_total_cart c,tb_member a ,tb_recommend b where c.member_sn=a.sn and a.recommend_sn=b.idx and  b.idx=".$child_sn." and b.parent_rec_sn='".$partner_sn."'  ".$whereStr." ".$where;

		} else { // 하위 총판회원까지 포함
				$sql="select count(1) as cnt from tb_total_cart c,tb_member a ,tb_recommend b where c.member_sn=a.sn and a.recommend_sn=b.idx and (    b.parent_rec_sn='".$partner_sn."'  or b.idx=" .$partner_sn .") ".$whereStr." ".$where;
		}

		$rs = $this->db->exeSql($sql);
 		return $rs[0]['cnt'];
	}

	//▶ 총판/하부총판 멤버의 배팅목록
	public function getRecommendMemberBetList( $page=0, $page_size=0, $beginDate='', $endDate='', $where='',$partner_sn,$child_sn )
	{
		if( !$this->is_integer_mysql_parameter($partner_sn))
		{
			exit;
		}

		$mModel = Lemon_Instance::getObject("MemberModel",true);
		$pModel = Lemon_Instance::getObject("PartnerModel",true);


/*

		$sql = "select *
						from tb_total_cart
						where member_sn ='".$memberSn."' and kubun ='Y' ".$where;

		//Date
		if($beginDate!="" && $endDate!="") 	{$sql.=" and (bet_date between '".$beginDate."' and '".$endDate."') ";}

		//order by, limit
		$sql.=  " order by betting_no desc";
		if($page_size > 0)
			{$sql.= " limit ".$page.",".$page_size;}

		//excute

		*/

		$whereStr=" and c.kubun ='Y' ";
		if( $startDate!="" && $endDate!="")
		{
			$whereStr.= " and (c.bet_date between '".$startDate."' and '".$endDate."')";
		}



		 if($child_sn>0 ){ //해당총판만
			//$sql = "select count(1) as cnt from tb_member 	where recommend_sn = '".$child_sn."' ".$where;
			if($partner_sn == $child_sn)
				$sql="select c.*, a.sn,a.uid,a.nick,b.rec_name  from tb_total_cart c, tb_member a ,tb_recommend b where c.member_sn=a.sn and a.recommend_sn=b.idx and  b.idx=".$partner_sn .$whereStr." ".$where ;
			else
				$sql="select c.*, a.sn,a.uid,a.nick,b.rec_name  from tb_total_cart c,tb_member a ,tb_recommend b where c.member_sn=a.sn and a.recommend_sn=b.idx and  b.idx=".$child_sn." and b.parent_rec_sn='".$partner_sn."'  ".$whereStr." ".$where;

		} else { // 하위 총판회원까지 포함
				$sql="select c.* , a.sn,a.uid,a.nick,b.rec_name  from tb_total_cart c,tb_member a ,tb_recommend b where c.member_sn=a.sn and a.recommend_sn=b.idx and (    b.parent_rec_sn='".$partner_sn."'  or b.idx=" .$partner_sn .") ".$whereStr." ".$where;
		}
		$sql.=  " order by c.betting_no desc";
		if($page_size > 0)
			{$sql.= " limit ".$page.",".$page_size;}






		$rs = $this->db->exeSql($sql);

		$itemList = array();

		/*
		$addWhere = " sn = ".$memberSn;
		$rsm = $mModel->getMemberRows('*', $addWhere);

		$recommend_sn =  $rsm[0]['recommend_sn'];
		if( $recommend_sn != '' )
			$rec_name = $pModel->getPartnerField( $recommend_sn, 'rec_name');
		*/
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]["betting_no"];
			$rs[$i]['result_rate'] = $this->calcResultRate($bettingNo);
			$event = $rs[$i]["event"];

			$itemList[$bettingNo] = $rs[$i];
		//	$itemList[$bettingNo]['member'] = $rsm[0];
		//	$itemList[$bettingNo]['rec_name'] = $rec_name;

			$sql = "select a.sub_child_sn,a.select_no,a.home_rate,a.away_rate,a.draw_rate,a.select_rate,a.game_type,a.result,
								b.sn as child_sn, b.home_team,b.away_team,b.home_score,b.away_score,b.special,b.gameDate,b.gameHour,b.gameTime,
								c.name as league_name,c.lg_img as league_image, d.win
							from tb_total_betting a, tb_child b, tb_league c, tb_subchild d
							where a.betting_no='".$bettingNo."' and a.sub_child_sn=d.sn and b.league_sn=c.sn and b.sn=d.child_sn";

			if($orderby!='') {$sql.=" order by ".$orderby;}

			$rsi = $this->db->exeSql($sql);

			$index = 0;

			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$itemList[$bettingNo]['win_money'] = (int)($rs[$i]['betting_money']*$rs[$i]['result_rate']);
				$itemList[$bettingNo]['folder_bonus']=0;

				$gameDate = trim($rsi[$j]['gameDate']);
				$gameHour = trim($rsi[$j]['gameHour']);
				$gameTime = trim($rsi[$j]['gameTime']);

				$strDay = date('w',strtotime($gameDate));
				switch($strDay)
				{
				case 0: $Weekday_name = "(일)"; break;
				case 1: $Weekday_name = "(월)"; break;
				case 2: $Weekday_name = "(화)"; break;
				case 3: $Weekday_name = "(수)"; break;
				case 4: $Weekday_name = "(목)"; break;
				case 5: $Weekday_name = "(금)"; break;
				case 6: $Weekday_name = "(토)"; break;
				}
				$g_date = substr($gameDate,5,2)."/".  substr($gameDate,8,2) . $Weekday_name ." ". $gameHour .":". $gameTime;
				$rsi[$j]['g_date'] = $g_date;

				//폴더보너스 체크시 적용
				if($event==0)
				{
					$cModel = Lemon_Instance::getObject("ConfigModel",true);

					$level	= $mModel->getMemberField($memberSn,'mem_lev');
					$field  = $cModel->getLevelConfigRow($level);
					$folderBonuses = explode(":", $field['lev_folder_bonus']);

					$bonusRate=0;
					switch($rs[$i]['betting_cnt'])
					{
						case 3:  $bonusRate=$folderBonuses[0]; break;
						case 4:  $bonusRate=$folderBonuses[1]; break;
						case 5:  $bonusRate=$folderBonuses[2]; break;
						case 6:  $bonusRate=$folderBonuses[3]; break;
						case 7:  $bonusRate=$folderBonuses[4]; break;
						case 8:  $bonusRate=$folderBonuses[5]; break;
						case 9:  $bonusRate=$folderBonuses[6]; break;
						case 10: $bonusRate=$folderBonuses[7]; break;
					}

					$itemList[$bettingNo]['bonus_rate'] = $bonusRate;
					$itemList[$bettingNo]['folder_bonus'] = (int)($itemList[$bettingNo]['win_money']*$bonusRate/100);
				}
				else if($event==1)
				{
					$cModel = Lemon_Instance::getObject("ConfigModel",true);

					$folderBonuses = $cModel->getEventConfigRow();

					$amount=0;
					switch($rs[$i]['betting_cnt'])
					{
						case 1:  $amount=$folderBonuses['bonus1']; break;
						case 2:  $amount=$folderBonuses['bonus2']; break;
						case 3:  $amount=$folderBonuses['bonus3']; break;
						case 4:  $amount=$folderBonuses['bonus4']; break;
						case 5:  $amount=$folderBonuses['bonus5']; break;
						case 6:  $amount=$folderBonuses['bonus6']; break;
						case 7:  $amount=$folderBonuses['bonus7']; break;
						case 8:  $amount=$folderBonuses['bonus8']; break;
						case 9:  $amount=$folderBonuses['bonus9']; break;
						case 10: $amount=$folderBonuses['bonus10']; break;
					}

					$itemList[$bettingNo]['folder_bonus'] = $amount;
				}

				$rsi[$j]['index'] = $index;
				$itemList[$bettingNo]['item'][] = $rsi[$j];
				++$index;
			//	$itemList[$bettingNo]['item'][] = $rsi[$j];

			} // end of 2nd for
		} // end of 1st for

		return $itemList;
	}


	//▶ 유저가 베팅한 카트횟수
	function getMemberTotalCartCount($sn, $startDate="", $endDate="",$where="")
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select count(*) as cnt from tb_total_cart
						where /*user_del<>'Y' and*/  member_sn ='".$sn."' and kubun ='Y' ".$where;

		if( $startDate!="" && $endDate!="")
		{
			$sql.= " and (bet_date between '".$startDate."' and '".$endDate."')";
		}


		$rs = $this->db->exeSql($sql);

		return $rs[0]['cnt'];
	}

	//▶ 유저가 베팅한 베팅금액
	function getMemberTotalBetMoney($sn, $beginDate="", $endDate="",$where="")
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select sum(betting_money) as bet_money
							from tb_total_cart
								where member_sn ='".$sn."' and kubun ='Y' ".$where;

		if( $beginDate!="" && $endDate!="")
		{
			$sql.= " and (bet_date between '".$beginDate."' and '".$endDate."')";
		}
		$rs = $this->db->exeSql($sql);

		return $rs[0]['bet_money'];
	}

	//▶ 유저가 베팅한 베팅횟수
	function getMemberTotalBetCount($sn, $startDate="", $endDate="",$where="")
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$cnt = 0;

		$sql = "select betting_no
				from tb_total_cart
					where user_del<>'Y' and member_sn ='".$sn."' and kubun ='Y' ".$where;

		if( $startDate!="" && $endDate!="")
		{
			$sql.= " and (bet_date between '".$startDate."' and '".$endDate."')";
		}
		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]["betting_no"];

			$sql = "select count(*) as cnt
					from tb_total_betting a, tb_subchild b
						where a.betting_no='".$bettingNo."' and a.sub_child_sn = b.sn";

			$rs_cnt = $this->db->exeSql($sql);

			$cnt += $rs_cnt[0]['cnt'];
		}

		return $cnt;
	}

	//▶ 진행중인 베팅횟수
	function getMemberIngBetCount($sn, $startDate="", $endDate="")
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$cnt = 0;

		$sql = "select betting_no
				from tb_total_cart
					where user_del<>'Y' and member_sn ='".$sn."' and kubun ='Y' ";

		if( $startDate != "" && $endDate != "" )
		{
			$sql.= " and (a.bet_date between '".$startDate."' and '".$endDate."')";
		}
		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]["betting_no"];

			$sql = "select count(*) as cnt
					from tb_total_betting a, tb_subchild b
						where a.betting_no='".$bettingNo."' and a.sub_child_sn=b.sn and a.result=0";

			$rs_cnt = $this->db->exeSql($sql);

			$cnt += $rs_cnt[0]['cnt'];
		}

		return $cnt;
	}

	//▶ 끝난 베팅횟수
	function getMemberEndBetCount($sn, $startDate="", $endDate="")
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$cnt = 0;

		$sql = "select betting_no
				from tb_total_cart
					where user_del<>'Y' and member_sn ='".$sn."' and kubun ='Y' ";

		if( $startDate!="" && $end_date!="" )
		{
			$sql.= " and (a.bet_date between '".$startDate."' and '".$end_date."')";
		}
		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]["betting_no"];

			$sql = "select  count(*) as cnt
					from tb_total_betting a , tb_subchild b
						where a.betting_no='".$bettingNo."' and a.sub_child_sn=b.sn and a.result>0";

			$rs_cnt = $this->db->exeSql($sql);
			$cnt += $rs_cnt[0]['cnt'];
		}

		return $cnt;

	}

	//▶ 금일 이벤트 베팅횟수
	function getMemberTodayEventBetCount($memberSn)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$sql = "select count(*) as cnt
				from tb_total_betting a,tb_child b,tb_total_cart c
					where a.sub_child_sn=b.sn and a.betting_no=c.betting_no
					and a.member_sn ='".$memberSn."' and b.event=1 and date(c.regdate)=date(now())";

		$rs = $this->db->exeSql($sql);

		return $rs[0]['cnt'];

	}

	//▶ 베팅 총합
	function getBettingListTotal($where="", $active=0, $gameSn="", $selectNo="")
	{
		if( !$this->is_integer_mysql_parameter($gameSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($selectNo))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($active))
		{
			exit;
		}

		if($active==1) 	$where = " and a.result!=2 ";
		if($gameSn!="")
		{
			$sql = "select sn from tb_subchild where child_sn=".$gameSn;
			$rs = $this->db->exeSql($sql);
			$subSn = $rs[0]['sn'];

			$addWhere.= " and betting_no in(select distinct(betting_no) from tb_total_betting where sub_child_sn=".$subSn;

			if($selectNo!="")
				$addWhere.= " and select_no=".$selectNo;

			$addWhere.= ")";
		}

		$sql = "select count(*) as cnt
						from tb_total_cart a inner join tb_member b
						where a.member_sn=b.sn and a.logo='".$this->logo."' and a.kubun='Y' and a.is_account=1 ".$addWhere.$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 베팅 목록
	function getBettingList($where, $page, $page_size, $active=0, $gameSn="", $selectNo="")
	{
		if( !$this->is_integer_mysql_parameter($gameSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($selectNo))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($active))
		{
			exit;
		}

		if($active==1)
			$where=" and a.result!=2 ";

		if($gameSn!="")
		{
			$sql = "select sn from tb_subchild where child_sn=".$gameSn;
			$rs = $this->db->exeSql($sql);
			$subSn = $rs[0]['sn'];

			$addWhere.= " and betting_no in(select distinct(betting_no) from tb_total_betting where sub_child_sn=".$subSn;

			if($selectNo!="")
				$addWhere.= " and select_no=".$selectNo;

			$addWhere.= ")";
		}

		if($page_size!=0)
			$limit = "limit ".$page.",".$page_size;

		$sql = "select 	a.member_sn, a.betting_no, a.betting_money, a.betting_ip, a.regDate,
										a.result_money, a.result_rate, a.result as aresult, a.betting_cnt, b.uid, b.nick, b.recommend_sn
						from tb_total_cart a,tb_member b
						where		a.betting_no in(
										select betting_no from
										(select betting_no from tb_total_cart where logo='".$this->logo."' and kubun='Y' ".$addWhere."order by betting_no desc ".$limit.") as t)
										and a.member_sn=b.sn and a.is_account=1".$where." order by a.regdate desc";

		$rs = $this->db->exeSql($sql);

		$partnerModel = Lemon_Instance::getObject("PartnerModel",true);
		for($i=0;$i<sizeof($rs); ++$i)
		{
			$member_sn = $rs[$i]['member_sn'];
			$betting_no =  $rs[$i]['betting_no'];
			$recommend_sn = $rs[$i]['recommend_sn'];

			$rsi = $this->getMemberBetDetailList($betting_no, $member_sn);

			if($recommend_sn!="")
				$rec_id = $partnerModel->getPartnerField( $recommend_sn, 'rec_id');

			else $rec_id = "무소속";

			$rs[$i]['win_count']=0;
			for($j=0; $j<sizeof($rsi); ++$j)
				if($rsi[$j]['result']==1) {$rs[$i]['win_count']+=1;}

			$rs[$i]['rec_id'] = $rec_id;
			$rs[$i]['item'] = $rsi;
		}

		return $rs;
	}

	//배팅내역 DB추출
	function getBet_Export()
	{
		$sql = "select a.member_sn,a.betting_no,a.betting_money,a.betting_ip, a.regDate,a.result_money,a.result_rate,a.result as aresult,a.betting_cnt,b.uid,b.nick,b.recommend_sn ";
		$sql.= " from tb_total_cart a inner join tb_member b ";
		$sql.= " where a.member_sn=b.sn and a.regdate between date_add(now(), interval -1 day) and now() order by a.regdate desc";

		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$member_sn 		= $rs[$i]['member_sn'];
			$betting_no 	=  $rs[$i]['betting_no'];
			$recommend_sn = $rs[$i]['recommend_sn'];

			$rsi = $this->getMemberBetDetailList($betting_no, $member_sn);

			$rs[$i]['item'] = $rsi;
		}

		return $rs;
	}

	function modifyExceptionBet($total_betting_sn)
	{
		if( !$this->is_integer_mysql_parameter($total_betting_sn))
		{
			exit;
		}

		$sql = "select betting_no, select_rate from tb_total_betting where sn=".$total_betting_sn;
		$rs = $this->db->exeSql($sql);
		$bettingNo  = $rs[0]['betting_no'];
		$selectRate = $rs[0]['select_rate'];

		if($selectRate == "1.00")
		{
			return;
		}
		$sql = "update tb_total_betting set home_rate='1.00',draw_rate='1.00',away_rate='1.00',select_rate='1.00'
						where sn=".$total_betting_sn;
		$this->db->exeSql($sql);


		$sql = "update tb_total_cart set result_rate=result_rate/".$selectRate." where betting_no='".$bettingNo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 베팅내역 상태변화
	function modifyViewState($memberSn, $bettingNo, $isHide=1)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($bettingNo))
		{
			exit;
		}

		if($isHide==1)
			$sql = "update tb_total_cart set user_del='Y' where betting_no = '". $bettingNo ."'and member_sn=". $memberSn;
		else
			$sql = "update tb_total_cart set user_del='N' where betting_no = '". $bettingNo ."'and member_sn=". $memberSn;

		return $this->db->exeSql($sql);
	}

	function hide_all_betting($member_sn)
	{
		if( !$this->is_integer_mysql_parameter($member_sn))
		{
			exit;
		}

		$sql = "update tb_total_cart
						set user_del='Y'
						where result <>0 and member_sn=". $member_sn;
						//echo $sql; exit;

		return $this->db->exeSql($sql);
	}

	//▶ 베팅삭제
	function delCart($bettingNo)
	{
		if( !$this->is_integer_mysql_parameter($bettingNo))
		{
			exit;
		}

		$this->copyCartCancelToCart($bettingNo);
		$sql = "delete from tb_total_cart
				where betting_no = '". $bettingNo ."'";
		$this->db->exeSql($sql);

		$sql = "delete from tb_total_betting
					where betting_no='". $bettingNo ."'";
		$this->db->exeSql($sql);
	}

	//▶ 취소배팅내역 복사
	function copyCartCancelToCart($bettingNo)
	{
		//취소배팅카트복사
		$sql = "INSERT INTO tb_total_cart_cancel select * from tb_total_cart where betting_no='".$bettingNo."'";
		$this->db->exeSql($sql);

		//취소시간 업데이트
		$sql = "update tb_total_cart_cancel set operdate=now() where betting_no = '".$bettingNo."'";
		$this->db->exeSql($sql);

		//취소배팅토탈복사
		$sql = "INSERT INTO tb_total_betting_cancel select * from tb_total_betting where betting_no='".$bettingNo."'";
		$this->db->exeSql($sql);
	}

	//▶ 보너스 배당 수정
	function modifyBonusRate($sn, $bonusRate, $bettingNo)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($bettingNo))
		{
			exit;
		}

		$sql = "update tb_total_cart
						set bouns_rate = '".$bonusRate."'
						where member_sn='".$sn."' and betting_no='".$bettingNo."'";

		$this->db->exeSql($sql);
	}

	//▶ 베팅
	function addBet($memberSn, $childSn, $subChildSn, $protoId, $selectedIdx, $rate1, $rate2, $rate3, $selectedRate, $gameType, $buy, $betting)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($subChildSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($protoId))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($selectedIdx))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($gameType))
		{
			exit;
		}

		//가라배팅 내역
		$result=0;

		if($memberSn==0)
		{
			$sq="select win from tb_subchild where sn=".$subChildSn;

			$rs=$this->db->exeSql($sq);
			$win=$rs[0]['win'];

			if($win==4){$result='4';}
			else if($win==$selectedIdx){$result='1';}
			else {$result='2';}
		}

		$sql = "insert into tb_total_betting(sub_child_sn,member_sn,betting_no,select_no,home_rate,draw_rate,away_rate, select_rate,game_type,event,result,kubun,bet_money)";
		$sql.= "values('". $subChildSn ."','". $memberSn."','". $protoId ."','". $selectedIdx ."',";
		$sql.= "'". $rate1 ."','". $rate2 ."','". $rate3 ."','". $selectedRate ."','". $gameType ."','0','".$result."','". $buy ."','". $betting ."')";

		$this->db->exeSql($sql);

		$sql = "update tb_child set bet_money=bet_money +".$betting."
						where sn=".$childSn;
		$this->db->exeSql($sql);
	}

	// 농구 실시간 기준점 자동 변경
	function checkMultiBet($dd, $be)
	{
		$sql = "SELECT * FROM tb_child WHERE kubun = 0 AND special = 2 AND gameDate = date(now())";
		$rs = $this->db->exeSql($sql);

		$nCnt = sizeof($rs);

		for($i=0; $i<$nCnt; $i++)
		{
			if($rs[$i]['sn'] == $dd)
			{
				$sql = "SELECT * FROM tb_subchild WHERE child_sn = {$rs[$i]['sn']}";
				$rs2 = $this->db->exeSql($sql);

				if($rs2[0]['betting_type'] == 2)
				{
					$sql1 = "SELECT sum(bet_money) as bet_money FROM tb_total_betting WHERE sub_child_sn = {$rs2[0]['sn']} AND select_no = 1";
					$left = $this->db->exeSql($sql1);

					$sql2 = "SELECT sum(bet_money) as bet_money FROM tb_total_betting WHERE sub_child_sn = {$rs2[0]['sn']} AND select_no = 2";
					$right = $this->db->exeSql($sql2);

					$left_money = $left[0]['bet_money'];
					$right_money = $right[0]['bet_money'];

					if($left_money == "")
					{
						$left_money = 0;
					}

					if($right_money == "")
					{
						$right_money = 0;
					}

					if($left_money > $right_money)
					{
	    				if(($left_money - $right_money) >= 500000)
	    				{
	    					$upSql = "UPDATE tb_subchild SET draw_rate = draw_rate - 0.5 WHERE sn = {$rs2[0]['sn']}";
	    					$this->db->exeSql($upSql);
	    				}
	    			}

	    			else if($left_money < $right_money)
					{
	    				if(($right_money - $left_money) >= 500000)
	    				{
	    					$upSql = "UPDATE tb_subchild SET draw_rate = draw_rate + 0.5 WHERE sn = {$rs2[0]['sn']}";
	    					$this->db->exeSql($upSql);
	    				}
	    			}
	        	}

	        	else if($rs2[0]['betting_type'] == 4)
				{
					$sql1 = "SELECT sum(bet_money) as bet_money FROM tb_total_betting WHERE sub_child_sn = {$rs2[0]['sn']} AND select_no = 1";
					$left = $this->db->exeSql($sql1);

					$sql2 = "SELECT sum(bet_money) as bet_money FROM tb_total_betting WHERE sub_child_sn = {$rs2[0]['sn']} AND select_no = 2";
					$right = $this->db->exeSql($sql2);

					$left_money = $left[0]['bet_money'];
					$right_money = $right[0]['bet_money'];

					if($left_money == "")
					{
						$left_money = 0;
					}

					if($right_money == "")
					{
						$right_money = 0;
					}

					if($left_money > $right_money)
					{
	    				if(($left_money - $right_money) >= 500000)
	    				{
	    					$upSql = "UPDATE tb_subchild SET draw_rate = draw_rate + 0.5 WHERE sn = {$rs2[0]['sn']}";
	    					$this->db->exeSql($upSql);
	    				}
	    			}

	    			else if($left_money < $right_money)
					{
	    				if(($right_money - $left_money) >= 500000)
	    				{
	    					$upSql = "UPDATE tb_subchild SET draw_rate = draw_rate - 0.5 WHERE sn = {$rs2[0]['sn']}";
	    					$this->db->exeSql($upSql);
	    				}
	    			}
	        	}
	        }
		}
	}

	//▶ 카트
	function addCart($sn, $parentIdx, $bettingNo, $buy, $betCount, $dbCash, $betMoney, $resultRate, $partnerSn='', $rollingSn='', $accountEnable=1/*정산에 포함여부*/,$bet_date='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($bettingNo))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($betCount))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($dbCash))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($betMoney))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($partnerSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($rollingSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($accountEnable))
		{
			exit;
		}

		$result=0;
		if($sn==0)
		{
			$sq="select result from tb_total_betting where betting_no='".$bettingNo."'";

			$rs=$this->db->exeSql($sq);

			for($i=0; $i < sizeof($rs); $i++)
			{
				if($rs[$i]['result']=='2'){$result=2;break;}
				else {$result=1;}
			}
		}

		$commonModel = Lemon_Instance::getObject("CommonModel",true);

		if($bet_date=='')	$bet_date='now()';
		else 							$bet_date="'".$bet_date."'";

		if($partnerSn=="")	$partnerSn="0";
		if($rollingSn=="")	$rollingSn="0";

		$bettingIp = $commonModel->getIp();

		//웹게임 베팅머니 추가 2016.02.01
		$web_betMoney =0;
		$sql="select    ifnull(sum( case when b.special  in (5,6,7) then 1 else 0 end ),0)  web_cnt, count(1) cnt   from tb_total_betting a , tb_child b, tb_subchild c where a.sub_child_sn=c.sn and b.sn=c.child_sn  and  a.betting_no='".$bettingNo."'";

		$rs=$this->db->exeSql($sql);
		if ($rs[0]['cnt'] !=0){
			$web_betMoney =  ( $rs[0]['web_cnt']    /  $rs[0]['cnt'] ) * $betMoney ;
		}


		$sql = "insert into tb_total_cart(member_sn, betting_no, parent_sn, regdate, operdate, kubun, result, betting_cnt, before_money, betting_money, result_rate,result_money,";
		$sql.= "partner_sn,rolling_sn,bouns_rate,user_del,bet_date,is_account,betting_ip,logo,web_betting_money) values('".$sn."','". $bettingNo ."','".$parentIdx."',now(),now(),";
		$sql.=  "'". $buy ."','".$result."','". $betCount ."',".$dbCash.",'".$betMoney."','".$resultRate."',0,".$partnerSn.",".$rollingSn.",'0','N',".$bet_date.",".$accountEnable.",'".$bettingIp."','".$this->logo."',".$web_betMoney.")";
		//echo $sql;exit;
		$this->db->exeSql($sql);
	}

	//동일경기 배팅횟수제한
	function isLimitBetCnt($childSn, $selected, $memberSn)
	{
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($selected))
		{
			exit;
		}

		$sql = "select count(*) as totcnt
							from tb_child a, tb_subchild b, tb_total_betting c, tb_total_cart d
					 	where a.sn=b.child_sn and b.sn=c.sub_child_sn and c.betting_no=d.betting_no
					 		and a.sn='".$childSn."'
					 		and d.member_sn='".$memberSn."' and c.select_no=".$selected;
		$rs = $this->db->exeSql($sql);

		if($rs[0]['totcnt'] > 0)
			return 0;
		else return 1;

	}

	//사다리 같은 회차 금지
	function isLimitSameNthBetting($childSn, $memberSn, $specialType='5')
	{
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$sql = "select * from tb_child where sn = $childSn ";
		$child = $this->db->exeSql($sql);

		$sql = "select count(*) as totcnt
							from tb_child a, tb_subchild b, tb_total_betting c, tb_total_cart d
					 	where a.sn=b.child_sn and b.sn=c.sub_child_sn and c.betting_no=d.betting_no
					 		and d.member_sn='".$memberSn."' and a.special=$specialType and a.gameDate='".$child[0]['gameDate']."' and a.gameHour='".$child[0]['gameHour']."' and a.gameTime='".$child[0]['gameTime']."' ";
		$rs = $this->db->exeSql($sql);

		if($rs[0]['totcnt'] > 0)
			return 0;
		else return 1;
	}

	function isRateChanged($childSn, $homeRate, $drawRate, $awayRate)
	{
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}

		$sql = "select cast(b.home_rate as decimal(10,2)) as home_rate, cast(b.draw_rate as decimal(10,2)) as draw_rate, cast(b.away_rate as decimal(10,2)) as away_rate
						from tb_child a, tb_subchild b
						where a.sn=b.child_sn and a.sn=".$childSn;

		$rs = $this->db->exeSql($sql);

		if($rs[0]['home_rate']==$homeRate && $rs[0]['draw_rate']==$drawRate && $rs[0]['away_rate']==$awayRate)
		{
			return 0;
		}

		else
		{
			return 1;
		}
	}

	function isBettingCancelEnable($betting_no, $enalbeTime)
	{
		if( !$this->is_integer_mysql_parameter($betting_no))
		{
			exit;
		}

		$sql = "select sub_child_sn from tb_total_betting where betting_no='".$betting_no."'";
		$rows = $this->db->exeSql($sql);

		for($i=0; $i < count($rows); ++$i)
		{
			$sub_child_sn = $rows[$i]['sub_child_sn'];

			$sql = "select gameDate, gameHour, gameTime from tb_child a, tb_subchild b
								where a.sn=b.child_sn and b.sn=$sub_child_sn";
			$game_rows = $this->db->exeSql($sql);

			$gameTime 	= Trim($game_rows[0]["gameDate"]) ." ".Trim($game_rows[0]["gameHour"]).":". Trim($game_rows[0]["gameTime"]);
			$remainTime = (strtotime($gameTime)-strtotime(date("Y-m-d H:i")))/60;
			if($remainTime < $enalbeTime)
			{
				return 0;
			}
		}

		return 1;

		/* slow log query
		$sql = "select gameDate, gameHour, gameTime from tb_child a, tb_subchild b
						where a.sn=b.child_sn and b.sn in (select sub_child_sn from tb_total_betting where betting_no='".$bettingNo."')";
		$rows = $this->db->exeSql($sql);

		if($this->auth->getId()=="nadia")
		{
			echo $sql;
		}

		for($i=0; $i<sizeof($rows); ++$i)
		{
			$gameTime 	= Trim($rows[$i]["gameDate"]) ." ".Trim($rows[$i]["gameHour"]).":". Trim($rows[$i]["gameTime"]);
			$remainTime = (strtotime($gameTime)-strtotime(date("Y-m-d H:i")))/60;

			if($remainTime < $enalbeTime)
			{
				return 0;
			}
		}
		return 1;
		*/
	}

	function topWinnersList()
	{
		$sql = "select b.uid, a.result_money, a.result_rate  from
					tb_total_cart a, tb_member b
					where 	a.member_sn=b.sn
								and a.result_money>0
								and b.mem_status='N'
					order by a.operdate desc
					limit 0, 10";


		$rs = $this->db->exeSql($sql);
		for($i = 0; $i < sizeof($rs); ++$i)
		{
			$id = $rs[$i]["uid"];
			$len = strlen($id);
			$rs[$i]["uid"] = substr($id, 0, 3);
			$rs[$i]["uid"] = str_pad($rs[$i]["uid"], $len, '*', STR_PAD_RIGHT );
		}
		return $rs;
	}

	function getLiveGameMemberBettingListTotal($member_sn, $begin_date="", $end_date="")
	{
		if( !$this->is_integer_mysql_parameter($member_sn))
		{
			exit;
		}

		$sql = "select count(*) as cnt from tb_live_betting
						where member_sn=".$member_sn;

		if( $begin_date!="" && $end_date!="")
		{
			$sql.= " and (reg_time between '".$begin_date."' and '".$end_date."')";
		}
		$rows = $this->db->exeSql($sql);

		return $rows[0]['cnt'];
	}

	function getLiveGameMemberBettingList($member_sn, $page=0, $page_size=0, $begin_date='', $end_date='')
	{
		if( !$this->is_integer_mysql_parameter($member_sn))
		{
			exit;
		}

		$sql = "select * from tb_member a,  tb_live_game b,  tb_live_betting c, tb_live_game_detail d, tb_live_game_template e
						where a.sn=c.member_sn and b.sn=c.live_sn and d.sn=c.live_detail_sn and d.template=e.template and a.sn =".$member_sn;

		if( $begin_date!="" && $end_date!="")
		{
			$sql.= " and (c.reg_time between '".$begin_date."' and '".$end_date."')";
		}

		$sql.= " order by c.betting_no desc";
		if($page_size>0)
			$sql.= " limit ".$page.",".$page_size;

		$rows = $this->db->exeSql($sql);

		return $rows;
	}

	//동일경기 배팅횟수제한 - 사다리
	function isEnableBetting($game_sn, $selected, $member_sn)
	{
		if( !$this->is_integer_mysql_parameter($game_sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($selected))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($member_sn))
		{
			exit;
		}

		$sql = "select count(*) as cnt from tb_child a, tb_subchild b, tb_total_betting c, tb_total_cart d
					 where a.sn=b.child_sn and b.sn=c.sub_child_sn and c.betting_no=d.betting_no and a.sn='".$game_sn."' and d.member_sn='".$member_sn."' and c.select_no='".$selected."'";
		$rs = $this->db->exeSql($sql);

		if($rs[0]['cnt'] > 0)
			return 0;
		else return 1;

	}
}
?>
