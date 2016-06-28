<?php
// 용어구분
// recommend 	= 추천인
// partner		= 총판

class PartnerModel extends Lemon_Model
{
	//▶ 파트너의 정산비율 수정    
	function modifyRate($id,$rate)
	{
		$sql = "update tb_recommend 
							set rec_rate='".$rate."' 
								where logo='".$this->logo."' and rec_id='".$id."'";
		return $this->db->exeSql($sql);																		
	}
	//▶ 파트너멤버  추가     
	function addPartnerJoin($id, $name, $level, $password, $phone, $e_mail, $bank_name, $bank_num, $bank_username, $parentSn="", $logo="totobang",$default_rate,$nc_rate,$wb_rate,$sb_rate, $parent_rec_sn,$parent_rate,$parent_nc_rate,$parent_wb_rate,$parent_sb_rate,$rec_grd)
	{
		if($parentSn=="") $parentSn = "0";
		//수수료 항목 추가 2016.02.01
		$sql = "insert into tb_recommend 
						(rec_id, rec_name, rec_lev, rec_psw, rec_phone, rec_email, rec_rate, reg_date, rec_bankname, rec_banknum, rec_bankusername, status, parent_sn, logo  , default_rate,nc_rate,wb_rate,sb_rate , parent_rec_sn,parent_rate,parent_nc_rate,parent_wb_rate,parent_sb_rate,rec_grd) 
						values ('".$id."','".$name."',".$level.",'".md5($password)."','".$phone."','".$e_mail."','10', now(),'".$bank_name."','".$bank_num."','".$bank_username."','1',".$parentSn.",'".$logo."',".$default_rate.",".$nc_rate.",".$wb_rate.",".$sb_rate." , ".$parent_rec_sn .",".$parent_rate.",".$parent_nc_rate.",".$parent_wb_rate.",".$parent_sb_rate.",".$rec_grd.")";
					 
 		return $this->db->exeSql($sql);												
	}
	
	//▶ 추천등록된 횟수
	function getJoinRecommendCount($recommendSn)
	{
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		$sql = "select count(*) as cnt
						from tb_join_recommend 
						where recommend_sn='".$recommendSn."' and logo='".$this->logo."'";
					
		$rs = $this->db->exeSql($sql);
		
		return $rs[0]['cnt'];
	}
	
	//▶ 추천인 수입마일리지
	function getJoinRecommendBenefit($recommendSn)
	{
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		$sql = "select sum(amount) as benefit from tb_mileage_log
						where state=2 and member_sn=".$recommendSn;

		$rs = $this->db->exeSql($sql);
		
		return $rs[0]['benefit'];
	}
	
	//▶ 추천인 하위 데이터
	function getJoinRecommendSubList($recommendSn)
	{
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		$sql = "select a.*, b.nick,b.uid from tb_join_recommend a, tb_member b
						where a.member_sn=b.sn and a.recommend_sn=".$recommendSn;
					
		$rs = $this->db->exeSql($sql);
		
		$item = array();
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$item[$i]['sn']		= $rs[$i]['member_sn'];
			$item[$i]['uid'] 	= $rs[$i]['uid'];
			$item[$i]['nick'] = $rs[$i]['nick'];
			
			$sql = "select sum(amount) as benefit from tb_mileage_log
							where state=2 and member_sn=".$recommendSn." and status_message like('%".$item[$i]['uid']."%')";
			$rsi = $this->db->exeSql($sql);
			
			$item[$i]['benefit'] = $rsi[0]['benefit'];
		}
		
		return $item;
	}
	
	//▶ 파트너의 데이터 목록
	function getPartnerAdd()
	{
		$sql = "select partadd from tb_admin 
						where logo='".$this->logo."'";
							
		$rs =  $this->db->exeSql($sql);					
		
		return $rs[0]['partadd'];
	}
	
	//▶ 필드 데이터
	function getPartnerBySn($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		
		$sql = "select * from tb_recommend where Idx='".$sn."'";
		$rs =  $this->db->exeSql($sql);
		
		return $rs[0];
	}
	
	//▶ 필드 데이터
	function getPartnerById($uid)
	{
		$sql = "select * from tb_recommend where rec_id='".$uid."'";
		$rs =  $this->db->exeSql($sql);
		
		return $rs[0];
	}
	
	//▶ 파트너의 계좌 수정  
	function modifyChangeBank($bankname,$banknum,$bankusername,$sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		
		$sql = "update tb_recommend 
							set rec_bankname='".$bankname."',rec_banknum='".$banknum."',rec_bankusername='".$bankusername."' 
								where Idx='".$sn."'";
								
		return $this->db->exeSql($sql);						
	}
	
	//▶ 파트너의 패스워드 목록 
	function getPassword($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		
		$sql = "select rec_psw 
							from tb_recommend 
								where Idx='".$sn."'";
		$rs = $this->db->exeSql($sql);						
		
		return $rs[0]['rec_psw']; 
	}
	
	//▶ 파트너의 패스워드 변졍 
	function modifyChangePassword($sn,$password)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		
		$sql = "update tb_recommend set rec_psw='".md5($password)."' 
						where Idx='".$sn."'";
								
		return $this->db->exeSql($sql);
	}
	
	//▶ 파트너의 멤버
	function getPartnerMember($partnerSn)
	{
		$sql = "select 
						(select count(*) from tb_member b where a.idx=b.recommend_sn group by recommend_sn) as countmem,
						(select sum(g_money) from tb_member b where a.idx=b.recommend_sn) as countmoney,
						(select count(*) from tb_member b where a.idx=b.recommend_sn and b.regdate=date(now())) as countday 
							from tb_recommend a where rec_id='".$partnerSn."'";

		$rs = $this->db->exeSql($sql); 		
		
		return $rs[0];				
	}
	
	function getPartnerMemberListTotal($partner_sn, $where)
	{
		if( !$this->is_integer_mysql_parameter($partner_sn))
		{
			exit;
		}
		
		$sql = "select count(*) as cnt
						from tb_member  
						where recommend_sn='".$partner_sn."' ".$where;
						
		$rows = $this->db->exeSql($sql); 		
		return $rows[0]['cnt'];
	}
	
	//▶ 파트너의 멤버목록 
	//▶ 파트너의 멤버목록 
	function getPartnerInMemberList($partner_sn, $where="", $page=0, $page_size=0,$child_sn,$beginDate='',$endDate='',$ordType='a.regdate desc')
	{
		if( !$this->is_integer_mysql_parameter($partner_sn))
		{
			exit;
		}

 

		 
		if($child_sn>0 ) { //해당총판만 
			if($partner_sn == $child_sn) //총판 자기꺼
				$sql = "select sn, uid, nick, g_money, regdate, mem_ip, reg_ip, last_date, mem_status, mem_lev, bank_member,
							(select rec_id from tb_recommend where Idx=recommend_sn) as recommend_uid
							from tb_member a 
							where recommend_sn='".$partner_sn."' ".$where." order by ".$ordType; //
							//order by regdate desc ";

			else //총판의 하위총판꺼
				$sql = "select sn, uid, nick, g_money, regdate, mem_ip, reg_ip, last_date, mem_status, mem_lev, bank_member,
							(select rec_id from tb_recommend where Idx=recommend_sn) as recommend_uid
							from tb_member a ,tb_recommend b 
							where a.recommend_sn=b.idx and   b.idx='".$child_sn."' and  b.parent_rec_sn=".$partner_sn."    ".$where." order by ".$ordType; //order by a.regdate desc ";


		} else { //총판, 하위총판까지 
			$sql = "select sn, uid, nick, g_money, regdate, mem_ip, reg_ip, last_date, mem_status, mem_lev, bank_member,
							(select rec_id from tb_recommend where Idx=recommend_sn) as recommend_uid
							from tb_member a ,tb_recommend b 
							where a.recommend_sn=b.idx and (  b.parent_rec_sn=".$partner_sn."   or b.idx=" .$partner_sn ." ) ".$where." order by ".$ordType; //a.regdate desc ";
		}
		
		if($page_size!=0)	
		{
			$sql.= " limit ".$page.",".$page_size;
		}
		
		$rs = $this->db->exeSql($sql);
		

		if($beginDate !='' && $endDate !='')
			$dateStr = " and operdate between '".$beginDate ." 00:00:00' and '".$endDate." 23:59:59' ";
		else
			$dateStr="";

		for($i=0; $i < count($rs); ++$i )
		{
			$member_sn = $rs[$i]['sn'];	
			
			//출금총액
			$sql = "select sum(agree_amount) as money, count(1) as cnt from tb_exchange_log where state = 1 ".$dateStr." and member_sn=".$member_sn;
			 
			$rsi	= $this->db->exeSql($sql);
			$rs[$i]['exchange_money'] = $rsi[0]['money'];
			$rs[$i]['exchange_cnt'] = $rsi[0]['cnt'];
			/*   --일단 쓰는데없는것같아 빼놓음.
			$sql = "select sum(balance) as amount from tb_exchange_sum_log 
							where member_sn=".$member_sn;
			$rsi	= $this->db->exeSql($sql);
			 
			$rs[$i]['exchange_money'] = $rs[$i]['exchange_money'] + $rsi[0]['amount'];
			*/
			//입금총액
			$sql = "select sum(agree_amount) as money, count(1) as cnt from tb_charge_log 
							where state = 1 ".$dateStr." and member_sn=".$member_sn;
			$rsi	= $this->db->exeSql($sql);
			$rs[$i]['charge_money'] = $rsi[0]['money'];
			$rs[$i]['charge_cnt'] = $rsi[0]['cnt'];
			
			/*   --일단 쓰는데없는것같아 빼놓음.
			$sql = "select sum(balance) as amount from tb_charge_sum_log 
							where member_sn=".$member_sn;
			$rsi	= $this->db->exeSql($sql);
			$rs[$i]['charge_money'] = $rs[$i]['charge_money'] + $rsi[0]['amount'];
			*/
			//배팅총액
			$sql = "select count(1) as cnt, sum(betting_money) as money,sum(result_money) as win_money  from tb_total_cart
							where logo='".$this->logo."' and member_sn ='".$member_sn."' ".$dateStr." and kubun ='Y' ";
			$rsi = $this->db->exeSql($sql);
		
			$rs[$i]['bet_cnt'] = $rsi[0]['cnt'];
			$rs[$i]['bet_money'] = $rsi[0]['money'];
			
			//당첨금액
			/* $sql = "select sum(result_money) as win_money from tb_total_cart
							where logo='".$this->logo."' and member_sn ='".$member_sn."' and kubun ='Y' ";
			$rsi = $this->db->exeSql($sql);
			*/
			$rs[$i]['win_money'] = $rsi[0]['win_money'];
		}
	
		return $rs;
	}
	
	//▶ 파트너의 멤버수 
	function getPartnerInMemberTotal($partner_sn, $where,$child_sn)
	{
		if( !$this->is_integer_mysql_parameter($partner_sn))
		{
			exit;
		}
		if($child_sn>0 ){ //해당총판만 
			//$sql = "select count(1) as cnt from tb_member 	where recommend_sn = '".$child_sn."' ".$where;
			if($partner_sn == $child_sn)
				$sql="select count(1) as cnt from tb_member a ,tb_recommend b where a.recommend_sn=b.idx and  b.idx=".$partner_sn  ;
			else
				$sql="select count(1) as cnt from tb_member a ,tb_recommend b where a.recommend_sn=b.idx and  b.idx=".$child_sn." and b.parent_rec_sn='".$partner_sn."'  ";
		
		} else { // 하위 총판회원까지 포함
				$sql="select count(1) as cnt from tb_member a ,tb_recommend b where a.recommend_sn=b.idx and (    b.parent_rec_sn='".$partner_sn."'  or b.idx=" .$partner_sn .") ";
		}
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	//총판가입통계
	function getStatRecommendJoin($partner_sn,$sdate,$edate){
		$dcnt = intval(date("d",strtotime($edate)));
		$item = array();
		//일자별 통계
			$sql = " select CONVERT(b.regdate, DATE) dt, 
						sum(case when b.mem_status='N' then 1 else 0 end ) cnt_N,
						sum(case when b.mem_status='S' then 1 else 0 end ) cnt_S,
						sum(case when b.mem_status='B' then 1 else 0 end ) cnt_B,
						sum(case when b.mem_status='W' then 1 else 0 end ) cnt_W,
						sum(case when b.mem_status='G' then 1 else 0 end ) cnt_G 
						from  tb_member b 
						inner join tb_recommend c on b.recommend_sn=c.idx 
						where    c.idx = ".$partner_sn."  and 
						 b.regdate between '".$sdate." 00:00:00'  and   '".$edate." 23:59:59'
						group by CONVERT(b.regdate, DATE)
						order by dt asc ";
			 
		$rs = $this->db->exeSql($sql);
		for($i=0;$i<=$dcnt;$i++){
			$item[$i]['dt'] = $i;			
			$item[$i]['cnt_N']=0;
			$item[$i]['cnt_S']=0;
			$item[$i]['cnt_B']=0;
			$item[$i]['cnt_W']=0;
			$item[$i]['cnt_G']=0;
			
			/*{? list.mem_status == 'N'}정상
					{: list.mem_status == 'S'}정지
					{: list.mem_status == 'B'}불량
					{: list.mem_status == 'W'}신규
					{: list.mem_status == 'G'}테스터*/
		}
		for($i=0; $i<sizeof($rs); ++$i)
		{			 
			$item[intval(date("d",strtotime($rs[$i]['dt'])))]['cnt_N']   = $rs[$i]['cnt_N'];
			$item[intval(date("d",strtotime($rs[$i]['dt'])))]['cnt_S']   = $rs[$i]['cnt_S'];
			$item[intval(date("d",strtotime($rs[$i]['dt'])))]['cnt_B']   = $rs[$i]['cnt_B'];
			$item[intval(date("d",strtotime($rs[$i]['dt'])))]['cnt_W']   = $rs[$i]['cnt_W'];
			$item[intval(date("d",strtotime($rs[$i]['dt'])))]['cnt_G']   = $rs[$i]['cnt_G'];

			 
		}
		return $item;
	}

	//총판 로그인 통계
	function getStatRecommendLogin($partner_sn,$sdate,$edate,$tp){
		$dcnt = intval(date("d",strtotime($edate)));
		$item = array();
		if($tp==1) { //일자별 통계
			$sql = " select CONVERT(a.visit_date, DATE) dt, count(1) cnt
						from tb_visit a inner join tb_member b on a.member_id=b.uid
						inner join tb_recommend c on b.recommend_sn=c.idx 
						where a.status='0'   and c.idx = ".$partner_sn."  
						and a.visit_date between '".$sdate." 00:00:00'  and   '".$edate." 23:59:59'
						group by CONVERT(a.visit_date, DATE)
						order by dt asc ";
		} else { // 상태별 통계

		}		 
		$rs = $this->db->exeSql($sql);
		for($i=0;$i<=$dcnt;$i++){
			$item[$i]['dt'] = $i;
			$item[$i]['cnt'] = 0;
		}
		for($i=0; $i<sizeof($rs); ++$i)
		{			 
			$item[intval(date("d",strtotime($rs[$i]['dt'])))]['cnt']   = $rs[$i]['cnt'];
			 
		}
		return $item;
	}
	//▶ 롤링 목록
	function getRollingList($partnerSn, $where="", $page=0, $page_size=0)
	{
		if( !$this->is_integer_mysql_parameter($partnerSn))
		{
			exit;
		}
		
		$sql = "select * from tb_recommend where parent_sn=".$partnerSn." ";
		
		if($where!="") 
			$sql.= $where;
			
		if($page_size!=0)	
			$sql.= " limit ".$page.",".$page_size;
						
		$rs = $this->db->exeSql($sql);
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$rsi = $this->getPartnerInMemberList($rs[$i]['Idx']);
			
			//회원수
			$rs[$i]['member_count'] += sizeof($rsi);
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				//출금총액
				$rs[$i]['exchange_money'] += $rsi[$j]['exchange_money'];
				//입금총액
				$rs[$i]['charge_money'] += $rsi[$j]['charge_money'];
				//입금회원수
				$rs[$i]['charge_cnt'] += $rsi[$j]['charge_cnt'];
				//배팅총액
				$rs[$i]['bet_money'] += $rsi[$j]['bet_money'];
				//당첨총액
				$rs[$i]['win_money'] += $rsi[$j]['win_money'];
			}
		}
		return $rs;
	}
	
	//▶ 롤링 목록
	function getSelectorRollingList($parentSn)
	{
		if( !$this->is_integer_mysql_parameter($parentSn))
		{
			exit;
		}
		
		$sql = "select * from tb_recommend where status=1 and parent_sn=".$parentSn;
						
		return $this->db->exeSql($sql);
	}
	
	//▶ 롤링 목록
	function getRollingTotal($partnerSn, $where="")
	{
		if( !$this->is_integer_mysql_parameter($partnerSn))
		{
			exit;
		}
		
		$sql = "select count(*) as cnt from tb_recommend where parent_sn=".$partnerSn." ";
		if($where!="")
			$sql.= $where;
						
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	//▶ 총판 회원  목록
	function getRecommendMemberList($recommendLevel/*1=총판, 2=롤링*/, $recommendSn, $where="", $page=0, $page_size=0)
	{
		if( !$this->is_integer_mysql_parameter($recommendLevel))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		if($page_size!=0)
			$limit.= " limit ".$page.",".$page_size;
		
		if(1==$recommendLevel)
			$where.= " and recommend_sn='".$recommendSn."'" ;
		elseif(2==$recommendLevel)
			$where.= " and rolling_sn='".$recommendSn."'" ;

		$sql = "select sn, uid, nick, g_money, regdate, mem_ip, mem_status, mem_lev, bank_member,
								(select rec_id from tb_recommend where Idx=recommend_sn) as recommend_uid,
								(select rec_id from tb_recommend where Idx=rolling_sn) as rolling_uid
						from tb_member  
						where logo='".$this->logo."'".$where." order by regdate desc ".$limit;
		
		$rs = $this->db->exeSql($sql);
		
		for($i=0; $i < sizeof($rs); ++$i )
		{
			$member_sn = $rs[$i]['sn'];	
	
			//출금총액
			$sql = "select sum(agree_amount) as money, count(*) as cnt from tb_exchange_log where state = 1 and member_sn=".$member_sn;
			$rsi	= $this->db->exeSql($sql);
			$rs[$i]['exchange_money'] = $rsi[0]['money'];
			$rs[$i]['exchange_cnt'] = $rsi[0]['cnt'];
			
			//입금총액
			$sql = "select sum(agree_amount) as money, count(*) as cnt from tb_charge_log 
							where state = 1 and member_sn=".$member_sn;
			$rsi	= $this->db->exeSql($sql);
		
			$rs[$i]['charge_money'] = $rsi[0]['money'];
			$rs[$i]['charge_cnt'] = $rsi[0]['cnt'];

			//배팅총액
			$sql = "select count(*) as cnt, sum(betting_money) as money from tb_total_cart
							where logo='".$this->logo."' and member_sn ='".$member_sn."' and kubun ='Y' ";
			$rsi = $this->db->exeSql($sql);
		
			$rs[$i]['bet_cnt'] = $rsi[0]['cnt'];
			$rs[$i]['bet_money'] = $rsi[0]['money'];
			
			//당첨금액
			$sql = "select sum(result_money) as win_money from tb_total_cart
							where logo='".$this->logo."' and member_sn ='".$member_sn."' and kubun ='Y' ";
			$rsi = $this->db->exeSql($sql);
		
			$rs[$i]['win_money'] = $rsi[0]['win_money'];
		}
	
		return $rs;
	}
	
	//▶ 총판 회원회원 총합
	function getRecommendMemberTotal($recommendLevel/*1=총판, 2=롤링*/, $recommendSn, $where="")
	{
		if( !$this->is_integer_mysql_parameter($recommendLevel))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		if(1==$recommendLevel)
			$where.= " and recommend_sn='".$recommendSn."'" ;
		elseif(2==$recommendLevel)
			$where.= " and rolling_sn='".$recommendSn."'" ;
			
		$sql = "select count(*) as cnt
						from tb_member  
						where logo='".$this->logo."'".$where." order by regdate desc";
		
		$rs = $this->db->exeSql($sql);
		
		return $rs[0]['cnt'];
	}

	function getRecommendChargeList( $recommendSn, $where="", $page=0, $page_size=0,$child_sn)
	{
		 
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		if($page_size > 0)
			$limit = " limit ".$page.",".$page_size;
			
		
		
		$sql = "select a.sn, a.regdate, a.operdate, a.member_sn, a.amount, a.agree_amount, a.before_money, a.after_money, a.bonus,a.bank_owner, a.state,
						b.uid, b.nick, b.g_money, b.bank_member,  c.rec_id  as recommend_id
						from tb_charge_log a,tb_member b , tb_recommend c 
						where a.member_sn=b.sn and  b.recommend_sn=c.idx ";
		if($child_sn>0 ) { //해당총판만 
			if($recommendSn == $child_sn) //총판 자기꺼
				$sql.= " and c.idx=".$child_sn;
			else
				$sql.= " and c.idx=".$child_sn." and c.parent_rec_sn='".$recommendSn."'  ";
		} else {
			$sql.= " and (   c.parent_rec_sn='".$partner_sn."'  or c.idx=" .$recommendSn .") ";

		}

		$sql .=  $where." order by a.regdate desc ".$limit;
		 	
		return $this->db->exeSql($sql);
	}
	
	//▶ 총판입금 총합
	function getRecommendChargeTotal( $recommendSn, $where="",$child_sn)
	{
	 
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
			
		if($child_sn>0 ) { //해당총판만 
			if($recommendSn == $child_sn) //총판 자기꺼
					$sql = "select count(1) as cnt
						from tb_charge_log a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and c.idx=".$child_sn . " " .$where ;
			else
					$sql = "select count(1) as cnt
						from tb_charge_log a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and c.idx=".$child_sn." and c.parent_rec_sn='".$recommendSn."'  ". " " .$where;
		} else { //해당총판+ 하위총판
			$sql = "select count(1) as cnt
						from tb_charge_log a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and (    c.parent_rec_sn='".$recommendSn."'  or c.idx=" .$recommendSn .") ". " " .$where;
		}
			 	
		$rs = $this->db->exeSql($sql);
		
		return $rs[0]['cnt'];
	}

	//▶ 총판입금금/출금금 총합
	function getRecommendMoneySum( $recommendSn, $where="",$child_sn,$type)
	{
	 
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		if($type==1) //입금
			$tbl= "tb_charge_log";
		else //출금
			$tbl= "tb_exchange_log";
			
		if($child_sn>0 ) { //해당총판만 
			if($recommendSn == $child_sn) {//총판 자기꺼
					$sql = "select sum(agree_amount) as agree_amount
						from ".$tbl." a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and c.idx=".$child_sn . " " .$where ;
					 
			
			} else {
					$sql = "select sum(agree_amount) as agree_amount
						from ".$tbl." a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and c.idx=".$child_sn." and c.parent_rec_sn='".$recommendSn."'  ". " " .$where;
					 
			}
		} else { //해당총판+ 하위총판
			$sql = "select sum(agree_amount) as agree_amount
						from ".$tbl." a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and (    c.parent_rec_sn='".$recommendSn."'  or c.idx=" .$recommendSn .") ". " " .$where;
			
		}
			 	
		$rs = $this->db->exeSql($sql);
		
		return $rs[0]['agree_amount'];
	}

	
	//▶ 롤링 출금목록
	function getRecommendExchangeList(  $recommendSn, $where="", $page=0, $page_size=0,$child_sn)
	{
	 
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		if($page_size > 0)
			$limit = " limit ".$page.",".$page_size;
		

		$sql = "select a.sn, a.regdate, a.operdate, a.member_sn, a.amount, a.agree_amount, a.before_money, a.after_money,   a.bank_owner, a.state,
						b.uid, b.nick, b.g_money, b.bank_member,  c.rec_id  as recommend_id
						from tb_exchange_log a,tb_member b , tb_recommend c 
						where a.member_sn=b.sn and  b.recommend_sn=c.idx ";
		if($child_sn>0 ) { //해당총판만 
			if($recommendSn == $child_sn) //총판 자기꺼
				$sql.= " and c.idx=".$child_sn;
			else
				$sql.= " and c.idx=".$child_sn." and c.parent_rec_sn='".$recommendSn."'  ";
		} else {
			$sql.= " and (   c.parent_rec_sn='".$partner_sn."'  or c.idx=" .$recommendSn .") ";

		}

		$sql .=  $where." order by a.regdate desc ".$limit;
		 	

		return $this->db->exeSql($sql);
	}

	function getRecommendExchangeTotal($recommendSn, $where="",$child_sn)
	{
		 
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
	 
		if($child_sn>0 ) { //해당총판만 
			if($recommendSn == $child_sn) //총판 자기꺼
					$sql = "select count(1) as cnt
						from tb_exchange_log a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and c.idx=".$child_sn . " " .$where ;
			else
					$sql = "select count(1) as cnt
						from tb_exchange_log a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and c.idx=".$child_sn." and c.parent_rec_sn='".$recommendSn."'  ". " " .$where;
		} else { //해당총판+ 하위총판
			$sql = "select count(1) as cnt
						from tb_exchange_log a,tb_member b ,tb_recommend c   
						where a.member_sn=b.sn and b.recommend_sn=c.idx and (    c.parent_rec_sn='".$recommendSn."'  or c.idx=" .$recommendSn .") ". " " .$where;
		}

		$rs = $this->db->exeSql($sql);
		
		return $rs[0]['cnt'];
	}
	
	function getRecommendMoneyList($recommendSn, $beginDate, $endDate, $logo='',$child_sn)
	{
		$sql = " select  datediff('".$endDate."' , '".$beginDate."' )+1 as datecnt ";
		$rs = $this->db->exeSql($sql); 
		$sumday =  $rs[0]['datecnt'];

		$list = array();
		for($i=0; $i<$sumday; $i++){

			$list[$i]['current_date']= date("Y-m-d", strtotime("+".$i." day", strtotime ( $beginDate ) ));
			$list[$i]['charge'] = 0;
			$list[$i]['count_charge'] = 0;
			$list[$i]['exchange'] = 0;
			$list[$i]['count_exchange'] = 0;
			$list[$i]['betting'] = 0;
			$list[$i]['win_money'] = 0;
			$list[$i]['nc_money'] = 0;

			  
		}

		//충전
		$sql=" select datediff(operdate,'".$beginDate."') dno  ,sum(agree_amount) as charge_sum , count(1) cnt 
				from tb_charge_log  a,tb_member b , tb_recommend c  
				where a.member_sn=b.sn 
				and b.recommend_sn=c.idx
				and a.state=1 
				and a.operdate between '".$beginDate." 00:00:00' and '".$endDate." 23:59:59' ";

		if($child_sn>0 ) { //해당총판만 
			if($recommendSn == $child_sn) //총판 자기꺼
					$sql .= " and  c.idx=".$child_sn ;
			else
					$sql .= " and c.idx=".$child_sn." and c.parent_rec_sn='".$recommendSn."'  ";
		} else { //해당총판+ 하위총판
			$sql .= " and (    c.parent_rec_sn='".$recommendSn."'  or c.idx=" .$recommendSn .") ";
		} 

		$sql .= " group by datediff(operdate,'".$beginDate."') 
				order by dno asc ";
  
		$rsi = $this->db->exeSql($sql);
		for($i=0; $i<sizeof($rsi); ++$i)
		{
			//echo  $rsi[$i]['dno']. "-".$rsi[$i]['dno'] - ($sumday+$i) + 1."<br>";
			$list[$rsi[$i]['dno']]['charge'] = $rsi[$i]['charge_sum'];
			$list[$rsi[$i]['dno']]['count_charge'] = $rsi[$i]['cnt'];

		}
		//환전

		$sql=" select datediff(operdate,'".$beginDate."') dno  ,sum(agree_amount) as exchange_sum , count(1) cnt 
				from tb_exchange_log  a,tb_member b , tb_recommend c  
				where a.member_sn=b.sn 
				and b.recommend_sn=c.idx
				and a.state=1 
				and a.operdate between '".$beginDate." 00:00:00' and '".$endDate." 23:59:59' ";

		if($child_sn>0 ) { //해당총판만 
			if($recommendSn == $child_sn) //총판 자기꺼
					$sql .= " and  c.idx=".$child_sn ;
			else
					$sql .= " and c.idx=".$child_sn." and c.parent_rec_sn='".$recommendSn."'  ";
		} else { //해당총판+ 하위총판
			$sql .= " and (    c.parent_rec_sn='".$recommendSn."'  or c.idx=" .$recommendSn .") ";
		} 

		$sql .= " group by datediff(operdate,'".$beginDate."') 
				order by dno asc ";
  
		$rsi = $this->db->exeSql($sql);
		for($i=0; $i<sizeof($rsi); ++$i)
		{
			//echo  $rsi[$i]['dno']. "-".$rsi[$i]['dno'] - ($sumday+$i) + 1."<br>";
			$list[$rsi[$i]['dno']]['exchange'] = $rsi[$i]['exchange_sum'];
			$list[$rsi[$i]['dno']]['count_exchange'] = $rsi[$i]['cnt'];

		}


		$sql=" select datediff(operdate,'".$beginDate."') dno  ,sum(betting_money) as betting_sum , sum(result_money) as win_sum , sum(case when a.result=2 then ( a.betting_money - a.web_betting_money )* c.nc_rate * 0.01 else 0 end ) nc_sum ,  sum(case when a.result in (1,2) then a.web_betting_money * c.wb_rate * 0.01  else 0 end ) wb_sum , sum( case when a.result in (1,2) then (a.betting_money - a.web_betting_money ) * c.sb_rate * 0.01  else 0 end ) sb_sum 
				from tb_total_cart  a,tb_member b , tb_recommend c  
				where a.member_sn=b.sn 
				and b.recommend_sn=c.idx
				and a.is_account=1 and a.kubun ='Y'  
				and a.operdate between '".$beginDate." 00:00:00' and '".$endDate." 23:59:59' ";

		if($child_sn>0 ) { //해당총판만 
			if($recommendSn == $child_sn) //총판 자기꺼
					$sql .= " and  c.idx=".$child_sn ;
			else
					$sql .= " and c.idx=".$child_sn." and c.parent_rec_sn='".$recommendSn."'  ";
		} else { //해당총판+ 하위총판
			$sql .= " and (    c.parent_rec_sn='".$recommendSn."'  or c.idx=" .$recommendSn .") ";
		} 

		$sql .= " group by datediff(operdate,'".$beginDate."') 
				order by dno asc ";
 
		$rsi = $this->db->exeSql($sql);
        for($i=0; $i<sizeof($rsi); ++$i)
		{
			$list[$rsi[$i]['dno']]['betting'] = $rsi[$i]['betting_sum'];
			$list[$rsi[$i]['dno']]['win_money'] = $rsi[$i]['win_sum'];
			$list[$rsi[$i]['dno']]['nc_money'] = $rsi[$i]['nc_sum'];
			$list[$rsi[$i]['dno']]['wb_money'] = $rsi[$i]['wb_sum'];
			$list[$rsi[$i]['dno']]['sb_money'] = $rsi[$i]['sb_sum'];

		}


	
		return $list;
	}

	function getRecommendMoneyList_old($recommendSn, $beginDate, $endDate, $logo='')
	{
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		$statModel = Lemon_Instance::getObject("StatModel",true);
	
		$list = array();
		$rs = $statModel->getMoneyList("", $recommendSn, $beginDate, $endDate, $logo);
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$list[$i]['current_date']		=  $rs[$i]['current_date'];
			$list[$i]['exchange']				+= $rs[$i]['exchange'];
			$list[$i]['count_exchange']	+= $rs[$i]['count_exchange'];
			$list[$i]['charge']					+= $rs[$i]['charge'];
			$list[$i]['count_charge']		+= $rs[$i]['count_charge'];
			$list[$i]['betting']				+= $rs[$i]['betting'];
			$list[$i]['win_money']			+= $rs[$i]['win_money'];
			$list[$i]['benefit']				+= ($rs[$i]['charge']-$rs[$i]['exchange']);
		}

		return $list;
	}
	
	//▶ 롤링별 입출금 통계 
	function getRollingMoneyList($rollingSn, $beginDate='', $endDate='')
	{
		if( !$this->is_integer_mysql_parameter($rollingSn))
		{
			exit;
		}
		
		$statModel = Lemon_Instance::getObject("StatModel",true);
	
		$list = array();
		$rs = $statModel->getRollingMoneyList("", $rollingSn, $beginDate, $endDate);
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$list[$i]['current_date']		=  $rs[$i]['current_date'];
			$list[$i]['exchange']				+= $rs[$i]['exchange'];
			$list[$i]['count_exchange']	+= $rs[$i]['count_exchange'];
			$list[$i]['charge']					+= $rs[$i]['charge'];
			$list[$i]['count_charge']		+= $rs[$i]['count_charge'];
			$list[$i]['betting']				+= $rs[$i]['betting'];
			$list[$i]['win_money']			+= $rs[$i]['win_money'];
			$list[$i]['benefit']				+= ($rs[$i]['charge']-$rs[$i]['exchange']);
		}

		return $list;
	}
	
	//▶ 정산 신청 
	function addAccounting($partner_sn,$start_date,$end_date,$exchange_money,$charge_money,$rate,$optmoney,$bank_name,$bank_num,$bank_username)
	{
		$sql="insert into tb_recommend_account(rec_idx,start_date,end_date,exchange_money,charge_money,rate,opt_money,reg_date,status,bank_name,bank_num,bank_username,logo) values";
		$sql=$sql."(".$partner_sn.",'".$start_date."','".$end_date."','".$exchange_money."','".$charge_money."','".$rate."','".$optmoney."',now(),0,'".$bank_name."','".$bank_num."','".$bank_username."','".$this->logo."')";
	
		$this->db->exeSql($sql);
	}
	
	//▶ 정산 신청할수 있는  파트너 목록 
	function getPartnerAccounting($partner_sn)
	{
		$array = array();
		
		$sql = "select *,(select rec_id from tb_recommend where logo='".$this->logo."' 
			and idx=tb_recommend_account.rec_idx)as name from tb_recommend_account 
			where  status=0 and logo='".$this->logo."' and rec_idx=".$partner_sn;
			
		$rs = $this->db->exeSql($sql);
		
		$array['size'] = sizeof($rs);
		
		if( sizeof($rs) > 0 )
		{
			$array['name'] = $rs[0]['name'];
			$array['start_date'] = $rs[0]['start_date'];
			$array['end_date'] = $rs[0]['end_date'];
			$array['exchange_money'] = $rs[0]['exchange_money'];
			$array['charge_money'] = $rs[0]['charge_money'];
			$array['rate'] = $rs[0]['rate'];
			$array['opt_money'] = $rs[0]['opt_money'];
			$array['bank_name'] = $rs[0]['bank_name'];
			$array['bank_num'] = $rs[0]['bank_num'];
			$array['bank_username'] =$rs[0]['bank_username'];					
			
		}
		else
		{
			$sql = "select max(date(reg_date)) as reg_date 
							from tb_recommend_account 
								where logo='".$this->logo."' and  rec_idx=".$partner_sn;
			$rsi = $this->db->exeSql($sql);			
			$array['reg_date'] = $rsi[0]['reg_date'];			
			if( $array['reg_date'] == '' )
			{
				$array['reg_date'] = "2012-01-01";				
			}	
		
			$sql = "select date(now())='".$array['reg_date']."' as diff";
			$rsi = $this->db->exeSql($sql);			
			$array['stat'] = $rsi[0]['diff'];
		
			$sql = "select date(date_sub(now(),interval 1 day)) as objdate";
			$rsi = $this->db->exeSql($sql);		
			$array['objdate'] = $rsi[0]['objdate'];
		
			// 충전금액
		
			$sql = "select sum(agree_amount) as charge_money
							from tb_charge_log 
								where date(regdate)>='".$array['reg_date']."' and date(regdate)<='".$array['objdate']."' and state=1 
									and member_sn in (select sn from tb_member 
										where  logo='".$this->logo."' and recommend_sn=".$partner_sn.") ";								
			$rsi = $this->db->exeSql($sql);			
			
			if( sizeof($rsi) > 0 )
			{
				$array['charge_money'] = $rsi[0]["charge_money"];
			}
		
			// 환전금액
			$sql = "select sum(agree_amount) as exchange_money
							from tb_exchange_log 
								where date(regdate)>='".$array['reg_date']."' and date(regdate)<='".$array['objdate']."' and state=1 
									and member_sn in (select sn from tb_member 
										where  logo='".$this->logo."' and recommend_sn='".$partner_sn."') ";
			$rsi = $this->db->exeSql($sql);												
			if( sizeof($rsi) > 0 )
			{
				$array['exchange_money'] = $rsi[0]["exchange_money"];
			}
			
			$sql = "select rec_rate,rec_bankname,rec_banknum,rec_bankusername 
							from tb_recommend 
								where logo='".$this->logo."' and  idx=".$partner_sn;
								
			$rsi = $this->db->exeSql($sql);										
			if( sizeof($rsi) > 0 )
			{
				$array['rate'] 				= $rsi[0]["rec_rate"];
				$array['bank_name'] 		= $rsi[0]["rec_bankname"];
				$array['bank_num'] 			= $rsi[0]["rec_banknum"];
				$array['bank_username'] = $rsi[0]["rec_bankusername"];
			}	
		}
		
		return $array;
	}
	
	//▶ 정산 데이터 목록 
	function getAccounting($where='') 
	{
		$sql = "select a.idx,a.rec_idx,date(a.start_date) as start_date,date(a.end_date) as end_date,
				a.exchange_money,a.charge_money,a.rate,a.opt_money,a.reg_date,
				a.bank_name,a.bank_num,a.bank_username,b.rec_id
				from tb_recommend_account a,tb_recommend b 
					where a.rec_idx=b.idx and a.logo='".$this->logo."' and a.status=0".$where;
		
		return $this->db->exeSql($sql);
	}
	
	//▶ 필드 데이터
	function getPartnerIdList($logo='',$where='')
	{
		 
        if( $logo == '') {
            $sql = "select rec_id, Idx from tb_recommend 
						where 1=1 ".$where." and parent_sn = 0 order by Idx desc";
        } else {
		    $sql = "select rec_id, Idx from tb_recommend 
						where logo='".$logo."' ".$where . " and parent_sn = 0 order by Idx desc";
		}
			 
		return $this->db->exeSql($sql);
	}
	

	// 하부총판 설정
	function getPartnerRateList($logo='',$parent_rec_sn)
	{
        if( $logo == '')
            $sql = "select rec_id, Idx, default_rate, nc_rate,wb_rate,sb_rate,parent_rate, parent_rec_sn,parent_nc_rate, parent_wb_rate, parent_sb_rate  from tb_recommend 
						where parent_sn = 0  and parent_rec_sn=".$parent_rec_sn." order by Idx desc";
        else
		    $sql = "select rec_id, Idx, default_rate, nc_rate,wb_rate,sb_rate,parent_rate, parent_rec_sn ,parent_nc_rate, parent_wb_rate, parent_sb_rate from tb_recommend 
						where logo='".$logo."' and parent_sn = 0 and parent_rec_sn=".$parent_rec_sn." order by Idx desc";
		 	
		return $this->db->exeSql($sql);
	}

	// 하부총판 설정 저장
	function setChildRecValues($newChildRecSn, $newParentRate, $newParentNcRate , $newParentWbRate, $newParentSbRate, $urlidx)
	{
		$sql = "update tb_recommend set parent_rec_sn = $urlidx, parent_rate = $newParentRate , parent_nc_rate =$newParentNcRate , parent_wb_rate = $newParentWbRate, parent_sb_rate=$newParentSbRate where Idx = $newChildRecSn";
		 
		$this->db->exeSql($sql);
	}

	// 하부총판의 상부 정산률 수정
	function setChildRate($child_rec_sn, $parent_rate, $parent_nc_rate, $parent_wb_rate, $parent_sb_rate)
	{
		$sql = "update tb_recommend set parent_rate = $parent_rate, parent_nc_rate =$parent_nc_rate,   parent_wb_rate =$parent_wb_rate ,  parent_sb_rate =$parent_sb_rate  where Idx = $child_rec_sn";
	 
		$this->db->exeSql($sql);
	}

	// 상부총판 정보 삭제
	function removeParentRecSn($child_rec_sn)
	{
		$sql = "update tb_recommend set parent_rec_sn = 0 where Idx = $child_rec_sn";
		$this->db->exeSql($sql);
	}



	//▶ 필드 데이터
	function getPartnerSnList()
	{
		$sql = "select Idx from tb_recommend 
						where logo='".$this->logo."' and parent_sn = 0 order by Idx";
		return $this->db->exeSql($sql);
	}
	
	//▶ 필드 데이터
	function getPartnerRow($sn, $addWhere='', $field="*")
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		
		$where = "Idx=".$sn;
		
		if($addWhere!='') {$where .=' and '.$addWhere;}
		
		return $this->getRow($field, $this->db_qz.'recommend', $where);
	}

	function getPartnerList($recommendSn) // 해당하는 하위총판 목록 / 본인
	{
		 
		$rec_grd = $this->getPartnerField($recommendSn,"rec_grd","");
		if($rec_grd==1) //상위총판인경우 하위까지 전부다
			$sql = "select idx,rec_id from tb_recommend where idx=".$recommendSn ." or parent_rec_sn=".$recommendSn;
		else
			$sql="select   idx,rec_id  from tb_recommend where idx=".$recommendSn ;
		$sql.= " order by idx ";
		  
		return $this->db->exeSql($sql);

	}


	
	//▶ 필드 데이터
	function getPartnerField($sn, $field, $addWhere='')
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		
		//$where = "Idx=".$sn." and logo='".$this->logo."'";
		$where = "Idx=".$sn;
		
		if($addWhere!='') {$where .=' and '.$addWhere;}
		
		$rs = $this->getRow($field, $this->db_qz.'recommend', $where);
		return $rs[$field];
	}
	
	//▶ 필드 데이터
	function getPartnerFieldById($uid, $field, $addWhere='')
	{
		$where = "rec_id='".$uid."' and logo='".$this->logo."'";
		
		if($addWhere!='') {$where .=' and '.$addWhere;}
		
		$rs = $this->getRow($field, $this->db_qz.'recommend', $where);
		return $rs[$field];
	}
	
	function modifyRecommend($id, $status)
	{
		$sql = "update tb_recommend 
						set status='".$status."' where rec_id='".$id."'";
								
		return $this->db->exeSql($sql);
	}
	
	function delRecommend($id)
	{
		$sql = "delete from tb_recommend 
							where rec_id='".$id."'";
							
		return $this->db->exeSql($sql);
	}
	
	function getRecommendTotal($where)
	{
		$sql = "select count(*) as cnt from tb_recommend where status!=2 /*and parent_sn=0 */".$where;
							
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	function getRecIdx($recRst,$recSn)
	{
	 
		for($idx=0;$idx<sizeof($recRst); ++$idx){
			
			if($recRst[$idx]['Idx'] == $recSn){
				return $idx;
			}
		} 
		return -1;
	}
	function getRecommendList($where,  $beginDate, $endDate, $page, $page_size)
	{
		$sql = "select * from tb_recommend 
						where status != 2 /*and parent_sn=0*/ and rec_grd=1 /* 상위총판 */ ".$where." 
						order by reg_date limit ".$page.",".$page_size;
			
		$mrs = $this->db->exeSql($sql);
		$recStr="";
		$cnt = sizeof($mrs);

		

 		for($i=0; $i<$cnt; ++$i)
		{
			$recommend_sn = $mrs[$i]['Idx'];
			if($recStr=="")
				$recStr= $mrs[$i]['Idx'];
			else
				$recStr.= " , " . $mrs[$i]['Idx'];

			//이월금액
			$ret = $this->getCarriedMoneyList($mrs[$i]['rec_id'], $beginDate);

			$mrs[$i]['carried'] = $ret['carried_money'];
			$mrs[$i]['lastDate'] = $ret['end_date'];

		}

		if($recStr!="")
			$recStr = " in ( " . $recStr." ) ";
		
		if($beginDate!="" && $endDate!=""){
			$dateStr = " between  '". $beginDate." 00:00:00' and '". $endDate ." 23:59:59'";

			$dateStr2 = "regDate > '{$beginDate}' and regDate < '{$endDate}'";
		}

		//회원수
		$subsql = "select recommend_sn , count(1) as cnt from tb_member where recommend_sn ".$recStr." and ".$dateStr2."  group by recommend_sn order by  recommend_sn  ";
	 
	 	
		$rsi = $this->db->exeSql($subsql);


		for($i=0; $i<sizeof($rsi); ++$i)
		{
			$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['recommend_sn']);
			$mrs[$arr_idx]['member_count'] = $rsi[$i]['cnt'];
		}
	 

		//입금횟수,금액			
		$subsql = "select b.recommend_sn , count(distinct(member_sn)) as cnt, sum(agree_amount ) as total_charge
					from tb_charge_log a, tb_member b 
					where a.member_sn=b.sn  and state=1  and a.operdate ".$dateStr." and  b.recommend_sn  ".$recStr."  group by b.recommend_sn order by b.recommend_sn ";
 

		$rsi = $this->db->exeSql($subsql);
		
		for($i=0; $i<sizeof($rsi); ++$i)
		{
			$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['recommend_sn']);
			$mrs[$arr_idx]['charge_count'] = $rsi[$i]['cnt'];
			$mrs[$arr_idx]['charge_sum'] = $rsi[$i]['total_charge'];
		}

		//출금횟수,금액
		$subsql = "select b.recommend_sn ,count(distinct(member_sn)) as cnt, sum(agree_amount  ) as total_exchange
						from tb_exchange_log a, tb_member b 
						where a.member_sn=b.sn   and state=1  and a.operdate ".$dateStr." and b.recommend_sn ".$recStr." group by b.recommend_sn order by b.recommend_sn  ";
 
		$rsi = $this->db->exeSql($subsql);
		
		for($i=0; $i<sizeof($rsi); ++$i)
		{
			$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['recommend_sn']);
			$mrs[$arr_idx]['exchange_count'] = $rsi[$i]['cnt'];
			$mrs[$arr_idx]['exchange_sum'] = $rsi[$i]['total_exchange'];
		}
		//낙첨수수료 
		$subsql = "select	b.recommend_sn, ifnull( sum( case when a.result=2 /* 실패 */  then  a.betting_money - a.web_betting_money else 0 end ) ,0) sum_nc_bm , ifnull(sum(a.web_betting_money),0) sum_wb_bm ,  ifnull(sum(a.betting_money - a.web_betting_money ),0)  sum_sb_bm 
							from tb_total_cart a , tb_member b  
							where  a.member_sn=b.sn  and a.is_account=1 and a.kubun ='Y'  
							and a.operdate  ".$dateStr." and a.result in (1,2) 
							and b.recommend_sn ". $recStr ." group by b.recommend_sn order by b.recommend_sn "; 
			
		$rsi = $this->db->exeSql($subsql);	
 
		
		
		for($i=0; $i<sizeof($rsi); ++$i)
		{
			$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['recommend_sn']);		 
			if($arr_idx>-1) {
				$mrs[$arr_idx]['rec_nc_account'] =  $rsi[$i]['sum_nc_bm'] *   $mrs[$arr_idx]['nc_rate'] / 100 ;
				$mrs[$arr_idx]['rec_wb_account'] = $rsi[$i]['sum_wb_bm'] * $mrs[$arr_idx]['wb_rate'] / 100 ;
				$mrs[$arr_idx]['rec_sb_account'] = $rsi[$i]['sum_sb_bm'] * $mrs[$arr_idx]['sb_rate'] / 100 ; 
			}

		}

		// 하부총판 회원수
		
		$subsql = " select  b.parent_rec_sn , count(1) cnt  from tb_member a , tb_recommend b 
				where a.recommend_sn = b.idx  and b.parent_rec_sn ".$recStr ."  group by b.parent_rec_sn order by b.parent_rec_sn ";
		$rsi = $this->db->exeSql($subsql);		


		for($i=0; $i<sizeof($rsi); ++$i)
		{
			$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['parent_rec_sn']);	
			if($arr_idx>-1) {
				$mrs[$arr_idx]['child_member_count'] = $rsi[$i]['cnt'];
			}

		}

		// 하부총판 입금 총액
		$subsql = "select c.parent_rec_sn, sum( agree_amount * c.parent_rate * 0.01) as child_total_charge from tb_charge_log a ,tb_member b,tb_recommend c 
				where a.member_sn=b.sn and b.recommend_sn=c.idx  and a.operdate ".$dateStr." and  a.state=1 and c.parent_rec_sn ".$recStr ."  group by c.parent_rec_sn order by c.parent_rec_sn ";
				 
		$rsi = $this->db->exeSql($subsql);	
		
 
		for($i=0; $i<sizeof($rsi); ++$i)
		{
			$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['parent_rec_sn']);	
			if($arr_idx>-1) {
				$mrs[$arr_idx]['child_total_charge'] = $rsi[$i]['child_total_charge'] ;
			}
		}

		// 하부총판 출금 총액
		$subsql = "select c.parent_rec_sn, sum(agree_amount * c.parent_rate * 0.01 ) as child_total_exchange from tb_exchange_log a ,tb_member b,tb_recommend c where  a.member_sn=b.sn and b.recommend_sn=c.idx  and a.operdate ".$dateStr." and a.state=1 and c.parent_rec_sn ".$recStr ."  group by c.parent_rec_sn order by c.parent_rec_sn ";
		$rsi = $this->db->exeSql($subsql);
		for($i=0; $i<sizeof($rsi); ++$i)
		{
			$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['parent_rec_sn']);	
			if($arr_idx>-1) {
				$mrs[$arr_idx]['child_total_exchange'] = $rsi[$i]['child_total_exchange'] ;
			}
		}
 
 	 	
		//하부총판 낙첨수수료
		$subsql = "select	c.parent_rec_sn, ifnull( sum( case when a.result=2 /* 실패 */  then  ( a.betting_money - a.web_betting_money ) * c.parent_nc_rate * 0.01 else 0 end ) ,0) child_sum_nc_bm , ifnull(sum(a.web_betting_money * c.parent_wb_rate* 0.01 ),0) child_sum_wb_bm ,  ifnull(sum( ( a.betting_money - a.web_betting_money ) * c.parent_sb_rate * 0.01 ),0)  child_sum_sb_bm 
							from tb_total_cart a , tb_member b ,tb_recommend c 
							where  a.member_sn=b.sn  and b.recommend_sn=c.idx  and a.is_account=1 and a.kubun ='Y'  
							and a.operdate  ".$dateStr."  and a.result in (1,2) 
							and c.parent_rec_sn ". $recStr ." group by c.parent_rec_sn order by c.parent_rec_sn ";
						 
		$rsi = $this->db->exeSql($subsql);	
 
 		for($i=0; $i<sizeof($rsi); ++$i)
		{
			$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['parent_rec_sn']);		 
			if($arr_idx>-1) {
				$mrs[$arr_idx]['child_rec_nc_account'] =  $rsi[$i]['child_sum_nc_bm'];// *   $rs[$arr_idx]['nc_rate'] / 100 ;
				$mrs[$arr_idx]['child_rec_wb_account'] = $rsi[$i]['child_sum_wb_bm'];// * $rs[$arr_idx]['wb_rate'] / 100 ;
				$mrs[$arr_idx]['child_rec_sb_account'] = $rsi[$i]['child_sum_sb_bm'] ;//* $rs[$arr_idx]['sb_rate'] / 100 ; 
				
			}

		}
 
		for($jj=0; $jj<$cnt; ++$jj)
		{
		
			if ($mrs[$jj]['charge_sum']=='')
				$mrs[$jj]['charge_sum'] = 0;
			if($mrs[$jj]['exchange_sum']=='')
				$mrs[$jj]['exchange_sum'] = 0;
			// 정산액
 	
			//echo $jj." : " .$mrs[$jj]['charge_sum']." - ". $mrs[$jj]['exchange_sum'] ." * ". $mrs[$jj]['default_rate'] ."<br>";
 			
			$mrs[$jj]['rec_account'] = ( $mrs[$jj]['charge_sum'] - $mrs[$jj]['exchange_sum'] ) * $mrs[$jj]['default_rate'] / 100;
			$mrs[$jj]['child_rec_account'] = ( $mrs[$jj]['child_total_charge'] - $mrs[$jj]['child_total_exchange'] );
 
			//총정산액 : 정산액 + 낙첨수수료 + 하부총판정산액 + 하부 낙첨 수수료
			$mrs[$jj]['total_rec_account'] = $mrs[$jj]['rec_account'] + $mrs[$jj]['child_rec_account'] + $mrs[$jj]['rec_nc_account'] + $mrs[$jj]['child_rec_nc_account'] ;
 
			
		}

		return $mrs;
	}

	function insertAccount($pid, $jc, $ic, $im, $om, $crm, $clm, $tmo, $bt, $et)
	{
		$sql = "INSERT INTO tb_recommend_account_log
		(recommend, join_cnt, income_cnt, income_money, withdraw_money, carried_money, calculated_money, total_money_out, begin_date, end_date)
		VALUES ('{$pid}', {$jc}, {$ic}, {$im}, {$om}, {$crm}, {$clm}, {$tmo} '{$bt}', '{$et}')";

		$rs = $this->db->exeSql($sql);

		return $rs;
	}

	function getCarriedMoneyList($pid, $beginDate)
	{
		$sql = "select * from tb_recommend_account_log
						where recommend = '{$pid}' and '{$beginDate}' < end_date";
		$rs = $this->db->exeSql($sql);

		if(sizeof($rs))
		{
			return "already";
		}

		$sql = "select * from tb_recommend_account_log
						where recommend = '{$pid}' and '{$beginDate}' > end_date
						order by process_date desc limit 1";
		
		$rs = $this->db->exeSql($sql);

		return $rs[0];
	}

	function getRecommendList_old($where, $page, $page_size)
	{
		$sql = "select * from tb_recommend 
						where status != 2 and parent_sn=0 ".$where." 
						order by reg_date limit ".$page.",".$page_size;
		
		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$recommend_sn = $rs[$i]['Idx'];
			
			//회원수
			$sql = "select count(*) as cnt from tb_member where recommend_sn=".$recommend_sn;
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['member_count'] = $rsi[0]['cnt'];
			
			//입금횟수,금액			
			$sql = "select count(distinct(member_sn)) as cnt, sum(agree_amount) as total_charge
							from tb_charge_log a, tb_member b, tb_recommend c
							where a.member_sn=b.sn and b.recommend_sn=c.Idx and state=1 and c.Idx=".$recommend_sn;
					
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['charge_count'] = $rsi[0]['cnt'];
			$rs[$i]['charge_sum'] = $rsi[0]['total_charge'];
			
			//출금횟수,금액
			$sql = "select count(distinct(member_sn)) as cnt, sum(agree_amount) as total_exchange
						from tb_exchange_log a, tb_member b, tb_recommend c
						where a.member_sn=b.sn and b.recommend_sn=c.Idx and state=1 and c.Idx=".$recommend_sn;
			$rsi = $this->db->exeSql($sql);
			
			$rs[$i]['exchange_count'] = $rsi[0]['cnt'];
			$rs[$i]['exchange_sum'] = $rsi[0]['total_exchange'];
			
			//$rsi = $this->getPartnerInMemberList($recommend_sn, "");
			//$rs[$i]['item'] = $rsi;
		}
		return $rs;
	}

	function getchildRecommendList($where,  $beginDate, $endDate, $page, $page_size)
	{
		$sql = "select * from tb_recommend 
						where status != 2 /*and parent_sn=0*/ and rec_grd=2 /* 하위총판 */ ".$where." 
						order by reg_date limit ".$page.",".$page_size;
				
		$mrs = $this->db->exeSql($sql);
		$recStr="";
		$cnt = sizeof($mrs);
		 
 		for($i=0; $i<$cnt; ++$i)
		{
			$recommend_sn = $mrs[$i]['Idx'];
			if($recStr=="")
				$recStr= $mrs[$i]['Idx'];
			else
				$recStr.= " , " . $mrs[$i]['Idx'];
		}
		if($recStr!="")
			$recStr = " in ( " . $recStr." ) ";
		
		if($beginDate!="" && $endDate!=""){
			$dateStr = " between  '". $beginDate." 00:00:00' and '". $endDate ." 23:59:59'";
		}
		
		if($cnt>0) { 

			//회원수
			$subsql = "select recommend_sn , count(1) as cnt from tb_member where recommend_sn is not null  and  recommend_sn ".$recStr."  group by recommend_sn order by  recommend_sn  ";
		 
			$rsi = $this->db->exeSql($subsql);
			for($i=0; $i<sizeof($rsi); ++$i)
			{

				$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['recommend_sn']);
				if($arr_idx>-1) {
					$mrs[$arr_idx]['member_count'] = $rsi[$i]['cnt'];
				}
			}
		

			//입금횟수,금액			
			$subsql = "select b.recommend_sn , count(distinct(member_sn)) as cnt, sum(agree_amount ) as total_charge
						from tb_charge_log a, tb_member b 
						where a.member_sn=b.sn  and state=1  and a.operdate ".$dateStr." and  b.recommend_sn  ".$recStr."  group by b.recommend_sn order by b.recommend_sn ";
	 

			$rsi = $this->db->exeSql($subsql);
			
			for($i=0; $i<sizeof($rsi); ++$i)
			{
				$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['recommend_sn']);
				if($arr_idx>-1) {
					$mrs[$arr_idx]['charge_count'] = $rsi[$i]['cnt'];
					$mrs[$arr_idx]['charge_sum'] = $rsi[$i]['total_charge'];
				}
			}

			//출금횟수,금액
			$subsql = "select b.recommend_sn ,count(distinct(member_sn)) as cnt, sum(agree_amount  ) as total_exchange
							from tb_exchange_log a, tb_member b 
							where a.member_sn=b.sn   and state=1  and a.operdate ".$dateStr." and b.recommend_sn ".$recStr." group by b.recommend_sn order by b.recommend_sn  ";
	 
			$rsi = $this->db->exeSql($subsql);
			
			for($i=0; $i<sizeof($rsi); ++$i)
			{
				$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['recommend_sn']);
				if($arr_idx>-1) {
					$mrs[$arr_idx]['exchange_count'] = $rsi[$i]['cnt'];
					$mrs[$arr_idx]['exchange_sum'] = $rsi[$i]['total_exchange'];
				}
			}
			//낙첨수수료 
			$subsql = "select	b.recommend_sn, ifnull( sum( case when a.result=2 /* 실패 */  then  a.betting_money - a.web_betting_money else 0 end ) ,0) sum_nc_bm , ifnull(sum(a.web_betting_money),0) sum_wb_bm ,  ifnull(sum(a.betting_money - a.web_betting_money ),0)  sum_sb_bm 
								from tb_total_cart a , tb_member b  
								where  a.member_sn=b.sn  and a.is_account=1 and a.kubun ='Y'  
								and a.operdate  ".$dateStr." and a.result in (1,2) 
								and b.recommend_sn ". $recStr ." group by b.recommend_sn order by b.recommend_sn "; 
				
			$rsi = $this->db->exeSql($subsql);	
	 
		
			
			for($i=0; $i<sizeof($rsi); ++$i)
			{
				$arr_idx = $this->getRecIdx($mrs,$rsi[$i]['recommend_sn']);		 
				if($arr_idx>-1) {
					$mrs[$arr_idx]['rec_nc_account'] =  $rsi[$i]['sum_nc_bm'] *   $mrs[$arr_idx]['nc_rate'] / 100 ;
					$mrs[$arr_idx]['rec_wb_account'] = $rsi[$i]['sum_wb_bm'] * $mrs[$arr_idx]['wb_rate'] / 100 ;
					$mrs[$arr_idx]['rec_sb_account'] = $rsi[$i]['sum_sb_bm'] * $mrs[$arr_idx]['sb_rate'] / 100 ; 
				}

			}

			
			for($jj=0; $jj<$cnt; ++$jj)
			{
			
				if ($mrs[$jj]['charge_sum']=='')
					$mrs[$jj]['charge_sum'] = 0;
				if($mrs[$jj]['exchange_sum']=='')
					$mrs[$jj]['exchange_sum'] = 0;
				// 정산액
		
				//echo $jj." : " .$mrs[$jj]['charge_sum']." - ". $mrs[$jj]['exchange_sum'] ." * ". $mrs[$jj]['default_rate'] ."<br>";
				
				$mrs[$jj]['rec_account'] = ( $mrs[$jj]['charge_sum'] - $mrs[$jj]['exchange_sum'] ) * $mrs[$jj]['default_rate'] / 100;
			 
	 
				//총정산액 : 정산액 + 낙첨수수료  
				$mrs[$jj]['total_rec_account'] = $mrs[$jj]['rec_account']   + $mrs[$jj]['rec_nc_account'];
	 
				
			}
		}
		return $mrs;
	}


	
	function getRecommend_Lev2List($where, $page, $page_size)
	{
		$sql = "select * from tb_recommend 
						where status != 2 and parent_sn=0 ".$where." 
						order by reg_date limit ".$page.",".$page_size;
					
		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$recommend_sn = $rs[$i]['Idx'];
			
			//회원수
			$sql = "select count(*) as cnt from tb_member where recommend_sn=".$recommend_sn;
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['member_count'] = $rsi[0]['cnt'];
			
			//입금횟수,금액
			$sql = "select count(distinct(member_sn)) as cnt, sum(agree_amount) as total_charge
							from tb_charge_log 
							where member_sn in (select sn from tb_member where recommend_sn=".$recommend_sn.") and agree_amount > 0";
					
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['charge_count'] = $rsi[0]['cnt'];
			$rs[$i]['charge_sum'] = $rsi[0]['total_charge'];
			
			//출금횟수,금액
			$sql = "select count(distinct(member_sn)) as cnt, sum(agree_amount) as total_exchange
						from tb_exchange_log 
						where member_sn in (select sn from tb_member where recommend_sn=".$recommend_sn.") and agree_amount > 0";
			$rsi = $this->db->exeSql($sql);
			
			$rs[$i]['exchange_count'] = $rsi[0]['cnt'];
			$rs[$i]['exchange_sum'] = $rsi[0]['total_exchange'];
			
			/*
			//배팅금액, 당첨금액
			$sql = "select sum(betting_money) as total_betting, sum(result_money) as total_result
						from tb_total_cart
						where member_sn in (select sn from tb_member where recommend_sn=".$recommend_sn.")";
			$rsi = $this->db->exeSql($sql);
			
			$rs[$i]['betting_sum'] = $rsi[0]['total_betting'];
			$rs[$i]['result_sum'] = $rsi[0]['total_result'];
			*/
			
			
			//$rsi = $this->getPartnerInMemberList($recommend_sn, "");
			$rs[$i]['item'] = $rsi;
		}
		return $rs;
	}
	
	function modifyRecommendjoin($idx)
	{
		if( !$this->is_integer_mysql_parameter($idx))
		{
			exit;
		}
		
		$sql = "update tb_recommend 
							set status=1 where Idx=".$idx."";
								
		return $this->db->exeSql($sql);
	}	

	
	function delRecommendjoinList($idx)
	{
		$sql = "delete from tb_recommend where idx in(".$idx.")";
							
		return $this->db->exeSql($sql);
	}
	
	function delRecommendjoin($idx)
	{
		if( !$this->is_integer_mysql_parameter($idx))
		{
			exit;
		}
		
		$sql = "delete from tb_recommend 
							where idx=".$idx."";
							
		return $this->db->exeSql($sql);
	}

	function getRecommendjoinTotal($where)
	{
		$sql = "select count(*) as cnt
						from tb_recommend 
							where logo='".$this->logo."' and status=2".$where;
							
		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}
	
	function getRecommendjoinList($where, $page, $page_size)
	{
		$sql = "select * from tb_recommend 
							where logo='".$this->logo."' and status=2 ".$where." order by reg_date desc limit ".$page.",".$page_size;
							
		$rs = $this->db->exeSql($sql);
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$beginIndex = 0;
			$length = 15;
			$string = $rs[i]['rec_email'];
			
			$substr = substr( $string, $beginIndex, $length * 2 );
    	$multi_size = preg_match_all( '/[\\x80-\\xff]/', $substr, $multi_chars);

    	if($multi_size > 0)
       $length = $length + intval( $multi_size / 3 ) - 1;

    	if(strlen( $string ) > $length)
    	{
				$string = substr( $string, $beginIndex, $length );
     		$string = preg_replace( '/(([\\x80-\\xff]{3})*?)([\\x80-\\xff]{0,2})$/', '$1', $string );
     		$string .= '...';
    	}
    	
    	$rs[$i]['rec_email'] = $string;
		}	
			
		return $rs;
	}
	
	function delAccounting($idx)
	{
		if( !$this->is_integer_mysql_parameter($idx))
		{
			exit;
		}
		
		$sql="delete from tb_recommend_account 
						where logo='".$this->logo."' and  idx=".$idx;
		return $this->db->exeSql($sql);				
	}
	
	
	function modifyAccounting($idx)
	{
		if( !$this->is_integer_mysql_parameter($idx))
		{
			exit;
		}
		
		$sql = "update tb_recommend_account 
						set opt_date=now(), status=1 
						where logo='".$this->logo."' and  idx=".$idx;
		
		return $this->db->exeSql($sql);			
	}
	
	function getAccountingfinTotal($where)
	{		
		$sql = "select count(*) as cnt
							from tb_recommend_account a,tb_recommend b 
									where a.rec_idx=b.idx and a.logo='".$this->logo."'".$where;
									
		$rs = $this->db->exeSql($sql);

		echo $sql;

		return $rs[0]['cnt'];							
	}
	
	function getAccountingfinList($where='', $page, $page_size)
	{
		$sql = "select a.idx,a.rec_idx,date(a.start_date) as start_date,date(a.end_date) as end_date,a.exchange_money,
				a.charge_money,a.rate,a.opt_money,a.reg_date,a.bank_name,a.status,a.bank_num,a.bank_username,b.rec_id
				from tb_recommend_account a,tb_recommend b 
								where a.rec_idx=b.idx and a.logo='".$this->logo."' and a.status=1 ".$where."limit ".$page.",".$page_size."";
								
		return $this->db->exeSql($sql);
	}
	
	function modifyMemberDetails($where, $memo, $rec_lev, $rec_name, $rec_bankname, $rec_bankusername, $rec_banknum, $rec_email, $rec_phone, $idx )
	{
		if( !$this->is_integer_mysql_parameter($idx))
		{
			exit;
		}
		
		$sql = "update tb_recommend 
						set ".$where." memo='".$memo."', rec_name='".$rec_name."',rec_bankname='".$rec_bankname."',rec_bankusername='".$rec_bankusername."',rec_banknum='".$rec_banknum."',rec_email='".$rec_email."',rec_phone='".$rec_phone."' 
						where idx='".$idx."'";
								
		return $this->db->exeSql($sql);
	}
	
function getMemberDetails($recommendSn)
	{
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		$sql = "select *
						from tb_recommend 
						where idx=".$recommendSn;
		
		$rs = $this->db->exeSql($sql); 
		 
		$sql = "select count(1) as member_count
							from tb_member 
								where recommend_sn=".$recommendSn;
		$rsi = $this->db->exeSql($sql); 
		
		$rs[0]['member_count'] = $rsi[0]['member_count'];
		
		
		//입금횟수,금액
		$sql = "select count(*) as cnt, sum(agree_amount) as total_charge
					from tb_charge_log 
					where member_sn in (select sn from tb_member where recommend_sn=".$recommendSn.")";
		$rsi = $this->db->exeSql($sql);
		$rs[0]['charge_count'] = $rsi[0]['cnt'];
		$rs[0]['charge_sum'] = $rsi[0]['total_charge'];
			
			//출금횟수,금액
		$sql = "select count(*) as cnt, sum(agree_amount) as total_exchange
					from tb_exchange_log 
					where member_sn in (select sn from tb_member where recommend_sn=".$recommendSn.")";
		$rsi = $this->db->exeSql($sql);
		$rs[0]['exchange_count'] = $rsi[0]['cnt'];
		$rs[0]['exchange_sum'] = $rsi[0]['total_exchange'];
		
		// 정산액
		$rs[0]['rec_account'] = ( $rs[0]['charge_sum'] - $rs[0]['exchange_sum'] ) * $rs[0]['default_rate'] / 100;

		if ( $rs[0]['rec_grd'] ==2) { //하부총판일경우 
			//상부총판 정산액
			$rs[0]['parent_total_charge'] = 0;
			$rs[0]['parent_total_exchange'] = 0;
			$rs[0]['parent_rec_account'] = 0;
			
			// 상부총판 입금 총액
			$subsql=" select sum(agree_amount * c.parent_rate * 0.01) as parent_total_charge from tb_charge_log a, tb_member b,tb_recommend c 
					where a.member_sn=b.sn and b.recommend_sn = c.parent_rec_sn and b.recommend_sn>0 and c.Idx= ".$recommendSn;
			$rsi = $this->db->exeSql($subsql);
			$rs[0]['parent_total_charge'] = $rsi[0]['parent_total_charge']  ;

			// 하부총판 입금 총액
			$subsql=" select sum(agree_amount * c.parent_rate * 0.01) as parent_total_exchange from tb_exchange_log a, tb_member b,tb_recommend c 
					where a.member_sn=b.sn and b.recommend_sn = c.parent_rec_sn and b.recommend_sn>0 and c.Idx= ".$recommendSn;
			$rsi = $this->db->exeSql($subsql);
			$rs[0]['parent_total_exchange'] = $rsi[0]['parent_total_exchange']  ;
			$rs[0]['parent_rec_account'] = ( $rs[0]['parent_total_exchange'] - $rs[0]['parent_total_exchange'] );

			$rs[0]['total_rec_account'] = $rs[0]['rec_account'] + $rs[0]['parent_rec_account'];
		} else {

			// 하부총판 정산액
			$rs[0]['child_total_charge'] = 0;
			$rs[0]['child_total_exchange'] = 0;
			$rs[0]['child_rec_account'] = 0;

			$sql = "select * from tb_recommend where parent_rec_sn = $recommendSn";
			$child_rec = $this->db->exeSql($sql);

			for( $j=0; $j < sizeof($child_rec); $j++)
			{
				// 하부총판 입금 총액
				$sql = "select sum(agree_amount) as child_total_charge from tb_charge_log where state=1 and member_sn in (select sn from tb_member where recommend_sn = ".$child_rec[$j]['Idx'].")";
				$rsi = $this->db->exeSql($sql);

				$rs[0]['child_total_charge'] += $rsi[0]['child_total_charge'] * $child_rec[$j]['parent_rate'] / 100 ;
				//echo "<pre>", var_dump($rsi), "</pre>";
				// 하부총판 출금 총액
				$sql = "select sum(agree_amount) as child_total_exchange from tb_exchange_log where state=1 and member_sn in (select sn from tb_member where recommend_sn = ".$child_rec[$j]['Idx'].")";
				$rsi = $this->db->exeSql($sql);

				$rs[0]['child_total_exchange'] += $rsi[0]['child_total_exchange'] * $child_rec[$j]['parent_rate'] / 100 ;
				//echo "<pre>", var_dump($rsi), "</pre>";
			}

			$rs[0]['child_rec_account'] = ( $rs[0]['child_total_charge'] - $rs[0]['child_total_exchange'] );

			$rs[0]['total_rec_account'] = $rs[0]['rec_account'] + $rs[0]['child_rec_account'];
		}
			/*
		//충전회원수, 금액
		
		//환전금액
		$rs[0]['charge_member_sum'] = $rsi[0]["charge_member_sum"];
		
		$sql = "select sum(a.resamount) as summoney,a.kubun 
							from tb_money a,tb_member b 
								where a.result=1 and a.mem_idx=b.sn and a.logo='".$this->logo."' and b.recommend_sn=".$recommendSn." group by a.kubun";	
		$rsi = $this->db->exeSql($sql);
		
		
		$rs[0]['sum_charge_money'] = 0;
		$rs[0]['sum_exchange_money'] = 0;
		
		if($rsi[0]["kubun"]==0) {$rs[0]['sum_charge_money'] = $rsi[0]["summoney"];}
		else					{$rs[0]['sum_exchange_money'] = $rsi[0]["conmoney"];}							
		*/
			
		return $rs[0];
	}
	
	
	function addRecommend($sn, $recommendSn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($recommendSn))
		{
			exit;
		}
		
		$configModel  = Lemon_Instance::getObject("ConfigModel",true);
		
		$joinRecommendRate = $configModel->getLevelConfigField(1, 'lev_join_recommend_mileage_rate');
		$rates = explode(":",$joinRecommendRate);
		
		if($recommendSn=="")
		{
			$sql = "insert into tb_join_recommend(member_sn,recommend_sn,logo,step1_rate,step2_rate,step3_rate) values(".$sn.",0,'".$this->logo."',".$rates[0].",".$rates[1].",".$rates[2].")";
		}
		else
		{
			if($recommendSn!='')
			{
				$sql = "insert into tb_join_recommend(member_sn,recommend_sn,logo,step1_rate,step2_rate,step3_rate) values(".$sn.",".$recommendSn.",'".$this->logo."',".$rates[0].",".$rates[1].",".$rates[2].")";
			}
			else
			{
				$sql = "insert into tb_join_recommend(member_sn,recommend_sn,logo,step1_rate,step2_rate,step3_rate) values(".$sn.",0,'".$this->logo."',".$rates[0].",".$rates[1].",".$rates[2].")";
			}
		}

		return $this->db->exeSql($sql);
	}
	
	//▶ 추천인 마일리지 배당
	function joinRecommendRate($memberSn, $step=1)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($step))
		{
			exit;
		}
		
		$memberModel  = Lemon_Instance::getObject("MemberModel",true);
		$configModel  = Lemon_Instance::getObject("ConfigModel",true);
		
		$rate = 0;
		$sql = "select recommend_sn from tb_join_recommend where member_sn=".$memberSn;
		$rs = $this->db->exeSql($sql);
		$recommendSn = $rs[0]['recommend_sn'];
		
		if($recommendSn!=''&&$recommendSn!=0)
		{
			$recommendLevel = $memberModel->getMemberField($recommendSn,'mem_lev');
			$recommend_status = $memberModel->getMemberField($recommendSn,'mem_status');
			
			$rs = $configModel->getLevelConfigField($recommendLevel, 'lev_join_recommend_mileage_rate');
		
			$array = explode(':', $rs);
			if($step==1) $rate=$array[0];
			else if($step==2) $rate=$array[1];
			else if($step==3) $rate=$array[2];
		}
		
		return array("recommend_sn" => $recommendSn, "rate" => $rate, "recommend_status" => $recommend_status);
	}
}
?>    