<?php
/*
 *
 * 설정과 관련된 테이블
 * tb_admin, tb_point_config, tb_popup
 *
*/

class ConfigModel extends Lemon_Model
{

	//▶ 팝업 수정
	function modifyPopup($P_SUBJECT,$P_CONTENT,$P_POPUP_U,$P_STARTDAY,$P_ENDDAY,$P_WIN_WIDTH,$P_WIN_HEIGHT,$P_WIN_LEFT,$P_WIN_TOP,$P_MOVEURL,$imgsrc,$P_STYLE,$idx)
	{
		$sql = "UPDATE ".$this->db_qz."POPUP SET P_SUBJECT = '".$P_SUBJECT."',";
		$sql .= " P_CONTENT = '".$P_CONTENT."',";
		$sql .= " P_POPUP_U= '".$P_POPUP_U."',";
		$sql .= " P_STARTDAY='".$P_STARTDAY."',";
		$sql .= " P_ENDDAY='".$P_ENDDAY."',";
		$sql .= " P_WIN_WIDTH='".$P_WIN_WIDTH."',";
		$sql .= " P_WIN_HEIGHT='".$P_WIN_HEIGHT."',";
		$sql .= " P_WIN_LEFT='".$P_WIN_LEFT."',";
		$sql .= " P_WIN_TOP='".$P_WIN_TOP."',";
		$sql .= " P_MOVEURL='".$P_MOVEURL."',";
		$sql .= " P_FILE='".$imgsrc."',";
		$sql .= " P_STYLE = '".$P_STYLE."' WHERE IDX = ".$idx."";

		return $this->db->exeSql($sql);
	}

	//▶ 기본설정 수정
	function modifyGlobal($PARAM, $logo)
	{
		$n = 0;

		$PARAM_cnt=sizeof($PARAM);

		$sql = "UPDATE ".$this->db_qz."ADMIN SET";

		$sql2 = "UPDATE ".$this->db_qz."point_rate SET";

		foreach($PARAM as $key=>$value)
		{


			if($key == "first_charge1")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "first_charge2")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "first_charge3")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "first_charge4")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "first_charge5")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "every_charge1")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "every_charge2")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "every_charge3")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "every_charge4")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else if($key == "every_charge5")
			{
				$PARAM_cnt=$PARAM_cnt-1;
			}

			else
			{
				$sql .= " ".$key."='".$value."'";
				$PARAM_cnt=$PARAM_cnt-1;

				if($n == $PARAM_cnt)
				{

				}

				else if($PARAM_cnt!=0)
				{
					$sql .= ",";
				}
			}

			$n++;
		}
		$sql .= " WHERE logo='".$logo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 팝업 추가
	function addPopup($P_SUBJECT,$P_CONTENT,$P_POPUP_U,$P_STARTDAY,$P_ENDDAY,$P_WIN_WIDTH,$P_WIN_HEIGHT,$P_WIN_LEFT,$P_WIN_TOP,$P_MOVEURL,$imgsrc,$P_STYLE, $logo)
	{
		$sql = "insert into
						".$this->db_qz."POPUP (P_SUBJECT,P_CONTENT,P_POPUP_U,P_WRITEDAY,P_STARTDAY,P_ENDDAY,P_WIN_WIDTH,P_WIN_HEIGHT,P_WIN_LEFT,P_WIN_TOP,P_MOVEURL,P_FILE,P_STYLE,logo)
							 VALUES   ('".$P_SUBJECT."','".$P_CONTENT."','".$P_POPUP_U."',now(),'".$P_STARTDAY."','".$P_ENDDAY."','".$P_WIN_WIDTH."','".$P_WIN_HEIGHT."','".$P_WIN_LEFT."','".$P_WIN_TOP."','".$P_MOVEURL."','".$imgsrc."','".$P_STYLE."','".$logo."')";

		return $this->db->exeSql($sql);
	}
	//▶ 팝업 sn으로  삭제
	function delPopupbySn($sn)
	{
		$sql = "delete from ".$this->db_qz."popup
							where IDX = '".$sn."'";

		return $this->db->exeSql($sql);
	}

	function getPopupbyOrderby()
	{
		$sql="select * from ".$this->db_qz."popup order by logo, IDX desc ";

		return $this->db->exeSql($sql);
	}

	//▶ 필드 데이터
	function getPopupRow($field, $addWhere='')
	{
		$where = "logo='".$this->logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'popup', $where);
		return $rs[$field];
	}

	//▶ 필드 데이터's
	function getPopupRows($field, $addWhere)
	{
		$where = "1=1";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRows($field, $this->db_qz.'popup', $where);
	}

	//▶ 회원가입 무료머니
	function getJoinFreeMoney()
	{
		$rs = $this->getRow('mem_join', $this->db_qz.'point_config', "logo='".$this->logo."'");
		return $rs['mem_join'];
	}

	//▶ 필드 데이터
	function getPointConfigField($field, $addWhere='')
	{
		$where = "logo='".$this->logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'point_config', $where);
		return $rs[$field];
	}

	//▶ 필드 데이터's
	function getPointConfigRow($field, $addWhere='')
	{
		$where = "logo='".$this->logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRow($field, $this->db_qz.'point_config', $where);
	}

	//▶ 필드 데이터's
	function getPointConfigRowFromLogo($logo, $field, $addWhere='')
	{
		$where = "logo='".$logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRow($field, $this->db_qz.'point_config', $where);
	}

	//▶ 필드 데이터's
	function getPointConfigRows($field, $addWhere='')
	{
		$where = "logo='".$this->logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRows($field, $this->db_qz.'point_config', $where);
	}

	//▶ 포인트 설정 저장
	function savePointConfig(	$joinFreeMoney, $bonus3, $bonus4, $bonus5, $bonus6, $bonus7, $bonus8, $bonus9, $bonus10,
														$folderPlus,
														$replyPoint, $replyLimit, $boardWritePoint, $boardWriteLimit, $bettingBoardWritePoint, $bettingBoardWriteLimit, $jackpot, $jackpot_rate)
	{
		$folderBonus	= sprintf("%d:%d:%d:%d:%d:%d:%d:%d:", $bonus3,$bonus4,$bonus5,$bonus6,$bonus7,$bonus8,$bonus9,$bonus10);
		$sql = "update ".$this->db_qz."level_config set lev_folder_bonus='".$folderBonus."' where logo='".$this->logo."'";
		$this->db->exeSql($sql);

		$sql = "update ".$this->db_qz."point_config
						set mem_join=".$joinFreeMoney.",
							folder_bouns3=".$bonus3.",folder_bouns4=".$bonus4.",folder_bouns5=".$bonus5.",folder_bouns6=".$bonus6.",
							folder_bouns7=".$bonus7.",folder_bouns8=".$bonus8.",folder_bouns9=".$bonus9.",folder_bouns10=".$bonus10.",
							chk_folder='".$folderPlus."',
							reply_point=".$replyPoint.", reply_limit=".$replyLimit.", board_write_point=".$boardWritePoint.",
							board_write_limit=".$boardWriteLimit.", betting_board_write_point=".$bettingBoardWritePoint.", betting_board_write_limit=".$bettingBoardWriteLimit.",
							jackpot_give_rate=".$jackpot_rate.", jackpot_sum_money=".$jackpot."
						where logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 필드 데이터
	function getAdminConfigField($field='*', $addWhere='')
	{
		$where = "logo='".$this->logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'admin', $where);
		return $rs[$field];
	}

	//▶ 필드 데이터's
	function getAdminConfigRow($field='*', $addWhere='', $logo='')
	{
		if($logo=='') 	$where = "logo='".$this->logo."'";
		else					$where = "logo='".$logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRow($field, $this->db_qz.'admin', $where);
	}

	//▶ 필드 데이터's
	function getAdminConfigRows($field='*', $addWhere='', $logo)
	{
		$where = "logo='".$logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRows($field, $this->db_qz.'admin', $where);
	}

	function getHead($admin_id)
	{
		$sql="select * from ".$this->db_qz."head
						where head_id = '".$admin_id."' and kubun=1 and logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 어드민 설정
	function getAdminConfig()
	{
		$sql="select * from ".$this->db_qz."admin
						where logo='".$this->logo."'";

		$rs =  $this->db->exeSql($sql);

		return $rs[0];
	}

	//▶ 잭팟 갱신
	function getPointConfig()
	{
		$sql="select * from ".$this->db_qz."point_config
						where logo='".$this->logo."'";

		$rs =  $this->db->exeSql($sql);
		return $rs[0];
	}

	//▶ 잭팟 금액
	function getJackpot()
	{
		$rs = $this->getRow('jackpot_sum_money', $this->db_qz.'point_config', "logo='".$this->logo."'");
		return $rs['jackpot_sum_money'];
	}

	//▶ 잭팟 갱신
	function modifyJackpot($jackpot)
	{
		$sql = "update ".$this->db_qz."point_config
				set jackpot_sum_money=jackpot_sum_money-".$jackpot."
					where logo='".$this->logo."'";
		$this->db->exeSql($sql);
	}

	function getJackpotRate()
	{
		$sql = "select jackpot_give_rate from ".$this->db_qz."point_config
					where logo='".$this->logo."'";
		$rs = $this->db->exeSql($sql);
		return $rs[0]['jackpot_give_rate'];
	}

	//▶ 잭팟 givejackpot 갱신
	function modifygiveJackpot($jackpot)
	{
		$sql = "update ".$this->db_qz."point_config
				set jackpot_sum_money=jackpot_sum_money+".$jackpot."
					where logo='".$this->logo."'";
		$this->db->exeSql($sql);
	}

	//▶ 레벨 목록
	function getLevelConfigRows($field='*', $addWhere='')
	{
		$where = " logo='".$this->logo."'";

		if($addWhere!='')
			$where.=" and ".$addWhere;

		return $this->getRows($field, $this->db_qz.'level_config', $where." order by lev asc");
	}

	//▶ 레벨 목록
	function getLevelConfigRowsFromLogo($logo)
	{
		$field = "*";
		$where = " logo='".$logo."'";

		return $this->getRows($field, $this->db_qz.'level_config', $where." order by lev asc");
	}

	//▶ 레벨 목록
	function getLevelConfigRow($level, $field='*', $addWhere='')
	{
		$where = " logo='".$this->logo."' and lev=".$level;
		if($addWhere!='') $where.=" and ".$addWhere;

		return $this->getRow($field, $this->db_qz.'level_config', $where);
	}

	//▶ 필드 데이터's
	function getFirstRateRow($field='*', $addWhere='', $lev, $logo)
	{
		$where = "mLevel=".$lev." and logo='".$logo."'";

		echo $where;

		if($addWhere!='') {$where .=' and '.$addWhere;}

		return $this->getRow($field, $this->db_qz.'point_rate', $where);
	}

	//▶ 레벨 목록
	function getLevelConfigField($level, $field, $addWhere='')
	{
		$where = "lev=".$level;

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'level_config', $where);

		return $rs[$field];
	}

	//▶ 레벨 설정
	function modifyLevelConfig($sn, $levName, $minMoney, $maxMoney, $maxMoneySpecial, $maxBonus, $maxBonusSpecial, $maxSingle, $maxSingleSpecial, $chargeRate, $loseRate, $recommendRate, $folderBonus, $bank, $bankAccount, $bankOwner, $minCharge, $minExchange, $recommendLimit, $domain)
	{
		$sql = "update ".$this->db_qz."level_config
						set lev_name='".$levName."',lev_min_money=".$minMoney.",lev_max_money=".$maxMoney.",lev_max_bonus=".$maxBonus.",lev_charge_mileage_rate=".$chargeRate.", lev_bet_failed_mileage_rate=".$loseRate.",
						lev_join_recommend_mileage_rate='".$recommendRate."',lev_folder_bonus='".$folderBonus."', lev_bank='".$bank."', lev_bank_account='".$bankAccount."', lev_bank_owner='".$bankOwner."',
						lev_max_money_special='".$maxMoneySpecial."',lev_max_bonus_special='".$maxBonusSpecial."', lev_max_money_single='".$maxSingle."', lev_max_money_single_special='".$maxSingleSpecial."',lev_recommend_limit=".$recommendLimit.",
						lev_bank_min_charge=".$minCharge.", lev_bank_min_exchange=".$minExchange.", lev_domain='".$domain."'
						where logo='".$this->logo."' and Id=".$sn;

		return $this->db->exeSql($sql);
	}

	//▶ 레벨 설정(다폴더 보너스 마일리지가 없는 경우)
	function _modifyLevelConfig($logo, $sn, $levName, $minMoney, $maxMoney, $maxMoneySpecial, $maxBonus, $maxBonusSpecial, $maxSingle, $maxSingleSpecial, $chargeRate, $loseRate, $recommendRate, $bank, $bankAccount, $bankOwner, $minCharge, $minExchange, $recommendLimit, $domain)
	{
		$sql = "update ".$this->db_qz."level_config
						set lev_name='".$levName."',lev_min_money=".$minMoney.",lev_max_money=".$maxMoney.",lev_max_bonus=".$maxBonus.",lev_charge_mileage_rate=".$chargeRate.", lev_bet_failed_mileage_rate=".$loseRate.",
						lev_join_recommend_mileage_rate='".$recommendRate."', lev_bank='".$bank." ', lev_bank_account='".$bankAccount."', lev_bank_owner='".$bankOwner."',
						lev_max_money_special='".$maxMoneySpecial."',lev_max_bonus_special='".$maxBonusSpecial."', lev_max_money_single='".$maxSingle."', lev_max_money_single_special='".$maxSingleSpecial."',lev_recommend_limit=".$recommendLimit.",
						lev_bank_min_charge=".$minCharge.", lev_bank_min_exchange=".$minExchange.", lev_domain='".$domain."'
						where logo='".$logo."' and Id=".$sn;

		return $this->db->exeSql($sql);
	}

	//▶ 레벨 설정 추가
	function addLevelConfig()
	{
		$sql = "select max(lev) as level
				from ".$this->db_qz."level_config";
		$rs = $this->db->exeSql($sql);

		$level = $rs[0]['level']+1;

		$sql = "insert into ".$this->db_qz."level_config(lev,lev_min_money,lev_max_money,lev_max_bonus,lev_charge_mileage_rate,logo)
				values(".$level.",0,0,0,0,'".$this->logo."')";

		return $this->db->exeSql($sql);
	}

	//▶ 레벨 설정 삭제
	function delLevelConfig()
	{
		$sql = "select max(Id) as sn
				from ".$this->db_qz."level_config";
		$rs = $this->db->exeSql($sql);

		$sn = $rs[0]['sn'];

		$sql = "delete from ".$this->db_qz."level_config
				where logo='".$this->logo."' and Id =".$sn;

		return $this->db->exeSql($sql);
	}

	//▶ 이벤트 설정 목록
	function getEventConfigRows($field='*', $addWhere='')
	{
		$where = " logo='".$this->logo."'";
		if($addWhere!='') $where.=" and ".$addWhere;

		$sql = "select ".$field."
				from ".$this->db_qz."event_config ".$where;
		return $this->db->exeSql($sql);
	}

	//▶ 이벤트 설정 목록
	function getEventConfigRow($field='*', $addWhere='')
	{
		$where = " logo='".$this->logo."'";
		if($addWhere!='') $where.=" and ".$addWhere;

		return $this->getRow($field, $this->db_qz.'event_config', $where);
	}

	//▶ 이벤트 설정 목록
	function getEventConfigField($field, $addWhere='')
	{
		$where = " logo='".$this->logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'event_config', $where);
		return $rs[$field];
	}

	//▶ 이벤트 설정
	function modifyEventConfig($minCharge,$bonus1,$bonus2,$bonus3,$bonus4,$bonus5,$bonus6,$bonus7,$bonus8,$bonus9,$bonus10)
	{
		$sql = "update ".$this->db_qz."event_config
				set min_charge=".$minCharge.",bonus1=".$bonus1.",bonus2=".$bonus2.",bonus3=".$bonus3.", bonus4=".$bonus4.",
				bonus5=".$bonus5.",bonus6=".$bonus6.",bonus7=".$bonus7.", bonus8=".$bonus8.",bonus9=".$bonus9.",bonus10=".$bonus10."
					where logo='".$this->logo."'";

		return $this->db->exeSql($sql);
	}

	//▶ 이벤트 추가
	function addEventConfig($minCharge,$bonus1,$bonus2,$bonus3,$bonus4,$bonus5,$bonus6,$bonus7,$bonus8,$bonus9,$bonus10)
	{
		if($minCharge=='')	$minCharge	= 100000;
		if($bonus1=='')		$bonus1 	= 0;
		if($bonus2=='')		$bonus2 	= 0;
		if($bonus3=='')		$bonus3 	= 5000;
		if($bonus4=='')		$bonus4 	= 10000;
		if($bonus5=='')		$bonus5 	= 30000;
		if($bonus6=='')		$bonus6 	= 100000;
		if($bonus7=='')		$bonus7 	= 200000;
		if($bonus8=='')		$bonus8 	= 700000;
		if($bonus9=='')		$bonus9 	= 1000000;
		if($bonus10=='')	$bonus10 	= 2000000;

		$sql = "insert into ".$this->db_qz."event_config(min_charge,bonus1,bonus2,bonus3,bonus4,bonus5,bonus6,bonus7,bonus8,bonus9,bonus10,logo)
				values(".$minCharge.",".$bonus1.",".$bonus2.",".$bonus3.",".$bonus4.",".$bonus5.",".$bonus6.",".$bonus7.",".$bonus8.",".$bonus9.",".$bonus10."'".$this->logo."')";
		return $this->db->exeSql($sql);
	}

	//▶ 사이트 설정 목록
	function getSiteConfigRows($field='*', $addWhere='')
	{
		$where = " logo='".$this->logo."'";
		if($addWhere!='') $where.=" and ".$addWhere;

		$sql = "select ".$field."
				from ".$this->db_qz."site_config ".$where;
		return $this->db->exeSql($sql);
	}


	//▶ 사이트 설정 목록
	function getSiteConfigRow($field='*', $addWhere='')
	{
		$where = " logo='".$this->logo."'";
		if($addWhere!='') $where.=" and ".$addWhere;

		return $this->getRow($field, $this->db_qz.'site_config', $where);
	}

	//▶ 사이트 설정 목록
	function getSiteConfigRowFromLogo($logo, $field='*', $addWhere='')
	{
		$where = " logo='".$logo."'";
		if($addWhere!='') $where.=" and ".$addWhere;

		return $this->getRow($field, $this->db_qz.'site_config', $where);
	}

	//▶ 사이트 설정 목록
	function getSiteConfigField($field, $addWhere='')
	{
		$where = " logo='".$this->logo."'";

		if($addWhere!='') {$where .=' and '.$addWhere;}

		$rs = $this->getRow($field, $this->db_qz.'site_config', $where);
		return $rs[$field];
	}

	//▶ 사이트 설정
	function modifySiteConfig($betRule,$betRulevh,$betRulevu,$betRulehu,$minBetCount, $logo)
	{
		$sql = "update ".$this->db_qz."site_config
				set bet_rule=".$betRule.",bet_rule_vh=".$betRulevh.",bet_rule_vu=".$betRulevu.",bet_rule_hu=".$betRulehu.", min_bet_count=".$minBetCount."
					where logo='".$logo."'";

		return $this->db->exeSql($sql);
	}

	function addSiteConfig($betRule,$betRulevh,$betRulevu,$betRulehu,$minBetCount)
	{
		if($minBetCount=='')	$minBetCount=1;
		$sql = "insert into ".$this->db_qz."site_config(bet_rule,bet_rule_vh,bet_rule_vu,bet_rule_hu,min_bet_count,logo)
				values(".$betRule.",".$betRulevh.",".$betRulevu.",".$betRulehu.",".$minBetCount.",'".$this->logo."')";
		return $this->db->exeSql($sql);
	}

	function getDomainList()
	{
		$sql = "select * from ".$this->db_qz."domain";
		return $this->db->exeSql($sql);
	}


	//▶ 알람설정
	function modifyAlramFlag($field,$flag)
	{
		$sql = "update ".$this->db_qz."alarm_flag
						set ".$field."='".$flag."' where idx=1";

		return $this->db->exeSql($sql);
	}

	function modify_ladder_config($sn, $ladder_min_betting, $ladder_max_betting, $ladder_max_prize)
	{
		$data['ladder_min_betting'] = $ladder_min_betting;
		$data['ladder_max_betting'] = $ladder_max_betting;
		$data['ladder_max_prize'] = $ladder_max_prize;

		$this->db->setUpdate($this->db_qz."level_config", $data, " Id=".$sn);
		return $this->db->exeSql();
	}

	function modify_powerball_config($powerball_min_betting, $powerball_max_betting, $powerball_max_prize)
	{
		$data['powerball_min_betting'] = $powerball_min_betting;
		$data['powerball_max_betting'] = $powerball_max_betting;
		$data['powerball_max_prize'] = $powerball_max_prize;

		$this->db->setUpdate($this->db_qz."level_config", $data, "1=1");
		return $this->db->exeSql();
	}

	function modify_snail_race_config($sn, $snail_race_min_betting, $snail_race_max_betting, $snail_race_max_prize)
	{
		$data['snail_race_min_betting'] = $snail_race_min_betting;
		$data['snail_race_max_betting'] = $snail_race_max_betting;
		$data['snail_race_max_prize'] = $snail_race_max_prize;

		$this->db->setUpdate($this->db_qz."level_config", $data, " Id=".$sn);
		return $this->db->exeSql();
	}

	//▶ 첫충전
	function first_charge_rate($logo, $level)
	{
	//	$sql = "select first_charge from ".$this->db_qz."admin where logo='".$logo."'";

		$sql = "select firstRate from tb_point_rate where mLevel='{$level}' and logo='{$logo}'";

		$rows = $this->db->exeSql($sql);
		return $rows[0]['firstRate'];
	}

	function every_charge_rate($logo, $level)
	{
	//	$sql = "select first_charge from ".$this->db_qz."admin where logo='".$logo."'";

		$sql = "select everyRate from tb_point_rate where mLevel='{$level}' and logo='{$logo}'";

		$rows = $this->db->exeSql($sql);
		return $rows[0]['everyRate'];
	}

	function max_charge_rate($logo, $level)
	{
		$sql = "select maxPoint from tb_point_rate where mLevel='{$level}' and logo='{$logo}'";

		$rows = $this->db->exeSql($sql);
		return $rows[0]['maxPoint'];
	}

	function getChangeRateConfig()
	{
		$sql = "select * from tb_change_rate_config order by idx asc";
		return $this->db->exeSql($sql);
	}

	function getChangeRateConfigByTypeSpecial($type, $special)
	{
		$sql = "select * from tb_change_rate_config where type=$type and special=$special ";
		$rs = $this->db->exeSql($sql);
		return $rs[0];
	}

	function modifyChangeRateConfig($idx, $home_rate, $draw_rate, $away_rate, $allow_rate_change, $allow_base_change, $allow_betting_auto, $allow_magam_auto)
	{
		if($allow_rate_change != 'Y')
			$allow_rate_change = 'N';
		if($allow_base_change != 'Y')
			$allow_base_change = 'N';
		if($allow_betting_auto != 'Y')
			$allow_betting_auto = 'N';
		if($allow_magam_auto != 'Y')
			$allow_magam_auto = 'N';

		$sql = "update tb_change_rate_config set home_rate=$home_rate, draw_rate=$draw_rate, away_rate=$away_rate ";
		$sql .=" , allow_rate_change='$allow_rate_change' , allow_base_change='$allow_base_change' ";
		$sql .=" , allow_betting_auto='$allow_betting_auto' , allow_magam_auto='$allow_magam_auto' ";
		$sql .=" where idx = $idx";
		//echo $sql;
		return $this->db->exeSql($sql);
	}
}

?>
