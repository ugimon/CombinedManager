<?php

class GameListModel extends Lemon_Model 
{
	/**
	 * GameListModel
	 *--------------------------------------------------------------------
	 *
	 * 게임 목록을 얻어 올때 사용되는 함수,
	 * _function 은 기본함수이며, 이 함수를 가지고 파생을 시켜,
	 * 중복을 피한다.
	 *
	 *--------------------------------------------------------------------
	 * Copyright (C) 
	 */
	 
	 
	//▶ 게임목록
 	public function _gameList($where="", $orderby="", $page=0, $page_size=0)
	{
		$sql = "select a.sn as child_sn,a.sport_name,a.league_sn,a.home_team,a.home_score,a.away_team,a.away_score,
								a.win_team,a.handi_winner,a.gameDate,a.gameHour,a.gameTime,a.notice, a.kubun,a.type, a.special, a.is_specified_special,
								b.sn as league_sn, b.lg_img as league_image,b.name as league_name, b.nation_sn, b.view_style, b.link_url,
								c.sn as sub_child_sn,c.betting_type,c.home_rate,c.draw_rate,c.away_rate, c.result as sub_child_result, c.win
						from tb_child a, tb_league b, tb_subChild c
						where a.league_sn=b.sn and a.sn=c.child_sn 
						and b.sn!=500 and b.sn!=505 and b.sn!=504 and b.sn!=503 and b.sn!=502 and b.sn!=501 and b.sn!=584 and b.sn!=583 and b.sn!=570 and b.sn!=568 and b.sn!=569 ".$where;

		if($orderby!='')
			$sql.= " order by ".$orderby;
			
		if($page_size!=0)
			$sql.= " limit ".$page.",".$page_size;
			
		return $this->db->exeSql($sql);
	} 

	public function _webGameList($where="", $orderby="", $page=0, $page_size=0)
	{
		$sql = "select a.sn as child_sn,a.sport_name,a.league_sn,a.home_team,a.home_score,a.away_team,a.away_score,
								a.win_team,a.handi_winner,a.gameDate,a.gameHour,a.gameTime,a.notice, a.kubun,a.type, a.special, a.is_specified_special,
								b.sn as league_sn, b.lg_img as league_image,b.name as league_name, b.nation_sn, b.view_style, b.link_url,
								c.sn as sub_child_sn,c.betting_type,c.home_rate,c.draw_rate,c.away_rate, c.result as sub_child_result, c.win
						from tb_child a, tb_league b, tb_subChild c
						where a.league_sn=b.sn and a.sn=c.child_sn ".$where;

		if($orderby!='')
			$sql.= " order by ".$orderby;
			
		if($page_size!=0)
			$sql.= " limit ".$page.",".$page_size;
			
		return $this->db->exeSql($sql);
	} 
	
	public function _gameListTotal($where="")
	{
		$sql = "select count(*) as cnt
						from tb_child  a,tb_league b,tb_subChild c
						where a.league_sn=b.sn and a.sn=c.child_sn
						and b.sn!=500 and b.sn!=505 and b.sn!=504 and b.sn!=503 and b.sn!=502 and b.sn!=501 and b.sn!=584 and b.sn!=583 and b.sn!=570 and b.sn!=568 and b.sn!=569 ".$where;
				
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	//▶ 게임목록 총합_gameList
	public function Total($where="")
	{
		$sql = "select count(*) as cnt
							from tb_child  a,tb_league b,tb_subChild c,tb_nation d
						where a.league_sn=b.sn and a.sn=c.child_sn and b.nation_sn=d.sn ".$where;
		
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	//▶ 기준점 변경에 따른 재계산
	//return value 1=홈팀승,2=원정승,3=무승부,4=취소
	function calcResult($gameType, $selectDrawRate, $homeScore, $awayScore)
	{
		if($gameType==2)
	 	{
	 		if(($homeScore+$selectDrawRate) > $awayScore)				$winCode = 1;
	 		else if(($homeScore+$selectDrawRate) < $awayScore)		$winCode = 2;
	 		else if(($homeScore+$selectDrawRate) == $awayScore)	$winCode = 4;
		}
		else if($gameType==4)
	 	{
	 		if(($homeScore+$awayScore)==$selectDrawRate) $winCode = 4;
	 		else
	 			$winCode = (($homeScore+$awayScore) > $selectDrawRate) ? 1:2;
		}
		return $winCode;
	}
	
	//▶ 배팅목록
	public function _bettingList($memberSn='', $page=0, $page_size=0, $state=-1, $event=0, $beginDate='', $endDate='', $orderby='', $bettingNo='')
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($bettingNo))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($event))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}
		
		$sql = "select a.betting_no,a.regdate,a.betting_cnt,a.result_rate,a.betting_money,a.result, a.bet_date,
						d.sn as child_sn
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn 
						and a.logo='".$this->logo."' and a.user_del<>'Y' and a.kubun ='Y'";
		
		//where			
		if($beginDate!="" && $endDate!="") 	
		{
			$sql.=" and (a.bet_date between '".$beginDate."' and '".$endDate."') ";
		}
		if($memberSn!='')		{$sql.=" and a.member_sn=".$memberSn;}
		
		if($event==0)				{$sql.=" and d.special!=4 ";}
		else if($event==1)	{$sql.=" and d.special==4 ";}

		//진행중, 종료
		if($state==0)				{$sql.=" and a.result=0 ";}
		else if($state==1)	{$sql.=" and a.result>0 ";}
		else if($state==10)	{$sql.=" and a.result=1 ";}
		else if($state==11)	{$sql.=" and a.result=2 ";}
		
		if($bettingNo!='')	
		{
			$sql.=" and a.betting_no='".$bettingNo."'";
		}
		
		$sql.= " group by a.betting_no ";

		//order by, limit
		$sql.=  " order by a.betting_no desc";
		if($page_size > 0)
		{
			$sql.= " limit ".$page.",".$page_size;
		}
	
		//excute
		$rs = $this->db->exeSql($sql);
		
		$itemList = array();
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]["betting_no"];

			$sql = "select a.sub_child_sn,a.select_no,a.home_rate,a.away_rate,a.draw_rate,a.select_rate,a.game_type,a.result, a.member_sn,
							b.sn as child_sn, b.home_team,b.away_team,b.home_score,b.away_score,b.special,b.gameDate,b.gameHour,b.gameTime, 
							c.name as league_name,c.lg_img as league_image, d.win
							from tb_total_betting a, tb_child b, tb_league c, tb_subchild d 
							where a.betting_no='".$bettingNo."' and a.sub_child_sn=d.sn and b.league_sn=c.sn and b.sn=d.child_sn";

			if($orderby!='') 
			{
				$sql.=" order by ".$orderby;
			}
							
			$rsi = $this->db->exeSql($sql);
			
			$itemList[$bettingNo] = $rs[$i];
			
			if($rsi[$i]['special']==2 || $rsi[$i]['special']==5)
				$itemList[$bettingNo]['cancel_enable'] = 0;
			else
				$itemList[$bettingNo]['cancel_enable'] = 1;
			
		
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				//적특으로 인한 result_rate, select_rate를 변경해 준다.
				if($rsi[$j]['result']==4)
				{
					$rate = $rsi[$j]['select_rate'];
					$rate = round($rs[$i]['result_rate']/$rate,2);
					
					$rs[$i]['result_rate'] = $rate;
					$rsi[$j]['select_rate']=1;
					
					$itemList[$bettingNo]['result_rate'] = $rate;
				} 
				else if($rsi[$j]['game_type']!=1 && $rsi[$j]['result']!=0)
				{
					$rsi[$j]['win'] = $this->calcResult($rsi[$j]['game_type'], $rsi[$j]['draw_rate'], $rsi[$j]['home_score'], $rsi[$j]['away_score']);
				}
				
				$itemList[$bettingNo]['win_money'] = (int)($rs[$i]['betting_money']*$rs[$i]['result_rate']);
				$itemList[$bettingNo]['folder_bonus']=0;
				
				if($itemList[$bettingNo]['cancel_enable']==1 && $rsi[$j]['result']!=0)
				{
					$itemList[$bettingNo]['cancel_enable'] = 0;
				}
				
				// 가라배팅 경기결과값
				if($rsi[$j]['member_sn']=='0')
				{
					$itemList[$bettingNo]['result_money']=$itemList[$bettingNo]['win_money'];

				}
				
				//폴더보너스 체크시 적용
				if($event==0)
				{	
					$cModel = Lemon_Instance::getObject("ConfigModel",true);
					$mModel = Lemon_Instance::getObject("MemberModel",true);
						
					$level	= $mModel->getMemberField($this->auth->getSn(),'mem_lev');
					$field  = $cModel->getLevelConfigRow($level,"lev_folder_bonus");					
				
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
					$itemList[$bettingNo]['bonus_rate'] = 0;	
					$itemList[$bettingNo]['folder_bonus'] = $amount;
				}
				
				$itemList[$bettingNo]['item'][] = $rsi[$j];
			} // end of 2nd for
		}
		
		return $itemList;
	}
	
	//▶ 관리자 배팅목록
	public function _bettingList_admin($memberSn='', $page=0, $page_size=0, $state=-1, $event=0, $beginDate='', $endDate='', $orderby='', $bettingNo='')
	{
		$sql = "select a.betting_no,a.regdate,a.betting_cnt,a.result_rate,a.betting_money,a.result, a.bet_date,
						d.sn as child_sn
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn 
						and a.kubun ='Y'";
		
		//where			
		if($beginDate!="" && $endDate!="") 	{$sql.=" and (a.bet_date between '".$beginDate."' and '".$endDate."') ";}
		if($memberSn!='')		{$sql.=" and a.member_sn=".$memberSn;}
		
		if($event==0)				{$sql.=" and d.special!=4 ";}
		else if($event==1)	{$sql.=" and d.special==4 ";}

		//진행중, 종료
		if($state==0)				{$sql.=" and a.result=0 ";}
		else if($state==1)	{$sql.=" and a.result>0 ";}
		else if($state==10)	{$sql.=" and a.result=1 ";}
		else if($state==11)	{$sql.=" and a.result=2 ";}
		
		if($bettingNo!='')	
		{
			$sql.=" and a.betting_no='".$bettingNo."'";
		}
		
		$sql.= " group by a.betting_no ";

		//order by, limit
		$sql.=  " order by a.betting_no desc";
		if($page_size > 0)	
		{
			$sql.= " limit ".$page.",".$page_size;
		}
	
		//excute
		$rs = $this->db->exeSql($sql);
		
		$itemList = array();
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo = $rs[$i]["betting_no"];

			$sql = "select a.sub_child_sn,a.select_no,a.home_rate,a.away_rate,a.draw_rate,a.select_rate,a.game_type,a.result, a.member_sn,
							b.sn as child_sn, b.home_team,b.away_team,b.home_score,b.away_score,b.special,b.gameDate,b.gameHour,b.gameTime, 
							c.name as league_name,c.lg_img as league_image, d.win
							from tb_total_betting a, tb_child b, tb_league c, tb_subchild d 
							where a.betting_no='".$bettingNo."' and a.sub_child_sn=d.sn and b.league_sn=c.sn and b.sn=d.child_sn";

			if($orderby!='') {$sql.=" order by ".$orderby;}
							
			$rsi = $this->db->exeSql($sql);
			
			$itemList[$bettingNo] = $rs[$i];
			$itemList[$bettingNo]['cancel_enable'] = 1;
			
		
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				//적특으로 인한 result_rate, select_rate를 변경해 준다.
				if($rsi[$j]['result']==4)
				{
					$rate = $rsi[$j]['select_rate'];
					$rate = round($rs[$i]['result_rate']/$rate,2);
					
					$rs[$i]['result_rate'] = $rate;
					$rsi[$j]['select_rate']=1;
					
					$itemList[$bettingNo]['result_rate'] = $rate;
				} 
				else if($rsi[$j]['game_type']!=1 && $rsi[$j]['result']!=0)
				{
					$rsi[$j]['win'] = $this->calcResult($rsi[$j]['game_type'], $rsi[$j]['draw_rate'], $rsi[$j]['home_score'], $rsi[$j]['away_score']);
				}
				
				$itemList[$bettingNo]['win_money'] = (int)($rs[$i]['betting_money']*$rs[$i]['result_rate']);
				$itemList[$bettingNo]['folder_bonus']=0;
				
				if($itemList[$bettingNo]['cancel_enable']==1 && $rsi[$j]['result']!=0)
				{
					$itemList[$bettingNo]['cancel_enable'] = 0;
				}
				
				// 가라배팅 경기결과값
				if($rsi[$j]['member_sn']=='0')
				{
					$itemList[$bettingNo]['result_money']=$itemList[$bettingNo]['win_money'];

				}
				
				//폴더보너스 체크시 적용
				if($event==0)
				{	
					$cModel = Lemon_Instance::getObject("ConfigModel",true);
					$mModel = Lemon_Instance::getObject("MemberModel",true);
						
					$level	= $mModel->getMemberField($this->auth->getSn(),'mem_lev');
					$field  = $cModel->getLevelConfigRow($level,"lev_folder_bonus");					
				
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
					$itemList[$bettingNo]['bonus_rate'] = 0;	
					$itemList[$bettingNo]['folder_bonus'] = $amount;
				}
				
				$itemList[$bettingNo]['item'][] = $rsi[$j];
			} // end of 2nd for
		}
		
		return $itemList;
	}
	
	//▶ 배팅목록 총합
	public function _bettingListTotal($memberSn, $state=-1, $event=0, $beginDate='', $endDate='')
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($event))
		{
			exit;
		}
		
		$sql = "select count(distinct(a.betting_no)) as cnt
						from tb_total_cart a,tb_total_betting b,tb_subchild c,tb_child d
						where a.betting_no=b.betting_no and b.sub_child_sn=c.sn and c.child_sn=d.sn 
						and a.logo='".$this->logo."' and a.user_del<>'Y' and a.member_sn=".$memberSn." and a.kubun ='Y'";
		
		//where			
		if($beginDate!="" && $endDate!="") 	{$sql.=" and (a.bet_date between '".$beginDate."' and '".$endDate."') ";}
		
		if($event==0)				{$sql.=" and d.special!=4 ";}
		else if($event==1)	{$sql.=" and d.special==4 ";}
		//진행중, 종료
		if($state==0)						{$sql.=" and a.result=0 ";}
		else if($state==1)			{$sql.=" and a.result>0 ";}
		else if($state==10)			{$sql.=" and a.result=1 ";}
		else if($state==11)			{$sql.=" and a.result=2 ";}
		
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	//▶ 게임결과 목록
	public function _resultList($where="", $page, $page_size)
	{
		$rs = $this->_gameList($where, 'a.gameDate desc, a.gameHour desc, a.gameTime desc, a.league_sn',$page, $page_size);
		
		if(sizeof($rs)<=0) return array();
		
		$itemList = array();
		for($i=0; $i<sizeof($rs); ++$i)
		{
			if($rs[$i]['handi_winner']=="Home") 		{$win_handi = 1;}
			else if($rs[$i]['handi_winner']=="Away")	{$win_handi = 2;}
			
			if($rs[$i]['betting_type']==2)
			{
				if($win_hand == 1) 		{$rs[$i]['result_title'] = "승";}
				else if($win_handi ==2) {$rs[$i]['result_title'] = "패";}
			}
			if($rs[$i]['betting_type']==4)
			{
				if ($rs[$i]['win'] == 2) {$rs[$i]['result_title']="패";}
			}
			
			//리그별 정렬
			$key = $rs[$i]['league_sn'];
			$key.= $gameTime;
			$itemList[$key]['league_image'] = $rs[$i]['league_image'];
			$itemList[$key]['league_name'] 	= $rs[$i]['league_name'];
			$itemList[$key]['item'][] = $rs[$i];
		}
		
		return $itemList;
	}
	
	public function getGameList($where="")
	{
		$live_list = array();
		
		$sql = "select * from tb_live_game where state<>'FIN' ";
		$live_rows = $this->db->exeSql($sql);
		if(count($live_rows)>0) {
			foreach($live_rows as $live_game) {
				$live_list[] = $live_game['home_team'];
			}
		}
		
		$orderby = "a.gameDate asc, a.gameHour asc, a.gameTime asc, league_name, a.home_team, a.type";
		$where .= " and a.kubun=0 ";
		
		$rs = $this->_gameList($where, $orderby);
		
		if(sizeof($rs)<=0) return array();
		
		//게임 배팅가능 여부 플래그 설정
		$configModel = Lemon_Instance::getObject("ConfigModel",true);
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$enable_betting_count = 0;
			$disable_betting_count = 0;
			$notice_betting_count = 0;
			
			//시간마감
			$gameTime 	= Trim($rs[$i]["gameDate"])." ".Trim($rs[$i]["gameHour"]) .":".Trim($rs[$i]["gameTime"]);
			$remainTime = (strtotime($gameTime)-strtotime(date("Y-m-d H:i:s")));
			$configTime = $configModel->getAdminConfigField('bettingendtime');
			
			//가장 상단에 올릴 게임 구분
			if($rs[$i]['view_style'] != '5')
			{
				if($remainTime < $configTime)
				{
					$rs[$i]['bet_enable']=0;
					++$disable_betting_count;
				}
				else
				{
					$rs[$i]['bet_enable']=1;
					++$enable_betting_count;
				}
			}
			else
			{
				$rs[$i]['bet_enable']=5;
				++$notice_betting_count;
			}
				

			$strDay = date('w',strtotime($gameTime));
			switch($strDay)
			{
				case 0: $week = "[일]";break;
				case 1: $week = "[월]";break;
				case 2: $week = "[화]";break;
				case 3: $week = "[수]";break;
				case 4: $week = "[목]";break;
				case 5: $week = "[금]";break;
				case 6: $week = "[토]";break;	
			}
			$rs[$i]['week'] = $week;

			//관리자 결과 입력
			if(1==$rs[$i]['bet_enable'])
			{
				$result = $rs[$i]['sub_child_result'];
				if($result==1 || $result==4)
				{
					$rs[$i]['bet_enable']=0;
					++$disable_betting_count;
					--$enable_betting_count;
				}
				else
				{
					$rs[$i]['bet_enable']=1;
				}
			}

			
			//라이브 게임 여부 판단
			if( in_array($rs[$i]['home_team'], $live_list)) {
				$rs[$i]['is_live'] = 1;
				$sql = "select event_id from tb_live_game where home_team='".$rs[$i]['home_team']."'";
				$rows = $this->db->exeSql($sql);
				$rs[$i]['event_id'] = $rows[0]['event_id'];
			} else {
				$rs[$i]['is_live'] = 0;
			}
			
			//리그별 정렬
			$key = $rs[$i]['league_sn'];
			$key.= $gameTime;
			$leagueGameList[$key]['league_image'] = $rs[$i]['league_image'];
			$leagueGameList[$key]['league_name'] 	= $rs[$i]['league_name'];
			$leagueGameList[$key]['view_style'] 	= $rs[$i]['view_style'];
			$leagueGameList[$key]['link_url'] 	= $rs[$i]['link_url'];
			$leagueGameList[$key]['item'][] = $rs[$i];
			$leagueGameList[$key]['enable_betting_count'] = $enable_betting_count;
			$leagueGameList[$key]['disable_betting_count'] = $disable_betting_count;
			$leagueGameList[$key]['notice_betting_count'] = $notice_betting_count;
			// 리그정보에 시간 추가
			$leagueGameList[$key]['gameDate'] = $rs[$i]['gameDate'];
			$leagueGameList[$key]['gameHour'] = $rs[$i]['gameHour'];
			$leagueGameList[$key]['gameTime'] = $rs[$i]['gameTime'];
			$leagueGameList[$key]['week'] = $week;
		}

		return $leagueGameList;
	}
	
	public function getKeywordGameList($specialType='',$gameType='')
	{
		$sql = "select distinct(b.name) as league_name, b.sn as league_sn
						from tb_child  a,tb_league b,tb_subChild c
						where a.kubun=0 and a.league_sn=b.sn and a.sn=c.child_sn";
				
		switch($specialType)
		{
			case '0': $sql.= " and a.special=0"; break;
			case '1': $sql.= " and a.special=1"; break;
			case '2': $sql.= " and a.special=2"; break;
			case '3': $sql.= " and a.special=3"; break;
			case '4': $sql.= " and a.special=4"; break;
			case '5': $sql.= " and a.special=5"; break;
		}
		switch($gameType)
		{
			case '1': $sql.= " and a.type=1"; break;
			case '2': $sql.= " and (a.type=2 or a.type=4)"; break;
		}
		$sql.=" order by b.name asc";

		return $this->db->exeSql($sql);
	}
	
	//▶ 게임복사에 사용되는 목록
	public function getCopyGameList($category='', $league='', $homeTeam='', $awayTeam='', $beginDate="", $endDate="")
	{
		$sql = "select 	a.*, 
										b.*,
										c.name as league_name
						from tb_child a, "
									.$this->db_qz."subchild b, "
									.$this->db_qz."league c
						where a.sn=b.child_sn 
									and a.league_sn=c.sn 
									and a.kubun is null 
									and a.type=1 
									and a.special=0";
						
		if($category!='') $sql.= " and a.sport_name='".$category."'";
		if($league!='') 	$sql.= " and c.name like('%".$league."%')";
		if($homeTeam!='') $sql.= " and a.home_team like('%".$homeTeam."%')";
		if($awayTeam!='') $sql.= " and a.away_team like('%".$awayTeam."%')";
		if($beginDate!='') 		$sql.= " and a.gameDate between '".$beginDate."' and '".$endDate."'";

		return $this->db->exeSql($sql);
	}
	
	//▶ 배팅목록
	public function getMemberBettingList($memberSn="", $where="", $page=0, $page_size=0, $orderby="")
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		
		if($page_size > 0)
			$limit = " limit ".$page.", ".$page_size;
			
		if($memberSn!="")
			$where.= " and a.member_sn=".$memberSn;
			
		if($orderby=="")
			$orderby = " order by a.betting_no desc ";
			
		$sql = "select	a.betting_no, a.regdate, a.operdate, a.betting_cnt, a.result_rate, a.bet_date, a.logo,
										a.before_money, a.betting_money, a.result, a.result_money, a.member_sn, a.betting_ip
						from tb_total_cart a,tb_total_betting b, tb_member c, tb_child d, tb_subchild e
						where 	a.betting_no=b.betting_no and a.member_sn=c.sn and b.sub_child_sn=e.sn and d.sn=e.child_sn
										and a.kubun ='Y' ".$where." 
						group by a.betting_no ".$orderby.$limit;
		$rs = $this->db->exeSql($sql);
		
		$list = array();
		
		//배팅번호로 그룹화
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingSn = $rs[$i]["betting_no"];

			$sql = "select a.sn as total_betting_sn, a.sub_child_sn, a.select_no, a.home_rate, a.away_rate, a.draw_rate, a.select_rate, a.game_type, a.result,
							b.sn as child_sn, b.home_team, b.away_team, b.home_score, b.away_score, b.special, b.gameDate, b.gameHour, b.gameTime,
							c.name as league_name,c.lg_img as league_image, d.win
							from tb_total_betting a, tb_child b, tb_league c, tb_subchild d 
							where a.betting_no='".$bettingSn."' and a.sub_child_sn=d.sn and b.league_sn=c.sn and b.sn=d.child_sn order by gameDate, gameHour, gameTime";

			$rsi = $this->db->exeSql($sql);
			
			$list[$bettingSn] = $rs[$i];			
			$list[$bettingSn]['win_count']=0;
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				if($rsi[$j]["result"]==1)
					$list[$bettingSn]['win_count']+=1;
					
				$list[$bettingSn]['win_money'] = (int)($rs[$i]['betting_money']*$rs[$i]['result_rate']);
				$list[$bettingSn]['folder_bonus']=0;
				
				if($memberSn=="")
					$memberSn = $rs[$i]['member_sn'];
				
				//폴더보너스 체크시 적용
				$levelConfigModel = Lemon_Instance::getObject("LevelConfigModel",true);
				
				$bonusRate = $levelConfigModel->getMemberFolderBounsRate($memberSn, $rs[$i]['betting_cnt']);
				$list[$bettingSn]['bonus_rate'] = $bonusRate;
				$list[$bettingSn]['folder_bonus'] = (int)($list[$bettingSn]['win_money']*$bonusRate/100);
				$list[$bettingSn]["item"][] = $rsi[$j];
			} // end of 2nd for
		}
		
		
		return $list;
	}
	
	public function getMemberBettingListTotal($memberSn, $where="")
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		
		if($memberSn!="")
			$where.= " and a.member_sn=".$memberSn;
			
		$sql = "select count(distinct(a.betting_no)) as cnt
							from tb_total_cart a,tb_total_betting b, tb_member c,
							tb_child d, tb_subchild e
							where a.betting_no=b.betting_no and a.member_sn=c.sn and b.sub_child_sn=e.sn and d.sn=e.child_sn
									and a.kubun ='Y' ".$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	//관리자 배팅현황 - 유저(추가: 유저정보)
	public function getAdminMemberBettingList($memberSn, $where="", $page=0, $page_size=0, $orderby="")
	{
		$memberModel 	= Lemon_Instance::getObject("MemberModel",true);
		
		$rs = $this->getMemberBettingList($memberSn, $where, $page, $page_size, $orderby);
		
		foreach($rs as $bettingSn => $item)
		{
			$rsi = $memberModel->getMemberRow($memberSn);
			$rs[$bettingSn]['member'] = $rsi;
		}
		
		return $rs;
	}
	
	//관리자 배팅현황 - 유저
	public function getAdminMemberBettingListTotal($memberSn, $where="")
	{
		return $this->getMemberBettingListTotal($memberSn, $where);
	}
	
	//파트너 배팅현황 - 유저
	public function getPartnerBettingList($partnerSn, $where="", $page=0, $page_size=0)
	{
		if( !$this->is_integer_mysql_parameter($partnerSn))
		{
			exit;
		}
		
		$memberModel 	= Lemon_Instance::getObject("MemberModel",true);
		
		$where.= " and a.partner_sn=".$partnerSn;
		
		$rs = $this->getMemberBettingList("", $where, $page, $page_size);
		
		foreach($rs as $bettingSn => $item)
		{
			$rsi = $memberModel->getMemberRow($rs[$bettingSn]["member_sn"]);
			$rs[$bettingSn]['member'] = $rsi;
		}
		return $rs;
	}
	
	//파트너 배팅현황 - 유저
	public function getPartnerBettingListTotal($partnerSn, $where="") 
	{
		if( !$this->is_integer_mysql_parameter($partnerSn))
		{
			exit;
		}
		
		$where.= " and a.partner_sn=".$partnerSn;
		return $this->getMemberBettingListTotal("", $where);
	}
	
	//관리자 배팅현황 
	public function getAdminBettingList($childSn="", $where="", $page=0, $page_size=0)
	{
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}
		
		$memberModel 	= Lemon_Instance::getObject("MemberModel",true);
		$partnerModel = Lemon_Instance::getObject("PartnerModel",true);
		
		if($childSn!="")
			$where.=" and b.sub_child_sn = (select sn from tb_subchild where child_sn=".$childSn.")";

		$orderby = " order by a.regdate desc ";
		$rs = $this->getMemberBettingList("", $where, $page, $page_size, $orderby);
		
		foreach($rs as $bettingSn => $item)
		{
			$rsi = $memberModel->getMemberRow($rs[$bettingSn]["member_sn"]);
			$rs[$bettingSn]['member'] = $rsi;
			
			$recommend_sn =  $rsi['recommend_sn'];
			if($recommend_sn!="")
				$partnerId = $partnerModel->getPartnerField($recommend_sn,"rec_id");
				
			$rs[$bettingSn]['partner_id'] = $partnerId;
		}

		return $rs;
	}
	
	
	//관리자 배팅취소현황 
	public function getAdminBettingCancelList($childSn="", $where="", $page=0, $page_size=0)
	{
		$memberModel 	= Lemon_Instance::getObject("MemberModel",true);
		$partnerModel = Lemon_Instance::getObject("PartnerModel",true);
		
		if($childSn!="")
			$where.=" and b.sub_child_sn = (select sn from tb_subchild where child_sn=".$childSn.")";

		$orderby = " order by a.regdate desc ";
		//$rs = $this->getMemberBettingList("", $where, $page, $page_size, $orderby);
		
		if($page_size > 0)
			$limit = " limit ".$page.", ".$page_size;
			
		$sql = "select	a.betting_no, a.regdate, a.operdate, a.betting_cnt, a.result_rate, a.bet_date, a.logo,
										a.before_money, a.betting_money, a.result, a.result_money, a.member_sn, a.betting_ip
						from tb_total_cart_cancel a,tb_total_betting_cancel b, tb_member c
						where 	a.betting_no=b.betting_no and a.member_sn=c.sn
										and a.kubun ='Y' ".$where." 
						group by a.betting_no ".$orderby.$limit;
		$rs = $this->db->exeSql($sql);

		$list = array();
		
		//배팅번호로 그룹화
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingSn = $rs[$i]["betting_no"];

			$sql = "select a.sn as total_betting_sn, a.sub_child_sn, a.select_no, a.home_rate, a.away_rate, a.draw_rate, a.select_rate, a.game_type, a.result,
							b.sn as child_sn, b.home_team, b.away_team, b.home_score, b.away_score, b.special, b.gameDate, b.gameHour, b.gameTime,
							c.name as league_name,c.lg_img as league_image, d.win
							from tb_total_betting_cancel a, tb_child b, tb_league c, tb_subchild d 
							where a.betting_no='".$bettingSn."' and a.sub_child_sn=d.sn and b.league_sn=c.sn and b.sn=d.child_sn order by gameDate, gameHour, gameTime";

			$rsi = $this->db->exeSql($sql);
			
			$list[$bettingSn] = $rs[$i];			
			$list[$bettingSn]['win_count']=0;
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				if($rsi[$j]["result"]==1)
					$list[$bettingSn]['win_count']+=1;
					
				$list[$bettingSn]['win_money'] = (int)($rs[$i]['betting_money']*$rs[$i]['result_rate']);
				$list[$bettingSn]['folder_bonus']=0;
				
				if($memberSn=="")
					$memberSn = $rs[$i]['member_sn'];
				
				//폴더보너스 체크시 적용
				$levelConfigModel = Lemon_Instance::getObject("LevelConfigModel",true);
				
				$bonusRate = $levelConfigModel->getMemberFolderBounsRate($memberSn, $rs[$i]['betting_cnt']);
				$list[$bettingSn]['bonus_rate'] = $bonusRate;
				$list[$bettingSn]['folder_bonus'] = (int)($list[$bettingSn]['win_money']*$bonusRate/100);
				$list[$bettingSn]["item"][] = $rsi[$j];
			} // end of 2nd for
		}

		foreach($list as $bettingSn => $item)
		{
			$rsi = $memberModel->getMemberRow($list[$bettingSn]["member_sn"]);
			$list[$bettingSn]['member'] = $rsi;
			
			$recommend_sn =  $rsi['recommend_sn'];
			if($recommend_sn!="")
				$partnerId = $partnerModel->getPartnerField($recommend_sn,"rec_id");
				
			$list[$bettingSn]['partner_id'] = $partnerId;
		}

		return $list;
	}
	
	
	//관리자 배팅현황
	public function getAdminBettingListTotal($childSn="", $where="") 
	{
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}
		
		if($childSn!="")
			$where.=" and b.sub_child_sn = (select sn from tb_subchild where child_sn=".$childSn.")";
			
		$rs = $this->getMemberBettingListTotal("", $where);
		return $rs;
	}
	
	//관리자 배팅취소현황
	public function getAdminBettingCancelListTotal($childSn="", $where="") 
	{
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}
		
		$sql = "select count(distinct(a.betting_no)) as cnt
							from tb_total_cart_cancel a,tb_total_betting_cancel b, tb_member c
						where a.betting_no=b.betting_no and a.member_sn=c.sn
									and a.kubun ='Y' ".$where;
		
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	//▶ 지정된 게임의 배팅 총합
	function getGameSnBettingListTotal($where="", $active=0, $gameSn="", $selectNo="")
	{
		if( !$this->is_integer_mysql_parameter($active))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($gameSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($selectNo))
		{
			exit;
		}
		
		if($active==1)
			$where.=" and a.result!=2 ";
			
		if($selectNo!="")
			$where.= " and b.select_no=".$selectNo;
		
		$sql = "select 	count(*) as cnt
						from "	.$this->db_qz."total_cart a,"
										.$this->db_qz."total_betting b,"
										.$this->db_qz."child c,"
										.$this->db_qz."subchild d,"
										.$this->db_qz."member e
						where		a.betting_no=b.betting_no
										and b.sub_child_sn=d.sn
										and d.child_sn=c.sn
										and a.member_sn=e.sn
										and a.is_account=1 and a.kubun='Y'
										and c.sn=".$gameSn.$where;
		
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	//▶ 지정된 게임의 배팅 목록
	function getGameSnBettingList($where, $page, $page_size, $active=0, $gameSn="", $selectNo="")
	{
		if( !$this->is_integer_mysql_parameter($active))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($gameSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($selectNo))
		{
			exit;
		}
		
		if($active==1)
			$where.=" and a.result!=2 ";
			
		if($selectNo!="")
			$where.= " and b.select_no=".$selectNo;
			
			/*
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
		*/
		
		if($page_size!=0)
			$limit = "limit ".$page.",".$page_size;
		
		$sql = "select 	a.member_sn, a.betting_no, a.betting_money, a.betting_ip, a.regDate,
										a.result_money, a.result_rate, a.result as aresult, a.betting_cnt, 
										e.uid, e.nick, e.recommend_sn
						from "	.$this->db_qz."total_cart a,"
										.$this->db_qz."total_betting b,"
										.$this->db_qz."child c,"
										.$this->db_qz."subchild d,"
										.$this->db_qz."member e
						where		a.betting_no=b.betting_no
										and b.sub_child_sn=d.sn
										and d.child_sn=c.sn
										and a.member_sn=e.sn
										and a.is_account=1 and a.kubun='Y'
										and c.sn=".$gameSn.$where."
						order by a.betting_no desc ".$limit;
										/*
										a.betting_no in(
										select betting_no from
										(select betting_no from tb_total_cart where logo='".$this->logo."' and kubun='Y' ".$addWhere."order by betting_no desc ".$limit.") as t)
										and a.is_account=1".$where." order by a.regdate desc";
										*/
		
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
	
	//▶ 배팅목록
	/*
		@param
			[$finish] -1=전체,0=ing, 1=finish
	*/
	public function getBettingList($memberSn, $page, $page_size, $chkFolder, $finish=-1,  $beginDate="", $endDate="") 
	{
		return $this->_bettingList($memberSn, $page, $page_size, $finish,0,$beginDate, $endDate, 'b.gameDate desc');
	}
	
	public function getBoardBettingItem($bettingNo)
	{
		return $this->_bettingList('', 0, 0,-1,-1,'', '', '', $bettingNo);
	}
	
	public function getBoardBettingItem_admin($bettingNo)
	{
		return $this->_bettingList_admin('', 0, 0,-1,-1,'', '', '', $bettingNo);
	}
	
	public function getBettingListTotal($memberSn, $finish=-1, $event=0, $beginDate='', $endDate='')
	{
		return $this->_bettingListTotal($memberSn, $finish, $event, $beginDate, $endDate);
	}
	
	//▶ 게임결과 - 배당지급된 게임된 리스트
	public function getResultGameList($where="", $page, $page_size)
	{
		return $this->_resultList($where, $page, $page_size);
	}

	//▶ 게임결과 총합
	public function getResultGameListTotal($where="")
	{
		return $this->_gameListTotal($where);
	}
	
	public function ladderResultListTotal($where="")
	{
		$sql = "select count(*) as cnt 
						from tb_child where 1=1 $where";
				
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	public function ladderResultList($where="", $page, $page_size)
	{
		/*
		$sql = "select a.sn as child_sn,a.sport_name,a.league_sn,a.home_team,a.home_score,a.away_team,a.away_score,
								a.win_team,a.handi_winner,a.gameDate,a.gameHour,a.gameTime,a.notice, a.kubun,a.type, a.special, a.is_specified_special,
								b.sn as league_sn, b.lg_img as league_image,b.name as league_name, b.nation_sn, b.view_style, b.link_url,
								c.sn as sub_child_sn,c.betting_type,c.home_rate,c.draw_rate,c.away_rate, c.result as sub_child_result, c.win
						from tb_child  a,tb_league b,tb_subChild c
						where a.league_sn=b.sn and a.sn=c.child_sn ".$where. " order by a.gameDate desc, a.gameHour desc, a.gameTime desc ";
		*/
						
		if($page_size!=0)
			$limit = " limit ".$page.",".$page_size;
			
		$sql = "select a.sn as child_sn,a.sport_name,a.league_sn,a.home_team,a.home_score,a.away_team,a.away_score,
								a.win_team,a.handi_winner,a.gameDate,a.gameHour,a.gameTime,a.notice, a.kubun,a.type, a.special, a.is_specified_special,
								b.sn as league_sn, b.lg_img as league_image,b.name as league_name, b.nation_sn, b.view_style, b.link_url,
								c.sn as sub_child_sn,c.betting_type,c.home_rate,c.draw_rate,c.away_rate, c.result as sub_child_result, c.win
		from (select * from tb_child where 1=1 $where order by gameDate desc, gameHour desc, gameTime desc  $limit) as a,
						tb_league b,tb_subChild c
						where a.league_sn=b.sn and a.sn=c.child_sn";
			
		$rows = $this->db->exeSql($sql);
		
		if(sizeof($rows)<=0) 
			return array();
		
		$list = array();
		for($i=0; $i<sizeof($rows); ++$i)
		{
			//정렬
			$key = $rows[$i]['gameDate'].$rows[$i]['gameHour'].$rows[$i]['gameTime'];
			$key.= $gameTime;
			$list[$key]['league_image'] = $rows[$i]['league_image'];
			$list[$key]['league_name'] 	= $rows[$i]['league_name'];
			$list[$key]['item'][] = $rows[$i];
		}
		
		return $list;
	}

	public function getWebGameList($where="", $specialType='', $count=2 )
	{
		$live_list = array();
		
		$sql = "select * from tb_live_game where state<>'FIN' ";
		$live_rows = $this->db->exeSql($sql);
		if(count($live_rows)>0) {
			foreach($live_rows as $live_game) {
				$live_list[] = $live_game['home_team'];
			}
		}

		// $count 는 보여질 회차수 시간으로 계산하여 가져온다
		$endTime = date("Y-m-d H:i", strtotime("+11 min"));

		$orderby = "a.gameDate asc, a.gameHour asc, a.gameTime asc, a.league_sn, a.home_team, a.type";
		$where .= " and a.kubun=0 and concat(gameDate,' ',gameHour,':',gameTime) >= now() ";
		$where .= " and concat(gameDate,' ',gameHour,':',gameTime) <= '$endTime' ";
		
		$rs = $this->_webGameList($where, $orderby);
		//echo "<pre>", var_dump($rs[0]), "</pre>";
		if(sizeof($rs)<=0) return array();
		
		//게임 배팅가능 여부 플래그 설정
		$configModel = Lemon_Instance::getObject("ConfigModel",true);
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$enable_betting_count = 0;
			$disable_betting_count = 0;
			$notice_betting_count = 0;
			
			//시간마감
			$gameTime 	= Trim($rs[$i]["gameDate"])." ".Trim($rs[$i]["gameHour"]) .":".Trim($rs[$i]["gameTime"]);
			$remainTime = (strtotime($gameTime)-strtotime(date("Y-m-d H:i:s")));
			
			if( $specialType==5 )
				$configTime = $configModel->getAdminConfigField('ladder_bettingendtime');
			else if( $specialType==6 )
				$configTime = $configModel->getAdminConfigField('powerball_bettingendtime');
			else if( $specialType==7 )
				$configTime = $configModel->getAdminConfigField('snail_bettingendtime');
			else 
				$configTime = $configModel->getAdminConfigField('bettingendtime');
			
			//가장 상단에 올릴 게임 구분
			if($rs[$i]['view_style'] != '5')
			{
				if($remainTime < $configTime)
				{
					$rs[$i]['bet_enable']=0;
					++$disable_betting_count;
				}
				else
				{
					$rs[$i]['bet_enable']=1;
					++$enable_betting_count;
				}
			}
			else
			{
				$rs[$i]['bet_enable']=5;
				++$notice_betting_count;
			}
				

			$strDay = date('w',strtotime($gameTime));
			switch($strDay)
			{
				case 0: $week = "[일]";break;
				case 1: $week = "[월]";break;
				case 2: $week = "[화]";break;
				case 3: $week = "[수]";break;
				case 4: $week = "[목]";break;
				case 5: $week = "[금]";break;
				case 6: $week = "[토]";break;	
			}
			$rs[$i]['week'] = $week;

			//관리자 결과 입력
			if(1==$rs[$i]['bet_enable'])
			{
				$result = $rs[$i]['sub_child_result'];
				if($result==1 || $result==4)
				{
					$rs[$i]['bet_enable']=0;
					++$disable_betting_count;
					--$enable_betting_count;
				}
				else
				{
					$rs[$i]['bet_enable']=1;
				}
			}

			
			//라이브 게임 여부 판단
			if( in_array($rs[$i]['home_team'], $live_list)) {
				$rs[$i]['is_live'] = 1;
				$sql = "select event_id from tb_live_game where home_team='".$rs[$i]['home_team']."'";
				$rows = $this->db->exeSql($sql);
				$rs[$i]['event_id'] = $rows[0]['event_id'];
			} else {
				$rs[$i]['is_live'] = 0;
			}
			
			//리그별 정렬
			$key = $rs[$i]['gameDate'];
			$key.= $rs[$i]['gameHour'];
			$key.= $rs[$i]['gameTime'];
			$leagueGameList[$key]['league_image'] = $rs[$i]['league_image'];
			$leagueGameList[$key]['league_name'] 	= $rs[$i]['league_name'];
			$leagueGameList[$key]['view_style'] 	= $rs[$i]['view_style'];
			$leagueGameList[$key]['link_url'] 	= $rs[$i]['link_url'];
			$leagueGameList[$key]['item'][] = $rs[$i];
			$leagueGameList[$key]['enable_betting_count'] = $enable_betting_count;
			$leagueGameList[$key]['disable_betting_count'] = $disable_betting_count;
			$leagueGameList[$key]['notice_betting_count'] = $notice_betting_count;
			// 리그정보에 시간 추가
			$leagueGameList[$key]['gameDate'] = $rs[$i]['gameDate'];
			$leagueGameList[$key]['gameHour'] = $rs[$i]['gameHour'];
			$leagueGameList[$key]['gameTime'] = $rs[$i]['gameTime'];
			$leagueGameList[$key]['week'] = $week;
		}

		return $leagueGameList;
	}
}
?>