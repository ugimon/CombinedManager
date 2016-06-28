<?php
class PowerBallModel extends Lemon_Model 
{
	function getGame(&$play_th)
	{
		$sql = "select th from ".$this->db_qz."power_ball_state where state='REGISTER' order by sn desc limit 2";
		$rows = $this->db->exeSql($sql);
		$play_th = $rows[1]['th'];
		
		$sql = "select home_team from ".$this->db_qz."power_ball_template where state=1";
		$template_rows = $this->db->exeSql($sql);
	
		$where=" and (";
		for($i=0; $i < count($template_rows); ++$i)
		{
			$where.=" home_team='".$play_th.$template_rows[$i]['home_team']."'";
			if($i < count($template_rows)-1)
				$where.=" or ";
		}
		$where.=")";
		
		$sql = "select a.sn as game_sn, a.gameDate, a.gameHour, a.gameTime,
						b.sn as detail_sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate
						from ".$this->db_qz."child a,  ".$this->db_qz."subchild b
						where a.sn=b.child_sn and a.kubun=0 and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) >= sysdate()".$where." order by a.sn asc";
		$rows = $this->db->exeSql($sql);
		//echo $sql;
		return $rows;
	}
	
	function getRecentGame($play_th, $game_count)
	{
		for( $i=0; $i < $game_count; ++$i)
		{
			$th = $play_th - ($i+1);
			$sql = "select * from ".$this->db_qz."power_ball_state where th=".$th;
			$rows = $this->db->exeSql($sql);
			
			if( count($rows) <=0)
				continue;
			
			$list[$i]['th'] = $rows[0]['th'];
			$balls = $rows[0]['balls'];
			for( $j=0; $j < 5; ++$j)
			{
				$list[ $i]['ball'.$j] = substr($balls, $j*2, 2);
				$sum_of_ball +=(int)$list[ $i]['ball'.$j];
			}
			$powerball = $rows[0]['powerball'];
			if($powerball < 10) 	$list[ $i]['powerball'] = "0".$powerball;
			else							$list[ $i]['powerball'] = $powerball;
			$list[$i]['sum_of_ball']=$sum_of_ball;
			
			$sum_of_ball = 0;
		}
		
		return $list;
	}
	
	function getPlayTh()
	{
		$sql = "select th from ".$this->db_qz."power_ball_state where state='REGISTER' order by sn desc limit 0,1";
		$rows = $this->db->exeSql($sql);
		$play_th = $rows[0]['th'];
		
		return $play_th;
	}
	
	function laddder_member_list_total($where="")
	{
		$sql = "select count(*) as cnt from ".$this->db_qz."member a where mem_status<>'G' ".$where;
		$rows = $this->db->exeSql($sql);
		return $rows[0]['cnt'];
	}
	
	function laddder_member_list($page, $page_size, $sort_field="", $where="")
	{
		if($sort_field!="")
		{
			$sql = "select * from 
						(
							select c.member_sn, ifnull(sum(c.betting_money),0) as ladder_total_betting_money, 
								ifnull(sum(c.result_money),0) as ladder_total_prize 
								from ".$this->db_qz."child a, ".$this->db_qz."subchild b, ".$this->db_qz."total_cart c, ".$this->db_qz."total_betting d
								where a.sn=b.child_sn and b.sn=d.sub_child_sn and c.betting_no=d.betting_no  and c.member_sn > 0
								and (a.league_sn=903 or a.league_sn=950 or a.league_sn=951) and c.result<>0 group by c.member_sn
						) as rank, ".$this->db_qz."member a where rank.member_sn=a.sn and a.mem_status<>'G' ".$where." 
						order by ".$sort_field." desc limit ".$page.",".$page_size;
			$rank_rows = $this->db->exeSql($sql);
			
			$rows = array();
			for($i=0; $i < count($rank_rows); ++$i)
			{
				$member_sn = $rank_rows[$i]['member_sn'];
				$sql = "select * from ".$this->db_qz."member a, ".$this->db_qz."level_config b where a.mem_lev=b.lev and a.sn=".$member_sn;
				$member_rows = $this->db->exeSql($sql);
				
				$member_rows[0]['ladder_total_betting_money'] = $rank_rows[$i]['ladder_total_betting_money'];
				$member_rows[0]['ladder_total_prize'] = $rank_rows[$i]['ladder_total_prize'];
				$member_rows[0]['ladder_benefit'] = $rank_rows[$i]['ladder_total_prize']-$rank_rows[$i]['ladder_total_betting_money'];
				$rows[] = $member_rows[0];
			}
			
			
			return $rows;
		}
		else
		{
			$sql = "select * from ".$this->db_qz."member a, ".$this->db_qz."level_config b where a.mem_lev=b.lev and a.mem_status<>'G' ".$where." limit ".$page.",".$page_size;
			$rows = $this->db->exeSql($sql);
			
			for( $i=0; $i < count($rows); ++$i)
			{
				$sql = "select ifnull(sum(c.betting_money),0) as ladder_total_betting_money, ifnull(sum(c.result_money),0) as ladder_total_prize 
							from ".$this->db_qz."child a, ".$this->db_qz."subchild b, ".$this->db_qz."total_cart c, ".$this->db_qz."total_betting d
							where a.sn=b.child_sn and b.sn=d.sub_child_sn and c.betting_no=d.betting_no
							and (a.league_sn=903 or a.league_sn=950 or a.league_sn=951) and c.member_sn=".$rows[$i]['sn']." and c.result<>0";
				$ladder_rows = $this->db->exeSql($sql);
				
				$rows[$i]['ladder_total_betting_money'] = $ladder_rows[0]['ladder_total_betting_money'];
				$rows[$i]['ladder_total_prize'] = $ladder_rows[0]['ladder_total_prize'];
				$rows[$i]['ladder_benefit'] = $ladder_rows[0]['ladder_total_prize']-$ladder_rows[0]['ladder_total_betting_money'];
			}
		}
		
		return $rows;
	}
	
	function isRegisterGame($th)
	{
		$sql = "select count(*) as cnt
						from ".$this->db_qz."power_ball_state where th=".$th;
		$rows = $this->db->exeSql($sql);
		
		if($rows[0]['cnt']==0)
		{
			return 0;
		}
		return 1;
	}
	
	function RegisterGame($th, $num=0)
	{
		$game_model = Lemon_Instance::getObject("GameModel",true);
		
		$sql = "select league_sn, home_team, away_team, home_rate, draw_rate, away_rate, template
						from ".$this->db_qz."power_ball_template where state=1";
		$rows = $this->db->exeSql($sql);
		
		//5분후로 설정
		$unix_play_time = strtotime(date("Y-m-d H:i"))+(60*5)*($num+1);
		
		$game_date = date("Y-m-d", $unix_play_time);
		$game_hour = date("H", $unix_play_time);
		$game_minute = date("i", $unix_play_time);
		
		for($i=0; $i < count($rows); ++$i)
		{
			if($rows[$i]['draw_rate']=="")
				$rows[$i]['draw_rate']=1;
			$type = 1;
			if($rows[$i]['template'] == 5)
				$type = 4;
			$game_model->addChild(0, '기타', $rows[$i]['league_sn'] ,$th.$rows[$i]['home_team'], $th.$rows[$i]['away_team'], $game_date, $game_hour, $game_minute, '', '0', 1, 6, $rows[$i]['home_rate'],$rows[$i]['draw_rate'],$rows[$i]['away_rate']);
		}
		
		unset($data);
		$data['th'] = $th;
		$data['state'] = 'REGISTER';
		$data['reg_time'] = 'SYSDATE()';
		$this->db->setInsert($this->db_qz."power_ball_state", $data);
		$this->db->exeSql();
	}
	
	function isAccountGame($th)
	{
		$sql = "select state from ".$this->db_qz."power_ball_state where th=".$th;
		$rows = $this->db->exeSql($sql);
		
		if($rows[0]['state']=='ACCOUNT')
			return 1;
			
		return 0;
	}
	
	function accountGame($th, $result)
	{
		$process_model = Lemon_Instance::getObject("ProcessModel",true);
		
		$sql = "select league_sn, home_team, away_team, home_rate, draw_rate, away_rate, template
						from ".$this->db_qz."power_ball_template"; // where state=1";
		$rows = $this->db->exeSql($sql);
		
		for( $i=0; $i < count($rows); ++$i)
		{
			$sql = "select sn from ".$this->db_qz."child where home_team='".$th.$rows[$i]['home_team']."'";
			$game_rows = $this->db->exeSql($sql);
			
			if( count($game_rows) <= 0)
				continue;
			
			$game_sn = $game_rows[0]['sn'];
			
			//홀짝
			if($rows[$i]['template']==1)
			{
				if($result['powerball']%2==1)
				{
					$home_score = '1';
					$away_score = '0';
				}
				else
				{
					$home_score = '0';
					$away_score = '1';
				}
			}
			//파워볼 [5~9], [0~4]
			else if($rows[$i]['template']==2)
			{
				if($result['powerball']>=5 &&$result['powerball']<=9)
				{
					$home_score = '1';
					$away_score = '0';
				}
				else
				{
					$home_score = '0';
					$away_score = '1';
				}
			}
			else if($rows[$i]['template']==3)
			{
				if($result['big_small']=="big")
				{
					$home_score = '1';
					$away_score = '0';
				}
				else if($result['big_small']=="middle")
				{
					$home_score = '0';
					$away_score = '0';
				}
				else
				{
					$home_score = '0';
					$away_score = '1';
				}
			}
			//일반볼합 홀/짝
			else if($rows[$i]['template']==4)
			{
				if($result['sum_of_ball']%2==1)
				{
					$home_score = '1';
					$away_score = '0';
				}
				else
				{
					$home_score = '0';
					$away_score = '1';
				}
			}
			//일반볼합 언오버(기준점 72.5)
			else if($rows[$i]['template']==5)
			{
				if($result['sum_of_ball'] > $rows[$i]['draw_rate'])    // 72.5)
				{
					$home_score = '1';
					$away_score = '0';
				}
				else
				{
					$home_score = '0';
					$away_score = '1';
				}
			}
			
			$process_model->resultGameProcess($game_sn, $home_score, $away_score);
		}
		
		unset($data);
		$data['state'] = 'ACCOUNT';
		$data['balls'] = $result['balls'];
		$data['powerball'] = $result['powerball'];
		$data['account_time'] = 'SYSDATE()';
		$this->db->setUpdate($this->db_qz."power_ball_state", $data, "th=".$th);
		$this->db->exeSql();
	}
	
	//베팅제약
	//01. 한회차당 같은 구좌 베팅은 1번만 허용
	//02. 대중소 베팅은 1번만 허용
	//03. 테스트회원(G)은 제외
	function isEnableBetting($game_sn, $selected, $member_sn)
	{
		$sql = "select mem_status from ".$this->db_qz."member where sn=".$member_sn;
		$rows = $this->db->exeSql($sql);
		if( $rows[0]['mem_status']=='G')
			return 1;
		
		$sql = "select count(*) as cnt from ".$this->db_qz."child a, ".$this->db_qz."subchild b, ".$this->db_qz."total_betting c, ".$this->db_qz."total_cart d
					 where a.sn=b.child_sn and b.sn=c.sub_child_sn and c.betting_no=d.betting_no 
					 and a.sn='".$game_sn."' and d.member_sn='".$member_sn."' and c.select_no='".$selected."'";
		$rows = $this->db->exeSql($sql);
		
		if($rows[0]['cnt'] > 0)
			return -1;
			
		$sql = "select count(*) as cnt from ".$this->db_qz."child a, ".$this->db_qz."subchild b, ".$this->db_qz."total_betting c, ".$this->db_qz."total_cart d
					 where a.sn=b.child_sn and b.sn=c.sub_child_sn and c.betting_no=d.betting_no 
					 and a.league_sn=568 and a.sn='".$game_sn."' and d.member_sn='".$member_sn."'";
		$rows = $this->db->exeSql($sql);
		
		if($rows[0]['cnt'] > 0)
			return -2;
		
		return 1;
	}
}
?>