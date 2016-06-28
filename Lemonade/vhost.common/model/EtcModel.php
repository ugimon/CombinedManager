<?php


class EtcModel extends Lemon_Model
{
	function getLogoList()
	{
		$sql = "select * from logolist order by sn";

		return $this->db->exeSql($sql);
	}

	//▶ 탑메뉴 리플레시
	function getPartnerRefresh($id)
	{
		$array = array();
		$sql = "select count(*)as tot_cnt,newreadnum
							from ".$this->db_qz."memoboard
								where kubun=1 and isdelete!=1 and logo='".$this->logo."' and toid='".$id."'
									group by newreadnum";

		$rs = $this->db->exeSql($sql);

		if($rs[0]["newreadnum"]==1)
		{
			$newreadnum = $rs[0]["tot_cnt"];
		}else if($rs[0]["newreadnum"]==0)
		{
			$newweinum = $rs[0]["tot_cnt"];
		}

		if(is_null($newreadnum) || $newreadnum=="")
		{
			$newreadnum=0;
		}
		if(is_null($newweinum) || $newweinum=="")
		{
			$newweinum=0;
		}

		$total = $newweinum+$newreadnum;

		$array[]=$id."@@@".$newweinum."@@@".$total;

		echo $array[0];

	}

	//▶ 탑메뉴 리플레시
	function getRefresh()
	{
		$array = array();

		//charge count
		$sql = "select is_read from ".$this->db_qz."charge_log where state=0";
		$rs = $this->db->exeSql($sql);
		$charge 			= sizeof($rs);
		$chargeAlarm 	= 0;

		for($i=0; $i < $charge; ++$i)
		{
			if($rs[$i]['is_read']==0)
			{
				$chargeAlarm = 1; break;
			}
		}

		//exchange count
		$sql = "select is_read from ".$this->db_qz."exchange_log where state=0";
		$rs = $this->db->exeSql($sql);
		$exchange = sizeof($rs);
		$exchangeAlarm 	= 0;
		for($i=0; $i < $exchange; ++$i)
		{
			if($rs[$i]['is_read']==0)
			{
				$exchangeAlarm = 1; break;
			}
		}

		//board
		$sql = "select count(ref) as ref  from ".$this->db_qz."board
						where hit < 3 ";
		$rs = $this->db->exeSql($sql);
		$board = $rs[0]['ref'];

		//question
		$sql = "select idx, is_read from ".$this->db_qz."question
						where result='0' and reply='0' ";
		$rs = $this->db->exeSql($sql);
		$question = sizeof($rs);
		$questionAlarm 	= 0;
		for($i=0; $i < $question; ++$i)
		{
			if($rs[$i]['is_read']==0)
			{
				$questionAlarm = 1; break;
			}
		}
		$questionSn = $rs[0]['idx'];

		//new member
		$sql = "select count(sn) as cnt
				from ".$this->db_qz."member
					where mem_status = 'W' and date(regdate)=date(now()) ";
		$rs = $this->db->exeSql($sql);
		$new_member = $rs[0]['cnt'];

		$sql = "select * from ".$this->db_qz."alarm_flag";
		$rs = $this->db->exeSql($sql);
		$new_memberAlarm = $rs[0]['new_member'];

		//new memo
		$sql = "select count(mem_idx) as idx
				from ".$this->db_qz."memoboard
					where newreadnum<>1 AND toid='운영팀'";
		$rs = $this->db->exeSql($sql);
		$new_memo = $rs[0]['idx'];

		//total memo
		$sql = "select count(mem_idx) as idx from ".$this->db_qz."memoboard
						where toid='운영팀'";
		$rs = $this->db->exeSql($sql);
		$total_memo = $rs[0]['idx'];

		// recommender
		$sql = "select count(*) as idx from ".$this->db_qz."recommend
						where  status=2";

		$rs = $this->db->exeSql($sql);
		$recommend = $rs[0]['idx'];

		//라이브 데몬 실행시간
		$sql = "select TIMESTAMPDIFF(SECOND, main_bets_access_timer, SYSDATE()) as diff from ".$this->db_qz."live_daemon_state";
		$rs = $this->db->exeSql($sql);
		$live_flag = "ON";


		if($rs[0]['diff'] >=35)
			$live_flag = "OFF";

		$rs = array();
		$rs[]=$charge."||".$chargeAlarm."||".$exchange."||".$exchangeAlarm."||".$board."||".$question."||".$new_member."||".$new_memo."||".$total_memo."||".$recommend."||".$questionAlarm."||".$questionSn."||".$new_memberAlarm."||".$live_flag;

		echo $rs[0];
	}

	//▶ 아이피를 통한 국가명 검색
	function getNationByIp($ip)
	{
		$sql = "select country_code
				from ".$this->db_qz."ip_group_country
					where ip_start <= INET_ATON( '".$ip."' )
						order by ip_start desc limit 1 ";

		$rs = $this->db->exeSql($sql);
		return $rs[0]['country_code'];
	}

	//▶ 레벨 정보
	function getLevel()
	{
		$sql = "select lev, lev_name from ".$this->db_qz."level_config";

		return $this->db->exeSql($sql);
	}

	//▶ 레벨 정보
	function getLevel2($logo)
	{
		$sql = "select lev, lev_name from ".$this->db_qz."level_config where logo = '".$logo."'";

		return $this->db->exeSql($sql);
	}

	function getLevelName($level)
	{
		$sql = "select lev_name from ".$this->db_qz."level_config
					where lev=".$level;
		$rs = $this->db->exeSql($sql);

		return $rs[0]['lev_name'];
	}

	//▶ 멤버레벨
	function getMemberLevRow($level, $field)
	{
		$where = "lev=".$level;

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRow($field, $this->db_qz.'level_config', $where);
	}

	//▶ 레벨 설정 변경
	function modifyLevelConfig($value, $levelName, $levelMin, $levelMax, $levelBonus)
	{
		$sql = "update ".$this->db_qz."level_config
				set lev_name='".$levelName."',lev_min_money='".$levelMin."',lev_max_money='".$levelMax."',lev_max_bouns='".$levelBonus."'
					where id='".$value."' and logo='".$lthis->ogo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 유저 접속횟수
	function visitCount($memberId)
	{
		$sql = "select count(*) as cnt from ".$this->db_qz."visit where member_id='".$memberId."'";
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 아이피 차단해제
	function revokeIp($ip)
	{
		$sql = "update sql_sqlin
				set  Kill_ip=NULL
					where SqlIn_IP='".$ip."' and Kill_ip='true'";
		return $this->db->exeSql($sql);
	}

	//▶ 아이피 차단
	function killIp($ip, $web)
	{
		$sql = "insert into sql_sqlin (SqlIn_IP,SqlIn_WEB,SqlIn_TIME,Kill_ip)
				values ('".trim($ip)."','".htmlspecialchars($web)."',now(),'true')";
		return $this->db->exeSql($sql);
	}

	//▶ 차단아이피 총합
	function killIpTotal()
	{
		$sql = "select count(*) as cnt
				from sql_sqlin
					where Kill_ip='true'";
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 차단아이피 목록
	function killIpList($page, $page_size)
	{
		$sql = "select *
				from sql_sqlin
					where Kill_ip='true' order by SqlIn_TIME desc limit ".$page.",".$page_size;
		return $this->db->exeSql($sql);
	}

	function isKillIp($remoteIp)
	{
		$sql = "select count(id) as cnt1 from sql_sqlin where kill_ip='true' and SqlIn_IP='".$remoteIp."'";
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt1'];
	}

	//▶ 관리자 암호
	function getAdminPasswd($password)
	{
		$sql = "select *
				from ".$this->db_qz."head
					where head_pw=md5('".$password."') and head_id='".$_SESSION["member"]["id"]."' and logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 관리자 암호변경
	function modifyAdminPasswd($newPasswd)
	{
		$sql = "update ".$this->db_qz."head
				set head_pw=md5('".$newPasswd."')
					where head_id='".$_SESSION["member"]["id"]."' and logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 유저잭팟 초기화
	function clearJackpot()
	{
		$sql = "update ".$this->db_qz."member
						set jackpot_num=0
						where logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	function getPopup()
	{
		$today = date("Y-m-d");

		$sql = "select *
						from ".$this->db_qz."popup
						where logo='".$this->logo."'
						and P_POPUP_U='Y'
						and P_STARTDAY <= '".$today."' and P_ENDDAY >='".$today."' ";

		return $this->db->exeSql($sql);
	}

    function getPopupBySn($sn)
	{
		$today = date("Y-m-d");

		$sql = "select *
						from ".$this->db_qz."popup
						where logo='".$this->logo."'
						and P_POPUP_U='Y' and IDX=".$sn."
						and P_STARTDAY <= '".$today."' and P_ENDDAY >='".$today."' ";

		return $this->db->exeSql($sql);
	}

	function getPhoneJoinMember($state='REQ', $where='')
	{
		$sql = "select * from ".$this->db_qz."phone_join_member where state='".$state."' ".$where." order by regdate desc";
		return $this->db->exeSql($sql);
	}

	function updatePhoneJoinMember($phone)
	{
		$data['state'] = 'FIN';
		$data['operdate'] = "SYSDATE()";

		$this->db->setUpdate($this->db_qz."phone_join_member", $data, "phone='".$phone."'");
		return $this->db->exeSql();
	}

	// tb_admin_ip tables function
	function is_admin_ip($ip)
	{
		$sql = "select count(*) as cnt from tb_admin_ip where ip='$ip' ";
		$rows = $this->db->exeSql($sql);

		if($rows[0]['cnt'] > 0)
			return 1;

		return 0;
	}

	function admin_ip_listup()
	{
		$sql = "select * from tb_admin_ip ";
		$rows = $this->db->exeSql($sql);

		return $rows;
	}

	function insert_admin_ip($ip)
	{
		$data['ip'] = $ip;
		$this->db->setInsert("tb_admin_ip", $data);
		return $this->db->exeSql();
	}

	function modify_admin_ip($sn, $ip)
	{
		$data['ip'] = $ip;
		$this->db->setUpdate("tb_admin_ip", $data, "sn=$sn");
		return $this->db->exeSql();
	}

	function delete_admin_ip($sn)
	{
		$this->db->setDelete("tb_admin_ip", "sn=$sn");
		return $this->db->exeSql();
	}
}
?>
