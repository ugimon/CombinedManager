<?php
class HackingLogModel extends Lemon_Model 
{
	//▶ 유저 통계
	function insert($userid, $memo, $query="")
	{
		$remote_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		
		if(!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',$remote_ip))
		{
			session_destroy();
			header("Location:http://www.google.com");
			exit;
		}
		
		$data['userid'] = $userid;
		$data['memo'] = $memo;
		$data['ip'] = $remote_ip;
		$data['query'] = $query;
		$data['regdate'] = "SYSDATE()";
		
		$this->db->setInsert($this->db_qz."hacking_debug_log", $data);
		
		$result = $this->db->exeSql();
		
		//아이피 차단
		//$this->kill_ip($remote_ip, $memo);
		//$this->kill_session();
		
		return $result;
	}
	
	function kill_session()
	{
		session_destroy();
		header("Location:http://www.google.com");
		exit;
	}
	
	function kill_ip($ip, $web)
	{
		$data['SqlIn_IP'] = trim($ip);
		$data['SqlIn_WEB'] = htmlspecialchars($web);
		$data['SqlIn_TIME'] = "SYSDATE()";
		$data['Kill_ip'] = "true";
		
		$this->db->setInsert("sql_sqlin", $data);
		
		return $this->db->exeSql();
	}
	
	function is_block_ip($ip)
	{
		$sql = "select count(*) as cnt from sql_sqlin where kill_ip='true' and SqlIn_IP='".$ip."'";
		$rows = $this->db->exeSql($sql);
		if($rows[0]['cnt']> 0) 
			return 1;
		return 0;
	}
	
	function injection_log($userid, $ip, $query)
	{
		$data['userid'] = $userid;
		$data['ip'] = $ip;
		$data['query'] = $query;
		$data['reg_time'] = "now()";
		
		$this->db->setInsert("tb_injection_log", $data);
		$rows = $this->db->exeSql();
	}
}

?>