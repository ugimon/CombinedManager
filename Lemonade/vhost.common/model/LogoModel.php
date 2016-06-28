<?php
class logoModel extends Lemon_Model
{
	//▶ 관리자 로그인 로그 목록
	function getList()
	{
		$sql = "select * from logo_list where isUse = 'Y' order by nick asc";

		$rs = $this->db->exeSql($sql);

		return $rs;
	}
}
?>
