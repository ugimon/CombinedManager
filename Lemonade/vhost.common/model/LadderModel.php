<?php
class LadderModel extends Lemon_Model 
{
	function misGameList($date)
	{
		$sql = "select * from tb_child where league_sn='505' and kubun<>1 and gameDate='".$date."' and  concat(gameDate, ' ', gameHour, ':', gameTime) < now()";
		$rs = $this->db->exeSql($sql);

		return $rs;
	}
	
	function getGame($th, $date)
	{
		if( !$this->is_integer_mysql_parameter($th))
		{
			exit;
		}
		
		$sql = "select a.sn as game_sn, a.gameDate, a.gameHour, a.gameTime,
						b.sn as detail_sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate
						from tb_child a,  tb_subchild b 
						where a.sn=b.child_sn and a.league_sn='505' and a.home_team='".$th."회차 [홀]' and a.gameDate='".$date."' and a.kubun=0";
		$rows = $this->db->exeSql($sql);
		
		$list[0] = $rows[0];


		$sql = "select a.sn as game_sn, a.gameDate, a.gameHour, a.gameTime,
						b.sn as detail_sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate
						from tb_child a,  tb_subchild b 
						where a.sn=b.child_sn and a.league_sn='504' and a.home_team='".$th."회차 [좌측시작]' and a.gameDate='".$date."' and a.kubun=0";
		$rows = $this->db->exeSql($sql);
		
		$list[1] = $rows[0];		
		
		$sql = "select a.sn as game_sn, a.gameDate, a.gameHour, a.gameTime,
						b.sn as detail_sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate
						from tb_child a,  tb_subchild b 
						where a.sn=b.child_sn and a.league_sn='503' and a.home_team='".$th."회차 [3줄]' and a.gameDate='".$date."' and a.kubun=0";
		$rows = $this->db->exeSql($sql);
		
		$list[2] = $rows[0];
		
	
		
		return $list;
	}
}
?>