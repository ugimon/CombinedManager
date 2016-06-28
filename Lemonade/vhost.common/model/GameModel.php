<?php

class GameModel extends Lemon_Model
{
	//▶ 경기 상태 수정
	function modifyChildStaus($childSn, $state)
	{
		$sql = "select home_rate,away_rate from tb_subchild
							where child_sn in(".$childSn.")";
		$rs = $this->db->exeSql($sql);

		if(sizeof($rs) > 0)
		{
			$homeRate = $rs[0]["home_rate"];
			$drawRate = $rs[0]["away_rate"];
			if(is_null($homeRate)||$homeRate==""||is_null($drawRate)||$drawRate=="")
			{
				$msg = "배당입력을 하지 않은 게임이 존재합니다.먼저 배당을 입력하십시오";
				echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><script language='javascript'> alert('".$msg."'); opener.location.reload();self.close();</script>";

				exit();
			}
		}

		if($state==-1)	$sql = "update tb_child set kubun = null where sn in (".$childSn.")";
		else						$sql = "update tb_child set kubun = '".$state."' where sn in (".$childSn.")";

		$this->db->exeSql($sql);
	}

	//▶ 경기 결과 취소
	function cancelResultChild($child_sn)
	{
		$cModel  = Lemon_Instance::getObject("CommonModel",true);
		$pModel  = Lemon_Instance::getObject("ProcessModel",true);

		$array = array();

		$sql = "select * from tb_Child
							where sn ='". $child_sn ."'";
		$rs = $this->db->exeSql($sql);
		if(sizeof($rs) > 0)
		{

			$sub_sn = $this->getSubChildField($child_sn, "sn");

			$home_score = $rs[0]["home_score"];
			$away_score = $rs[0]["away_score"];
		}

		if (is_null($home_score) || is_null($away_score))
		{
			return -1;
		}
		else
		{
			$pModel->rollbackGameProcess($child_sn);
			return 1;
		}
	}

	//▶ 경기 배당수정
	function modifyChildRate($child_sn,$bettype,$home_rate,$draw_rate,$away_rate)
	{
		$array = array();
		$sql = "select child_sn from tb_subchild_log
							where child_sn=". $child_sn ." and betting_type=". $bettype;
		$rs = $this->db->exeSql($sql);
		if( sizeof($rs) <= 0 )
		{
			$sql = "select home_rate,draw_rate,away_rate
								from tb_subchild
									where child_sn=". $child_sn ." and betting_type=". $bettype;
			$rs = $this->db->exeSql($sql);
			if( sizeof($rs) > 0 )
			{
				$array['home_rate'] = $rs[0]['home_rate'];
				$array['draw_rate'] = $rs[0]['draw_rate'];
				$array['away_rate'] = $rs[0]['away_rate'];

				$sql = "insert into tb_subchild_log(child_sn,betting_type,home_rate,draw_rate,away_rate,regdate)
									values('".$child_sn."','".$bettype."','".$array['home_rate']."','".$array['draw_rate']."','".$array['away_rate']."',now())";
				$this->db->exeSql($sql);
			}
		}

		$sql = "insert into tb_subchild_log(child_sn,betting_type,home_rate,draw_rate,away_rate,regdate)values";
		$sql.= "('".$child_sn."','".$bettype."','".$home_rate."','".$draw_rate."','".$away_rate."',now())";
		$this->db->exeSql($sql);

		$sql = "update tb_subchild SET ";
		$sql = $sql . "home_rate='".$home_rate."',";
		$sql = $sql . "draw_rate='".$draw_rate."',";
		$sql = $sql . "away_rate='".$away_rate."'";
		$sql = $sql . " where child_sn=".$child_sn."";
		$sql = $sql . " and betting_type=".$bettype."";

		$this->db->exeSql($sql);
	}

	function modifyChildRate_Date($child_sn,$gameDate,$gameHour,$gameTime)
	{
		$sql = "update tb_child SET ";
		$sql = $sql . "gameDate='".$gameDate."',";
		$sql = $sql . "gameHour='".$gameHour."',";
		$sql = $sql . "gameTime='".$gameTime."'";
		$sql = $sql . " where sn=".$child_sn."";

		$this->db->exeSql($sql);
	}

	//▶ 서브차일드 삭제
	function delSubChild($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql ="delete from tb_subchild where child_sn = '".$sn."'";

		return $this->db->exeSql($sql);
	}

	//▶ 차일드 삭제
	function delChild($sn)
	{
		$sql = "select * from tb_child a, tb_subchild b, tb_total_betting c where a.sn=b.child_sn and b.sn=c.sub_child_sn and a.sn in (".$sn.")";
		$rs = $this->db->exeSql($sql);

		if(sizeof($rs)>0)
		{
			throw new Lemon_ScriptException("배팅내역이 있는 경기입니다. 다시한번 확인 하세요.");
			exit;
		}

		$sql ="delete from tb_Child where sn in (".$sn.")";
		$this->db->exeSql($sql);

		$arr_sn	= explode(',',$sn);
		for( $i=0; $i<sizeof($arr_sn); ++$i )
		{
			$sql ="delete from tb_subchild where child_sn = (".$arr_sn[$i].")";
			$this->db->exeSql($sql);
		}

		$sql = "update tb_child_comming_ini set state=-1 where sn in ($sn)";
		$this->db->exeSql($sql);
	}

	//▶ 게임종류 변경
	function modifyGameType($childSn, $specialType, $gameType)
	{
		$gameModel 	= Lemon_Instance::getObject("GameModel",true);

		$where 	= " sn=".$childSn;
		$set  	= "type=".$gameType.",";
		$set 	 .= "special = " .$specialType;
		$gameModel->modifyChild($set, $where);

		$where 	= "";
		$set 		= "";
		$set 		= "betting_type = ".$gameType;

		$where = " child_sn=".$childSn;
		$gameModel->modifySubChild($set, $where);

		$sql = "select sn
						from tb_subchild
						where child_sn=".$childSn;
		$rs = $this->db->exeSql($sql);
		$subChildSn = $rs[0]["sn"];

		$sql = "update tb_total_betting
						set game_type=".$gameType."
						where sub_child_sn=".$subChildSn;
		return $this->db->exeSql($sql);
	}

	//▶ 서브차일드 수정
	function modifySubChild($addset, $addwhere)
	{
		$set = " set ".$addset;
		if($addwhere!=''){$where=" where ".$addwhere;}

		$sql = "update tb_subChild ".$set.$where;

		return $this->db->exeSql($sql);
	}
	//▶ 서브차일드 목록
	function getSubChildRow($sn, $field='*', $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$where = " child_sn=".$sn;
		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRow($field, $this->db_qz.'subChild', $where);
	}

	//▶ 서브차일드 목록
	function getSubChildField($sn, $field, $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$where = " child_sn=".$sn;

		if($addWhere!='') {$where .=' and '.$addWhere;}
		$rs = $this->getRow($field, $this->db_qz.'subChild', $where);

		return $rs[$field];
	}

	//▶ 서브차일드 목록
	function getSubChildRows($sn, $field='*', $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$where = " child_sn=".$sn;
		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRows($field, $this->db_qz.'subChild', $where);
	}

	//▶ 게임종류만 틀리고 동일한 게임 목록
	function getSameChild($gameDate, $gameHour, $gameTime, $leagueSn, $homeTeam, $awayTeam)
	{
		$sql = "select sn from tb_child
						where gameDate='".$gameDate."' and gameHour='".$gameHour."' and gameTime='".$gameTime."' and
						league_sn=".$leagueSn." and home_team='".$homeTeam."' and away_team='".$awayTeam."'";

		return $this->db->exeSql($sql);
	}

    //▶ 게임종류만 다른 동일한 게임을 가져온다. gameType 으로 선택
	function getAnotherTypeChild($gameDate, $gameHour, $gameTime, $leagueSn, $homeTeam, $awayTeam, $gameType)
	{
		$sql = "select sn from tb_child
						where gameDate='".$gameDate."' and gameHour='".$gameHour."' and gameTime='".$gameTime."' and
						league_sn=".$leagueSn." and home_team='".$homeTeam."' and away_team='".$awayTeam."'";
		$sql = $sql." and type=".$gameType." ";

		return $this->db->exeSql($sql);
	}

	//▶ 경기 수정
	function modifyChild($addset,$addwhere='')
	{
		$set = " set ".$addset;
		if($addwhere!='') {$where .=' where '.$addwhere;}

		$sql = "update tb_child ".$set.$where;
    	//echo "modify child : ".$sql."\n";
		return $this->db->exeSql($sql);
	}

	//▶ 경기 추가
	function addChild($parentSn,$category,$leagueSn,$homeTeam,$awayTeam,$gameDate,$gameHour,$gameTime,$notice,$isUpload,$type,$special,$homeRate,$drawRate,$awayRate, $is_specified_special='0')
	{
		$sql = "insert into tb_child(";
		$sql = $sql ." parent_sn, sport_name, league_sn, home_team, away_team," ;
		$sql = $sql ." gameDate, gameHour, gameTime, notice, kubun, type, special, is_specified_special)";
		$sql = $sql . " values("  	;
		$sql = $sql . "'" . $parentSn . "','". $category ."',";
		$sql = $sql . "'" . $leagueSn . "',";
		$sql = $sql . "'" . $homeTeam . "',";
		$sql = $sql . "'" . $awayTeam . "',";
		$sql = $sql . "'" . $gameDate . "',";
		$sql = $sql . "'" . $gameHour . "',";
		$sql = $sql . "'" . $gameTime . "',";
		$sql = $sql . "'" . $notice . "',";
		if($isUpload == 'null') $sql = $sql. "null,";
		else $sql = $sql . "'".$isUpload. "',";

		$sql = $sql . "'" . $type . "',";
		$sql = $sql . "'" . $special . "',";
		$sql = $sql . "'" . $is_specified_special . "')";


		$childSn = $this->db->exeSql($sql);

		if($childSn <= 0)
		{
			return 0;
		}

		$sql = "insert into tb_subchild(child_sn,betting_type,home_rate,draw_rate,away_rate)
				values('".$childSn."','".$type."','".$homeRate."','".$drawRate."','".$awayRate."')";

		return $this->db->exeSql($sql);
	}

	//▶ 게임 총합
	public function getListTotal($state=''/*kubun*/, $category='', $gameType='', $specialType='', $beginDate='', $endDate='', $minBettingMoney='', $leagueSn='', $homeTeam='', $awayTeam='', $bettingEnable='', $otherWhere = '')
	{
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($gameType))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($specialType))
		{
			exit;
		}

		$where='';

		if($state!='')
		{
			if($state==1)
			{
				$where=" and a.kubun=1";$sort='desc';
			}
			else if($state==2) 	$where=" and a.kubun=0";
			else if($state==3) 	$where = " and a.kubun is null";
			else if($state==4) 	$where = " and (a.kubun is null || a.kubun=0)";
		}

		if($category!='')
			$where.=" and a.sport_name='".$category."'";
		if($gameType!='')
			$where.=" and a.type='".$gameType."'";

		if($specialType!=="")
		{
			switch($specialType)
			{
			case '1': $where.=" and a.special=0";  break;
			case '2': $where.=" and a.special=1";  break;
			case '4': $where.=" and a.special=2";  break;
			case '5': $where.=" and a.special=5";  break;
			case '6': $where.=" and a.special=6";  break;
			case '7': $where.=" and a.special=7";  break;
			}
		}
		if($beginDate!='' && $endDate!='')
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) between '".$beginDate."' and '".$endDate." 23:59:59'";

		if($bettingEnable=='1')
		{
			$now = date("Y-m-d H:i:s");
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) >= '".$now."'";
		}
		else if($bettingEnable=='-1')
		{
			$now = date("Y-m-d H:i:s");
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) <= '".$now."'";
		}

		if($minBettingMoney!='')
			$where .=" and c.total_money >= ".$minBettingMoney;

		if($leagueSn!='')
		{
			if(!is_array($leagueSn))
			{
				$where.=" and a.league_sn=".$leagueSn;
			}
			else
			{
				$where.=" and a.league_sn in(";
				for($i=0; $i<sizeof($leagueSn); ++$i)
				{
					if($i==0)	$where.=$leagueSn[$i];
					else			$where.=",".$leagueSn[$i];
				}
				$where.=")";
			}
		}
		if($homeTeam!='')
			$where.=" and a.home_team like('%".$homeTeam."%')";

		if($awayTeam!='')
			$where.=" and a.away_team like('%".$awayTeam."%')";

		if($minBettingMoney!='')
		{
			$sql = "select count(*) as cnt
							from tb_child a, tb_league c, tb_subchild b left outer join
							(select sum(betting_money) as total_money, sub_child_sn
								from tb_total_cart c, tb_total_betting d
								where c.betting_no=d.betting_no and c.is_account=1 and logo='".$this->logo."'
								group by sub_child_sn) as c on b.sn=c.sub_child_sn
							where a.sn=b.child_sn and a.league_sn=c.sn and a.league_sn!=505 and a.league_sn!=504 and a.league_sn!=503 ".$where." $otherWhere ";
		}

		else
		{
			$sql = "select count(*) as cnt
							from tb_child a
							where a.league_sn!=505 and a.league_sn!=504 and a.league_sn!=503 ".$where." $otherWhere ";

							 //echo $sql;
		}

		$rs = $this->db->exeSql($sql);

		return $rs[0]['cnt'];
	}

	//▶ 게임 목록
	public function getList($page, $page_size, $state=''/*kubun*/, $category='', $gameType='', $specialType='', $beginDate='', $endDate='', $minBettingMoney='', $leagueSn='', $homeTeam='', $awayTeam='', $bettingEnable='', $otherWhere='')
	{
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($gameType))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($specialType))
		{
			exit;
		}

		$where='';
		$sort='asc';

		if($state!='')
		{
			if($state==1) 			{$where=" and a.kubun=1";$sort='desc';}
			else if($state==2) 	$where=" and a.kubun=0";
			else if($state==3) 	$where = " and a.kubun is null";
			else if($state==4) 	$where = " and (a.kubun is null || a.kubun=0)";
		}

		if($category!='')
			$where.=" and a.sport_name='".$category."'";
		if($gameType!='')
			$where.=" and a.type='".$gameType."'";

		if($specialType!=="")
		{
			switch($specialType)
			{
			case '1': $where.=" and a.special=0";  break;
			case '2': $where.=" and a.special=1";  break;
			case '4': $where.=" and a.special=2";  break;
			case '5': $where.=" and a.special=5";  break;
			case '6': $where.=" and a.special=6";  break;
			case '7': $where.=" and a.special=7";  break;
			case '8': $where.=" and a.special=8";  break;
			}
		}
		if($beginDate!='' && $endDate!='')
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) between '".$beginDate."' and '".$endDate." 23:59:59'";

		if($bettingEnable=='1')
		{
			$now = date("Y-m-d H:i:s");
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) >= '".$now."'";
		}
		else if($bettingEnable=='-1')
		{
			$now = date("Y-m-d H:i:s");
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) <= '".$now."'";
		}

		if($minBettingMoney!='')
			$where .=" and c.total_money >= ".$minBettingMoney;

		if($leagueSn!='')
		{
			if(!is_array($leagueSn))
			{
				$where.=" and a.league_sn=".$leagueSn;
			}
			else
			{
				$where.=" and a.league_sn in(";
				for($i=0; $i<sizeof($leagueSn); ++$i)
				{
					if($i==0)	$where.=$leagueSn[$i];
					else			$where.=",".$leagueSn[$i];
				}
				$where.=")";
			}
		}
		if($homeTeam!='')
			$where.=" and a.home_team like('%".$homeTeam."%')";

		if($awayTeam!='')
			$where.=" and a.away_team like('%".$awayTeam."%')";

		if($page_size > 0)
			$limit = "limit ".$page.",".$page_size;

		if($minBettingMoney!='')
		{
			$sql = "select a.sn as child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score, a.away_score,
								a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun,
								a.type, a.special, a.bet_money, c.name as league_name,
								b.sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate, b.win, b.result
							from tb_child a, tb_league c, tb_subchild b left outer join
							(select sum(betting_money) as total_money, sub_child_sn
								from tb_total_cart c, tb_total_betting d
								where c.betting_no=d.betting_no and c.is_account=1
								group by sub_child_sn) as c on b.sn=c.sub_child_sn
							where a.sn=b.child_sn and a.league_sn=c.sn ".$where.$otherWhere."
							order by a.gameDate ".$sort.", a.gameHour ".$sort.", a.gameTime ".$sort.", league_name, a.home_team, a.special, a.type, a.sn asc " .$limit;
		}

		else
		{

		/*
			$sql = "select a.sn as child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score, a.away_score,
								a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun,
								a.type, a.special, a.bet_money, c.name as league_name,
								b.sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate, b.win, b.result
							from tb_child a, tb_subchild b, tb_league c
							where a.sn=b.child_sn and a.league_sn=c.sn ".$where."
							order by a.gameDate ".$sort.", a.gameHour ".$sort.", a.gameTime ".$sort.", league_name, a.home_team, a.special, a.type, a.sn asc " .$limit;
		*/

			$sql = "select a.child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score,
								a.away_score, a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun, a.type,
								a.special, a.bet_money, a.league_name, b.sn, b.child_sn, b.betting_type, b.home_rate,
								b.draw_rate, b.away_rate, b.win, b.result
							from (select a.sn as child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score,
								a.away_score, a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun, a.type,
								a.special, a.bet_money, b.name as league_name
							from tb_child a, tb_league b where a.league_sn=b.sn $where $otherWhere
								order by gameDate $sort, gameHour $sort, gameTime $sort, name, home_team, special, type, a.sn asc $limit ) a, tb_subchild b where a.child_sn=b.child_sn";
		}

		return $this->db->exeSql($sql);
	}

	//▶ 게임 목록
	public function getList22($page, $page_size, $state=''/*kubun*/, $category='', $gameType='', $specialType='', $beginDate='', $endDate='', $minBettingMoney='', $leagueSn='', $homeTeam='', $awayTeam='', $bettingEnable='', $otherWhere='')
	{
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($gameType))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($specialType))
		{
			exit;
		}

		$where='';
		$sort='asc';

		if($state!='')
		{
			if($state==1) 			{$where=" and a.kubun=1";$sort='desc';}
			else if($state==2) 	$where=" and a.kubun=0";
			else if($state==3) 	$where = " and a.kubun is null";
			else if($state==4) 	$where = " and (a.kubun is null || a.kubun=0)";
		}

		if($category!='')
			$where.=" and a.sport_name='".$category."'";
		if($gameType!='')
			$where.=" and a.type='".$gameType."'";

		if($specialType!=="")
		{
			switch($specialType)
			{
			case '1': $where.=" and a.special=0";  break;
			case '2': $where.=" and a.special=1";  break;
			case '4': $where.=" and a.special=2";  break;
			case '5': $where.=" and a.special=5";  break;
			case '6': $where.=" and a.special=6";  break;
			case '7': $where.=" and a.special=7";  break;
			}
		}
		if($beginDate!='' && $endDate!='')
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) between '".$beginDate."' and '".$endDate." 23:59:59'";

		if($bettingEnable=='1')
		{
			$now = date("Y-m-d H:i:s");
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) >= '".$now."'";
		}
		else if($bettingEnable=='-1')
		{
			$now = date("Y-m-d H:i:s");
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) <= '".$now."'";
		}

		if($minBettingMoney!='')
			$where .=" and c.total_money >= ".$minBettingMoney;

		if($leagueSn!='')
		{
			if(!is_array($leagueSn))
			{
				$where.=" and a.league_sn=".$leagueSn;
			}
			else
			{
				$where.=" and a.league_sn in(";
				for($i=0; $i<sizeof($leagueSn); ++$i)
				{
					if($i==0)	$where.=$leagueSn[$i];
					else			$where.=",".$leagueSn[$i];
				}
				$where.=")";
			}
		}
		if($homeTeam!='')
			$where.=" and a.home_team like('%".$homeTeam."%')";

		if($awayTeam!='')
			$where.=" and a.away_team like('%".$awayTeam."%')";

		if($page_size > 0)
			$limit = "limit ".$page.",".$page_size;

		if($minBettingMoney!='')
		{
			$sql = "select a.sn as child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score, a.away_score,
								a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun,
								a.type, a.special, a.bet_money, c.name as league_name,
								b.sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate, b.win, b.result
							from tb_child a, tb_league c, tb_subchild b left outer join
							(select sum(betting_money) as total_money, sub_child_sn
								from tb_total_cart c, tb_total_betting d
								where c.betting_no=d.betting_no and c.is_account=1
								group by sub_child_sn) as c on b.sn=c.sub_child_sn
							where a.sn=b.child_sn and a.league_sn=c.sn ".$where.$otherWhere."
							order by a.gameDate ".$sort.", a.gameHour ".$sort.", a.gameTime ".$sort.", league_name, a.home_team, a.special, a.type, a.sn asc " .$limit;
		}

		else
		{

		/*
			$sql = "select a.sn as child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score, a.away_score,
								a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun,
								a.type, a.special, a.bet_money, c.name as league_name,
								b.sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate, b.win, b.result
							from tb_child a, tb_subchild b, tb_league c
							where a.sn=b.child_sn and a.league_sn=c.sn ".$where."
							order by a.gameDate ".$sort.", a.gameHour ".$sort.", a.gameTime ".$sort.", league_name, a.home_team, a.special, a.type, a.sn asc " .$limit;
		*/

			$sql = "select a.child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score,
								a.away_score, a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun, a.type,
								a.special, a.bet_money, a.league_name, b.sn, b.child_sn, b.betting_type, b.home_rate,
								b.draw_rate, b.away_rate, b.win, b.result
							from (select a.sn as child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score,
								a.away_score, a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun, a.type,
								a.special, a.bet_money, b.name as league_name
							from tb_child a, tb_league b where a.league_sn=b.sn $where $otherWhere
								order by gameDate $sort, gameHour $sort, gameTime $sort, name, home_team, special, type, a.sn asc $limit ) a, tb_subchild b where a.child_sn=b.child_sn";
		}

		return $this->db->exeSql($sql);
	}

	//▶ 최건경기순 경기목록
	public function getList_asc($page, $page_size, $state=''/*kubun*/, $category='', $gameType='', $specialType='', $beginDate='', $endDate='', $minBettingMoney='', $leagueSn='', $homeTeam='', $awayTeam='', $bettingEnable='')
	{
		if( !$this->is_integer_mysql_parameter($state))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($gameType))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($specialType))
		{
			exit;
		}

		$where='';

		if($state!='')
		{
			if($state==1) 			$where=" and a.kubun=1";
			else if($state==2) 	$where=" and a.kubun=0";
			else if($state==3) 	$where = " and a.kubun is null";
			else if($state==4) 	$where = " and (a.kubun is null || a.kubun=0)";
		}

		if($category!='')
			$where.=" and a.sport_name='".$category."'";
		if($gameType!='')
			$where.=" and a.type='".$gameType."'";

		if($specialType!=="")
		{
			switch($specialType)
			{
			case '1': $where.=" and a.special=0";  break;
			case '2': $where.=" and a.special=1";  break;
			case '4': $where.=" and a.special=2";  break;
			}
		}
		if($beginDate!='' && $endDate!='')
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) between '".$beginDate."' and '".$endDate." 23:59:59'";

		if($bettingEnable=='1')
		{
			$now = date("Y-m-d H:i:s");
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) >= '".$now."'";
		}
		else if($bettingEnable=='-1')
		{
			$now = date("Y-m-d H:i:s");
			$where.=" and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) <= '".$now."'";
		}

		if($minBettingMoney!='')
			$where .=" and c.total_money >= ".$minBettingMoney;

		if($leagueSn!='')
		{
			if(!is_array($leagueSn))
			{
				$where.=" and a.league_sn=".$leagueSn;
			}
			else
			{
				$where.=" and a.league_sn in(";
				for($i=0; $i<sizeof($leagueSn); ++$i)
				{
					if($i==0)	$where.=$leagueSn[$i];
					else			$where.=",".$leagueSn[$i];
				}
				$where.=")";
			}
		}
		if($homeTeam!='')
			$where.=" and a.home_team like('%".$homeTeam."%')";

		if($awayTeam!='')
			$where.=" and a.away_team like('%".$awayTeam."%')";

		if($page_size > 0)
			$limit = "limit ".$page.",".$page_size;

		$sql = "select a.sn as child_sn, a.parent_sn, a.sport_name, a.home_team, a.away_team, a.home_score, a.away_score,
							a.gameDate, a.gameHour, a.gameTime, a.notice, a.win_team,a.kubun,
							a.type, a.special, a.bet_money, (select name from tb_league where sn=a.league_sn) as league_name,
							b.sn, b.child_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate, b.win, b.result
						from tb_child a, tb_subchild b
						where a.sn=b.child_sn and a.league_sn!=500 and a.league_sn!=504 and a.league_sn!=503
						and a.league_sn!=500 and a.league_sn!=584 and a.league_sn!=583 and a.league_sn!=570 and a.league_sn!=568 and a.league_sn!=569
						".$where."
						order by a.gameDate desc, a.gameHour desc, a.gameTime desc, league_name, a.home_team, a.special, a.type, a.sn asc ".$limit;



		return $this->db->exeSql($sql);
	}

	//▶ 한경기에대한 게임 목록
	public function getListBychild_sn($child_sn)
	{
		if( !$this->is_integer_mysql_parameter($child_sn))
		{
			exit;
		}

		$sql = "select a.sn as child_sn,a.parent_sn,a.sport_name,a.home_team,a.away_team,a.home_score,a.away_score,
						a.gameDate,a.gameHour,a.gameTime,a.notice,a.win_team,a.kubun,
						a.type,a.special,a.bet_money,(select name from tb_league where sn=a.league_sn) as league_name,
						b.sn,b.child_sn,b.betting_type,b.home_rate,b.draw_rate,b.away_rate,b.win,b.result
						from tb_child a,tb_subchild b
						where a.sn=b.child_sn and b.child_sn=".$child_sn;


		return $this->db->exeSql($sql);
	}

	//▶ TOP 5 게임 목록
	public function getTopList()
	{
		$cartModel 	= Lemon_Instance::getObject("CartModel",true);
		$beginDate	= date("Y-m-d",strtotime ("-1 days"))." 00:00:00";
		$endDate 		= date("Y-m-d",strtotime ("+1 days"))." 23:59:59";

		$sql = "select a.sn as child_sn, a.parent_sn, a.sport_name, a.league_sn, a.home_team, sum(d.betting_money) as total_money,
						a.home_score, a.away_team, a.away_score, a.win_team,a.handi_winner, a.gameDate, a.gameHour,
						a.type, a.gameTime, a.special, a.kubun, b.sn as subchild_sn, b.betting_type, b.home_rate, b.draw_rate, b.away_rate, c.betting_no,
						c.select_no, c.select_rate, c.game_type, c.event, c.result, d.result as bet_result, d.betting_money, d.is_account,
						(select name from tb_league where sn=a.league_sn) as league_name
						from tb_child a
							  inner join tb_subchild b on a.sn=b.child_sn
							  inner join tb_total_betting c on b.sn=c.sub_child_sn
							  inner join tb_total_cart d on c.betting_no=d.betting_no
						where is_account=1 and d.result=0 and c.result=0 and concat(a.gameDate,' ', a.gameHour,':', a.gameTime) between '".$beginDate."' and '".$endDate."'
						group by child_sn, select_no order by total_money limit 0,5";
		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			if($rs[$i]['child_sn']!=null)
			{
				$item = $cartModel->getTeamTotalBetMoney($rs[$i]['child_sn']);

				$rs[$i]['home_total_betting'] = $item['home_total_betting'];
				$rs[$i]['active_home_total_betting'] = $item['active_home_total_betting'];
				$rs[$i]['home_count'] = $item['home_count'];

				$rs[$i]['draw_total_betting'] = $item['draw_total_betting'];
				$rs[$i]['active_draw_total_betting'] = $item['active_draw_total_betting'];
				$rs[$i]['draw_count'] = $item['draw_count'];

				$rs[$i]['away_total_betting'] = $item['away_total_betting'];
				$rs[$i]['active_away_total_betting'] = $item['active_away_total_betting'];
				$rs[$i]['away_count'] = $item['away_count'];

				$rs[$i]['total_betting'] = $item['home_total_betting']+$item['draw_total_betting']+$item['away_total_betting'];
				$rs[$i]['betting_count'] = $item['home_count']+$item['draw_count']+$item['away_count'];
				$arr[]=$rs[$i];
			}
		}

		return $arr;
	}

	function isGameExist($gameType, $specialType, $homeTeam, $awayTeam, $gameDate, $gameHour, $gameTime)
	{
		if( !$this->is_integer_mysql_parameter($gameType))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($specialType))
		{
			exit;
		}

		$sql = "select count(*) as cnt
						from tb_child
						where home_team='".$homeTeam."'
									and away_team='".$awayTeam."'
									and gameDate='".$gameDate."'
									and gameHour='".$gameHour."'
									and gameTime='".$gameTime."'
									and type=".$gameType."
									and special=".$specialType;
		$rs = $this->db->exeSql($sql);
		if($rs[0]['cnt']>0) return 1;
		return 0;
	}

	//▶ 차일드 목록
	function getChildRow($sn, $field='*', $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$where = " sn=".$sn;

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRow($field, $this->db_qz.'child', $where);
	}

	//▶ 차일드 목록
	function getChildField($sn, $field, $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$where = " sn=".$sn;

		if($addWhere!='') {$where .=' and '.$addWhere;}
		$rs = $this->getRow($field, $this->db_qz.'child', $where);

		return $rs[$field];
	}

	//▶ 차일드 목록
	function getChildRows($sn, $field='*', $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$where = " sn=".$sn;
		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRows($field, $this->db_qz.'child', $where);
	}

	//▶ 차일드,서브차일드 목록
	function getChildJoinSubChild($childSn)
	{
		if( !$this->is_integer_mysql_parameter($childSn))
		{
			exit;
		}

		$sql = "select a.*, b.home_rate, b.draw_rate, b.away_rate, b.win from tb_child a, tb_subchild b
						where a.sn='".$childSn."'  and a.sn=b.child_sn";
		return $this->db->exeSql($sql);
	}

	//▶ 경기 목록(회차)
	function getGameList($parentIdx, $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($parentIdx))
		{
			exit;
		}

		$where=" where parent_sn=".$parentIdx;
		if($addWhere!='') {$where.=" and ".$addWhere;}

		$sql = "select *
				from tb_Child".$where;

		return $this->db->exeSql($sql);
	}

	//▶ 경기 추가
	function add7mGame($parentIdx, $game_num_temp, $league_num_temp, $leagueSn,
						$team1_name_temp, $team2_name_temp, $game_date_temp, $game_hours_temp, $game_minute_temp,
						$kubun, $type, $special, $gameType)
	{
		$sql = "insert into tb_child (" ;
		$sql = $sql ." parent_sn,sport_name,league_sn,home_team,away_team," ;
		$sql = $sql ." gameDate, gameHour,gameTime,kubun,type,special,bet_money)" ;
		$sql = $sql . " values(";
		$sql = $sql . "'" . $parentIdx . "'," ;
		$sql = $sql . "'" . "축구" ."',"  ;
		$sql = $sql . "'" . $league_num_temp . "'," ;  // '리그고유번호
		$sql = $sql . "'" . $leagueSn . "'," ;    // '리그번호
		$sql = $sql . "'" . $team1_name_temp . "'," ;        //'홈팀이름
		$sql = $sql . "'" . $team2_name_temp . "'," ;        //'원정팀이름
		$sql = $sql . "'" . $game_date_temp . "'," ;   // '게임시간(날자)
		$sql = $sql . "'" . $game_hours_temp . "'," ;   //'게임시간(시)
		$sql = $sql . "'" . $game_minute_temp . "'," ;  // '게임시간(분)
		$sql = $sql . "'',".$kubun.",";
		$sql = $sql . "'" . $type . "',";
		$sql = $sql . "'" . $special . "'," ;
		$sql = $sql . "'0')";

		$childIdx = $this->db->exeSql($sql);

		if($childIdx>0)
		{
			$sql = "insert into tb_subchild (" ;
			$sql = $sql ." child_sn, betting_type, home_rate, draw_rate,away_rate)" ;
			$sql = $sql . " values("  ;
			$sql = $sql . "'" . $childIdx . "','". $gameType ."',";	//'베팅타입
			$sql = $sql . "'" . $rate1_temp . "',";
			$sql = $sql . "'" . $rate2_temp . "',";
			$sql = $sql . "'" . $rate3_temp . "')";
			$this->db->exeSql($sql);
		}
	}

	function modifyGameTime($childSn)
	{
		$nowDate=date('Y-m-d');
		$nowHour=date('H');
		$nowTime=date('i');

		$sql = "update tb_child set gameDate = '".$nowDate."', gameHour='".$nowHour."', gameTime='".$nowTime."' where sn in (".$childSn.")";
		$this->db->exeSql($sql);
	}

    function getChangeLogIdx($type=0)
    {
        $sql = "select * from tb_get_change_log where type=$type order by idx desc limit 1 ";
        $rs = $this->db->exeSql($sql);

        return $rs;
    }

    public function getChangeGames($url, $type=0)
    {
        //$gameModel = $this->getModel("GameModel");
        // 마지막 업데이트된 키 찾기

        $rs = $this->getChangeLogIdx($type);
        if( sizeof($rs) > 0 )
        {
            $idx = $rs[0]['last_idx'];
        } else {
            $idx = 0;
        }

        $url = $url."?idx=".$idx."&type=".$type;
        $ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);

		$data = curl_exec($ch);
		$err = curl_error($ch);

        if($data!==false)
        {
            $this->insertChangeLog($data, $type);
        }
    }

    function insertChangeLog($data, $type=0)
    {
        $arrData = json_decode($data,true);
        //$table = ($type == 0) ? 'tb_child_comming_log' : 'tb_subchild_comming_log';

        for($i=0; $i<sizeof($arrData); $i++)
        {
            $rowData = $arrData[$i];
            if($type==0)
            {
                $sql = "insert into tb_child_comming_log (state, sn, parent_sn, sport_name,league_sn,home_team,home_score,away_team,away_score,win_team,handi_winner,gameDate,gameHour,gameTime,notice,kubun,type,special,is_specified_special, regdate) ";
			    $sql = $sql." values (0, "
                    .($rowData['sn']? $rowData['sn'] : "null").", "
                    .($rowData['parent_sn']?$rowData['parent_sn'] : "0").", '"
                    .($rowData['sport_name'])."', "
                    .($rowData['league_sn']!==null?$rowData['league_sn'] : "null").", '"
                    .($rowData['home_team'])."', "
                    .($rowData['home_score']!==null?$rowData['home_score'] : "null").", '"
                    .($rowData['away_team'])."', "
                    .($rowData['away_score']!==null?$rowData['away_score'] : "null").", "
                    .($rowData['win_team']?"'".$rowData['win_team']."'" : "null").", "
                    .($rowData['handi_winner']?"'".$rowData['handi_winner']."'" : "null").", "
                    .($rowData['gameDate']?"'".$rowData['gameDate']."'" : "null").", "
                    .($rowData['gameHour']?"'".$rowData['gameHour']."'" : "null").", "
                    .($rowData['gameTime']?"'".$rowData['gameTime']."'" : "null").", "
                    .($rowData['notice']?"'".$rowData['notice']."'" : "null").", "
                    .($rowData['kubun']!==null?$rowData['kubun'] : "null").", "
                    .($rowData['type']!==null?$rowData['type'] : "null").", "
                    .($rowData['special']!==null?$rowData['special'] : "0").", "
                    .($rowData['is_specified_special']?$rowData['is_specified_special'] : "0").", '"
                    .$rowData['regdate']."' ) ";
                $rs = $this->db->exeSql($sql);
                //echo $sql."<br/>";
            }
            else if($type==1)
            {
                $sql = "insert into tb_subchild_comming_log (state, sn, child_sn, betting_type, home_rate, draw_rate, away_rate, win, result, regdate) ";
				$sql = $sql." values (0, "
                    .$rowData['sn'].", "
                    .$rowData['child_sn'].", "
                    .($rowData['betting_type']?$rowData['betting_type'] : "null").", "
                    .($rowData['home_rate']?"'".$rowData['home_rate']."'" : "null").", "
                    .($rowData['draw_rate']?"'".$rowData['draw_rate']."'" : "null").", "
                    .($rowData['away_rate']?"'".$rowData['away_rate']."'" : "null").", "
                    .($rowData['win']!==null?$rowData['win'] : "null").", "
                    .($rowData['result']!==null?$rowData['result'] : "null").", '"
                    .$rowData['regdate']."' ) ";
                $rs = $this->db->exeSql($sql);
            }

        }
        $last_idx = $rowData['idx'];

        if(sizeof($arrData)>0)
        {
            $sql = "insert into tb_get_change_log (last_idx, type) values ($last_idx, $type)";
            $rs = $this->db->exeSql($sql);
        }
    }

    public function applyChildLog()
    {
        $sql = "select * from tb_child_comming_log where state=0 order by idx asc ";
        $rs = $this->db->exeSql($sql);

        if(sizeof($rs) > 0)
        {
            for($i=0; $i < sizeof($rs); $i++)
            {
                $log = $rs[$i];
                $ini = $this->getChildIni($log['sn'], $log);

                $this->updateChildFromLog($log, $ini);
            }
        }
    }

    public function applySubChildLog()
    {
        $sql = "select * from tb_subchild_comming_log where state=0 order by idx asc ";
        $rs = $this->db->exeSql($sql);

        if(sizeof($rs) > 0)
        {
            for($i=0; $i < sizeof($rs); $i++)
            {
                $log = $rs[$i];
                $ini = $this->getChildIni($log['child_sn']);
                if( $ini != false )
                {
                    $this->updateSubChildFromLog($log, $ini);
                }
                else
                {
                    /*
                    $sql = "select * from tb_child_comming_log where state=0 and win_team = null and handi_winner = null";
                    $rs2 = $this->db->exeSql($sql);

                    if(sizeof($rs2) == 0)
                    {
                        // 경기정보 없음
                        // 처리시켜 더이상 연동하지않음
                        $sql = "update tb_subchild_comming_log set state = 1 where idx = ".$log['idx'];
                        $this->db->exeSql($sql);
                    }
                    */
                }

            }
        }
    }

    public function getChildIni($orignalSn, $log='')
    {
        $sql = "select * from tb_child_comming_ini where orignal_sn = $orignalSn ";
        $rs = $this->db->exeSql($sql);


        if( sizeof($rs) == 0 && $log != '')
        {
            // 아직 ini 파일 생성안됨
            // child 와 subchild 먼저 생성
            $subChildSn = $this->addChild($log['parent_sn'], $log['sport_name'], $log['leagueSn']
                , $log['homeTeam'], $log['awayTeam'], $log['gameDate'], $log['gameHour']
                , $log['gameTime'], $log['notice'], ($log['kubun']!==null?$log['kubun']:'null'), $log['type']
                , $log['special'], $log['homeRate'], $log['drawRate'],$log['awayRate']
                , $log['is_specified_special']);

            return $this->insertChildIni($orignalSn, $subChildSn);
        }
        else if( sizeof($rs) > 0 )
        {
            return $rs[0];
        }

        return false;
    }

    public function insertChildIni($orignalSn, $subChildSn)
    {
    	$configModel = Lemon_Instance::getObject("ConfigModel",true);
    	$leagueModel = Lemon_Instance::getObject("LeagueModel",true);
        // 생성된 child와 subchild 정보 가져오기
        $subchildSql = "select * from tb_subchild where sn = $subChildSn";
        $subchild = $this->db->exeSql($subchildSql);

        $childSql = "select * from tb_child where sn = ".$subchild[0]['child_sn'];
        $child = $this->db->exeSql($childSql);

        // 기본 환수율 정보 가져온다
        $changeRate = $configModel->getChangeRateConfigByTypeSpecial($child[0]['type'], $child[0]['special']);
        $leagueInfo = $leagueModel->getListBySn($child[0]['league_sn']);

        if($leagueInfo['view_style'] != 5)
        {
        	// 정책정보 ini 생성
	        $sql = "insert into tb_child_comming_ini (sn, orignal_sn, state, allow_rate_change, allow_betting_auto, allow_magam_auto, allow_base_change, add_home_rate, add_draw_rate, add_away_rate) values ("
	            .$child[0]['sn'].", ".$orignalSn.", 1, '".$changeRate['allow_rate_change']."', '".$changeRate['allow_betting_auto']."', '".$changeRate['allow_magam_auto']."', '".$changeRate['allow_base_change']."', ".$changeRate['home_rate'].", ".$changeRate['draw_rate'].", ".$changeRate['away_rate'].")";
        }
        else
        {
        	// 공지사항 경기정보
	        $sql = "insert into tb_child_comming_ini (sn, orignal_sn, state, allow_rate_change, allow_betting_auto, allow_magam_auto, allow_base_change) values ("
	            .$child[0]['sn'].", ".$orignalSn.", 1, 'Y', 'Y', 'Y', 'Y')";
        }

        $rs = $this->db->exeSql($sql);

        $sql = "select * from tb_child_comming_ini where idx = $rs";
        $rs = $this->db->exeSql($sql);
        return $rs[0];
    }

    public function updateChildFromLog($log, $ini)
    {
        $sql = "select * from tb_child where sn = ".$ini['sn'];
        $rs = $this->db->exeSql($sql);
        //echo var_dump($ini);
        if(sizeof($rs)>0)
        {
            $child = $rs[0];
            if($ini['state']==1 && $child['kubun'] != 1 && ($log['win_team'] === null && $log['handi_winner'] === null) && $log['kubun'] != 1 )
            {
                if( $ini['allow_betting_auto'] == 'Y' || !($child['kubun'] === null && $log['kubun'] === 0 ) )
                {
                    $updateSql = "update tb_child set sport_name = '".$log['sport_name']."', league_sn = ".$log['league_sn']
                            .", home_team = '".$log['home_team']."', away_team = '".$log['away_team']."', "
                            ." gameDate = '".$log['gameDate']."', gameHour = '".$log['gameHour']."', "
                            ." gameTime = '".$log['gameTime']."',  ";


					if($ini['allow_betting_auto'] == 'Y'){
						 $updateSql .= "  kubun = ".( $log['kubun']!==null?$log['kubun']:'null').", " ;
					}

                     $updateSql .= " type = ".$log['type'].", special = ".$log['special'].", "
                            ." is_specified_special = ".$log['is_specified_special']." where sn = ".$ini['sn'];

                    $this->db->exeSql($updateSql);

                    //echo $updateSql;
                }


            }

            $sql = "update tb_child_comming_log set state = 1 where idx = ".$log['idx'];
            $this->db->exeSql($sql);

        }
        else if($ini['state']==-1)
        {
        	$sql = "update tb_child_comming_log set state = 5 where idx = ".$log['idx'];
        	//echo $sql."<br/>";
            $this->db->exeSql($sql);
        }

        //echo $log['idx']." ".sizeof($rs).", ";
    }

    public function updateSubChildFromLog($log, $ini)
    {

        $sql = "select * from tb_subchild where child_sn = ".$ini['sn'];
        $rs = $this->db->exeSql($sql);
        $sql2 = "select * from tb_child where sn = ".$ini['sn'];
        $rs2 = $this->db->exeSql($sql2);

        if(sizeof($rs)>0)
        {
            $subchild = $rs[0];
            $child = $rs2[0];
            if($ini['state']==1 && $child['kubun'] != 1 && $log['win'] === null && $log['result'] === null )
            {
                // 배당 업데이트
                if($ini['allow_rate_change'] == 'Y')
                {
                    if( $child['special'] >= 0 && $child['special'] <= 2 )
                    {
                        // 환수율 적용
                        $home_rate = $log['home_rate'] ? $log['home_rate'] + $ini['add_home_rate'] : null;
                        $away_rate = $log['away_rate'] ? $log['away_rate'] + $ini['add_away_rate'] : null;

                        if( ($child['type'] == 2 || $child['type'] == 4) )
                        {
                            if( $ini['allow_base_change'] == 'Y' )
                            {
                                $draw_rate = $log['draw_rate'] ? $log['draw_rate'] + $ini['add_draw_rate'] : null;
                            }
                            else
                            {
                                $draw_rate = $subchild['draw_rate'];
                            }

                        }
                        else if( $log['draw_rate'] != 1)
                        {
                            $draw_rate = $log['draw_rate'] ? $log['draw_rate'] + $ini['add_draw_rate'] : null;
                        }
                        else
                        {
                        	$draw_rate = 1;
                        }

                        $update = "update tb_subchild set home_rate = ".($home_rate?"'".$home_rate."'":'null').", "
                                ."draw_rate = ".($draw_rate?"'".$draw_rate."'":'null').", "
                                ."away_rate = ".($away_rate?"'".$away_rate."'":'null')." where child_sn = ".$ini['sn'];;


                        //echo $update;

                        $this->db->exeSql($update);
                    }
                }

                $sql = "update tb_subchild_comming_log set state = 1 where idx = ".$log['idx'];
                $this->db->exeSql($sql);

            }
            else if($ini['state']==1 && $child['kubun'] != 1 && $log['result'] == -1 )
            {
                // 삭제 로그
                // todo : 이미 베팅이 되어있다면 삭제하면 안된다.
                if( $ini['allow_magam_auto'] == 'Y' )
                {
                    $delSql = "update tb_subchild_comming_log set state = 5 where idx = ".$log['idx'];
                    $this->db->exeSql($delSql);
                    /*
                    $delSql = "delete from tb_child_comming_log where sn = ".$log['child_sn'];
                    $this->db->exeSql($delSql);
                    */
                    $delSql = "update tb_child_comming_ini set state=-1 where orignal_sn = ".$log['child_sn'];
                    $this->db->exeSql($delSql);
                    $delSql = "delete from tb_child where sn = ".$ini['sn'];
                    $this->db->exeSql($delSql);
                    $delSql = "delete from tb_subchild where child_sn = ".$ini['sn'];
                    $this->db->exeSql($delSql);
                }
                else
                {
                    $sql = "update tb_subchild_comming_log set state = 1 where idx = ".$log['idx'];
                    $this->db->exeSql($sql);
                }

            }
            else if($ini['state']==1 && $child['kubun'] != 1 && $log['win'] !== null )
            {
                if( $log['result'] !== null)
                {
                    // 처리됨으로 표시
                    $sql = "update tb_subchild_comming_log set state = 1 where idx = ".$log['idx'];
                    $this->db->exeSql($sql);
                    return;
                }
                // 마감처리
                $processModel = Lemon_Instance::getObject("ProcessModel", true);
                if( $ini['allow_magam_auto'] == 'Y' )
                {
                    $sql = "select * from tb_child_comming_log where sn = ".$ini['orignal_sn']
                            ." and state = 1 and kubun=1";
                    $rs3 = $this->db->exeSql($sql);

                    $sql = "select * from tb_subchild_comming_log where child_sn = ".$ini['orignal_sn']
                            ." and state = 0 and win = null";
                    $rs4 = $this->db->exeSql($sql);
                    if( sizeof($rs3) > 0 && sizeof($rs4) == 0 )
                    {
                        //echo "magam : ".var_dump($rs3)."\n";
                        // 마감처리된 로그까지 모두 처리된 상태"
                        if( $log['win'] == 4 && $log['betting_type'] == 1)
                        {
                            $processModel->resultGameProcess($child['sn'], $rs3[0]['home_score'], $rs3[0]['away_score'], 1);
                        } else
                        {
                            $processModel->resultGameProcess($child['sn'], $rs3[0]['home_score'], $rs3[0]['away_score']);
                        }

                        $sql = "update tb_subchild_comming_log set state = 1 where idx = ".$log['idx'];
                        $this->db->exeSql($sql);
                    }
                }
                else
                {
                    $sql = "update tb_subchild_comming_log set state = 1 where idx = ".$log['idx'];
                    $this->db->exeSql($sql);
                }
            }
            else if($ini['state'] != 1 || $child['kubun'] == 1)
            {
                // 변경 로그 적용 처리
                $sql = "update tb_subchild_comming_log set state = 1 where idx = ".$log['idx'];
                $this->db->exeSql($sql);
            }
        }
        else if($ini['state']==-1)
        {
        	$sql = "update tb_subchild_comming_log set state = 5 where idx = ".$log['idx'];
            $this->db->exeSql($sql);
        }
    }

    public function getGameIni($idx)
    {
        $sql = "select * from tb_child_comming_ini where sn = $idx ";
        $rs = $this->db->exeSql($sql);


        return $rs[0];
    }

    public function modifyGameIni($idx, $allow_rate_change, $allow_betting_auto, $allow_magam_auto, $allow_base_change='Y', $add_home_rate=0, $add_draw_rate=0, $add_away_rate=0 )
    {
        $sql = "update tb_child_comming_ini set allow_rate_change='$allow_rate_change', "
                ."allow_betting_auto='$allow_betting_auto', "
                ."allow_magam_auto='$allow_magam_auto', "
                ."allow_base_change='$allow_base_change', "
                ."add_home_rate=$add_home_rate, "
                ."add_draw_rate=$add_draw_rate, "
                ."add_away_rate=$add_away_rate "
                ."where sn = $idx ";
        $this->db->exeSql($sql);
    }

    public function modifyGameIniEdited($idx, $allow_rate_change, $allow_betting_auto, $allow_magam_auto, $allow_base_change='Y', $add_home_rate=0, $add_draw_rate=0, $add_away_rate=0 )
    {
        $sql = "update tb_child_comming_ini set allow_rate_change='$allow_rate_change', "
                ."allow_betting_auto='$allow_betting_auto', "
                ."allow_magam_auto='$allow_magam_auto', "
                ."allow_base_change='$allow_base_change', "
                ."add_home_rate=$add_home_rate, "
                ."add_draw_rate=$add_draw_rate, "
                ."add_away_rate=$add_away_rate, "
                ."edited='Y' "
                ."where sn = $idx ";
        $this->db->exeSql($sql);
    }

    public function getLastSubChildLog($idx)
    {
        $sql = "select * from tb_subchild_comming_log where child_sn = $idx order by idx desc limit 1";
        $rs = $this->db->exeSql($sql);

        return $rs[0];
    }
}
?>
