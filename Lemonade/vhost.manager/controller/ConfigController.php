<?php
header("Content-Type: text/html; charset=UTF-8");
class ConfigController extends WebServiceController
{
	//▶ 기본 설정
	function globalconfigAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/global_config.html");

		$logo = $this->request('logo');

		$model = $this->getModel("ConfigModel");
		$list = $model->getAdminConfigRow("*", "", $logo);

		$rat1 = $model->getFirstRateRow("*", "", 1, $logo);
		$rat2 = $model->getFirstRateRow("*", "", 2, $logo);
		$rat3 = $model->getFirstRateRow("*", "", 3, $logo);
		$rat4 = $model->getFirstRateRow("*", "", 4, $logo);
		$rat5 = $model->getFirstRateRow("*", "", 5, $logo);

		$logoModel = $this->getModel("LogoModel");
		$logoList = $logoModel->getList();

		$this->view->assign('logolist', $logoList);

		$this->view->assign( "list", $list);
		$this->view->assign( "logo", $logo);

		$this->view->assign( "rat1", $rat1);
		$this->view->assign( "rat2", $rat2);
		$this->view->assign( "rat3", $rat3);
		$this->view->assign( "rat4", $rat4);
		$this->view->assign( "rat5", $rat5);

		$this->display();
	}

	//▶ 기본 설정 수정
	function globalProcessAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/global_config.html");


		$adminConfigArr	= $_POST;

		$logo = $this->request('logo');
		if($logo=='')
			$logo = $this->logo;


		$rs = $this->getModel("ConfigModel")->modifyGlobal($adminConfigArr, $logo);


		if($rs>0)
		{
			throw new Lemon_ScriptException("수정되었습니다.","","go","/config/globalconfig?logo=".$logo);
		}
		else{
			throw new Lemon_ScriptException("변경내역이 없거나 정상처리 되지 않았습니다..","","go","/config/globalconfig?logo=".$logo);
			exit;
		}

		//$this->globalconfigAction();
	}

	//▶ 포인트 설정
	function pointAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/point.html");

		$sel_logo = $this->request('logo');

		$configModel = $this->getModel("ConfigModel");

		$field = "*";

		$item = $configModel->getPointConfigRowFromLogo($sel_logo, $field);

		$logoModel = $this->getModel("LogoModel");
		$logoList = $logoModel->getList();

		$this->view->assign('logolist', $logoList);
		$this->view->assign('filter_logo', $sel_logo);
		$this->view->assign('item', $item);

		$this->display();
	}

	//▶ 레벨 설정
	function levelAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/level.html");

		$model = $this->getModel("ConfigModel");

		$mode = $this->request('mode');
		$sel_logo = $this->request('logo');

		if($mode=='save')
		{
			$sn								= $this->request('sn');
			$levelName 				= $this->request('lev_name');

			$minMoney 				= $this->request('min_money');
			$minMoney 				= str_replace(",","",$minMoney);

			$maxMoney 				= $this->request('max_money');
			$maxMoney 				= str_replace(",","",$maxMoney);

			$maxMoneySpecial 	= $this->request('max_money_special');
			$maxMoneySpecial 	= str_replace(",","",$maxMoneySpecial);

			$maxBonus 		= $this->request('max_bonus');
			$maxBonus 		= str_replace(",","",$maxBonus);

			$maxBonusSpecial 		= $this->request('max_bonus_special');
			$maxBonusSpecial 		= str_replace(",","",$maxBonusSpecial);

			$maxMoney 		= $this->request('max_money');
			$maxMoney 		= str_replace(",","",$maxMoney);

			$maxSingle 		= $this->request('max_money_single');
			$maxSingle 		= str_replace(",","",$maxSingle);

			$maxSingleSpecial 		= $this->request('max_money_single_special');
			$maxSingleSpecial 		= str_replace(",","",$maxSingleSpecial);

			$chargeRate 	= $this->request('charge_rate');
			$loseRate 		= $this->request('lose_rate');
			$recommendRate	= $this->request('recommend_rate');

			$bank			= $this->request('bank');
			$bankAccount	= $this->request('bank_account');
			$bankOwner		= $this->request('bank_owner');
			$minCharge		= $this->request('min_charge');
			$minCharge 		= str_replace(",","",$minCharge);
			$minExchange		= $this->request('min_exchange');
			$minExchange 		= str_replace(",","",$minExchange);

			$recommendRate  = sprintf("%d:%d:%d:", $recommendRate, 0, 0);
			$recommendLimit = $this->request('recommend_limit');
			$domain					= $this->request('domain_name');

			$sel_logo = $this->request('logo');

			$rs = $model->_modifyLevelConfig($sel_logo, $sn, $levelName, $minMoney, $maxMoney, $maxMoneySpecial, $maxBonus, $maxBonusSpecial, $maxSingle, $maxSingleSpecial, $chargeRate, $loseRate, $recommendRate, $bank, $bankAccount, $bankOwner, $minCharge, $minExchange, $recommendLimit, $domain);

			if($rs>0)
			{
				throw new Lemon_ScriptException("변경되었습니다.","","go","/config/level?logo=$sel_logo");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException("변경에 실패하였습니다.","","go","/config/level?logo=$sel_logo");
				exit;
			}
		}
		else if( $mode=="ladder_save")
		{
			$sn								= $this->request('sn');

			$ladder_min_betting = $this->request('ladder_min_betting');
			$ladder_max_betting = $this->request('ladder_max_betting');
			$ladder_max_prize = $this->request('ladder_max_prize');

			$ladder_min_betting = str_replace(",","",$ladder_min_betting);
			$ladder_max_betting = str_replace(",","",$ladder_max_betting);
			$ladder_max_prize = str_replace(",","",$ladder_max_prize);

			$result = $model->modify_ladder_config($sn, $ladder_min_betting, $ladder_max_betting, $ladder_max_prize);
			if($result > 0)
			{
				throw new Lemon_ScriptException("변경되었습니다.","","go","/config/level");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException("변경에 실패하였습니다.","","go","/config/level");
				exit;
			}
		}
		else if( $mode=="snail_race_save")
		{
			$sn								= $this->request('sn');

			$snail_race_min_betting = $this->request('snail_race_min_betting');
			$snail_race_max_betting = $this->request('snail_race_max_betting');
			$snail_race_max_prize = $this->request('snail_race_max_prize');

			$snail_race_min_betting = str_replace(",","",$snail_race_min_betting);
			$snail_race_max_betting = str_replace(",","",$snail_race_max_betting);
			$snail_race_max_prize = str_replace(",","",$snail_race_max_prize);

			$result = $model->modify_snail_race_config($sn, $snail_race_min_betting, $snail_race_max_betting, $snail_race_max_prize);
			if($result > 0)
			{
				throw new Lemon_ScriptException("변경되었습니다.","","go","/config/level");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException("변경에 실패하였습니다2.","","go","/config/level");
				exit;
			}
		}
		else if( $mode=="powerball_save")
		{
			$powerball_min_betting = $this->request('powerball_min_betting');
			$powerball_max_betting = $this->request('powerball_max_betting');
			$powerball_max_prize = $this->request('powerball_max_prize');

			$powerball_min_betting = str_replace(",","",$powerball_min_betting);
			$powerball_max_betting = str_replace(",","",$powerball_max_betting);
			$powerball_max_prize = str_replace(",","",$powerball_max_prize);

			$result = $model->modify_powerball_config($powerball_min_betting, $powerball_max_betting, $powerball_max_prize);
			if($result > 0)
			{
				throw new Lemon_ScriptException("변경되었습니다.","","go","/config/level");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException("변경에 실패하였습니다3.","","go","/config/level");
				exit;
			}
		}
		else if( $mode == 'add' )
		{
			$model->addLevelConfig();
		}
		else if( $mode == 'del' )
		{
			$model->delLevelConfig();
		}

		$list = $model->getLevelConfigRowsFromLogo($sel_logo);

		$domain_list=$model->getDomainList();

		for($i=0; $i<sizeof($list); ++$i)
		{
			$array = explode(':', $list[$i]['lev_join_recommend_mileage_rate']);

			$list[$i]['lev_join_recommend_mileage_rate_1'] = $array[0];
			$list[$i]['lev_join_recommend_mileage_rate_2'] = $array[1];
			$list[$i]['lev_join_recommend_mileage_rate_3'] = $array[2];

			$array = explode(':', $list[$i]['lev_folder_bonus']);
			$list[$i]['lev_folder_bonus_3']  = $array[0];
			$list[$i]['lev_folder_bonus_4']  = $array[1];
			$list[$i]['lev_folder_bonus_5']  = $array[2];
			$list[$i]['lev_folder_bonus_6']  = $array[3];
			$list[$i]['lev_folder_bonus_7']  = $array[4];
			$list[$i]['lev_folder_bonus_8']  = $array[5];
			$list[$i]['lev_folder_bonus_9']  = $array[6];
			$list[$i]['lev_folder_bonus_10'] = $array[7];

			$list[$i]['domain_list'] = $domain_list;
		}

		if(count($list)>0)
		{
			$ladder = array("ladder_min_betting" => $list[0]["ladder_min_betting"],
											"ladder_max_betting" => $list[0]["ladder_max_betting"],
											"ladder_max_prize" => $list[0]["ladder_max_prize"]);

			$powerball = array(	"powerball_min_betting" => $list[0]["powerball_min_betting"],
													"powerball_max_betting" => $list[0]["powerball_max_betting"],
													"powerball_max_prize" => $list[0]["powerball_max_prize"]);

			$snail_race = array(	"snail_race_min_betting" => $list[0]["snail_race_min_betting"],
													"snail_race_max_betting" => $list[0]["snail_race_max_betting"],
													"snail_race_max_prize" => $list[0]["snail_race_max_prize"]);
		}

		$logoModel = $this->getModel("LogoModel");
		$logoList = $logoModel->getList();
		$this->view->assign('logolist', $logoList);
		$this->view->assign('filter_logo', $sel_logo);
		$this->view->assign('list', $list);
		$this->view->assign('ladder', $ladder);
		$this->view->assign('snail_race', $snail_race);
		$this->view->assign('powerball', $powerball);

		$this->display();
	}

	//▶ 포인트 설정 저장
	function pointSaveProcessAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}

		$model = $this->getModel("ConfigModel");
		//
		$joinFreeMoney 		= $this->request("mem_join");
		$folder_bouns3 		= $this->request("folder_bouns3");
		$folder_bouns4 		= $this->request("folder_bouns4");
		$folder_bouns5 		= $this->request("folder_bouns5");
		$folder_bouns6 		= $this->request("folder_bouns6");
		$folder_bouns7 		= $this->request("folder_bouns7");
		$folder_bouns8 		= $this->request("folder_bouns8");
		$folder_bouns9 		= $this->request("folder_bouns9");
		$folder_bouns10 	= $this->request("folder_bouns10");
		$chk_folder 			= $this->request("chk_folder");

		$replyPoint				= $this->request("reply_point");
		$replyLimit				= $this->request("reply_limit");

		$boardWritePoint				= $this->request("board_write_point");
		$boardWriteLimit				= $this->request("board_write_limit");
		$bettingBoardWritePoint	= $this->request("betting_board_write_point");
		$bettingBoardWriteLimit	= $this->request("betting_board_write_limit");
		$jackpot = $this->request("jackpot_sum_money");
		$jackpot_rate = $this->request("jackpot_give_rate");

		$model->savePointConfig($joinFreeMoney, $folder_bouns3, $folder_bouns4, $folder_bouns5, $folder_bouns6,
														$folder_bouns7, $folder_bouns8, $folder_bouns9, $folder_bouns10,
														$chk_folder,
														$replyPoint, $replyLimit, $boardWritePoint, $boardWriteLimit, $bettingBoardWritePoint, $bettingBoardWriteLimit, $jackpot, $jackpot_rate);

		throw new Lemon_ScriptException("올바르게 수정되었습니다.","","go","/config/point");

	}

	//▶ 이벤트 설정
	function eventAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/event.html");

		$model = $this->getModel("ConfigModel");

		$mode = $this->request("mode");
		$item = $model->getEventConfigRow();
		if($mode=="save")
		{
			$minCharge	= $this->request("min_charge");
			$bonus1		= $this->request("bonus1");
			$bonus2		= $this->request("bonus2");
			$bonus3		= $this->request("bonus3");
			$bonus4		= $this->request("bonus4");
			$bonus5		= $this->request("bonus5");
			$bonus6		= $this->request("bonus6");
			$bonus7		= $this->request("bonus7");
			$bonus8		= $this->request("bonus8");
			$bonus9		= $this->request("bonus9");
			$bonus10	= $this->request("bonus10");


			if($item['sn']=='') {$rs = $model->addEventConfig($minCharge,$bonus1,$bonus2,$bonus3,$bonus4,$bonus5,$bonus6,$bonus7,$bonus8,$bonus9,$bonus10);}
			else				{$rs = $model->modifyEventConfig($minCharge,$bonus1,$bonus2,$bonus3,$bonus4,$bonus5,$bonus6,$bonus7,$bonus8,$bonus9,$bonus10);}

			if($rs>0)
			{
				throw new Lemon_ScriptException("변경되었습니다.","","go","/config/event");
				exit;
			}
		}

		$this->view->assign('item', $item);
		$this->display();
	}

	//▶ 베팅 설정
	function betAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/bet.html");

		$sel_logo = $this->request('logo');

		$model = $this->getModel("ConfigModel");

		$mode = $this->request("mode");
		$item = $model->getSiteConfigRowFromLogo($sel_logo);
		if($mode=="save")
		{
			$rule 			= $this->request("rule");
			$vh	 			= $this->request("vh");
			$vu 			= $this->request("vu");
			$hu 			= $this->request("hu");
			$min_bet_count 	= $this->request("min_bet_count");

			if($item['sn']=='') {$rs = $model->addSiteConfig($rule, $vh, $vu, $hu, $min_bet_count);}
			else				{$rs = $model->modifySiteConfig($rule, $vh, $vu, $hu, $min_bet_count, $sel_logo);}

			if($rs>0)
			{
				throw new Lemon_ScriptException("변경되었습니다.","","go","/config/bet?logo=$sel_logo");
				exit;
			}
		}

		$logoModel = $this->getModel("LogoModel");
		$logoList = $logoModel->getList();

		$this->view->assign('logolist', $logoList);
		$this->view->assign('filter_logo', $sel_logo);

		$this->view->assign('item', $item);

		$this->display();
	}

	//▶ DB 추출
	function dataexcelAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/data_excel.html");

		$model = $this->getModel("ConfigModel");

		$this->display();
	}

	function popuplistAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/popup.list.html");

		$model = $this->getModel("ConfigModel");

		$conf = Lemon_Configure::readConfig('config');
		if($conf['site']!='')
		{
			$UPLOAD_URL	 = $conf['site']['upload_url'];
			$SITE_DOMAIN = $conf['site']['site_domain'];
		}
		$ph_path  = $UPLOAD_URL."/upload/popup/";

		$act = $this->request("act");
		$idx = $this->request("idx");
		if($act == "del")
		{
			$rs =	$model->getPopupRow("P_FILE", "IDX=".$idx."" );
			if( sizeof( $rs ) > 0 )
			{
				if(file_exists($ph_path.$rs))
				{
					unlink($ph_path.$rs);
				}
			}
			$model->delPopupbySn( $idx );
		}

		$list = $model->getPopupbyOrderby();

		$this->view->assign('list', $list);
		$this->view->assign('ph_path', $ph_path);

		$this->display();
	}

	function popupaddAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/popup.add.html");

		$model = $this->getModel("ConfigModel");

		$act = $this->request("act");
		$idx = $this->request("idx");
		$logo = $this->request("logo");

		if($logo=='') $logo = $this->logo;

		if($act=="edit")
		{
			$rs =	$model->getPopupRows("*", "IDX=".$idx." and logo='".$logo."'" );


			$rs[0]['P_CONTENT'] = str_replace("<br />","",$rs[0]['P_CONTENT']);

			//print_r($rs);
			$this->view->assign( 'list', $rs[0]);
			$this->view->assign( 'act', $act);
			$this->view->assign( 'idx', $idx);
			$this->view->assign( 'logo', $logo);

		}

		$this->display();
	}

	function popupProcessAction()
	{
		$model = $this->getModel("ConfigModel");

		$upload_file=$_FILES['P_FILE']['tmp_name'];
		$upload_file_name=$_FILES['P_FILE']['name'];//接收上?文件的??名字
		$PopupDir = $config_upload_root ."/popup/";
		$act = $this->request("act");
		$idx = $this->request("idx");
		$logo = $this->request("logo");
		if($logo=='') $logo = $this->logo;
		if($act=="")
		{
			$act="add";
		}

		$conf = Lemon_Configure::readConfig('config');
		if($conf['site']!='')
		{
			$UPLOAD_URL	 = $conf['site']['upload_url'];
			$SITE_DOMAIN = $conf['site']['site_domain'];

			$ph_path  = $UPLOAD_URL."/upload/popup/";
		}

		$P_SUBJECT 			= $this->request("P_SUBJECT");				/// 제목
		$P_POPUP_U 			= $this->request("P_POPUP_U");				/// 팝업 사용유무
		$P_STARTDAY 		= $this->request("P_STARTDAY");			/// 시작일
		$P_ENDDAY 			= $this->request("P_ENDDAY");				/// 마감일
		$P_WIN_WIDTH 		= $this->request("P_WIN_WIDTH");			/// 팝업창 가로 사이즈
		$P_WIN_HEIGHT 	= $this->request("P_WIN_HEIGHT");		/// 팝업창 세로 사이지
		$P_WIN_LEFT 		= $this->request("P_WIN_LEFT");			/// 팝업 위치
		$P_WIN_TOP 			= $this->request("P_WIN_TOP");				/// 팝업 위치
		$P_STYLE 				= $this->request("P_STYLE");					/// 바디 스타일 이미지 통 또는 html
		$P_MOVEURL 			= $this->request("P_MOVEURL");				/// 이미지 통일 경우 클릭시 이동할 주소
		$P_CONTENT 			= $this->request("P_CONTENT");				/// Html 내용
		$P_CONTENT = nl2br($P_CONTENT);


		if ($P_WIN_WIDTH == "")
			$P_WIN_WIDTH = 0;
		if ($P_WIN_HEIGHT == "")
			$P_WIN_HEIGHT = 0;
		if ($P_WIN_LEFT == "")
			$P_WIN_LEFT = 0;
		if ($P_WIN_TOP == "")
			$P_WIN_TOP = 0;


		if ($act=="edit")
		{
			$rs =	$model->getPopupRows("*", "IDX=".$idx." and logo='".$logo."'" );
			if($upload_file_name=="")
			{
				if ($db->row["P_FILE"] != ""){
					$edit_fileName = $rs[0]["P_FILE"];
				}
				else
				{
					$edit_fileName = "";
				}
			}
		}

		if($upload_file)
		{
			$up=new upphoto;
			$up->ph_path=$ph_path;
			$up->get_ph_tmpname($_FILES['P_FILE']['tmp_name']);
			$up->get_ph_type($_FILES['P_FILE']['type']);
			$up->get_ph_size($_FILES['P_FILE']['size']);
			$up->get_ph_name($_FILES['P_FILE']['name']);
			$up->save();
			$imgsrc=substr($up->ph_name,24);
		}

		if($act=="add")
		{
			$model->addPopup($P_SUBJECT,$P_CONTENT,$P_POPUP_U,$P_STARTDAY,$P_ENDDAY,$P_WIN_WIDTH,$P_WIN_HEIGHT,$P_WIN_LEFT,$P_WIN_TOP,$P_MOVEURL,$imgsrc,$P_STYLE, $logo);
		}
		if($act=="edit")
		{
			if($imgsrc==""){
					$temp=$edit_fileName;
			}else{
				$temp=$upload_file_name;
			}

			$file =	$model->getPopupRow("P_FILE", "IDX=".$idx."" );

			if(file_exists($ph_path.$file))
			{
				unlink($ph_path.$file);
			}

			$model->modifyPopup($P_SUBJECT,$P_CONTENT,$P_POPUP_U,$P_STARTDAY,$P_ENDDAY,$P_WIN_WIDTH,$P_WIN_HEIGHT,$P_WIN_LEFT,$P_WIN_TOP,$P_MOVEURL,$imgsrc,$P_STYLE,$idx);

		}

		throw new Lemon_ScriptException("처리 되였습니다.","","go","/config/popuplist");

		exit;
	}

	function export_DBProcessAction()
	{
		$model = $this->getModel("MemberModel");
		$cmodel = $this->getModel("CartModel");

		$_TYPE 			= $this->request("_TYPE");				/// DB추출 구분값 (0:회원내역, 1:배팅내역)

		if(!strpos($_SESSION["quanxian"],"1004"))
		{
			echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><script>alert(' 해당 권한이 제한되었습니다 ');history.back();</script>";

			exit();

		}
		else
		{
			$filename="";

			switch($_TYPE)
			{
				case '0':$rs=$model->getMember_Export();$filename="member_";break;
				case '1':$rs=$cmodel->getBet_Export();$filename="betting_";break;
			}
			$filename.=date('YmdHis').".csv";//시간격식으로 파일 이름 지정

			Header("Content-type:charset=UTF-8");  //인코딩 설정
			header("Content-type:text/csv");//수출 파일 지정 csv
	    	header("Content-Disposition:attachment;filename=".$filename);
	    	header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	    	header('Expires:0');
	    	header('Pragma:public');
			echo $this->array_to_string($rs, $_TYPE);//처리 할 내용
		}
	}

	function array_to_string($array, $_TYPE)
	{
    if(empty($array))
    {
        return ("noData..Check Please");
    }

    if($_TYPE==0)
    {
	    $data=$this->convertformat("아이디").','.$this->convertformat("닉네임").','.$this->convertformat("비밀번호").','.$this->convertformat("환전비밀번호").','.$this->convertformat("전화번호").','.$this->convertformat("은행").','.$this->convertformat("예금주").','.$this->convertformat("계좌번호").','.$this->convertformat("보유머니").','.$this->convertformat("회원상태").','.$this->convertformat("총판").','.$this->convertformat("등록도메인").','.$this->convertformat("최근접속도메인").','.$this->convertformat("레벨").','.$this->convertformat("등록일").','.$this->convertformat("총입금액").','.$this->convertformat("총환전액").','.$this->convertformat("비고")."\n";

	    for($i=0;$i<sizeof($array);++$i){
		    $uid						="[".$array[$i]['uid']."]";
		    $nick						="[".$array[$i]['nick']."]";
		    $upass					="[".$array[$i]['upass']."]";
		    $exchange_pass	="[".$array[$i]['exchange_pass']."]";
		    $phone					="[".$array[$i]['phone']."]";
		    $bank_name			="[".$array[$i]['bank_name']."]";
		    $bank_member		="[".$array[$i]['bank_member']."]";
		    $bank_account		="[".$array[$i]['bank_account']."]";
		    $g_money				="[".$array[$i]['g_money']."]";
		    $mem_status			="[".$array[$i]['mem_status']."]";
		    $recommend_id		="[".$array[$i]['recommend_id']."]";
		    $reg_domain			="[".$array[$i]['reg_domain']."]";
		    $login_domain			="[".$array[$i]['login_domain']."]";
		    $mem_lev				="[".$array[$i]['mem_lev']."]";
		    $regdate					="[".$array[$i]['regdate']."]";
		    $tot_charge			="[".$array[$i]['tot_charge']."]";
		    $tot_exchange		="[".$array[$i]['tot_exchange']."]";
		    $logo						="[".$array[$i]['logo']."]";

	      $data.=$this->convertformat($uid).','.$this->convertformat($nick).','.$this->convertformat($upass).','.$this->convertformat($exchange_pass).','.$this->convertformat($phone).','.$this->convertformat($bank_name).','.$this->convertformat($bank_member).','.$this->convertformat($bank_account).','.$this->convertformat($g_money).','.$this->convertformat($mem_status).','.$this->convertformat($recommend_id).','.$this->convertformat($reg_domain).','.$this->convertformat($login_domain).','.$this->convertformat($mem_lev).','.$this->convertformat($regdate).','.$this->convertformat($tot_charge).','.$this->convertformat($tot_exchange).','.$this->convertformat($logo)."\n";
			}
		}else {
			$data="";
			for($i=0;$i<sizeof($array);++$i){
				switch($array[$i]['aresult']){
					case 0:$game_result="진행중";break;
					case 1:$game_result="적중";break;
					case 2:$game_result="실패";break;
				}
				$data.=$this->convertformat("배팅번호").','.$this->convertformat("아이디").','.$this->convertformat("닉네임").','.$this->convertformat("배팅금액").','.$this->convertformat("배당율").','.$this->convertformat("예상배당액").','.$this->convertformat("배팅날짜").','.$this->convertformat("결과")."\n";
	      $data.=$this->convertformat($array[$i]['betting_no']).','.$this->convertformat($array[$i]['uid']).','.$this->convertformat($array[$i]['nick']).','.$this->convertformat($array[$i]['betting_money']).','.$this->convertformat($array[$i]['result_rate']).','.$this->convertformat($array[$i]['result_money']).','.$this->convertformat($array[$i]['regDate']).','.$this->convertformat($game_result)."\n";
	      $data.=$this->convertformat("게임타입").','.$this->convertformat("경기시간").','.$this->convertformat("리그").','.$this->convertformat("홈팀").','.$this->convertformat("홈배당").','.$this->convertformat("무배당/기준점").','.$this->convertformat("원정팀").','.$this->convertformat("원정배당").','.$this->convertformat("점수").','.$this->convertformat("결과").','.$this->convertformat("배팅")."\n";

	      $rsi=$array[$i]['item'];
	      for($j=0;$j<sizeof($rsi);++$j){

	      	$game_type="";
	      	switch($rsi[$j]['special']){
	      		case 0:$game_type="일반";break;
	      		case 1:$game_type="스페셜";break;
	      		case 2:$game_type="멀티";break;
	      		case 3:$game_type="고액";break;
	      		case 4:$game_type="이벤트";break;
	      	}
	      	switch($rsi[$j]['game_type']){
	      		case 1:$game_type.="(승무패)";break;
	      		case 2:$game_type.="(핸디캡)";break;
	      		case 4:$game_type.="(언더오버)";break;
	      	}
	      	switch($rsi[$j]['select_no']){
	      		case 1:$selected_team="Home";break;
	      		case 2:$selected_team="Away";break;
	      		case 3:$selected_team="Draw";break;
	      	}
	      	$data.=$this->convertformat($game_type).','.$this->convertformat($rsi[$j]['gameDate'].'('.$rsi[$j]['gameHour'].':'.$rsi[$j]['gameTime'].')').','.$this->convertformat($rsi[$j]['league_name']).','.$this->convertformat($rsi[$j]['home_team']).','.$this->convertformat($rsi[$j]['home_rate']).','.$this->convertformat($rsi[$j]['draw_rate']).','.$this->convertformat($rsi[$j]['away_team']).','.$this->convertformat($rsi[$j]['away_rate']).','.$this->convertformat($rsi[$j]['home_score'].':'.$rsi[$j]['away_score']).','.$this->convertformat($rsi[$j]['win_team']).','.$this->convertformat($selected_team)."\n";
	      }
	      $data.="\n\n";
			}
		}
    return $data;
	}

	function convertformat($strInput)
	{
	    return iconv('utf-8','euc-kr',$strInput);//페이지코드가 utf-8인 경우 사용 ,아니면 한글 깨짐
	}

	function admin_ipAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/admin_ip.html");

		$act = $this->req->post("act");

		$etc_model = $this->getModel("EtcModel");

		if($act=="insert")
		{
			$insert_ip = $this->req->post("insert_ip");

			$result = $etc_model->insert_admin_ip($insert_ip);
			if($result > 0)
			{
				throw new Lemon_ScriptException("추가되었습니다!","","go","/config/admin_ip");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException("추가 실패하였습니다!","","go","/config/admin_ip");
				exit;
			}
		}
		else if($act=="modify")
		{
			$modify_sn = $this->request("modify_sn");
			$modify_ip = $this->request("modify_ip");
			$result = $etc_model->modify_admin_ip($modify_sn, $modify_ip);
			if($result > 0)
			{
				throw new Lemon_ScriptException("수정되었습니다!","","go","/config/admin_ip");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException("수정 실패하였습니다!","","go","/config/admin_ip");
				exit;
			}
		}
		else if($act=="delete")
		{
			$delete_sn = $this->request("delete_sn");
			$result = $etc_model->delete_admin_ip($delete_sn);
			if($result > 0)
			{
				throw new Lemon_ScriptException("삭제되었습니다!","","go","/config/admin_ip");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException("삭제 실패하였습니다!","","go","/config/admin_ip");
				exit;
			}
		}

		$list = $etc_model->admin_ip_listup();

		$this->view->assign('list', $list);

		$this->display();
	}

    //▶ 게임설정
	public function gamedataexcelAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		$this->view->define("content","content/config/game_db_list.html");

		$model 	= $this->getModel("GameModel");
		$cModel = $this->getModel("CartModel");
		$leagueModel = $this->getModel("LeagueModel");

		$act  				= $this->request("act");
		$state  				= $this->request("state");
		$search					= $this->request("search");
		$perpage				= $this->request("perpage");
		$specialType		= $this->request("special_type");
		$categoryName 	= $this->request("categoryName");
		$gameType 			= $this->request("game_type");

		$beginDate  		= $this->request('begin_date');
		$endDate 				= $this->request('end_date');
		$filterTeam			= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');
		$filterBettingTotal	= $this->request('filter_betting_total');

		if($act=='deadline_game')
		{
			$childSn = $this->request('child_sn'); //경기인텍스
			$model->modifyGameTime($childSn);
		}

		if($perpage=='') $perpage=9999;

		$minBettingMoney='';
		if($filterBettingTotal!='')	$minBettingMoney=$filterBettingTotal*10000; /*만원단위*/

		if($filterTeam!='')
		{
			if($filterTeamType=='league')
			{
				$rs = $leagueModel->getListByLikeName($filterTeam);
				for($i=0; $i<sizeof($rs); ++$i)
				{
					$leagueSn[] = $rs[$i]['sn'];
				}
			}
			else if($filterTeamType=='home_team') {$homeTeam = Trim($filterTeam);}
			else if($filterTeamType=='away_team')	{$awayTeam = Trim($filterTeam);}
		}
		if($beginDate=="" || $endDate=="")
		{
			$beginDate 	= date("Y-m-d");
			$endDate		= date("Y-m-d",strtotime ("+1 days"));
		}

        $otherWhere = " and special in (0, 1, 2) ";
		$page_act = "perpage=".$perpage."&state=".$state."&search=".$search."&special_type=".$specialType."&categoryName=".$categoryName."&game_type=".$gameType."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_betting_total=".$filterBettingTotal;

		$bettingEnable = "";
		if($state=="20")
		{
			$filterState 		= "2";
			$bettingEnable 	= "1";
		}
		else if($state=="21")
		{
			$filterState = "2";
			$bettingEnable 	= "-1";
		}
		else
		{
			$filterState = $state;
		}

		$total = $model->getListTotal($filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable, $otherWhere);
		$pageMaker = $this->displayPage($total, $perpage, $page_act);
		$list = $model->getList($pageMaker->first, $pageMaker->listNum, $filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable, $otherWhere);

		for($i=0; $i<sizeof($list); ++$i)
		{
			$item = $cModel->getTeamTotalBetMoney($list[$i]['child_sn']);

			$list[$i]['home_total_betting'] = $item['home_total_betting'];
			$list[$i]['active_home_total_betting'] = $item['active_home_total_betting'];
			$list[$i]['home_count'] = $item['home_count'];

			$list[$i]['draw_total_betting'] = $item['draw_total_betting'];
			$list[$i]['active_draw_total_betting'] = $item['active_draw_total_betting'];
			$list[$i]['draw_count'] = $item['draw_count'];

			$list[$i]['away_total_betting'] = $item['away_total_betting'];
			$list[$i]['active_away_total_betting'] = $item['active_away_total_betting'];
			$list[$i]['away_count'] = $item['away_count'];

			$list[$i]['total_betting'] = $item['home_total_betting']+$item['draw_total_betting']+$item['away_total_betting'];
			$list[$i]['active_total_betting'] = $item['active_home_total_betting']+$item['active_draw_total_betting']+$item['active_away_total_betting'];
			$list[$i]['betting_count'] = $item['home_count']+$item['draw_count']+$item['away_count'];
		}

		$categoryList = $leagueModel->getCategoryList();

		$this->view->assign("special_type",$specialType);
		$this->view->assign("gameType",$gameType);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('filter_team', $filterTeam);
		$this->view->assign('filter_team_type', $filterTeamType);
		$this->view->assign('filter_betting_total', $filterBettingTotal);
		$this->view->assign("categoryName",$categoryName);
		$this->view->assign("categoryList",$categoryList);
		$this->view->assign("state",$state);
		$this->view->assign("top_list",$topList);
		$this->view->assign("list",$list);

		$this->display();
	}

    public function getExcelAction()
	{

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}

		$model 	= $this->getModel("GameModel");
		$cModel = $this->getModel("CartModel");
		$leagueModel = $this->getModel("LeagueModel");

		$act  				= $this->request("act");
		$state  				= $this->request("state");
		$search					= $this->request("search");
		$perpage				= $this->request("perpage");
		$specialType		= $this->request("special_type");
		$categoryName 	= $this->request("categoryName");
		$gameType 			= $this->request("game_type");

		$beginDate  		= $this->request('begin_date');
		$endDate 				= $this->request('end_date');
		$filterTeam			= $this->request('filter_team');
		$filterTeamType	= $this->request('filter_team_type');
		$filterBettingTotal	= $this->request('filter_betting_total');

		if($act=='deadline_game')
		{
			$childSn = $this->request('child_sn'); //경기인텍스
			$model->modifyGameTime($childSn);
		}

		if($perpage=='') $perpage=9999;

		$minBettingMoney='';
		if($filterBettingTotal!='')	$minBettingMoney=$filterBettingTotal*10000; /*만원단위*/

		if($filterTeam!='')
		{
			if($filterTeamType=='league')
			{
				$rs = $leagueModel->getListByLikeName($filterTeam);
				for($i=0; $i<sizeof($rs); ++$i)
				{
					$leagueSn[] = $rs[$i]['sn'];
				}
			}
			else if($filterTeamType=='home_team') {$homeTeam = Trim($filterTeam);}
			else if($filterTeamType=='away_team')	{$awayTeam = Trim($filterTeam);}
		}
		if($beginDate=="" || $endDate=="")
		{
			$beginDate 	= date("Y-m-d");
			$endDate		= date("Y-m-d",strtotime ("+1 days"));
		}

        $otherWhere = " and special in (0, 1, 2) ";
		$page_act = "perpage=".$perpage."&state=".$state."&search=".$search."&special_type=".$specialType."&categoryName=".$categoryName."&game_type=".$gameType."&begin_date=".$beginDate."&end_date=".$endDate."&filter_team_type=".$filterTeamType."&filter_betting_total=".$filterBettingTotal;

		$bettingEnable = "";
		if($state=="20")
		{
			$filterState 		= "2";
			$bettingEnable 	= "1";
		}
		else if($state=="21")
		{
			$filterState = "2";
			$bettingEnable 	= "-1";
		}
		else
		{
			$filterState = $state;
		}


        header( "Content-type: application/vnd.ms-excel" );
        header( "Content-type: application/vnd.ms-excel; charset=utf-8");
        header( "Content-Disposition: attachment; filename = GameList_".date("Ymd").".xls" );

        echo "<table border='1'>";
        echo "<tr>";

		$total = $model->getListTotal($filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable, $otherWhere);
		$pageMaker = $this->displayPage($total, $perpage, $page_act);
		$list = $model->getList($pageMaker->first, $pageMaker->listNum, $filterState, $categoryName, $gameType, $specialType, $beginDate, $endDate, $minBettingMoney, $leagueSn, $homeTeam, $awayTeam, $bettingEnable, $otherWhere);

		for($i=0; $i<sizeof($list); ++$i)
		{
			$item = $cModel->getTeamTotalBetMoney($list[$i]['child_sn']);

			$list[$i]['home_total_betting'] = $item['home_total_betting'];
			$list[$i]['active_home_total_betting'] = $item['active_home_total_betting'];
			$list[$i]['home_count'] = $item['home_count'];

			$list[$i]['draw_total_betting'] = $item['draw_total_betting'];
			$list[$i]['active_draw_total_betting'] = $item['active_draw_total_betting'];
			$list[$i]['draw_count'] = $item['draw_count'];

			$list[$i]['away_total_betting'] = $item['away_total_betting'];
			$list[$i]['active_away_total_betting'] = $item['active_away_total_betting'];
			$list[$i]['away_count'] = $item['away_count'];

			$list[$i]['total_betting'] = $item['home_total_betting']+$item['draw_total_betting']+$item['away_total_betting'];
			$list[$i]['active_total_betting'] = $item['active_home_total_betting']+$item['active_draw_total_betting']+$item['active_away_total_betting'];
			$list[$i]['betting_count'] = $item['home_count']+$item['draw_count']+$item['away_count'];

            echo "<td>".$list[$i]['gameDate']." ".$list[$i]['gameHour'].":".$list[$i]['gameTime']."</td>";
            echo "<td>".$list[$i]['special']."</td>";
            echo "<td>".$list[$i]['type']."</td>";
            echo "<td>".$list[$i]['sport_name']."</td>";
            echo "<td>".$list[$i]['league_name']."</td>";
            echo "<td>".$list[$i]['home_team']."</td>";
            echo "<td>".$list[$i]['home_rate']."</td>";
            echo "<td>".$list[$i]['draw_rate']."</td>";
            echo "<td>".$list[$i]['away_rate']."</td>";
            echo "<td>".$list[$i]['away_team']."</td>";

            echo "</tr>";
		}

        echo "</table>";

		$categoryList = $leagueModel->getCategoryList();

        /*
		$this->view->assign("special_type",$specialType);
		$this->view->assign("gameType",$gameType);
		$this->view->assign('begin_date', $beginDate);
		$this->view->assign('end_date', $endDate);
		$this->view->assign('filter_team', $filterTeam);
		$this->view->assign('filter_team_type', $filterTeamType);
		$this->view->assign('filter_betting_total', $filterBettingTotal);
		$this->view->assign("categoryName",$categoryName);
		$this->view->assign("categoryList",$categoryList);
		$this->view->assign("state",$state);
		$this->view->assign("top_list",$topList);
		$this->view->assign("list",$list);

		$this->display();
        */

	}

	//▶ 환수율 설정
	function changeRateAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/config/changeRate.html");

		$model = $this->getModel("ConfigModel");

		$mode = $this->request('mode');

		if($mode=='save')
		{
			$home_rate = $this->request('home_rate');
			$draw_rate = $this->request('draw_rate');
			$away_rate = $this->request('away_rate');

			$allow_rate_change = $this->request('allow_rate_change');
			$allow_base_change = $this->request('allow_base_change');
			$allow_betting_auto = $this->request('allow_betting_auto');
			$allow_magam_auto = $this->request('allow_magam_auto');

			for($i=0; $i < sizeof($home_rate); $i++ )
			{
				$rs = $model->modifyChangeRateConfig(($i+1)
					, $home_rate[$i], $draw_rate[$i], $away_rate[$i]
					, $allow_rate_change[$i], $allow_base_change[$i]
					, $allow_betting_auto[$i], $allow_magam_auto[$i]);
			}


			/*$rs = $model->_modifyLevelConfig($sn, $levelName, $minMoney, $maxMoney, $maxMoneySpecial, $maxBonus, $maxBonusSpecial, $maxSingle, $maxSingleSpecial, $chargeRate, $loseRate, $recommendRate, $bank, $bankAccount, $bankOwner, $minCharge, $minExchange, $recommendLimit, $domain);

			if($rs>0)
			{
				throw new Lemon_ScriptException("변경되었습니다.","","go","/config/level");
				exit;
			}
			else
			{
				throw new Lemon_ScriptException("변경에 실패하였습니다.","","go","/config/level");
				exit;
			}*/
		}



		$list = $model->getChangeRateConfig();
		//echo "<pre>", var_dump($list), "</pre>";

		$this->view->assign('list', $list);

		$this->display();
	}
}
?>
