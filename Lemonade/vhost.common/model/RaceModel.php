<?php
class RaceModel extends Lemon_Model 
{
	function misGameList($date)
	{
		$sql = "select * from ".$this->db_qz."child where league_sn='500' and kubun<>1 and gameDate='".$date."' and  concat(gameDate, ' ', gameHour, ':', gameTime) < now()";
		$rs = $this->db->exeSql($sql);

		return $rs;
	}
	
	function getGame($th, $date)
	{
		$sql = "select a.sn as game_sn, a.gameDate, a.gameHour, a.gameTime,
						b.sn as detail_sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate
						from ".$this->db_qz."child a,  ".$this->db_qz."subchild b 
						where a.sn=b.child_sn and a.league_sn='500' and a.home_team='".$th."회차 네팽이' and a.gameDate='".$date."' and a.kubun=0";
		$rows = $this->db->exeSql($sql);
		
		return $rows[0];
	}
	
	//동일경기 배팅횟수제한
	function isEnableBetting($game_sn, $member_sn)
	{
		$sql = "select count(*) as cnt 
							from ".$this->db_qz."child a, ".$this->db_qz."subchild b, ".$this->db_qz."total_betting c, ".$this->db_qz."total_cart d
					 	where a.sn=b.child_sn and b.sn=c.sub_child_sn and c.betting_no=d.betting_no 
					 		and a.sn='".$game_sn."' and d.member_sn='".$member_sn."'";
		$rs = $this->db->exeSql($sql);
		
		if($rs[0]['cnt'] > 0)
			return 0;
		else return 1;
		
	}
}
?>