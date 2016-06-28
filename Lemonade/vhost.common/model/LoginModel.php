<?php
class LoginModel extends Lemon_Model
{
	//▶ 파트너 로그인
	function Partner_login($id, $passwd)
	{
		$sql = "select * from tb_recommend
						where binary rec_id = '".$id."' and rec_psw='".$passwd."'";

		$rs =  $this->db->exeSql($sql);

		/*
		 * returun code
		 * 0:failed, 1:success, 2:ready user 4:stop user
		*/

		if( sizeof($rs) > 0 )
		{
			if( $rs[0]["status"] == 1 )
			{
				$_SESSION['member']['id']		 			= $rs[0]['rec_id'];
				$_SESSION['member']['sn']		 			= $rs[0]['Idx'];
				$_SESSION['member']['parent_sn'] 	= $rs[0]['parent_sn'];
				$_SESSION['member']['name']		 		= $rs[0]['rec_name'];
				$_SESSION['member']['level']	 		= $rs[0]['rec_lev'];
				$_SESSION['member']['rate']		 		= $rs[0]['rec_rate'];
			}
			return $rs[0]["status"];
		}

		return 0;
	}

	function loginMember($id, $passwd)
	{
		$remoteip = $_SERVER["HTTP_X_FORWARDED_FOR"];

		if(!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',$remoteip))
		{
			return 7;
		}

		$passwd = trim($passwd);

		$sql = "select count(*) as cnt from sql_sqlin where kill_ip='true' and SqlIn_IP='".$remoteip."'";
		$rs = $this->db->exeSql($sql);

		/*
		 * returun code
		 * 0:failed, 1:success, 2:ip troble 3:new member, 4:stop user
		*/
		if($rs[0]['cnt']!=0)
		{
			return 2;
		}

		$sql = "select count(*) as cnt from tb_member
				where logo='".$this->logo."' and binary uid = '".mysql_real_escape_string($id)."'";

		$rs = $this->db->exeSql($sql);

		if($rs[0]['cnt']<=0)
		{
			$result = "아이디 틀림";

			$sql = "insert into tb_visit(member_id,visit_ip,visit_date,result,status,logo)
					values('".$id."','".$remoteip."',now(),'".$result."','1','".$this->logo."')";

			$this->db->exeSql($sql);
			return 0;
		}

		$sql = "select * from tb_member where logo='".$this->logo."' and binary uid = '".mysql_real_escape_string($id)."'
						and upass = '".mysql_real_escape_string($passwd)."'";

		$rs = $this->db->exeSql($sql);

		if($rs[0]['uid']!='')
		{
			if($rs[0]['mem_status']=='S')
			{
				return 4;
			}
			else if($rs[0]['mem_status']=='D')
			{
				return 5;
			}
			else if($rs[0]['mem_status']=='W')
			{
				return 3;
			}
			else // login success
			{
				$result = "로그인 성공(".$rs[0]['mem_status'].")";

				$_SESSION['member']['id']						= $rs[0]['uid'];
				$_SESSION['member']['sn']						= $rs[0]['sn'];
				$_SESSION['member']['name']					= $rs[0]['nick'];
				$_SESSION['member']['level']				= $rs[0]['mem_lev'];
				$_SESSION['member']['recommender']	= $rs[0]['recommend_sn'];
				$_SESSION['member']['state']				= $rs[0]['mem_status'];

				$config = Lemon_Configure::readConfig('config');
				$_SESSION['conf'] = $config ;

				$sql = "update tb_member
								set last_date = now(), sessionid='".session_id()."',
										login_domain='".$_SERVER['HTTP_HOST']."',
										mem_ip='".$remoteip."'
								where logo='".$this->logo."' and uid = '".$id."' ";
				$this->db->exeSql($sql);

				if($id!="nadia" && $id!="nadia1")
				{
					$sql = "insert into tb_visit(member_id,visit_ip,visit_date,result,status,logo) values('".$id."','".$remoteip."',now(),'".$result."','0','".$this->logo."')";
					$this->db->exeSql($sql);
				}

				return 1;
			}
		}
		else
		{
			$result = "비밀번호 틀림";

			$sql = "insert into tb_visit(member_id,visit_ip,visit_date,result,status,logo)
					values('".$id."','".$remoteip."',now(),'".$result."','1','".$this->logo."')";

			$this->db->exeSql($sql);
		}
		return 0;
	}

	//▶ 로그인
	function login($uid, $passwd, $inputPwd, $ip)
	{
		$sql = "select *
						from tb_head
						where logo='".$this->logo."' and binary head_id = '".$uid."' and head_pw='".$passwd."'";

		$rs = $this->db->exeSql($sql);

		if(sizeof($rs)>0)
		{
			$_SESSION["quanxian"] = $rs[0]["part_num"];

			$sql = "update tb_head
							set loginnum=loginnum+1 ,lastlogintime=now(),lastloginip='".$ip."' where logo='".$this->logo."' and  head_id='".$uid."'";

			$this->db->exeSql($sql);

			//if($uid!="tadmin18")
			if( 1== 1)
			{
				$sql = "insert into tb_admin_log (admin_id,login_ip,login_date,result,status,logo) values ('".$uid."','".$ip."',now(),'로그인 성공','0','".$this->logo."')";
				$this->db->exeSql($sql);
			}

			$_SESSION["member"]["id"] = $uid;
			$_SESSION["member"]["sn"]	= $rs[0]['idx'];

			return true;
		}
		else
		{
			$sql = "insert into tb_admin_log (admin_id,admin_pw,login_ip,login_date,result,status,logo)
				values ('".$uid."','".$inputPwd."','".$ip."',now(),'로그인 실패','1','".$this->logo."')";
			$this->db->exeSql($sql);
			return false;
		}
	}

	//▶ 로그인 로그 목록
	function getList($where, $page, $page_size)
	{
		$eModel = Lemon_Instance::getObject("EtcModel",true);

		$sql = "select a.logo, a.sn as aidx, a.nick,a.mem_lev,a.g_money, a.login_domain, a.bank_member, (select rec_id from tb_recommend where Idx=a.recommend_sn) as recommend_id, b.member_id,b.idx,b.visit_date,b.visit_ip,b.result,b.status
						from tb_member a right outer join tb_visit b on a.uid=b.member_id
						where a.mem_status<>'G'".$where." order by b.visit_date desc  limit ".$page.",".$page_size ;

		$rs = $this->db->exeSql($sql);

		$searchArray  = array();
		$blackIpArray = array();

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$ip  = $rs[$i]['visit_ip'];
			$memberSn = $rs[$i]['aidx'];

			if($searchArray[$ip][0]!='')
			{
				if($searchArray[$ip][0]!=$memberSn)
				{
					$blackIpArray[]=$ip;
				}
			}
			else
			{
				$searchArray[$ip][0]=$memberSn;
			}

			$rs[$i]['country_code'] = $eModel->getNationByIp($ip);
		}

		for($i=0; $i<sizeof($rs); ++$i)
		{
			if(strlen(array_search(trim($rs[$i]['visit_ip']), $blackIpArray))>0) {$rs[$i]['duplicate_ip']=1;}
			else	$rs[$i]['duplicate_ip']=0;
		}

		return $rs;
	}

	//▶ 로그인 로그 총합
	function getTotal($where)
	{
		$sql = "select count(*) as cnt
				from tb_member a right outer join tb_visit b on a.uid=b.member_id
					where 1=1 ".$where ;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 로그인 로그 삭제
	function del($sn)
	{
		$sql = "delete from tb_visit where idx in(".$sn.")";

		return $this->db->exeSql($sql);
	}

	//▶ 관리자 로그인 로그 총합
	function getAdminLoginTotal($where)
	{
		$sql = "select count(*) as cnt
				from tb_admin_log where logo='".$this->logo."' ".$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 관리자 로그인 로그 목록
	function getAdminLogList($where, $page, $page_size)
	{
		$eModel = Lemon_Instance::getObject("EtcModel",true);

		$sql = "select *
				from tb_admin_log
					where logo='".$this->logo."' ".$where."
						order by login_date desc limit ".$page.",".$page_size;

		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$ip  = $rs[$i]['login_ip'];
			$rs[$i]['country_code'] = $eModel->getNationByIp($ip);
		}

		return $rs;;
	}
}
?>
