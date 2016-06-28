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
		if($logo=='')
			$logo = $this->logo;
		
		$model = $this->getModel("ConfigModel");
		$list = $model->getAdminConfigRow("*", "", $logo);
		
		
		$this->view->assign( "list", $list);
		$this->view->assign( "logo", $logo);
			
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


		$rs=$this->getModel("ConfigModel")->modifyGlobal($adminConfigArr, $logo);
		
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
		
		$configModel = $this->getModel("ConfigModel");
	
		$field = "*";		

		$item = $configModel->getPointConfigRow($field);

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
			/*
			$folderBonus3 	= $this->request('folder_bonus3');
			$folderBonus4 	= $this->request('folder_bonus4');
			$folderBonus5 	= $this->request('folder_bonus5');
			$folderBonus6	= $this->request('folder_bonus6');
			$folderBonus7 	= $this->request('folder_bonus7');
			$folderBonus8 	= $this->request('folder_bonus8');
			$folderBonus9 	= $this->request('folder_bonus9');
			$folderBonus10 	= $this->request('folder_bonus10');
			*/
			
			$bank			= $this->request('bank');
			$bankAccount	= $this->request('bank_account');
			$bankOwner		= $this->request('bank_owner');
			$minCharge		= $this->request('min_charge');
			$minCharge 		= str_replace(",","",$minCharge);
			$minExchange		= $this->request('min_exchange');
			$minExchange 		= str_replace(",","",$minExchange);
			
			$recommendRate  = sprintf("%d:%d:%d:", $recommendRate, 0, 0);
			//$folderBonus	= sprintf("%d:%d:%d:%d:%d:%d:%d:%d:", $folderBonus3,$folderBonus4,$folderBonus5,$folderBonus6,$folderBonus7,$folderBonus8,$folderBonus9,$folderBonus10);
			
			$recommendLimit = $this->request('recommend_limit');
			$domain					= $this->request('domain_name');
			
			//등급별 다폴더보너스 마일리지를 사용하는 경우
			//$rs = $model->modifyLevelConfig($sn, $levelName, $minMoney, $maxMoney, $maxMoneySpecial, $maxBonus, $maxBonusSpecial, $maxSingle, $maxSingleSpecial, $chargeRate, $loseRate, $recommendRate, $folderBonus, $bank, $bankAccount, $bankOwner, $minCharge, $minExchange, $recommendLimit);
			
			$rs = $model->_modifyLevelConfig($sn, $levelName, $minMoney, $maxMoney, $maxMoneySpecial, $maxBonus, $maxBonusSpecial, $maxSingle, $maxSingleSpecial, $chargeRate, $loseRate, $recommendRate, $bank, $bankAccount, $bankOwner, $minCharge, $minExchange, $recommendLimit, $domain);
			
			if($rs>0)
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
		else if( $mode == 'add' )
		{
			$model->addLevelConfig();
		}
		else if( $mode == 'del' )
		{
			$model->delLevelConfig();
		}
		
		$list = $model->getLevelConfigRows('*');
		
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
	
		$this->view->assign('list', $list);	
		
		
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
		
		$model = $this->getModel("ConfigModel");
		
		$mode = $this->request("mode");
		$item = $model->getSiteConfigRow();
		if($mode=="save")
		{
			$rule 			= $this->request("rule");
			$vh	 			= $this->request("vh");
			$vu 			= $this->request("vu");
			$hu 			= $this->request("hu");
			$min_bet_count 	= $this->request("min_bet_count");
			
			if($item['sn']=='') {$rs = $model->addSiteConfig($rule, $vh, $vu, $hu, $min_bet_count);}
			else				{$rs = $model->modifySiteConfig($rule, $vh, $vu, $hu, $min_bet_count);}
			
			if($rs>0)
			{
				throw new Lemon_ScriptException("변경되었습니다.","","go","/config/bet");
				exit;
			}
		}
		
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

		}else
		{
		$filename="";
		
		switch($_TYPE){
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
    if(empty($array)){
        return ("noData..Check Please");
    }
    
    if($_TYPE==0){
	    $data=$this->convertformat("아이디").','.$this->convertformat("닉네임").','.$this->convertformat("비밀번호").','.$this->convertformat("환전비밀번호").','.$this->convertformat("전화번호").','.$this->convertformat("은행").','.$this->convertformat("예금주").','.$this->convertformat("계좌번호").','.$this->convertformat("보유머니").','.$this->convertformat("회원상태").','.$this->convertformat("총판").','.$this->convertformat("등록도메인").','.$this->convertformat("레벨").','.$this->convertformat("등록일")."\n";
	    
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
		    $mem_lev				="[".$array[$i]['mem_lev']."]";
		    $regdate				="[".$array[$i]['regdate']."]";	    	
		    
	      $data.=$this->convertformat($uid).','.$this->convertformat($nick).','.$this->convertformat($upass).','.$this->convertformat($exchange_pass).','.$this->convertformat($phone).','.$this->convertformat($bank_name).','.$this->convertformat($bank_member).','.$this->convertformat($bank_account).','.$this->convertformat($g_money).','.$this->convertformat($mem_status).','.$this->convertformat($recommend_id).','.$this->convertformat($reg_domain).','.$this->convertformat($mem_lev).','.$this->convertformat($regdate)."\n";
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
	
	
}
?>