<?php
class RecentModel extends Lemon_Model
{
	function getList($page, $page_size)
	{
		$sql = "select a.home_team, a.away_team, a.gameDate, a.gameHour, a.gameTime, a.sport_name, a.league_sn,
						b.home_rate, b.draw_rate, b.away_rate
						from tb_child a, tb_subchild b
					where a.sn=b.child_sn and a.kubun=1
						order by b.sn desc limit $page, $page_size";
						
		$rows = $this->db->exeSql($sql);
		
		for($i=0; $i<count($rows); ++$i)
		{
			$league_sn = $rows[$i]['league_sn'];
			$sql = "select name, lg_img from tb_league where sn='$league_sn'";
			
			$league_rows = $this->db->exeSql($sql);
			
			$rows[$i]['name'] = $league_rows[0]['name'];
			$rows[$i]['league_image'] = $league_rows[0]['lg_img'];
		}
	
		return $rows;
	}
	
	//ajax 절대 return 을 쓰면 안된다.
	function ajaxList($page, $page_size)
	{
		$array = $this->getList($page, $page_size);
		
		echo(json_encode($array));
	}
	
	function ajaxAdd($memberSn, $categorySn, $nationSn, $leagueSn)
	{
		$sql = "select member_sn
				from tb_favorite_list 
					where member_sn=".$memberSn."and category_sn=".$categorySn." and nation_sn=".$nationSn."and league_sn=".$leagueSn;
					
		$rs = $this->db->exeSql($sql);
		
		if($rs[0]['mem_idx']!='')
		{
			echo('true');
			return;
		}
		
		$sql = "insert into tb_favorite_list(member_sn,category_sn,nation_sn,league_sn) ";
		$sql.= "values(".$memberSn.",".$categorySn.",".$nationSn.",".$leagueSn.")";
		
		$rs = $this->db->exeSql($sql);
		
		if($rs>0) echo('true');
		else echo('false');
	}
	
	function ajaxDel($sn)
	{
		$sql = "delete from tb_favorite_list 
				where sn=".$sn;
		
		$rs = $this->db->exeSql($sql);	
		if($rs>0) echo('true');
		else echo('false');
	}
}
?>