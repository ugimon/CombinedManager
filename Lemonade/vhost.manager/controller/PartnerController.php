<?php


class PartnerController extends WebServiceController
{
	//▶ 총판목록
	function listAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/list.html");

		$model 		= $this->getModel("PartnerModel");


		$eModel 	= $this->getModel("EtcModel");

		$keyword 	= $this->request('keyword');
		$act 			= $this->request('act');
		$filter_logo 			= $this->request('filter_logo');
		$beginDate	= $this->request("begin_date");
		$endDate		= $this->request("end_date");

		//디폴트 날짜 = 1일부터 1개월
		if($beginDate=='' or $endDate=='')
		{
			$beginDate 	= date("Y-m-")."01";
			$newdate = strtotime ( '1 month' , strtotime ( $beginDate ) ) ;
			$newdate = strtotime ( '-1 day' , strtotime ( date ( 'Y-m-d' , $newdate ) ) ) ;
			$endDate = date ( 'Y-m-d' , $newdate );
		}

		if($act=="stop")
		{
			$id 		= $this->request('id');
			$status = $this->request('send');
			$model->modifyRecommend($id, $status);
		}
		if($act=="del")
		{
			$id 	= $this->request('id');
			$rs 	= $model->delRecommend($id);
			if( $rs > 0 )
			{
				throw new Lemon_ScriptException("삭제 되였습니다.", "", "go", "/partner/list");
				exit;
			}
		}

		$where = " and  rec_grd=1 /* 상위총판 */";

		if($keyword != "")
		{
			$where .= " and rec_id ='".$keyword."'";
		}

		if($filter_logo!='')
		{
			$where .= " and  logo='".$filter_logo."'";
		}

		$rs = $eModel->getLevel();

		$arr_mem_lev = array();
		for( $i=0; $i < sizeof($rs); ++$i )
		{
			$level = $rs[$i]['lev'];
			$levelName = $rs[$i]['lev_name'];
			$arr_mem_lev[ $level ] = $levelName;
		}

		$total = $model->getRecommendTotal($where);
		$page_act = "begin_date=".$beginDate."&end_date=".$endDate;
		$pageMaker 	= $this->displayPage($total, 10, $page_act);


		$list = $model->getRecommendList($where, $beginDate,$endDate,$pageMaker->first, $pageMaker->listNum);

		//echo "<pre>", var_dump($list), "</pre>";

		$logoModel = $this->getModel("LogoModel");
		$logoList = $logoModel->getList();


		$this->view->assign('arr_mem_lev', $arr_mem_lev);
		$this->view->assign('list', $list);
		$this->view->assign('filter_logo', $filter_logo);
		$this->view->assign('keyword', $keyword);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);

		$this->view->assign('logo_list', $logoList);

		$this->display();
	}

	function accountingAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/accounting.html");

		$model = $this->getModel("PartnerModel");
		$eModel 	= $this->getModel("EtcModel");

		$keyword 	= $this->request('keyword');
		$act 			= $this->request('act');
		$logo 			= $this->request('logo');
		$beginDate	= $this->request("begin_date");
		$endDate		= $this->request("end_date");

		//디폴트 날짜 = 1일부터 1개월
		if($beginDate=='' or $endDate=='')
		{
			$today = date("d", time());

			if($today > 15)
			{
				$beginDate 	= date("Y-m-")."16";
				$newdate = strtotime ( '1 month' , strtotime ( $beginDate ) ) ;
				$newdate = strtotime ( '-1 day' , strtotime ( date ( 'Y-m' , $newdate ) ) ) ;
				$endDate = date ( 'Y-m-d' , $newdate );
			}

			else
			{
				$beginDate 	= date("Y-m-")."01";
				$endDate = date ( 'Y-m-')."15";
			}


		}

		if($act == "account")
		{
			$pid 	= $this->request('pid');
			$jc 		= $this->request('j_cnt');
			$ic 		= $this->request('i_cnt');
			$im 		= $this->request('in_money');
			$om 		= $this->request('out_money');
			$crm 	= $this->request('carry_money');
			$clm 	= $this->request('calc_money');
			$tmo 	= $this->request('total_money');
			$bt 		= $this->request('begin_time');
			$et 		= $this->request('end_time');

			$rs = $model->insertAccount($pid, $jc, $ic, $im, $om, $crm, $clm, $tmo, $bt, $et);

			if(sizeof($rs) > 0)
			{
				throw new Lemon_ScriptException("성공적으로 처리되었습니다!", "", "go", "/partner/accounting");
				exit();
			}
		}

		if(isset($act) && $act=="cancel")
		{
			$idx = $this->request('idx');
			$rs = $model->delAccounting( $idx );

			if(sizeof($rs) > 0)
			{
				throw new Lemon_ScriptException("삭제 되였습니다.", "", "go", "/partner/accounting");
				exit();
			}
		}

		if(isset($act) && $act == "renew")
		{
			$idx = $this->request("idx");

			$rs = $model->modifyAccounting( $idx );

			if(sizeof($rs) > 0)
			{
				throw new Lemon_ScriptException("성공적으로 처리되었습니다!", "", "go", "/partner/accounting");
				exit();
			}
		}

		$where = " and  rec_grd=1 /* 상위총판 */ and isView = 'Y'";

		if($keyword != "")
		{
			$where .= " and rec_id ='".$keyword."'";
		}



		$rs = $eModel->getLevel();

		$arr_mem_lev = array();
		for( $i=0; $i < sizeof($rs); ++$i )
		{
			$level = $rs[$i]['lev'];
			$levelName = $rs[$i]['lev_name'];
			$arr_mem_lev[ $level ] = $levelName;
		}

		$total = $model->getRecommendTotal($where);
		$page_act = "begin_date=".$beginDate."&end_date=".$endDate;
		$pageMaker 	= $this->displayPage($total, 10, $page_act);


		$list = $model->getRecommendList($where, $beginDate,$endDate,$pageMaker->first, $pageMaker->listNum);

		$this->view->assign('list', $list);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);

		$this->display();
	}

	function accounting_finAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/accounting_fin.html");

		$model = $this->getModel("PartnerModel");
		$act	= $this->request('act');

		$where = "";
		if(isset($act) && $act=="members_list")
		{
			$rec_id = $this->request('rec_id');
			if($rec_id!="")
			{
				$where = "and b.rec_id='".$rec_id."'";
			}
		}

		$total 		= $model->getAccountingfinTotal($where);
		$pageMaker 	= $this->displayPage($total, 10);
		$list 		= $model->getAccountingfinList($where, $pageMaker->first, $pageMaker->listNum);

		$this->view->assign('list', $list);
		$this->display();
	}

	//▶ 하부총판목록
	function childlistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/childlist.html");

		$model 		= $this->getModel("PartnerModel");

		$eModel 	= $this->getModel("EtcModel");
		$keyword 	= $this->request('keyword');
		$act 			= $this->request('act');
		$logo 			= $this->request('logo');
		$beginDate	= $this->request("begin_date");
		$endDate		= $this->request("end_date");

		//디폴트 날짜 = 1일부터 1개월
		if($beginDate=='' or $endDate=='')
		{
			$beginDate 	= date("Y-m-")."01";
			$newdate = strtotime ( '1 month' , strtotime ( $beginDate ) ) ;
			$newdate = strtotime ( '-1 day' , strtotime ( date ( 'Y-m-d' , $newdate ) ) ) ;
			$endDate = date ( 'Y-m-d' , $newdate );
		}


		if($act=="stop")
		{
			$id 		= $this->request('id');
			$status = $this->request('send');
			$model->modifyRecommend($id, $status);
		}
		if($act=="del")
		{
			$id 	= $this->request('id');
			$rs 	= $model->delRecommend($id);
			if( $rs > 0 )
			{
				throw new Lemon_ScriptException("삭제 되였습니다.", "", "go", "/partner/childlist");
				exit;
			}
		}

		$where = " and  rec_grd=2 /* 하위총판 */ ";

		if($keyword != "")
		{
			$where .= " and rec_id ='".$keyword."'";
		}

		 $rs = $eModel->getLevel();

		$arr_mem_lev = array();
		for( $i=0; $i < sizeof($rs); ++$i )
		{
			$level = $rs[$i]['lev'];
			$levelName = $rs[$i]['lev_name'];
			$arr_mem_lev[ $level ] = $levelName;
		}

		$total = $model->getRecommendTotal($where);
		$page_act = "begin_date=".$beginDate."&end_date=".$endDate;
		$pageMaker 	= $this->displayPage($total, 10, $page_act);
		$list = $model->getChildRecommendList($where, $beginDate,$endDate,$pageMaker->first, $pageMaker->listNum);

		//echo "<pre>", var_dump($list), "</pre>";

		$this->view->assign('arr_mem_lev', $arr_mem_lev);
		$this->view->assign('list', $list);
		$this->view->assign('logo', $logo);
		$this->view->assign('keyword', $keyword);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);


		$this->display();
	}


	function popup_partner_member_listAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}

		$this->view->define("content","content/partner/popup_partner_member_list.html");

		$partner_sn = $this->request('partner_sn');
		$partner_name = $this->request('partner_name');

		$partner_model = $this->getModel("PartnerModel");
		$total = $partner_model->getPartnerMemberListTotal($partner_sn, $where);
		$pageMaker 	= $this->displayPage($total, 10);
		$list = $partner_model->getPartnerInMemberList($partner_sn, $where, $pageMaker->first, $pageMaker->listNum);

		$etc_model 	= $this->getModel("EtcModel");
		$level_rows = $etc_model->getLevel();

		for( $i=0; $i < sizeof($level_rows); ++$i )
		{
			$level = $level_rows[$i]['lev'];
			$levelName = $level_rows[$i]['lev_name'];
			$arr_mem_lev[ $level ] = $levelName;
		}

		$this->view->assign('arr_mem_lev', $arr_mem_lev);
		$this->view->assign('list', $list);
		$this->view->assign('partner_name', $partner_name);

		$this->display();
	}


	//▶ 총판목록
	function list_recAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/list.html");

		$model 		= $this->getModel("PartnerModel");
		$eModel 	= $this->getModel("EtcModel");
		$keyword 	= $this->request('keyword');
		$act 			= $this->request('act');
		$logo 			= $this->request('logo');

		if($act=="stop")
		{
			$id 		= $this->request('id');
			$status = $this->request('send');
			$model->modifyRecommend($id, $status);
		}
		if($act=="del")
		{
			$id 	= $this->request('id');
			$rs 	= $model->delRecommend($id);
			if( $rs > 0 )
			{
				throw new Lemon_ScriptException("삭제 되였습니다.", "", "go", "/partner/list");
				exit;
			}
		}

		if($keyword != "")
		{
			$where = " and rec_id ='".$keyword."'";
		}

		$rs = $eModel->getLevel();

		$arr_mem_lev = array();
		for( $i=0; $i < sizeof($rs); ++$i )
		{
			$level = $rs[$i]['lev'];
			$levelName = $rs[$i]['lev_name'];
			$arr_mem_lev[ $level ] = $levelName;
		}

		$total 			= $model->getRecommendTotal($where);
		$pageMaker 	= $this->displayPage($total, 10);
		$list 			= $model->getRecommend_Lev2List($where, $pageMaker->first, $pageMaker->listNum);

		$this->view->assign('arr_mem_lev', $arr_mem_lev);
		$this->view->assign('list', $list);
		$this->view->assign('logo', $logo);
		$this->view->assign('keyword', $keyword);

		$this->display();
	}

	//▶ 상세정보
	function memberDetailsAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/member_details.html");

		$model = $this->getModel("PartnerModel");

		$idx 	= $this->request('idx');
		$act = $this->request('act');

		if($act=="add")
		{
			$memid 							= $this->request('memid');
			$urlidx 						= $this->request('urlidx');
			$pwd 								= $this->request('pwd');
			$memo 							= $this->request('memo');
			$rec_lev 						= $this->request('rec_lev');
			$rec_name 					= $this->request('rec_name');
			$rec_bankname 			= $this->request('rec_bankname');
			$rec_bankusername 	= $this->request('rec_bankusername');
			$rec_banknum 				= $this->request('rec_banknum');
			$rec_email 					= $this->request('rec_email');
			$rec_phone 					= $this->request('rec_phone');
			// 정산률추가
			$default_rate				= $this->request('default_rate');
			//수수료 추가 2016.02.01
			$nc_rate				= $this->request('nc_rate');
			$wb_rate				= $this->request('wb_rate');
			$sb_rate				= $this->request('sb_rate');

			$parent_rec_sn				= $this->request('parent_rec_sn');
			$parent_rate				= $this->request('parent_rate');
			$parent_nc_rate				= $this->request('parent_nc_rate');
			$parent_wb_rate				= $this->request('parent_wb_rate');
			$parent_sb_rate				= $this->request('parent_sb_rate');

			if( $pwd == "default" )
			{
				$pwd="";
				$where="";
			}
			else
			{
				$pwd=md5($pwd);
				$where="rec_psw='".$pwd."',";
			}

			$where .= " default_rate=$default_rate , nc_rate=$nc_rate,wb_rate=$wb_rate,sb_rate=$sb_rate ,parent_rate=$parent_rate , parent_nc_rate=$parent_nc_rate,parent_wb_rate=$parent_wb_rate,parent_sb_rate=$parent_sb_rate , parent_rec_sn =$parent_rec_sn, ";

			$model->modifyMemberDetails($where, $memo, $rec_lev, $rec_name, $rec_bankname, $rec_bankusername, $rec_banknum, $rec_email, $rec_phone, $urlidx );
			echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' ><script>alert('수정 되였습니다.');</script>";
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=/partner/memberDetails?idx=".$urlidx."'>";
			exit;
		}

		$list = $model->getMemberDetails($idx);
		$partnerList = $model->getPartnerIdList(""," and rec_grd=1 "); //상부총판목록
		$this->view->assign('list', $list);

		$this->view->assign('partner_list', $partnerList);

		$this->display();
	}

	//▶ 하부총판 설정
	function memberChildRecAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/member_childRec.html");

		$model = $this->getModel("PartnerModel");

		$idx 	= $this->request('idx');
		$act = $this->request('act');

		if($act=="add")
		{
			$urlidx = $this->request('urlidx');
			$newChildRecSn = $this->request('new_child_partner_sn');
			$newParentRate = $this->request('new_parent_rate');

			// 수수료율 추가 2016.02.01
			$newParentNcRate = $this->request('new_parent_nc_rate');
			$newParentWbRate = $this->request('new_parent_wb_rate');
			$newParentSbRate = $this->request('new_parent_sb_rate');

			$model->setChildRecValues($newChildRecSn, $newParentRate, $newParentNcRate , $newParentWbRate, $newParentSbRate, $urlidx);

			echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' ><script>alert('추가 되였습니다.');</script>";
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=/partner/memberChildRec?idx=".$urlidx."'>";
			exit;
		}
		else if($act=="modify")
		{
			$urlidx = $this->request('urlidx');
			$child_rec_sn = $this->request('child_rec_sn');
			$parent_rate = $this->request('parent_rate');
			$parent_nc_rate = $this->request('parent_nc_rate');
			$parent_wb_rate = $this->request('parent_wb_rate');
			$parent_sb_rate = $this->request('parent_sb_rate');

			$model->setChildRate($child_rec_sn, $parent_rate, $parent_nc_rate, $parent_wb_rate, $parent_sb_rate);

			echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' ><script>alert('수정 되였습니다.');</script>";
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=/partner/memberChildRec?idx=".$urlidx."'>";
			exit;
		}
		else if($act=="delete")
		{
			$urlidx = $this->request('urlidx');
			$child_rec_sn = $this->request('child_rec_sn');

			$model->removeParentRecSn($child_rec_sn);

			echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' ><script>alert('삭제 되였습니다.');</script>";
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=/partner/memberChildRec?idx=".$urlidx."'>";
			exit;
		}



		$partnerList = $model->getPartnerIdList($filter_logo," and rec_grd=2 ");

		$list = $model->getMemberDetails($idx);
		$childlist = $model->getPartnerRateList("",$idx);

		//$this->view->assign('idx', $idx);
		$this->view->assign('partner_list', $partnerList);
		$this->view->assign('childlist', $childlist);
		$this->view->assign('list', $list);

		$this->display();
	}

	//▶ 하부총판 선택정보
	function getSelectedRecAction()
	{
		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}

		$model = $this->getModel("PartnerModel");
		$idx = $this->request('idx');

		$partner = $model->getPartnerRow($idx);

		echo json_encode($partner);
	}


	//▶ 파트너 메모추가
	function memoAdd_AccAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/memo_add_acc.html");

		$model 	= $this->getModel("PartnerModel");
		$mModel = $this->getModel("MemoModel");

		$act = $this->request('act');
		if(isset($act) && $act=="add")
		{
			$title=trim($this->request('title'));
			$toid=$this->request('toid');
			$time=trim($this->request('time'));
			$content=trim($this->request('content'));
			$content=htmlspecialchars($content);

			$mModel->writePartnerMemo($toid, $title, $content);

			echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' ><script>alert('발송되었습니다.');window.close();</script>";
			echo "<META HTTP-EQUIV='Refresh' CONTENT='0;URL=/partner/list'>";
			exit;
		}

		if($this->request('toid')!="")
		{
			$send = $this->request('toid');
		}
		else
		{
			throw new Lemon_ScriptException("잘못된 연결입니다!", "", "close");

			exit;
		}

		$this->view->assign('send', $send);


		$this->display();
	}

	/*
	 * 파트너 신청
	 */
	function joinAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/join_list.html");

		$model = $this->getModel("PartnerModel");

		$act	= $this->request('act');

		if($act == "delete_user")
		{
			$arrmemidx = $this->request('y_id');
			$str="";
			for($i=0; $i < count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}
			$str=substr($str,0,strlen($str)-1);

			$model->delRecommendjoinList( $str );
		}
		if($act == "delone")
		{
			$idx = $this->request('idx');
			$model->delRecommendjoin( $idx );
		}
		if($act == "add")
		{
			$idx = $this->request('idx');
			$model->modifyRecommendjoin( $idx );
		}

		$nname = $this->request('username');
		if(!empty($nname))
		{
			$where = " and rec_id like '%".$nname."%'";
		}

		$total = $model->getRecommendjoinTotal($where);
		$pageMaker = $this->displayPage($total, 10);
		$list = $model->getRecommendjoinList($where, $pageMaker->first, $pageMaker->listNum);

		$this->view->assign('list', $list);

		$this->display();
	}

	/*
	 * 파트너 공지
	 */
	function noticelistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/notice_list.html");

		$model 	= $this->getModel("PartnerModel");
		$bModel = $this->getModel("BoardModel");

		$act	= $this->request('act');

		if($act == "del")
		{
			$num = $this->request('num');
			$bModel->delBoard( $num );
		}

		$where = '';

		$total = $bModel->getBoardTotal($where,130);
		$pageMaker = $this->displayPage($total, 10);
		$list = $bModel->getBoardList($where, $pageMaker->first, $pageMaker->listNum,130);

		$this->view->assign('list', $list);

		$this->display();
	}

	/*
	* 파트너 공지쓰기
	*/
	function noticeaddAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/notice_add.html");

		$model 	= $this->getModel("PartnerModel");
		$bModel = $this->getModel("BoardModel");

		$act	= $this->request('act');
		if($act == "add")
		{
			$writer = $this->request('name');
			$write_datetime = $this->request('time');
			$content = htmlspecialchars($this->request('content'));
			$subject =htmlspecialchars($this->request('title'));
			$view_code = 130;

			if($subject!="" && $content!="" )
			{
				$bModel->addBoard($writer, $subject, $content, $write_datetime, $view_code );

				throw new Lemon_ScriptException("성공적으로 등록 되였습니다.", "", "go", "/partner/noticelist");
				exit;
			}
		}

		$this->display();
	}
	/*
	* 파트너 공지수정
	*/
	function noticeviewAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/notice_view.html");

		$model 	= $this->getModel("PartnerModel");
		$bModel = $this->getModel("BoardModel");

		$idx 	= $this->request('idx');
		$act	= $this->request('act');

		if($act == "edit")
		{
			$write_datetime = $this->request('time');
			$content = htmlspecialchars($this->request('content'));
			$subject =htmlspecialchars($this->request('title'));

			$bModel->modifyBoard($idx, $subject, $content, $write_datetime);

			throw new Lemon_ScriptException("성공적으로 등록 되였습니다.", "", "go", "/partner/noticelist");
			exit;
		}

		$list = $bModel->getPartnerBoard( $idx );
		$this->view->assign('list', $list);

		$this->display();
	}


	/*
	 * 파트너 쪽지
	 */
	function memolistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/memo_list.html");

		$model 	= $this->getModel("PartnerModel");
		$mModel = $this->getModel("MemoModel");

		$act	= $this->request('act');

		if(isset($act)&&$act == "del")
		{
			$id = $this->request('id');
			$mModel->delMemoByMemberSn( $id );
		}
		$where = " and toid='운영팀' and kubun='1' ";
		$total = $mModel->getMemoTotal($where);
		$pageMaker = $this->displayPage($total, 10);
		$list = $mModel->getMemoList($where, $pageMaker->first, $pageMaker->listNum, " writeday desc ");

		$this->view->assign('list', $list);

		$this->display();
	}

	/*
	 *  쪽지쓰기
	 */

	function memoaddAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/memo_add.html");

		$model 	= $this->getModel("PartnerModel");
		$mModel = $this->getModel("MemoModel");

		$act	= $this->request('act');
		if(isset($act) && $act=="add")
		{
			$title		= trim( $this->request('title') );
			$toid			=	$this->request('toid');
			$time			=	trim($this->request('time'));
			$content	= trim($this->request('content'));
			$content	= htmlspecialchars($content);

			$mModel->writePartnerMemo($toid, $title, $content);

			throw new Lemon_ScriptException("성공적으로 등록 되였습니다.", "", "go", "/partner/memoadd");
			exit;
		}

		if($this->request('toid')!="")
		{
			$send = $this->request["toid"];
		}else{
			$send = "전체파트너";
		}

		$this->view->assign('send', $send);

		$this->display();

	}

	/*
	 *   보낸 쪽지함
	 */
	function  memosendlistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/memo_sendlist.html");

		$model 	= $this->getModel("PartnerModel");
		$mModel = $this->getModel("MemoModel");

		$act	= $this->request('act');

		if(isset($act) && $act == "del")
		{
			$id = $this->request('id');
			$mModel->delMemoByMemberSn( $id );
		}

		if(isset($act)&& $act == "alldel")
		{
			$arrmemidx = $this->request['y_id'];
			for($i=0;$i<count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}
			$str=substr($str,0,strlen($str)-1);
			$mModel->delMemoByMemberSn( $str );
		}

		$where = "and fromid='운영팀' and kubun='1' ";
		$total = $mModel->getMemoTotal($where);
		$pageMaker = $this->displayPage($total, 10);
		$list = $mModel->getMemoList($where, $pageMaker->first, $pageMaker->listNum, " writeday desc ");

		$this->view->assign('list', $list);


		$this->display();
	}


	// ▶ 파트너 정산비율 수정 팝업
	function popup_rateAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}

		$model = $this->getModel("PartnerModel");

		$this->view->define("content","content/partner/popup.rate.html");

		$id 	= $this->request("id");
		$rate = $this->request("rate");

		if( $this->request("act")=="edit" )
		{

			if(preg_match('/^\d*$/',$rate))
			{
				$model->modifyRate($id,$rate);

				throw new Lemon_ScriptException("", "", "script", "alert('수정 되였습니다.');opener.document.location.reload(); self.close();");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException('정산비율은 숫자만 가능합니다!', '', 'back' );
				exit;
			}
		}

		$this->view->assign('id', $id);
		$this->view->assign('rate', $rate);

		$this->display();
	}

	//▶ 총판등록 팝업
	function popup_joinAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/popup.join_list.html");

		$partnerModel = $this->getModel("PartnerModel");

		$act	= $this->request('act');

		if($act=="add")
		{
			$uid 		= $this->request('uid');
			$name 	= $this->request('name');
			$phone 	= $this->request('phone');
			$memo 	= $this->request('memo');
			$passwd	= $this->request('passwd');
			$logo		= $this->request('logo');
			//수수료 추가 2016.02.01
			$default_rate		= $this->request('default_rate');
			$nc_rate		= $this->request('nc_rate');
			$wb_rate		= $this->request('wb_rate');
			$sb_rate		= $this->request('sb_rate');

			$partnerModel->addPartnerJoin($uid, $name, 1, $passwd, $phone, "", "", "", "", "", $logo,$default_rate,$nc_rate,$wb_rate,$sb_rate,0,0,0,0,0,1);

			throw new Lemon_ScriptException("", "", "script", "alert('등록되었습니다.');opener.document.location.reload(); self.close();");
				exit;
		}

		$this->display();
	}

	//▶ 총판등록시 ajax 확인
	function addCheckAjaxAction()
	{
		$uid = trim($this->req->request('uid'));

		$partnerModel = Lemon_Instance::getObject("PartnerModel",true);
		if($uid)
		{
			$rs = $partnerModel->getPartnerFieldById($uid, "rec_id");
			echo ($rs!="") ? 1 : 0;
		}
	}

	//▶ 하부 총판등록 팝업
	function popup_child_joinAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/partner/popup.childjoin_list.html");

		$partnerModel = $this->getModel("PartnerModel");

		$act	= $this->request('act');

		if($act=="add")
		{
			$uid 		= $this->request('uid');
			$name 	= $this->request('name');
			$phone 	= $this->request('phone');
			$memo 	= $this->request('memo');
			$passwd	= $this->request('passwd');
			$logo		= $this->request('logo');
			//수수료 추가 2016.02.01
			$default_rate		= $this->request('default_rate');
			$nc_rate		= $this->request('nc_rate');
			$wb_rate		= $this->request('wb_rate');
			$sb_rate		= $this->request('sb_rate');
			$parent_rate		= $this->request('parent_rate');
			$parent_rec_sn		= $this->request('parent_rec_sn');
			$parent_nc_rate		= $this->request('parent_nc_rate');
			$parent_wb_rate		= $this->request('parent_wb_rate');
			$parent_sb_rate		= $this->request('parent_sb_rate');

			$partnerModel->addPartnerJoin($uid, $name, 1, $passwd, $phone, "", "", "", "", "", $logo,$default_rate,$nc_rate,$wb_rate,$sb_rate,$parent_rec_sn,$parent_rate,$parent_nc_rate,$parent_wb_rate,$parent_sb_rate,2);

			throw new Lemon_ScriptException("", "", "script", "alert('등록되었습니다.');opener.document.location.reload(); self.close();");
				exit;
		}
		$partnerList = $partnerModel->getPartnerIdList(""," and rec_grd=1 "); //상부총판목록
		$this->view->assign('partner_list', $partnerList);
		$this->display();
	}

}

?>
