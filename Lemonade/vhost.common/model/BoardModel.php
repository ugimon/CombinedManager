<?php
class BoardModel extends Lemon_Model
{
	function getPartnerBoardbyRegdate()
	{
		$sql = "select * from tb_board
							where logo='".$this->logo."' and  lev=130 order by regdate desc";

		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$rs[$i]['num'] = $i;
		}
		return $rs;
	}

	//잭팟 게시글 자동추가
	function addJackpotContent($author, $title, $content, $bettings, $ip)
	{
		$sql = "insert into tb_content(province,title,imgnum,pic,author,top,content,ip,time,hit,betting_no,logo)
						values ('9','".$title."','0','','".$author."','1','".$content."','".$ip."',now(),'0','".$bettings."','".$this->logo."')";
		return $this->db->exeSql($sql);
	}
	//잭팟 게시글 취소
	function cancelJackpotContent($bettings)
	{
		$sql = "delete from tb_content where province='9' and betting_no='".$bettings."'";
		return $this->db->exeSql($sql);
	}

	//▶ 고객센터 총합
	function getCsTotal($where)
	{
/*
		$sql = "select count(*) as cnt from tb_question a, tb_member b
						where a.mem_id=b.uid and 1=1 ".$where." ";
*/

		$sql = "select count(*) as cnt from tb_question a
						where 1=1 ".$where." ";

		$rs = $this->db->exeSql($sql);

		return $rs[0]['cnt'];
	}

	//▶ 고객센터 목록
	function getCsList($where, $page, $page_size)
	{
		$sql = "select a.*, b.nick, b.uid, b.bank_member, b.bank_name, (select lev_name from tb_level_config where logo=b.logo and lev=b.mem_lev) as lev_name
						from tb_question a, tb_member b
						where a.mem_id=b.uid ".$where."
						order by regdate desc
						limit ".$page.",".$page_size."";

						echo $sql;

		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$sql = "update tb_question set is_read=1 where idx=".$rs[$i]['idx'];
			$this->db->exeSql($sql);

			$memberModel = Lemon_Instance::getObject("MemberModel",true);
			$memId = $rs[$i]["mem_id"];
			$rs[$i]['mem_idx'] = $memberModel->getMemberRowById($memId, "sn");
		}

		return $rs;

	}

	//▶ 보드 총합
	function getTotal($bbsNo, $where='')
	{
		if( !$this->is_integer_mysql_parameter($bbsNo))
		{
			exit;
		}

		$sql = "select count(*) as cnt from tb_content
						where logo='$this->logo' and province='$bbsNo' ".$where;

		$rs = $this->db->exeSql($sql);

		return $rs[0]['cnt'];
	}

	//▶ 보드 목록
	function getList($bbsNo, $page, $page_size, $where="")
	{
		if( !$this->is_integer_mysql_parameter($bbsNo))
		{
			exit;
		}

		$sql = "select *
						from tb_content
						where  province='$bbsNo' and logo='$this->logo' $where
						order by top desc, time desc limit $page, $page_size" ;

		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{

			$sql = "select count(*) as cnt from tb_content_reply where num='".$rs[$i]['id']."'";
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['reply'] = $rsi[0]['cnt'];
			$sql = "select mem_lev from tb_member where nick='".$rs[$i]['author']."'";
			$rsi = $this->db->exeSql($sql);
			if(sizeof($rsi)<=0) {$level = 2;}
			else								{$level = $rsi[0]['mem_lev'];}
			$rs[$i]['mem_lev']=$level;

			//게시물에 배팅내역이 있는지 유무 확인
			$isBetlist="Y";
			$betting_no=split(';', $rs[$i]['betting_no']);

			foreach($betting_no as $value){
				$Betrow=$this->getRow("user_del", "tb_total_cart", "betting_no='".$value."'");

				$isBetlist=$Betrow['user_del'];

				if($isBetlist=='N'){
					break;
				}
			}
			$rs[$i]['isBet_list']=$isBetlist;
		}

		return $rs;
	}

	//▶ 추천 보드 목록
	function getTopList()
	{
		$sql = "select *
				from tb_content
					where logo='$this->logo' and province=2 and top=2
						order by top desc, time desc";

		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$sql = "select count(*) as cnt
					from tb_content_reply
						where num='".$rs[$i]['id']."'";

			$rsi = $this->db->exeSql($sql);
			$rs[$i]['reply'] = $rsi[0]['cnt'];
		}
		return $rs;
	}

	//▶ 공지 목록
	function getNoticeList()
	{
		$sql = "select *
				from tb_content
					where logo='".$this->logo."' and province=2
						order by top desc,time desc limit 0, 10";

		return $this->db->exeSql($sql);
	}

	//▶ 자유게시판 목록
	function getFreeBoardList()
	{
		$sql = "select *
				from tb_content
					where logo='".$this->logo."' and province=5
						order by top desc,time desc limit 0, 10";

		return $this->db->exeSql($sql);
	}

	//▶ 보드 카테고리 수정
	function modifyBoardCategory($name, $en, $sn)
	{
		$sql = "update tb_board_category
				set name='".$name."',en='".$en."'
					where logo='".$this->logo."' and  sn in (".$sn.")";

		return $this->db->exeSql($sql);
	}

	//▶ 보드 카테고리 삭제
	function delBoardCategory($sn)
	{
		$sql = "delete from tb_board_category
				where logo='".$this->logo."' and sn in (".$sn.")";

		return $this->db->exeSql($sql);
	}

	//▶ 보드 카테고리 추가
	function addBoardCategory($name, $en)
	{
		$sql = "insert into tb_board_category(name,en,logo)
				values('".$name."','".$en."','".$this->logo."')";

		return $this->db->exeSql($sql);
	}

	//▶ 보드 카테고리 목록
	function getBoardCategoryList($where="")
	{
		$sql = "select *
						from tb_board_category
						where logo='".$this->logo."'".$where;

		return $this->db->exeSql($sql);
	}

	//▶ 게시글 추가
	function add($province, $title, $srcnum, $imgsrc, $author, $top, $content, $ip, $time, $hit,$betting_no='', $logo='')
	{
		if( $logo=='')
		{
			$logo = $this->logo;
		}
		if( !$this->is_integer_mysql_parameter($province))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($srcnum))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($top))
		{
			exit;
		}
		if( !$this->is_ip_mysql_parameter($ip))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($hit))
		{
			exit;
		}
		if( !$this->is_betting_no_mysql_parameter($betting_no))
		{
			exit;
		}

		$sql = "insert into tb_content(province,title,imgnum,pic,author,top,content,ip,time,hit,betting_no,logo)
				values ('".$province."','".$title."','".$srcnum."','".$imgsrc."','".$author."','".$top."','".$content."','".$ip."','".$time."','".$hit."','".$betting_no."','".$logo."')";

		return $this->db->exeSql($sql);
	}

	//▶ 게시글 수정
	function modify($province, $title, $srcnum, $imgsrc, $author, $top, $content, $ip, $time, $hit, $sn)
	{
		if( !$this->is_integer_mysql_parameter($province))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($srcnum))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($top))
		{
			exit;
		}
		if( !$this->is_ip_mysql_parameter($ip))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($hit))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "update tb_content
						set province='".$province."',title='".$title."',imgnum='".$srcnum."',pic='".$imgsrc."',author='".$author."',top='".$top."',content='".$content."',ip='".$ip."',time='".$time."',hit='".$hit."'
					where id in (".$sn.")";

		return $this->db->exeSql($sql);
	}

	//▶ 게시글 아이디 검색
	function getContentById($id)
	{
		$sql = "select imgnum,pic
						from tb_content
						where id in (".$id.")";


		return $this->db->exeSql($sql);
	}

	//▶ 게시글
	function getContent($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select a.*, ifnull(b.mem_lev,2) as mem_lev from tb_content a left outer join tb_member b on a.author=b.nick
						where a.id=".$sn."";

		$rs = $this->db->exeSql($sql);

		return $rs[0];
	}

	//▶ 게시글 bbs 검색
	function getContentByBbsNo($id)
	{
		$sql = "select imgnum,pic
				from tb_content
					where logo='".$this->logo."' and  province in (".$id.")";

		return $this->db->exeSql($sql);
	}

	//▶ 게시글 총합
	function getContentTotal($where, $province="")
	{
		if( !$this->is_integer_mysql_parameter($province))
		{
			exit;
		}

		if($province!=2 && $province!=7 && $province!=9 && $province!=10)
		{
			$sql = "select count(*) as cnt
							from tb_content a,tb_board_category b /*,tb_member c*/
							where a.province=b.sn /*and a.author=c .nick*/ ".$where;
		}
		//유저가 쓴 글이 아닐경우
		else
		{
			$sql = "select count(*) as cnt
							from tb_content a,tb_board_category b
							where a.province=b.sn ".$where;
		}

		$rs = $this->db->exeSql($sql);

		return $rs[0]['cnt'];
	}

	//▶ 게시글 목록
	function getContentList($arrId)
	{
		$sql = "select * from tb_content
						where id in (".$arrId.")";

		return $this->db->exeSql($sql);
	}

	//▶ 게시글 목록
	function getContentListPage($where, $page, $page_size, $province='')
	{
		/*
		if($province!=2 && $province!=7)
		{
			$sql = "select a.*, b.name as typename,
							c.uid, c.bank_member
							from tb_content a,tb_board_category b,tb_member c
							where a.logo='".$this->logo."' and a.province=b.sn and a.author=c.nick ".$where."
							order by time desc limit ".$page.",".$page_size."";
		}
		//유저가 쓴 글이 아닐경우
		else
		{
			$sql = "select a.*, b.name as typename
							from tb_content a,tb_board_category b
							where a.logo='".$this->logo."' and a.province=b.sn ".$where."
							order by time desc limit ".$page.",".$page_size."";
		}
		*/

		$sql = "select a.*, b.name as typename, c.uid, c.bank_member
							from tb_content a left outer join tb_member c on a.author=c.nick,tb_board_category b
						where a.province=b.sn ".$where."
						order by time desc limit ".$page.",".$page_size."";


		$rs = $this->db->exeSql($sql);

		//댓글
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$id  = $rs[$i]['id'];
			$sql = "select count(*) as cnt from tb_content_reply where num='".$id."'";

			$rsi = $this->db->exeSql($sql);
			$rs[$i]['reply'] = $rsi[0]['cnt'];

			$author = $rs[$i]["author"];
		}

		return $rs;
	}

	//▶ 게시글 삭제 아이디
	function delContentById($sn)
	{
		$sql = "delete from tb_content
						where id in (".$sn.")";

		return $this->db->exeSql($sql);
	}

	//▶ 게시글 삭제 bbs no
	function delContentByBbsNo($bbs)
	{
		$sql = "delete from tb_content
						where province in (".$bbs.")";

		return $this->db->exeSql($sql);
	}

	//▶ 게시글 추가
	function addContent($author, $title, $content, $bettingNo, $ip)
	{
		if( !$this->is_betting_no_mysql_parameter($bettingNo))
		{
			exit;
		}
		if( !$this->is_ip_mysql_parameter($ip))
		{
			exit;
		}

		//유저 검색(포인트 추가를 위함)
		$sql = "select sn from tb_member
						where nick='".$author."' and logo='".$this->logo."'";
		$rs = $this->db->exeSql($sql);
		$memberSn = $rs[0]['sn'];

		if(sizeof($rs)<=0)
			return array();

		if($this->enableBoardWrite($memberSn, 1)==0)
				return "auth_failed";

		$sql = "insert into tb_content(province,title,imgnum,pic,author,top,content,ip,time,hit,betting_no,logo)
						values ('5','".$title."','0','','".$author."','1','".$content."','".$ip."',now(),'0','".$bettingNo."','".$this->logo."')";
		$boardSn = $this->db->exeSql($sql);

		if($bettingNo=="" || $bettingNo==0)	{$message="게시물 작성"; $type=2;}
		else																{$message="배팅게시물 작성"; $type=3;}
		$point = $this->getBoardPoint($memberSn, $type, $boardSn);
		if($point > 0)
		{
			$processModel = Lemon_Instance::getObject("ProcessModel",true);
			//$sn, $amount, $status, $statusMessage, $rate=0, $winCount=''
			$processModel->modifyMileageProcess($memberSn, $point, 10, $message, 100);
		}

		return $boardSn;

	}

	//▶ 게시글 수정
	function modifyContent($sn, $title, $content )
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "update tb_content
							set title='".$title."', content='".$content."' where logo='".$this->logo."' and id = ".$sn;

		return $this->db->exeSql($sql);
	}

	//▶ 게시글 수정
	function modifyRandomHit($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "update tb_content
				set hit=hit+'".rand(0,5)."' where id=".$sn."";

		return $this->db->exeSql($sql);
	}

	function modifyTop($id, $top)
	{
		$sql = "update tb_content set top='".$top."' where id=".$id."";

		return $this->db->exeSql($sql);
	}

	//▶ 공지 총합
	function getBoardTotal($where='',$level=110)
	{
		$addwhere = " and lev = ".$level;
		$sql = "select count(*) as cnt
				from tb_board
					where logo='".$this->logo."'".$addwhere.$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 공지 리스트
	function getBoardList($where='', $page, $page_size, $level=110)
	{
		$addwhere = " and lev = ".$level;

		$sql = "select *
				from tb_board
					where logo='".$this->logo."'".$addwhere.$where."
						order by regdate desc limit ".$page.",".$page_size."";

		$rs = $this->db->exeSql($sql);

		return $rs;
	}

	//▶ 공지 추가
	function addBoard($writer, $subject, $content, $write_datetime, $view_code )
	{
		$sql = "insert into tb_board (name,nick,title,content,regdate,lev,step,owner,logo) values (";
		$sql = $sql."'".$writer."','".$writer."','".$subject."','".$content."','".$write_datetime. "','".$view_code."',";
		$sql = $sql." '0','2','".$this->logo."')";

		return $this->db->exeSql($sql);
	}

	//▶ 파트너 공지 총합
	function getPartnerBoardTotal($where)
	{
		$sql = "select count(*) as cnt
				from tb_board
					where logo='".$this->logo."' and lev = 130 ".$where;

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 파트너 공지 리스트
	function getPartnerBoardList($where, $page, $page_size)
	{
		$sql = "select num,title,regdate from tb_board
							where logo='".$this->logo."' and lev=130 order by regdate desc limit ".$page.",".$page_size;
		$rs = $this->db->exeSql($sql);
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$beginIndex = 0;
			$length = 30;
			$string = $rs[$i]['title'];

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

		$rs[$i]['title'] = $string;

		}
		return $rs;
	}

	//▶ 파트너 공지
	function getPartnerBoard($sn)
	{
		$sql = "select  * from tb_board
							where logo='".$this->logo."' and num = '".$sn."' limit 0,1";

		$rs = $this->db->exeSql($sql);
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$nick = $rs[$i]['nick'];
			$content = $rs[$i]['content'];
			$content = str_replace("cafe","<font color=red>낚시글조심</font>",$content);
			$rs[$i]['content'] = $content;

			$sql = "select uid from tb_member
								where logo='".$this->logo."' and nick = '".$nick."'";
			$rsi = $this->db->exeSql($sql);
			$mem_id = $rsi[0]['mem_id'];
			$rs[$i]['mem_id'] = $mem_id;

		}
		return $rs;
	}

	//▶ 공지 삭제
	function delBoard($idx)
	{
		$sql = "delete from tb_board
						where logo='".$this->logo."' and num in(".$idx.")";

		return $this->db->exeSql($sql);
	}

	//▶ 댓글 삭제
	function delReply($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "delete  from tb_content_reply where idx=".$sn."";

		return $this->db->exeSql($sql);
	}

	//▶ 관리자 댓글 수정
	function AdminmodifyReply($sn, $content)
	{
		$sql = "update tb_content_reply set content='".$content."' where idx=".$sn;

		return $this->db->exeSql($sql);
	}

	//▶ 댓글 삭제
	function delReplyById($replySn, $uid)
	{
		if( !$this->is_integer_mysql_parameter($replySn))
		{
			exit;
		}

		//댓글 포인트 확인
		$sql = "select sn, member_sn, point from tb_member_board_point
						where board_sn=".$replySn;
		$rs = $this->db->exeSql($sql);

		if(sizeof($rs)>0)
		{
			$sn 			= $rs[0]['sn'];
			$amount 	= $rs[0]['point'];
			$memberSn = $rs[0]['member_sn'];

			$processModel = Lemon_Instance::getObject("ProcessModel",true);
			$processModel->modifyMileageProcess($memberSn, -$amount, 11, "댓글 취소", 100);

			$sql = "delete from tb_member_board_point where sn=".$sn;
			$this->db->exeSql($sql);
		}

		$sql = "delete from tb_content_reply where idx=".$replySn." and mem_id='".$uid."'";

		return $this->db->exeSql($sql);
	}

	//▶ 댓글 목록
	function getReplyList($id)
	{
		$sql = "select * from tb_content_reply where num ='".$id."' order by idx";

		return $this->db->exeSql($sql);
	}

	//▶ 회원 댓글 입력확인
	function eventReplyEnable($sn, $author)
	{
		$sql = "select count(*) as cnt from tb_content_reply
						where num ='".$sn."' and mem_nick='".$author."'";

		$rs = $this->db->exeSql($sql);
		return $rs[0]['cnt'];
	}

	//▶ 댓글 입력
	function addReply($article, $content, $memberSn="")
	{
		$uid  = $this->req->request('mid');
		$nick = $this->req->request('mnk');

		if( !$this->is_integer_mysql_parameter($article))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		if($memberSn!="")
		{
			if($this->enableBoardWrite($memberSn, 2)==0)
				return "auth_failed";
		}

		$sql = "insert into tb_content_reply(num,mem_id,mem_nick,content,regdate)
						values ('".$article."','".$uid."','".$nick."','".$content."',now())";
		$replySn = $this->db->exeSql($sql);

		//유저 검색(활동포인트 추가를 위함)
		if($memberSn!="")
		{
			$sql = "select sn from tb_member
							where sn='".$memberSn."' and logo='".$this->logo."'";
			$rs = $this->db->exeSql($sql);
			if(sizeof($rs)>0)
			{
				$sn = $rs[0]['sn'];
				$type = 1;
				$point = $this->getBoardPoint($sn, $type, $replySn);
				if($point > 0)
				{
					$processModel = Lemon_Instance::getObject("ProcessModel",true);
					$processModel->modifyMileageProcess($sn, $point, 10, "댓글작성", 100);
				}
			}
		}
		return $replySn;
	}

	//▶ 댓글 입력
	function adminReply($article, $author, $content)
	{
		$sql = "insert into tb_content_reply(num,mem_id,mem_nick,content,regdate)
						values ('".$article."','관리자 리플','".$author."','".$content."',now())";
		$replySn = $this->db->exeSql($sql);

		//유저 검색(활동포인트 추가를 위함)
		if($memberSn!="")
		{
			$sql = "select sn from tb_member
							where sn='".$memberSn."' and logo='".$this->logo."'";
			$rs = $this->db->exeSql($sql);
			if(sizeof($rs)>0)
			{
				$sn = $rs[0]['sn'];
				$type = 1;
				$point = $this->getBoardPoint($sn, $type, $replySn);
				if($point > 0)
				{
					$processModel = Lemon_Instance::getObject("ProcessModel",true);
					$processModel->modifyMileageProcess($sn, $point, 10, "댓글작성", 100);
				}
			}
		}
		return $replySn;
	}

	//▶ 글을 쓸수 있는지 권한을 검사
	function enableBoardWrite($memberSn, $type/*1=게시글, 2=댓글, 3=고객센터*/)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		$sql = "select memo from tb_member where sn='".$memberSn."' and logo='".$this->logo."'";
		$rs = $this->db->exeSql($sql);

		$enable = 1;
		if(sizeof($rs)>0)
		{
			$auth = $rs[0]['memo'];
			//게시글
			if($type==1) 			{$enable = $auth[0];}
			else if($type==2) {$enable = $auth[1];}
			else if($type==3) {$enable = $auth[2];}
		}
		else
			$enable = 0;
		return $enable;
	}

	function modifyHit($sn,$hit=1)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($hit))
		{
			exit;
		}

		if($hit==0)	{$sql = "update tb_content set hit=hit+1 where logo='".$this->logo."' and id=".$sn."";}
		else				{$sql = "update tb_content set hit = ".$hit." where logo='".$this->logo."' and id=".$sn."";}

		return $this->db->exeSql($sql);
	}

	function hDel($author)
	{
		$boardSn = $this->req->request('hid');

		if( !$this->is_integer_mysql_parameter($boardSn))
		{
			exit;
		}

		//게시글 포인트 확인
		$sql = "select sn, member_sn, point, type from tb_member_board_point
						where board_sn=".$boardSn;
		$rs = $this->db->exeSql($sql);

		if(sizeof($rs)>0)
		{
			$sn 			= $rs[0]['sn'];
			$amount 	= $rs[0]['point'];
			$memberSn = $rs[0]['member_sn'];
			$type			= $rs[0]['type'];

			if($type==2)	{$message = "게시글 작성 취소";}
			else if($type==3)	{$message = "배팅게시글 작성 취소";}
			$processModel = Lemon_Instance::getObject("ProcessModel",true);
			$processModel->modifyMileageProcess($memberSn, -$amount, 11, $message, 100);

			$sql = "delete from tb_member_board_point
							where sn=".$sn;
			$this->db->exeSql($sql);
		}

		$sql = "delete from tb_content
						where id=".$boardSn." and author='".$author."' and logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	function modifyReply($comment, $reid, $id)
	{
		if( !$this->is_integer_mysql_parameter($reid))
		{
			exit;
		}

		$sql = "update tb_content_reply
				set content='".$comment."',regdate=now()
					where idx=".$reid." and mem_id='".$id."'";

		return $this->db->exeSql($sql);
	}

	function getReplyLastTime($article, $id)
	{
		if( !$this->is_integer_mysql_parameter($article))
		{
			exit;
		}

		$mid = $this->req->request('mid');

		$sql = "select regdate
				from tb_content_reply
					where num=".$article." and mem_id='".$id."' order by regdate desc limit 0,1";
		$rs = $this->db->exeSql($sql);
		return $rs[0]['regdate'];
	}

	function getQuestion($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select a.regdate as question_regdate, a.*, b.*, (select lev_name from tb_level_config where logo = b.logo and lev=b.mem_lev) as lev_name from tb_question a, tb_member b
							where a.mem_id=b.uid and idx = '".$sn."'";
		$rs = $this->db->exeSql($sql);

		for($i = 0; $i< sizeof($rs); ++$i )
		{
			$content = $rs[$i]['content'];
			$content = str_replace("cafe","<font color=red>낚시글조심</font>",$content);
			$rs[$i]['content']	= $content;

			$sql = "update tb_question set is_read=1 where idx=".$rs[$i]['idx'];
			$this->db->exeSql($sql);
		}

		echo $rs['logo'];

		return $rs;
	}

	function getCsReply($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select * from tb_question where reply = '".$sn."'";

		$rs = $this->db->exeSql($sql);
		for($i = 0; $i< sizeof($rs); ++$i )
		{
			$content = $rs[$i]['content'];
			$content = str_replace("cafe","<font color=red>낚시글조심</font>",$content);
			$rs[$i]['content']	= $content;
		}

		return $rs;
	}

	//▶ 고객센터 추가
	function addCs($sn, $comment)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$write_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];

		if(!preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',$write_ip))
		{
			session_destroy();
			header("Location:http://www.google.com");
			exit;
		}

		$sql = "insert into  tb_question (subject,content,mem_id,kubun,regdate,result,reply,chk,logo, write_ip)
							values ('','".$comment."','','',now(),0,'".$sn."',0,'".$this->logo."', '".$write_ip."')";
		return $this->db->exeSql($sql);
	}

	//▶ 댓글 고객센터 검색
	function getCsByReplySn($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select idx from tb_question where reply='".$sn."'";

		return $this->db->exeSql($sql);
	}

	//▶ 고객센터 수정
	function modifyCs($sn, $comment)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "update tb_question set content='".$comment."' where idx=".$sn;

		return $this->db->exeSql($sql);
	}

	//▶ 고객센터 수정
	function modifyCsReply($sn,$status)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($status))
		{
			exit;
		}

		$sql = "update tb_question set result = ".$status." where idx ='".$sn."'";

		return $this->db->exeSql($sql);
	}

	//▶ 고객센터 삭제
	function deleteMemberCsAll($uid, $logo='')
	{
		if($logo!='') $logo = " and logo='".$logo."'";
		$sql = "update tb_question set state='D'	where mem_id='".$uid."' ".$logo;
		$rs = $this->db->exeSql($sql);
		return $rs;
	}

	function deleteMemberCs($id, $uid)
	{
		if( !$this->is_integer_mysql_parameter($id))
		{
			exit;
		}

		$sql = "update tb_question set state='D'	where idx='".$id."' and mem_id='".$uid."' ";
		$rs = $this->db->exeSql($sql);
		return $rs;
	}

	function addMemberCs($uid, $subject, $content, $kubun, $memberSn="")
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}

		//유저 검색(포인트 추가를 위함)
		if($memberSn!="")
		{
			if($this->enableBoardWrite($memberSn, 3)==0)
					return "auth_failed";
		}

		$sql = "insert into tb_question(subject,content,mem_id,kubun,regdate,result,reply,logo)
						values('".$subject."','".$content."','".$uid."','".$kubun."',now(),'0','0','".$this->logo."')";

		return $this->db->exeSql($sql);
	}

	function getMemberCsTotal($uid)
	{
		$sql = "select count(*) as cnt
				from tb_question
					where logo='".$this->logo."' and mem_id = '".$uid."' and reply = 0";

		$rs = $this->db->exeSql($sql);

		return $rs[0]['cnt'];
	}


	function getMemberCsList($uid, $page, $page_size, $where="")
	{
		$sql = "select *
						from tb_question
						where logo='".$this->logo."' and  mem_id = '".$uid."' and reply = 0".$where." order by result asc,regdate desc  limit ".$page.",".$page_size;

		$rs = $this->db->exeSql($sql);

		for($i=0; $i<sizeof($rs); ++$i)
		{
			$idx = $rs[$i]['idx'];
			$sql = "select * from tb_question where reply = '".$idx."'";
			$rsi = $this->db->exeSql($sql);
			$rs[$i]['reply'] = $rsi[0];
		}

		return $rs;
	}

	//▶ 게시판 메모 추가
	function addBoard_Memo($id, $name, $comment)
	{
		$sql = "insert into tb_board_memo (num,mem_id,mem_nick,content,regdate,imo) VALUES (";
		$sql.= "'".$id."','".$name."','".$name."','".$comment."',now(),'4')";

		return $this->db->exeSql($sql);
	}

	//▶ 게시판 메모 삭제
	function delBoard_Memo($sn)
	{
		$sql = "delete from tb_board_memo
							where idx = '".$sn."'";

		return $this->db->exeSql($sql);
	}
	//▶ 게시판 메모 리스트
	function getBoard_Memo($num)
	{
		$sql = "select * from tb_board_memo
							where num ='".$num."' order by idx desc";

		return $this->db->exeSql($sql);
	}

	//▶ 게시판 내용수정
	function modifyBoard($id, $title, $content, $time, $hit)
	{
		if( !$this->is_integer_mysql_parameter($hit))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($id))
		{
			exit;
		}

		$sql = "update tb_board
							set title='".$title."',content='".$content."',regdate='".$time."',hit='".$hit."' where num=".$id."";

		return $this->db->exeSql($sql);
	}

	//▶ 게시판 내용목록 한개의 데이터
	function getBoardOne($sn)
	{
		if( !$this->is_integer_mysql_parameter($sn))
		{
			exit;
		}

		$sql = "select  * from tb_board
							where num = '".$sn."' limit 0,1";

		$rs = $this->db->exeSql($sql);
		for( $i = 0; $i < sizeof($rs); ++$i )
		{
			$content = str_replace("cafe","<font color=red>낚시글조심</font>",$rs[$i]['content']);
			$rs[$i]['content'] = $content;
		}
		return $rs[0];
	}

	///////////////////////////////////////////////////////////////////////////
	//
	//▶ process functions
	//
	///////////////////////////////////////////////////////////////////////////

	//▶ 메인화면에 노출되는 게시판 목록
	function getIndexBoardProcess($bbsNo)
	{
		if( !$this->is_integer_mysql_parameter($bbsNo))
		{
			exit;
		}

		$sql = "select id, title
				from tb_content
					where logo='".$this->logo."' and province=".$bbsNo."
						order by top desc,time desc limit 0, 6";
		$rs = $this->db->exeSql($sql);

		$rows = array();
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$rows[] = array('board' => $rs[$i]);
			$loop = &$rows[$i]['item'];

			$sql = "select count(*) as cnt
					from tb_content_reply
						where num='".$rs[$i]['id']."'";
			$rs_reply = $this->db->exeSql($sql);

			$loop[] = $rs_reply[0]['cnt'];
		}
		return $rows;
	}

	function getFaqList()
	{
		$sql = "select *
						from tb_content where logo='".$this->logo."' and  province=4 order by time desc";

		return $this->db->exeSql($sql);
	}

	function getCsId($id)
	{
		if( !$this->is_integer_mysql_parameter($id))
		{
			exit;
		}

		$sql = "select idx
						from tb_question where logo='".$this->logo."' and reply='".$id."' ";

		$rs = $this->db->exeSql($sql);

		return $rs[0]['idx'];
	}

	function getBoardPoint($memberSn, $type, $boardSn)
	{
		if( !$this->is_integer_mysql_parameter($memberSn))
		{
			exit;
		}
		if( !$this->is_integer_mysql_parameter($boardSn))
		{
			exit;
		}

		$point_field = "";
		$point_limit = "";

		if($type==1)
		{
			$point_field = "reply_point";
			$point_limit = "reply_limit";
		}
		elseif($type==2)
		{
			$point_field="board_write_point";
			$point_limit="board_write_limit";
		}
		elseif($type==3)
		{
			$point_field="betting_board_write_point";
			$point_limit="betting_board_write_limit";
		}

		$sql = "select ".$point_field." as point, ".$point_limit." as point_limit
						from tb_point_config
						where logo='".$this->logo."'";
		$rs = $this->db->exeSql($sql);

		$point = $rs[0]['point'];
		$pointLimit = $rs[0]['point_limit'];

		$today = date("Y-m-d");
		$sql = "select count(*) as cnt
						from tb_member_board_point
						where member_sn=".$memberSn." and type=".$type."
						and regdate between '".$today." 00:00:00' and '".$today." 23:59:59'";
		$rs = $this->db->exeSql($sql);

		if($rs[0]['cnt'] >= $pointLimit)
		{
			return 0;
		}
		else
		{
			$sql = "insert into tb_member_board_point(member_sn, regdate, type, point, board_sn, logo)
							values(".$memberSn.", now(), ".$type.",".$point.",".$boardSn.", '".$this->logo."')";
			$rs = $this->db->exeSql($sql);
		}
		return $point;
	}
	//▶ 사이트 규정 필드
	function getSiteRuleRow($type/*1=회원약관,2=배팅룰*/, $field="*")
	{
		$sql = "select ".$field." from tb_site_rule where type=".$type." and logo='".$this->logo."'";
		$rs = $this->db->exeSql($sql);

		if($field!="*")
			return $rs[0][$field];
		return $rs[0];
	}

	//▶ 사이트 규정 수정
	function modifySiteRule($ruleSn, $content)
	{
		$sql = "update tb_site_rule set content='".$content."'
						where sn=".$ruleSn;

		return $this->db->exeSql($sql);
	}

	function get_upload_betting_type($betting_no)
	{
		if( !$this->is_integer_mysql_parameter($betting_no))
		{
			exit;
		}

		//normal
		$sql = "select count(*) as cnt from tb_total_cart where betting_no='".$betting_no."'";
		$rows = $this->db->exeSql($sql);
		if($rows[0]['cnt'] > 0)
			return "N";

		//live
		$sql = "select count(*) as cnt from tb_live_betting where betting_no='".$betting_no."'";
		$rows = $this->db->exeSql($sql);
		if($rows[0]['cnt'] > 0)
			return "L";

		//virtual
		$sql = "select count(*) as cnt from tb_live_virtual_betting where betting_no='".$betting_no."'";
		$rows = $this->db->exeSql($sql);
		if($rows[0]['cnt'] > 0)
			return "V";

		//error
		return "E";
	}

	// 게시글 연동 부분
    function getLastId()
    {
        $remoteSite = 'kingdom';
        $sql = "select * from tb_board_log where site = '$remoteSite' order by idx desc limit 1";

        $rs = $this->db->exeSql($sql);

        if( sizeof($rs) == 0 )  // 가져온 기록이 없음
            return 0;
        else
            return $rs[0]['last_id'];

    }

    function setLastId($id, $detail , $remoteSite='kingdom')
    {
        $sql = "insert into tb_board_log (site, last_id, detail) values ('$remoteSite', $id, '$detail')";
        $this->db->exeSql($sql);
    }

    //▶ 게시글(연동) 추가
	function addRemoteContent($content)
	{

		$sql = "insert into tb_content(province,title,imgnum,pic,author,top,content,ip,time,hit,betting_no,logo,remote, orignal_id)
						values ('5','".$content['title']."','0','','".$content['author']."','1','".$content['content']."','".$content['ip']."','".$content['time']."','".$content['hit']."','".$content['betting_no']."','totobang','kingdom', ".$content['id'].")";
		$boardSn = $this->db->exeSql($sql);

		return $boardSn;

	}

    // 댓글 연동 부분
    function getCommentLastIdx()
    {
        $remoteSite = 'kingdom';
        $sql = "select * from tb_comment_log where site = '$remoteSite' order by idx desc limit 1";

        $rs = $this->db->exeSql($sql);

        if( sizeof($rs) == 0 )  // 가져온 기록이 없음
            return 0;
        else
            return $rs[0]['last_idx'];
    }

    function setCommentLastIdx($idx, $detail , $remoteSite='kingdom')
    {
        $sql = "insert into tb_comment_log (site, last_idx, detail) values ('$remoteSite', $idx, '$detail')";
        $this->db->exeSql($sql);
    }

    //▶ 댓글(연동) 추가
	function addRemoteComment($content)
	{
		if($content['mem_nick'] == '관리자')
            return;

		$sql = "select * from tb_content where orignal_id = ".$content['num'];
        $rs = $this->db->exeSql($sql);

        if( sizeof($rs) > 0 )
        {
            $row = $rs[0];
            $sql = "insert into tb_content_reply (num, mem_id, mem_nick, content, regdate)
                        values (".$row['id'].", null, '".$content['mem_nick']."', '".$content['content']."', '".$content['regdate']."')";
            $this->db->exeSql($sql);
        }

	}

    // 없는 게임 삭제
    function deleteBetNoHave($list)
    {
        foreach($list as $num => $betItem )
        {
            $delete = false;
            foreach($betItem['betting'] as $eachBetting)
            {
                foreach($eachBetting['item'] as $eachSubchild)
                {
                    // 각 sub_child_sn 검색
                    $subchildSql = "select * from tb_subchild_comming_log where sn = ".$eachSubchild['sub_child_sn'];
                    $rs = $this->db->exeSql($subchildSql);

                    if( $rs == false )
                        $delete = true;
                }
            }

            if($delete == true)
                unset($list[$num]);
        }

        return $list;
    }
}
?>
