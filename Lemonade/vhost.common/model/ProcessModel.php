<?php
/**
 * ProcessModel.php
 *--------------------------------------------------------------------
 *
 * working set - frequently updated process set
 *
 *--------------------------------------------------------------------
 * Copyright (C) 
 * http://www.monaco.com
 */
class ProcessModel extends Lemon_Model 
{
	public $memberModel	='';
	public $gameModel	='';
	public $cartModel	='';
	public $moneyModel	='';
	public $configModel	='';
	public $commonModel	='';
	public $boardModel	='';
	
	function __construct()
	{
		$this->memberModel 	= Lemon_Instance::getObject("MemberModel",true);
		$this->gameModel 	= Lemon_Instance::getObject("GameModel",true);
		$this->cartModel 	= Lemon_Instance::getObject("CartModel",true);
		$this->moneyModel  	= Lemon_Instance::getObject("MoneyModel",true);
		$this->configModel  = Lemon_Instance::getObject("ConfigModel",true);
		$this->commonModel  = Lemon_Instance::getObject("CommonModel",true);
		$this->partnerModel  = Lemon_Instance::getObject("PartnerModel",true);
		$this->levelConfigModel = Lemon_Instance::getObject("LevelConfigModel",true);
		$this->boardModel = Lemon_Instance::getObject("BoardModel",true);
	}
	
	/**
	*--------------------------------------------------------------------
 	*
 	* betting process
 	*
 	*--------------------------------------------------------------------
 	*/
 	
 	function bettingProcess($sn, $amount)
 	{
 		$this->modifyMoneyProcess($sn, -$amount, 3, '배팅');
 	}
 	
 	function virtualBettingProcess($sn, $amount)
 	{
 		$this->modifyVirtualMoneyProcess($sn, -$amount, 3, '배팅');
 	}
 
	//▶ 취소
	function bettingCancelProcess($bettingNo)
	{
		//이미 취소했는지 여부 판단
		$sql = "select count(betting_no) as cnt from ".$this->db_qz."money_log
						where state=5 and betting_no='".$bettingNo."'";
		$rs = $this->db->exeSql($sql);
		if($rs[0]['cnt']>0)
			return;
						
		$sql = "select member_sn, parent_sn, betting_money, result_money, bonus, result 
						from ".$this->db_qz."total_cart 
						where betting_no='".$bettingNo."'";
		$rs = $this->db->exeSql($sql);
		
		$sn					= $rs[0]["member_sn"];
		$parentIdx	= $rs[0]["parent_sn"];
		$betMoney		= $rs[0]["betting_money"];
		$result			= $rs[0]["result"];
		
	
		if(0==$result)
		{
			$this->modifyMoneyProcess($sn, $betMoney, 5, $bettingNo);
			$this->cartModel->delCart($bettingNo);
			$this->boardModel->cancelJackpotContent($bettingNo);
		}
	}
	
	/**
	*--------------------------------------------------------------------
 	*
 	* game process
 	*
 	*--------------------------------------------------------------------
 	*/
 	//▶ 취소
 	function cancelGameProcess($childSn)
 	{
 		$rs = $this->gameModel->getSubChildRows($childSn, "*");
 		for($i=0; $i<sizeof($rs); ++$i)
 		{
 			$subSn 		= $rs[$i]['sn'];
 			$betType 	= $rs[$i]['betting_type'];
 			
 			//01. modify subchild
	 		$sql = "update ".$this->db_qz."subchild set win=4, result=1
		 					where sn=".$subSn;
		 					
		 	$this->db->exeSql($sql);
 		}
 	}
 	
 	/*
 	function resultPreviewProcess($childSn, $homeScore, $awayScore, $gameCancel="")
 	{
 		if($childSn=="")
 			return;
 			
 		$sql = "select 	a.type,
 										b.draw_rate,
 										b.sn as sub_child_sn
 						from 	".$this->db_qz."child a,
 									".$this->db_qz."subchild b
 						where a.sn=b.child_sn
 									and a.sn=".$childSn;
 									
 		$childRs = $this->db->exeSql($sql);
 		$gameType = $childRs[0]['type'];
 		$drawRate = $childRs[0]['draw_rate'];
 		$subChildSn = $childRs[0]['sub_child_sn'];
 		
		////////////////////////////////////////////////////////////////////////////////
		//modify total_betting
		$sql = "select 	a.sn, 
										a.select_no, 
										a.game_type, 
										a.home_rate, 
										a.draw_rate,
										a.away_rate
						from ".$this->db_qz."total_betting a,
								 ".$this->db_qz."total_cart b
						where a.betting_no=b.betting_no
									and b.is_account=1
									and a.sub_child_sn=".$subChildSn;
						
		$rs = $this->db->exeSql($sql);
	
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$winTeam = '';
 			$winCode = '';
 		
			$betSn 		= $rs[$i]["sn"];
			$select 	= $rs[$i]["select_no"];
			$selectDrawRate	= $rs[$i]["draw_rate"];
			
			//핸디캡, 언더오버의 기준점 변경이 일어날 경우를 대비해, 배팅당시의 기준점으로 처리한다.
			
			//강제취소의 경우 자기 자신의 배당을 하지 않고, 전체 적용한다.
			if($gameCancel=="1")
			{
				$winCode=4;
			}
			else
			{
				//관리자에 의해 적특처리를 했을 경우 적특으로 처리를 해줘야 한다.
				if($rs[$i]["home_rate"]=="1.00" && $rs[$i]["draw_rate"]=="1.00" && $rs[$i]["away_rate"]=="1.00") 
					$winCode = 4;
				else
				{
					if($gameType==1) //승무패
					{
						if($homeScore > $awayScore)				$winCode = 1;
				 		else if($homeScore < $awayScore)	$winCode = 2;
				 		else if($homeScore == $awayScore)	$winCode = 3;
					}
					else if($gameType==2)
				 	{
				 		if(($homeScore+$selectDrawRate) > $awayScore)				$winCode = 1;
				 		else if(($homeScore+$selectDrawRate) < $awayScore)	$winCode = 2;
				 		else if(($homeScore+$selectDrawRate) == $awayScore)	$winCode = 4;
					}
					else if($gameType==4)
				 	{
				 		if(($homeScore+$awayScore)==$selectDrawRate) $winCode = 4;
				 		else
				 			$winCode = (($homeScore+$awayScore) > $selectDrawRate) ? 1:2;
					}
				}
			}
			if($winCode==4)							{$result=4;}
			else if($select==$winCode) 	{$result=1;}
			else				  							{$result=2;}
				
			$sql = "update ".$this->db_qz."total_betting 
							set preview_result=".$result." 
							where sn=".$betSn;
			$this->db->exeSql($sql);
		 }
		 
		 return $this->accountListProcess($childSn);
	}
	*/
	
	function resultPreviewProcess($childSn, $homeScore, $awayScore, $gameCancel="", $betData)
 	{
 		if($childSn=="")
 			return;
 			
 		$sql = "select 	a.type,
 										b.draw_rate,
 										b.sn as sub_child_sn
 						from 	".$this->db_qz."child a,
 									".$this->db_qz."subchild b
 						where a.sn=b.child_sn
 									and a.sn=".$childSn;
 									
 		$childRs = $this->db->exeSql($sql);
 		$gameType = $childRs[0]['type'];
 		$drawRate = $childRs[0]['draw_rate'];
 		$subChildSn = $childRs[0]['sub_child_sn'];
 		
		////////////////////////////////////////////////////////////////////////////////
		//modify total_betting
		$sql = "select sn, select_no, game_type, home_rate, draw_rate, away_rate
						from ".$this->db_qz."total_betting 
						where sub_child_sn=".$subChildSn;
						
		$rs = $this->db->exeSql($sql);
	
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$winTeam = '';
 			$winCode = '';
 		
			$betSn 		= $rs[$i]["sn"];
			$select 	= $rs[$i]["select_no"];
			$selectDrawRate	= $rs[$i]["draw_rate"];
			
			//핸디캡, 언더오버의 기준점 변경이 일어날 경우를 대비해, 배팅당시의 기준점으로 처리한다.
			
			//강제취소의 경우 자기 자신의 배당을 하지 않고, 전체 적용한다.
			if($gameCancel=="1")
			{
				$winCode=4;
			}
			else
			{
				//관리자에 의해 적특처리를 했을 경우 적특으로 처리를 해줘야 한다.
				if($rs[$i]["home_rate"]=="1.00" && $rs[$i]["draw_rate"]=="1.00" && $rs[$i]["away_rate"]=="1.00") 
					$winCode = 4;
				else
				{
					if($gameType==1) //승무패
					{
						if($homeScore > $awayScore)				$winCode = 1;
				 		else if($homeScore < $awayScore)	$winCode = 2;
				 		else if($homeScore == $awayScore)	$winCode = 3;
					}
					else if($gameType==2)
				 	{
				 		if(($homeScore+$selectDrawRate) > $awayScore)				$winCode = 1;
				 		else if(($homeScore+$selectDrawRate) < $awayScore)	$winCode = 2;
				 		else if(($homeScore+$selectDrawRate) == $awayScore)	$winCode = 4;
					}
					else if($gameType==4)
				 	{
				 		if(($homeScore+$awayScore)==$selectDrawRate) $winCode = 4;
				 		else
				 			$winCode = (($homeScore+$awayScore) > $selectDrawRate) ? 1:2;
					}
				}
			}
			if($winCode==4)							{$result=4;}
			else if($select==$winCode) 	{$result=1;}
			else				  							{$result=2;}
			
			
			$data["bet_sn"]			= $betSn;
			$data["child_sn"]		= $childSn;
			$data["home_score"] = $homeScore;
			$data["away_score"] = $awayScore;
			$data["win"]				= $winCode;
			$data["result"]			= $result;
			$betData[] = $data;
		 }
		 
		 return $this->accountListProcess($childSn, $betData);
	}
 	
 	//▶ [수정] - 결과에 따른 정보 갱신
 	function resultGameProcess($childSn, $homeScore, $awayScore, $gameCancel="")
 	{
  
 		$sql = "select 	a.type,
 										b.draw_rate,
 										b.sn as sub_child_sn
 						from 	".$this->db_qz."child a,
 									".$this->db_qz."subchild b
 						where a.sn=b.child_sn
 									and a.sn=".$childSn;
 									
 		$childRs = $this->db->exeSql($sql);
         
         
 		$gameType = $childRs[0]['type'];
 		$drawRate = $childRs[0]['draw_rate'];
 		$subChildSn = $childRs[0]['sub_child_sn'];
 		
 		$winTeam = '';
 		$winCode = '';
 		
 		// modify result flag
 		////////////////////////////////////////////////////////////////////////////////
 		if($gameCancel=="1")
 		{
 			$homeScore = 0;
			$awayScore = 0;
			$winTeam = "Cancel";
 		}
 		else
 		{
	 		if($gameType==1)
			{
				if($homeScore==$awayScore)
				{
					if($drawRate=="1.00")		$winTeam = 'Cancel';
					else										$winTeam = 'Draw';
				}
				else if($homeScore > $awayScore){$winTeam = 'Home';}
				else														{$winTeam = 'Away';}
			}
			else if($gameType==2)
			{
				if($homeScore+$drawRate > $awayScore) 			{$winTeam = 'Home';}
				else if($homeScore+$drawRate < $awayScore) 	{$winTeam = 'Away';}
				else																				{$winTeam = 'Cancel';}
			}
			else if($gameType==4)
			{
				if($homeScore+$awayScore > $drawRate) 			{$winTeam = 'Home';}
				else if($homeScore+$awayScore < $drawRate) 	{$winTeam = 'Away';}
				else																				{$winTeam = 'Cancel';}
			}
		}
		$set="";
		$where="";
		
		$set .=	"home_score=".$homeScore."," ;
		$set .=	"away_score=".$awayScore;
				
		if($gameType==1 or $gameType==4)	
			$set.=",win_team='".$winTeam."'";
			
		else if($gameType==2)
			$set.=",handi_winner='".$winTeam."'";
					
		$where=" sn=".$childSn;

		$this->gameModel->modifyChild($set, $where);
		
		if($winTeam=='Home') 				$winCode = 1;
		else if($winTeam=='Away') 	$winCode = 2;
		else if($winTeam=='Draw') 	$winCode = 3;
		else if($winTeam=='Cancel') $winCode = 4;
		
		$sql = "update ".$this->db_qz."subchild 
							set win=".$winCode."
							where child_sn=".$childSn;
		$this->db->exeSql($sql);
		
		////////////////////////////////////////////////////////////////////////////////
		//modify total_betting
		$sql = "select sn, select_no, game_type, home_rate, draw_rate, away_rate
						from ".$this->db_qz."total_betting 
						where sub_child_sn=".$subChildSn;
		$rs = $this->db->exeSql($sql);
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$winTeam = '';
 			$winCode = '';
 		
			$betSn 		= $rs[$i]["sn"];
			$select 	= $rs[$i]["select_no"];
			$selectDrawRate	= $rs[$i]["draw_rate"];
			
			//핸디캡, 언더오버의 기준점 변경이 일어날 경우를 대비해, 배팅당시의 기준점으로 처리한다.
			
			//강제취소의 경우 자기 자신의 배당을 하지 않고, 전체 적용한다.
			if($gameCancel=="1")
			{
				$winCode=4;
			}
			else
			{
				//관리자에 의해 적특처리를 했을 경우 적특으로 처리를 해줘야 한다.
				if($rs[$i]["home_rate"]=="1.00" && $rs[$i]["draw_rate"]=="1.00" && $rs[$i]["away_rate"]=="1.00") 
					$winCode = 4;
				else
				{
					if($gameType==1) //승무패
					{
						if($homeScore==$awayScore)
						{
							if($drawRate=="1.00")		$winCode = 4;
							else										$winCode = 3;
						}
						else if($homeScore > $awayScore){$winCode = 1;}
						else														{$winCode = 2;}
					}
					else if($gameType==2)
				 	{
				 		if(($homeScore+$selectDrawRate) > $awayScore)				$winCode = 1;
				 		else if(($homeScore+$selectDrawRate) < $awayScore)	$winCode = 2;
				 		else if(($homeScore+$selectDrawRate) == $awayScore)	$winCode = 4;
					}
					else if($gameType==4)
				 	{
				 		if(($homeScore+$awayScore)==$selectDrawRate) $winCode = 4;
				 		else
				 			$winCode = (($homeScore+$awayScore) > $selectDrawRate) ? 1:2;
					}
				}
			}
			if($winCode==4)							{$result=4;}
			else if($select==$winCode) 	{$result=1;}
			else				  							{$result=2;}
				
			$sql = "update ".$this->db_qz."total_betting set result=".$result." where sn=".$betSn;
			$this->db->exeSql($sql);
		 }
		 
		 $this->accountMoneyProcess($childSn);
	}
	
	//▶ [배당지급] - 결과에 따른 돈 지급
 	function accountMoneyProcess($childSn)
 	{
 		$rs = $this->gameModel->getChildRow($childSn, '*');
 		
 		$homeScore = $rs['home_score'];
 		$awayScore = $rs['away_score'];
 		$league_sn = $rs['league_sn']; //사다리게임만 체크를 위함
 		$special = $rs['special'];  // 낙첨에서 라이브배팅 제외
         
 		if(is_null($homeScore)||is_null($awayScore))
 			return -1;
 			
 		//이미 처리된게임인지 확인
 		if($rs['kubun']==1)
 			return 1;
 	
 		$rs = $this->gameModel->getSubChildRow($childSn, "sn");
 		$subChildSn = $rs['sn'];
 		
 		$sql = "update ".$this->db_qz."subchild set result=1 where sn=".$subChildSn;
		$this->db->exeSql($sql);
 			
 		$sql = "update ".$this->db_qz."child set kubun=1 where sn=".$childSn;
 		$this->db->exeSql($sql);
 
		$sql = "select b.betting_no, b.member_sn
						from 	".$this->db_qz."subchild a, 
									".$this->db_qz."total_betting b,
									".$this->db_qz."child c,
									".$this->db_qz."total_cart d
						where a.sn=b.sub_child_sn
									and a.child_sn=c.sn 
									and b.betting_no=d.betting_no 
									and d.result=0 
									and a.child_sn=".$childSn;
		$rs = $this->db->exeSql($sql);
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo  = $rs[$i]['betting_no'];
			$sn		 = $rs[$i]['member_sn'];
			
			$sql = "select * 
							from ".$this->db_qz."total_betting where betting_no='".$bettingNo."'";
			$rsi = $this->db->exeSql($sql);
			
			$winCount = $loseCount = $cancelCount = $ingGameCount = 0;
			$winRate	  = 1;
			$winMoney	  = 0;
			$total		 = sizeof($rsi);
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$betMoney = $rsi[$j]['bet_money'];
				
				if($rsi[$j]['result']==0)
					++$ingGameCount;
					
				else if($rsi[$j]['result']==1)
				{
					++$winCount;
					$winRate *= $rsi[$j]['select_rate'];
				}
				else if($rsi[$j]['result']==2)
				{
					++$loseCount;
					break;
				}
				else if($rsi[$j]['result']==4)
				{
					++$cancelCount;
					$winRate *= 1;
				}
			}
			
			if($loseCount>0) //낙첨
			{
				$sql = "update ".$this->db_qz."total_cart set result=2, operdate=now() where betting_no ='".$bettingNo."'";
				$rsi = $this->db->exeSql($sql);
				if($rsi<=0)
				{
					$sql = "insert into debug_detail_log(betting_no, total, win, lose, ing, cancel) 
									values('".$bettingNo."',".$total.",".$winCount.",".$loseCount.",".$ingGameCount.",".$cancelCount.")";
					$this->db->exeSql($sql);
				}
					
				//사다리는 지급하지 않는다.
				if($special!=2 && $special!=5 && $special!=6 && $special!=7 && $special!=8)
				{
					//낙첨 마일리지
					$this->modifyMileageProcess($sn, $betMoney, "4", $bettingNo);
					//추천인 낙첨 마일리지
					$this->recommendFailedGameMileage($sn,$betMoney,$bettingNo);
				}
				
				//잭팟 적립
				$jackpot_rate = $this->configModel->getJackpotRate();
				$jackpot = $betMoney/100*$jackpot_rate;
				if( $jackpot>0)
					$this->configModel->modifygiveJackpot($jackpot);
			}
			
			//모든게임종료
			else if($ingGameCount==0)
			{
				$memberSn = $rs[$i]['member_sn'];
				$sql = "select logo from ".$this->db_qz."member where sn=".$memberSn;
				$rsi = $this->db->exeSql($sql);
					
				$logo = $rsi[0]['logo'];
				
				//모두 취소된 게임
				if($total==$cancelCount)
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart 
									set result=4, operdate=now(), result_money='".$winMoney."' 
									where logo='".$logo."' 
												and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
					if($rsi<=0)
					{
						$sql = "insert into debug_detail_log(betting_no, total, win, lose, ing, cancel) 
										values('".$bettingNo."',".$total.",".$winCount.",".$loseCount.",".$ingGameCount.",".$cancelCount.")";
						$this->db->exeSql($sql);
					}
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
				}
				else if(($winCount+$cancelCount) >= $total) //당첨
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart
									set result=1, 
											operdate=now(),
											result_money='".$winMoney."' 
									where logo='".$logo."' 
												and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
					if($rsi<=0)
					{
						$sql = "insert into debug_detail_log(betting_no, total, win, lose, ing, cancel) 
										values('".$bettingNo."',".$total.",".$winCount.",".$loseCount.",".$ingGameCount.",".$cancelCount.")";
						$this->db->exeSql($sql);
					}
						
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
					
					//당첨문자메세지
					//$this->sendWinMsgCheck($memberSn);
					
					//다폴더 계산
					if($winCount>2)
					{
						$level = $this->memberModel->getMemberField($memberSn,'mem_lev');
						$rsj = $this->configModel->getLevelConfigField($level, 'lev_folder_bonus');
						$folderBonuses = explode(":", $rsj);
						if($winCount==3)			{$rate = $folderBonuses[0];}
						else if($winCount==4)	{$rate = $folderBonuses[1];}
						else if($winCount==5)	{$rate = $folderBonuses[2];}
						else if($winCount==6)	{$rate = $folderBonuses[3];}
						else if($winCount==7)	{$rate = $folderBonuses[4];}
						else if($winCount==8)	{$rate = $folderBonuses[5];}
						else if($winCount==9)	{$rate = $folderBonuses[6];}
						else if($winCount==10){$rate = $folderBonuses[7];}
						$this->modifyMileageProcess($sn, $betMoney, "3", $bettingNo, $rate, $winCount);
					}
				}
				else
				{
					$sql = "insert into debug_detail_log(betting_no, total, win, lose, ing, cancel, flag) 
									values('".$bettingNo."',".$total.",".$winCount.",".$loseCount.",".$ingGameCount.",".$cancelCount.",1)";
					$this->db->exeSql($sql);
				}
			}
		}// end of for
		
		if($bettingNo !='') //배팅포인트지급 2016.02.10
			$this->giveBetPoint($bettingNo);

		return 1;
 	}
 	

	// 배팅포인트 지급 2016.02.10
	function giveBetPoint($bettingNo){
			//웹게임, 스포츠게임 포인트 지급
			$sql="select b.uid,a.web_betting_money   as wb_point,c.wb_rate,
					( a.betting_money - a.web_betting_money)   as sb_point,
					b.recommend_sn , c.parent_rec_sn, 
					a.web_betting_money  as parent_wb_point, c.parent_wb_rate, 
					 ( a.betting_money - a.web_betting_money)  as parent_sb_point ,c.parent_sb_rate ,
					 ifnull(( select sn from tb_member where uid=c.rec_id ),0) as rec_mem_sn,
					ifnull(( select sn from tb_member where uid=d.rec_id ),0) as parent_rec_mem_sn
					from tb_total_cart a , tb_member b , tb_recommend c 
					left outer join tb_recommend d on c.parent_rec_sn=d.idx
					where a.member_sn=b.sn and b.recommend_sn =c.idx
					and a.result in (1,2) /*당첨,낙첨*/
					and a.betting_no = '".$bettingNo."'";
			

			  $rs = $this->db->exeSql($sql);

			 

			 if(sizeof($rs)>0){
				 $uid = $rs[0]['uid'];
				$wb_point = $rs[0]['wb_point'];
				$wb_rate = $rs[0]['wb_rate'];
				$sb_point = $rs[0]['sb_point'];
				$sb_rate = $rs[0]['sb_rate'];
				$parent_wb_rate = $rs[0]['parent_wb_rate'];
				$parent_sb_rate = $rs[0]['parent_sb_rate'];
				$parent_wb_point = $rs[0]['parent_wb_point'];
				$parent_sb_point = $rs[0]['parent_sb_point'];
				$recommend_sn = $rs[0]['recommend_sn'];
				$parent_rec_sn = $rs[0]['parent_rec_sn'];
				$rec_mem_sn= $rs[0]['rec_mem_sn'];
				$parent_rec_mem_sn= $rs[0]['parent_rec_mem_sn'];
				//총판 아이디 있는경우 
				if(	$rec_mem_sn>0) {
					 
					if ($wb_point>0 and $wb_rate>0  )//웹게임 배팅포인트 지급  
						$this->modifyMileageProcess($rec_mem_sn, $wb_point, "14", "웹게임 배팅포인트[배팅번호:".$bettingNo."]|총판회원".$uid,$wb_rate);
					if ($sb_point>0 and $sb_rate>0  )//스포츠게임 배팅포인트 지급  
						$this->modifyMileageProcess($rec_mem_sn, $sb_point, "15", "스포츠게임 배팅포인트[배팅번호:".$bettingNo."]|총판회원".$uid,$sb_rate);
				}

				//하부총판 아이디 있는경우
				if(	$parent_rec_mem_sn>0) {
					if ($parent_wb_point>0 and $parent_wb_rate>0  )//웹게임 배팅포인트 지급  
						$this->modifyMileageProcess($parent_rec_mem_sn, $parent_wb_point, "14", "웹게임 배팅포인트[배팅번호:".$bettingNo."]|하부총판회원".$uid,$parent_wb_rate);
					if ($parent_sb_point>0 and $parent_sb_rate>0  )//스포츠게임 배팅포인트 지급  
						$this->modifyMileageProcess($parent_rec_mem_sn, $parent_sb_point, "15", "스포츠게임 배팅포인트[배팅번호:".$bettingNo."]|하부총판회원".$uid,$parent_sb_rate);
				}

			 }
		 	
	}

 	/*
 	//▶ 당첨된 회원의 목록
	function accountListProcess($childSn)
	{
		$sql = "select 	a.member_sn, a.betting_no, a.betting_money, a.betting_ip, b.result,
										e.uid, e.nick, a.bet_date,
										c.type, c.gameDate, c.gameHour, c.gameTime, c.home_team, c.away_team,
										d.home_rate, d.draw_rate, d.away_rate, d.win
							from ".$this->db_qz."total_cart a
											,".$this->db_qz."total_betting b
											,".$this->db_qz."child c
											,".$this->db_qz."subchild d
											,".$this->db_qz."member e
							where a.betting_no=b.betting_no 
										and b.sub_child_sn=d.sn 
										and c.sn=d.child_sn
										and a.member_sn=e.sn
										and c.sn=".$childSn;
						
		$rs = $this->db->exeSql($sql);
	
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo  = $rs[$i]['betting_no'];

			$sql = "select 	a.betting_no, a.member_sn, a.bet_money, a.result, a.preview_result, a.select_rate, a.select_no,
											c.type, concat(c.gameDate,' ',c.gameHour,':',c.gameTime) as game_date, e.name as league_name, 
											c.home_team, c.away_team, a.home_rate, a.draw_rate, a.away_rate, c.home_score, c.away_score, d.win
							from ".$this->db_qz."total_betting a
											, ".$this->db_qz."total_cart b
											, ".$this->db_qz."child c
											, ".$this->db_qz."subchild d
											, ".$this->db_qz."league e
							where a.betting_no=b.betting_no 
										and a.sub_child_sn=d.sn 
										and d.child_sn=c.sn 
										and c.league_sn=e.sn
										and a.betting_no='".$bettingNo."'";
			$rsi = $this->db->exeSql($sql);
			
			$winCount = $loseCount = $cancelCount = $ingGameCount = 0;
			$winRate	  = 1;
			$winMoney	  = 0;
			$total	= sizeof($rsi);
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$memberSn	= $rsi[$j]['member_sn'];
				$betMoney = $rsi[$j]['bet_money'];
				$result = $rsi[$j]['result'];
				
				//다른곳에 처리를 해버렸을 경우
				if($result==0)
					$result	= $rsi[$j]['preview_result'];
				
				if($result==0)
				{
					++$ingGameCount;
					break;
				}
					
				else if($result==1)
				{
					++$winCount;
					$winRate *= $rsi[$j]['select_rate'];
				}
				else if($result==2)
				{
					++$loseCount;
					break;
				}
				else if($result==4)
				{
					++$cancelCount;
					$winRate *= 1;
				}
			}
			
			if($loseCount > 0 || $ingGameCount > 0) //낙첨
			{
			}
			//모든게임종료
			else if($ingGameCount==0)
			{
				//모두 취소된 게임
				if(($winCount+$cancelCount) >= $total) //당첨
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
					
					$rs[$i]["result_rate"] 	= $winRate;
					$rs[$i]["result_money"] = $winMoney;
					$rs[$i]["bonus_rate"] 	= 0;
					
					//다폴더 계산
					if($winCount>2)
					{
						$rate = $this->levelConfigModel->getMemberFolderBounsRate($memberSn, $winCount);
						$rs[$i]["bonus_rate"] 	= $rate;
						$rs[$i]["bonus_money"] 	= ($rate*$winMoney/100);
					}
					
					$list[$bettingNo] = $rs[$i];
					$list[$bettingNo]["item"]=$rsi;
				}
			}
			else
			{
			}
		}// end of for
		
		return $list;
 	}
 	
 	*/
 	
 	//▶ 당첨된 회원의 목록
	function accountListProcess($childSn, $betData)
	{
		$sql = "select 	a.member_sn, a.betting_no, a.betting_money, a.betting_ip, b.result,
										e.uid, e.nick, a.bet_date,
										c.type, c.gameDate, c.gameHour, c.gameTime, c.home_team, c.away_team,
										d.home_rate, d.draw_rate, d.away_rate, d.win
							from ".$this->db_qz."total_cart a
											,".$this->db_qz."total_betting b
											,".$this->db_qz."child c
											,".$this->db_qz."subchild d
											,".$this->db_qz."member e
							where a.betting_no=b.betting_no 
										and b.sub_child_sn=d.sn 
										and c.sn=d.child_sn
										and a.member_sn=e.sn
										and c.sn=".$childSn;
						
		$rs = $this->db->exeSql($sql);
	
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo  = $rs[$i]['betting_no'];

			$sql = "select 	a.sn as betSn, a.betting_no, a.member_sn, a.bet_money, a.result, a.preview_result, a.select_rate, a.select_no,
											c.type, concat(c.gameDate,' ',c.gameHour,':',c.gameTime) as game_date, e.name as league_name, 
											c.home_team, c.away_team, a.home_rate, a.draw_rate, a.away_rate, c.home_score, c.away_score, d.win
							from ".$this->db_qz."total_betting a
											, ".$this->db_qz."total_cart b
											, ".$this->db_qz."child c
											, ".$this->db_qz."subchild d
											, ".$this->db_qz."league e
							where a.betting_no=b.betting_no 
										and a.sub_child_sn=d.sn 
										and d.child_sn=c.sn 
										and c.league_sn=e.sn
										and a.betting_no='".$bettingNo."'";

			$rsi = $this->db->exeSql($sql);
			
			$winCount = $loseCount = $cancelCount = $ingGameCount = 0;
			$winRate	  = 1;
			$winMoney	  = 0;
			$total	= sizeof($rsi);
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$memberSn	= $rsi[$j]['member_sn'];
				$betMoney = $rsi[$j]['bet_money'];
				$result = $rsi[$j]['result'];
				
				//다른곳에 처리를 해버렸을 경우
				if($result==0)
				{
					$betSn	= $rsi[$j]['betSn'];
					for($k=0; $k<sizeof($betData); ++$k)
					{
						if($betSn==$betData[$k]['bet_sn'])
						{
							$result = $betData[$k]['result'];
							
							$rsi[$j]['home_score']	= $betData[$k]['home_score'];
							$rsi[$j]['away_score']	= $betData[$k]['away_score'];
							$rsi[$j]['win'] 				= $betData[$k]['win'];
							$rsi[$j]['result'] 			= $betData[$k]['result'];
						}
					}
				}
				
				if($result==0)
				{
					++$ingGameCount;
					break;
				}
					
				else if($result==1)
				{
					++$winCount;
					$winRate *= $rsi[$j]['select_rate'];
				}
				else if($result==2)
				{
					++$loseCount;
					break;
				}
				else if($result==4)
				{
					++$cancelCount;
					$winRate *= 1;
				}
			}
			
			if($loseCount > 0 || $ingGameCount > 0) //낙첨
			{
			}
			//모든게임종료
			else if($ingGameCount==0)
			{
				//모두 취소된 게임
				if(($winCount+$cancelCount) >= $total) //당첨
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
					
					$rs[$i]["result_rate"] 	= $winRate;
					$rs[$i]["result_money"] = $winMoney;
					$rs[$i]["bonus_rate"] 	= 0;
					
					//다폴더 계산
					if($winCount>2)
					{
						$rate = $this->levelConfigModel->getMemberFolderBounsRate($memberSn, $winCount);
						$rs[$i]["bonus_rate"] 	= $rate;
						$rs[$i]["bonus_money"] 	= ($rate*$winMoney/100);
					}
					
					$list[$bettingNo] = $rs[$i];
					$list[$bettingNo]["item"]=$rsi;
				}
			}
			else
			{
			}
		}// end of for
		
		$dataArray = array();
		$dataArray["list"]		= $list;
		$dataArray["betData"]	= $betData;
		
		return $dataArray;
 	}
	
 	//▶ [수정] - 결과에 따른 정보 갱신
 	function resultGameProcess__($childSn, $homeScore, $awayScore, $winTeam/*Home, Draw, Away, Cancel*/, $gameCancel="")
 	{
		$rs = $this->gameModel->getSubChildRows($childSn, "sn,betting_type,draw_rate");
	 		
		//01. modify subchild
		for($i=0; $i<sizeof($rs); ++$i)
	 	{
	 		$subSn 		= $rs[$i]['sn'];
	 		$betType 	= $rs[$i]['betting_type'];
	 		$drawRate	= $rs[$i]['draw_rate'];
	 		
			if($betType==1) //승무패
			{
				if($winTeam=="Home")				{$winCode = 1;}
				else if($winTeam=="Away")		{$winCode = 2;}
				else if($winTeam=="Draw")		{$winCode = 3;}
				else if($winTeam=="Cancel")	{$winCode = 4;}
			}
			else if($betType==2) //핸디캡
		 	{
		 		if($winTeam=="Home")				{$winCode = 1;}
				else if($winTeam=="Away")		{$winCode = 2;}
				else if($winTeam=="Cancel")	{$winCode = 4;}
			}
			else if($betType==4) //언더오버
		 	{
		 		if($winTeam=="Cancel")	$winCode=4;
		 		else										$winCode = (($homeScore+$awayScore) > $drawRate) ? 1:2;
			}
			 	
			$sql = "update ".$this->db_qz."subchild 
							set win=".$winCode."
							where sn=".$subSn." 
										and betting_type=".$betType;
			$this->db->exeSql($sql);
			 	
			//02. modify total_betting
			$sql = "select sn, select_no, game_type, home_rate, draw_rate, away_rate
							from ".$this->db_qz."total_betting 
							where sub_child_sn=".$subSn." and kubun='Y' and event='0'";
			$rsi = $this->db->exeSql($sql);
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$betSn 		= $rsi[$j]["sn"];
				$select 	= $rsi[$j]["select_no"];
				$selectDrawRate	= $rsi[$j]["draw_rate"];

				//관리자에 의해 적특처리를 했을 경우 적특으로 처리를 해줘야 한다.
				if($rsi[$j]["home_rate"]=="1.00" && $rsi[$j]["draw_rate"]=="1.00" && $rsi[$j]["away_rate"]=="1.00") $gameCancel="Cancel";
				
				//강제취소의 경우 자기 자신의 배당을 하지 않고, 전체 적용한다.
				if($gameCancel=="Cancel")
				{
					$winCode=4;
				}
				else
				{
					if($betType==1) //승무패
					{
						if($homeScore==$awayScore)
						{
							if($drawRate=="1.00")		$winCode = 4;
							else										$winCode = 3;
						}
						else if($homeScore > $awayScore){$winCode = 1;}
						else														{$winCode = 2;}
					}
					//핸디캡, 언더오버의 기준점 변경이 일어날 경우를 대비해, 배팅당시의 기준점으로 처리한다.
					else if($betType==2) //핸디캡
				 	{
				 		if(($homeScore+$selectDrawRate) > $awayScore)
				 			$winCode = 1;
				 		else if(($homeScore+$selectDrawRate) < $awayScore)
				 			$winCode = 2;
				 		else if(($homeScore+$selectDrawRate) == $awayScore)
				 			$winCode = 4;
					}
					else if($betType==4) //언더오버
				 	{
				 		if(($homeScore+$awayScore)==$selectDrawRate)	
				 			$winCode = 4;
				 		else
				 			$winCode = (($homeScore+$awayScore) > $selectDrawRate) ? 1:2;
					}
				}
				if($winCode==4)							{$result=4;}
				else if($select==$winCode) 	{$result=1;}
				else				  							{$result=2;}
					
				$sql = "update ".$this->db_qz."total_betting 
								set result=".$result." 
								where sn=".$betSn;
	
				$this->db->exeSql($sql);
			 }
	 	}
	 		
 		//실제 돈 지급
 		//return $this->resultMoneyProcess($childSn);
 	}
 	
 	
 	//▶ [배당지급] - 결과에 따른 돈 지급
 	function accountMoneyProcess__($childSn)
 	{
 		$rs = $this->gameModel->getChildRow($childSn, '*');
 		
 		$homeScore = $rs['home_score'];
 		$awayScore = $rs['away_score'];
 		
 		if(is_null($homeScore)||is_null($awayScore))
 			return -1;
 			
 		//이미 처리된게임인지 확인
 		if($rs['kubun']==1)
 			return 1;
 			
 		$rs = $this->gameModel->getSubChildRow($childSn, "sn");
 		$subChildSn = $rs['sn'];
 		$sql = "update ".$this->db_qz."subchild set result=1
						where sn=".$subChildSn;
		$this->db->exeSql($sql);
 			
 		$sql = "update ".$this->db_qz."child 
 						set kubun=1 where sn=".$childSn;
 		$this->db->exeSql($sql);
 
		$sql = "select b.betting_no, b.member_sn
						from 	".$this->db_qz."subchild a, 
									".$this->db_qz."total_betting b,
									".$this->db_qz."child c,
									".$this->db_qz."total_cart d
						where a.sn=b.sub_child_sn
									and a.child_sn=c.sn 
									and b.betting_no=d.betting_no 
									and d.result=0 
									and a.child_sn=".$childSn;
		$rs = $this->db->exeSql($sql);
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo  = $rs[$i]['betting_no'];
			$sn		 = $rs[$i]['member_sn'];
			
			$sql = "select * 
							from ".$this->db_qz."total_betting 
							where betting_no='".$bettingNo."'";
			$rsi = $this->db->exeSql($sql);
			
			$winCount = $loseCount = $cancelCount = $ingGameCount = 0;
			$winRate	  = 1;
			$winMoney	  = 0;
			$total		 = sizeof($rsi);
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$betMoney = $rsi[$j]['bet_money'];
				
				if($rsi[$j]['result']==0)
					++$ingGameCount;
					
				else if($rsi[$j]['result']==1)
				{
					++$winCount;
					$winRate *= $rsi[$j]['select_rate'];
				}
				else if($rsi[$j]['result']==2)
				{
					++$loseCount;
					break;
				}
				else if($rsi[$j]['result']==4)
				{
					++$cancelCount;
					$winRate *= 1;
				}
			}
			
			if($loseCount>0) //낙첨
			{
				$sql = "update ".$this->db_qz."total_cart 
								set result=2, operdate=now() 
								where betting_no =".$bettingNo;
				$rsi = $this->db->exeSql($sql);
					
				//낙첨 마일리지
				$this->modifyMileageProcess($sn, $betMoney, "4", $bettingNo);
				
				//추천인 낙첨 마일리지
				$this->recommendFailedGameMileage($sn,$betMoney,$bettingNo);
			}
			
			//모든게임종료
			else if($ingGameCount==0)
			{
				$memberSn = $rs[$i]['member_sn'];
				$sql = "select logo from ".$this->db_qz."member where sn=".$memberSn;
				$rsi = $this->db->exeSql($sql);
					
				$logo = $rsi[0]['logo'];
				
				//모두 취소된 게임
				if($total==$cancelCount)
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart 
									set result=4, operdate=now(), result_money='".$winMoney."' 
									where logo='".$logo."' 
												and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
				}
				else if(($winCount+$cancelCount) >= $total) //당첨
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart
									set result=1, operdate=now(), result_money='".$winMoney."' 
									where logo='".$logo."' 
												and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
						
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
					
					//당첨문자메세지
					//$this->sendWinMsgCheck($memberSn);
					
					//다폴더 계산
					if($winCount>2)
					{
						$level = $this->memberModel->getMemberField($memberSn,'mem_lev');
						$rsj = $this->configModel->getLevelConfigField($level, 'lev_folder_bonus');
						$folderBonuses = explode(":", $rsj);
						if($winCount==3)		{$rate = $folderBonuses[0];}
						else if($winCount==4)	{$rate = $folderBonuses[1];}
						else if($winCount==5)	{$rate = $folderBonuses[2];}
						else if($winCount==6)	{$rate = $folderBonuses[3];}
						else if($winCount==7)	{$rate = $folderBonuses[4];}
						else if($winCount==8)	{$rate = $folderBonuses[5];}
						else if($winCount==9)	{$rate = $folderBonuses[6];}
						else if($winCount==10)	{$rate = $folderBonuses[7];}
						$this->modifyMileageProcess($sn, $betMoney, "3", $bettingNo, $rate, $winCount);
					}
				}
			}
			else
			{
			}
		}// end of for
		return 1;
 	}
 	
 	//▶ 당첨된 회원의 목록
	function resultWinListProcess($childSn)
	{
		//승리한 게임의 정보를 넣어준다.
 		$rs = $this->gameModel->getChildRow($childSn, '*');
 		$homeScore = $rs['home_score'];
 		$awayScore = $rs['away_score'];
 		
 		if(is_null($homeScore)||is_null($awayScore))
 			return -1;
 			
 		//이미 처리된게임인지 확인
 		$sql = "select kubun from ".$this->db_qz."child where sn=".$childSn;
 		$rs = $this->db->exeSql($sql);
 		
 		$list = array();
 		if($rs[0]['kubun']==1)
 			return $list;
 		
		$sql = "select 	a.member_sn, a.betting_no, a.betting_money, a.betting_ip, b.result,
										e.uid, e.nick, a.bet_date,
										c.type, c.gameDate, c.gameHour, c.gameTime, c.home_team, c.away_team,
										d.home_rate, d.draw_rate, d.away_rate, d.win
							from ".$this->db_qz."total_cart a
											,".$this->db_qz."total_betting b
											,".$this->db_qz."child c
											, ".$this->db_qz."subchild d
											, ".$this->db_qz."member e
							where a.betting_no=b.betting_no 
										and b.sub_child_sn=d.sn 
										and c.sn=d.child_sn
										and a.member_sn=e.sn
										and a.is_account=1
										and c.sn=".$childSn;
						
		$rs = $this->db->exeSql($sql);
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo  = $rs[$i]['betting_no'];

			$sql = "select 	a.betting_no, a.member_sn, a.bet_money, a.result, a.select_rate, a.select_no,
											c.type, concat(c.gameDate,' ',c.gameHour,':',c.gameTime) as game_date, e.name as league_name, 
											c.home_team, c.away_team, d.home_rate, d.draw_rate, d.away_rate, c.home_score, c.away_score, d.win
							from ".$this->db_qz."total_betting a
											, ".$this->db_qz."total_cart b
											, ".$this->db_qz."child c
											, ".$this->db_qz."subchild d
											, ".$this->db_qz."league e
							where a.betting_no=b.betting_no 
										and a.sub_child_sn=d.sn 
										and d.child_sn=c.sn 
										and c.league_sn=e.sn
										and a.betting_no='".$bettingNo."'";
			$rsi = $this->db->exeSql($sql);
			
			$winCount = $loseCount = $cancelCount = $ingGameCount = 0;
			$winRate	  = 1;
			$winMoney	  = 0;
			$total	= sizeof($rsi);
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$memberSn	= $rsi[$j]['member_sn'];
				$betMoney = $rsi[$j]['bet_money'];
				
				if($rsi[$j]['result']==0)
				{
					++$ingGameCount;
					break;
				}
					
				else if($rsi[$j]['result']==1)
				{
					++$winCount;
					$winRate *= $rsi[$j]['select_rate'];
				}
				else if($rsi[$j]['result']==2)
				{
					++$loseCount;
					break;
				}
				else if($rsi[$j]['result']==4)
				{
					++$cancelCount;
					$winRate *= 1;
				}
			}
			
			if($loseCount > 0 || $ingGameCount > 0) //낙첨
			{
				//break;
			}
			//모든게임종료
			else if($ingGameCount==0)
			{
				//모두 취소된 게임
				if(($winCount+$cancelCount) >= $total) //당첨
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
					
					$rs[$i]["result_rate"] 	= $winRate;
					$rs[$i]["result_money"] = $winMoney;
					$rs[$i]["bonus_rate"] 	= 0;
					//다폴더 계산
					if($winCount>2)
					{
						$rate = $this->levelConfigModel->getMemberFolderBounsRate($memberSn, $winCount);
						$rs[$i]["bonus_rate"] 	= $rate;
						$rs[$i]["bonus_money"] 	= ($rate*$winMoney/100);
					}
					
					$list[$bettingNo] = $rs[$i];
					$list[$bettingNo]["item"]=$rsi;
				}
			}
			else
			{
			}
		}// end of for
		
		return $list;
 	}
 	
 	//▶ [배당지급] - 결과에 따른 돈 지급
 	function resultMoneyProcess($childSn)
 	{
 		$rs = $this->gameModel->getChildRow($childSn, '*');
 		$homeScore = $rs['home_score'];
 		$awayScore = $rs['away_score'];
 		if(is_null($homeScore)||is_null($awayScore))
 			return -1;
 		
 		$sql = "update ".$this->db_qz."child set kubun=1 where sn=".$childSn;
 		$this->db->exeSql($sql);
 
		$sql = "select b.betting_no, b.member_sn
						from ".$this->db_qz."subchild a, ".$this->db_qz."total_betting b, ".$this->db_qz."child c, ".$this->db_qz."total_cart d
						where a.sn=b.sub_child_sn and a.child_sn=c.sn and b.betting_no=d.betting_no and d.result=0 and a.child_sn=".$childSn;
		$rs = $this->db->exeSql($sql);		
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo  = $rs[$i]['betting_no'];
			$sn		 = $rs[$i]['member_sn'];
			
			$sql = "select * from ".$this->db_qz."total_betting where betting_no=".$bettingNo;
			$rsi = $this->db->exeSql($sql);
			
			$winCount = $loseCount = $cancelCount = $ingGameCount = 0;
			$winRate	  = 1;
			$winMoney	  = 0;
			$total		 	= sizeof($rsi);
			
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$betMoney = $rsi[$j]['bet_money'];
				
				if($rsi[$j]['result']==0)		
					++$ingGameCount;
				else if($rsi[$j]['result']==1)
				{
					++$winCount;
					$winRate *= $rsi[$j]['select_rate'];
				}
				else if($rsi[$j]['result']==2)
				{
					++$loseCount;
					break;
				}
				else if($rsi[$j]['result']==4)
				{
					++$cancelCount;
					$winRate *= 1;
				}
			}
			
			if($loseCount>0) //낙첨
			{
				$sql = "update ".$this->db_qz."total_cart set result=2 ,operdate=now() where betting_no =".$bettingNo;
				$rsi = $this->db->exeSql($sql);
					
				//낙첨 마일리지
				$this->modifyMileageProcess($sn,$betMoney,"4",$bettingNo);
				
				//추천인 낙첨 마일리지
				$this->recommendFailedGameMileage($sn,$betMoney,$bettingNo);
			}
			
			//모든게임종료
			else if($ingGameCount==0)
			{
				$memberSn = $rs[$i]['member_sn'];
				$sql = "select logo from ".$this->db_qz."member where sn=".$memberSn;
				$rsi = $this->db->exeSql($sql);
					
				$logo = $rsi[0]['logo'];
				
				//모두 취소된 게임
				if($total==$cancelCount)
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart set result=4, operdate=now(), result_money='".$winMoney."' 
									where logo='".$logo."' and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
				}
				else if(($winCount+$cancelCount) >= $total) //당첨
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart set result=1, operdate=now(), result_money='".$winMoney."' 
									where logo='".$logo."' and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
						
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
					//다폴더 계산
					if($winCount>2)
					{
						$level = $this->memberModel->getMemberField($memberSn,'mem_lev');
						$rsj = $this->configModel->getLevelConfigField($level, 'lev_folder_bonus');
						$folderBonuses = explode(":", $rsj);
						if($winCount==3)		{$rate = $folderBonuses[0];}
						else if($winCount==4)	{$rate = $folderBonuses[1];}
						else if($winCount==5)	{$rate = $folderBonuses[2];}
						else if($winCount==6)	{$rate = $folderBonuses[3];}
						else if($winCount==7)	{$rate = $folderBonuses[4];}
						else if($winCount==8)	{$rate = $folderBonuses[5];}
						else if($winCount==9)	{$rate = $folderBonuses[6];}
						else if($winCount==10)	{$rate = $folderBonuses[7];}
						$this->modifyMileageProcess($sn, $betMoney, "3", $bettingNo, $rate, $winCount);
					}
				}
			}
			else
			{
				
			}
			
		}// end of for
		return 1;
 	}
 	
 	//▶ [rollback]
 	function rollbackGameProcess($childSn)
 	{
 		// 01. 설정변경
 		//rollback child
 		$sql = "update ".$this->db_qz."child set home_score=null, away_score=null, win_team=null, handi_winner=null, kubun=0
 						where sn=".$childSn;
 		$this->db->exeSql($sql);

 		$sql = "select * from ".$this->db_qz."subchild where child_sn=".$childSn;
 		$rs = $this->db->exeSql($sql);
 		$subSn = $rs[0]['sn'];
 		
 		//cancel game
 		//01. rollback subchild 
		$sql = "update ".$this->db_qz."subchild set win=null,result=null
						where sn=".$subSn;
		$this->db->exeSql($sql);
		
		//02. rollback total_betting
		$sql = "update ".$this->db_qz."total_betting set result=0 where sub_child_sn=".$subSn;
		$this->db->exeSql($sql);

		// 02. rollback money - 당첨금, 낙첨마일리지, 다폴더마일리지, 추천인 낙첨 마일리지
		$sql = "select b.betting_no, b.member_sn
						from ".$this->db_qz."subchild a, ".$this->db_qz."total_betting b, ".$this->db_qz."child c, ".$this->db_qz."total_cart d
						where a.sn=b.sub_child_sn and a.child_sn=c.sn and b.betting_no=d.betting_no and d.result!=0 and a.child_sn=".$childSn;
		$rs = $this->db->exeSql($sql);		
		
		for($i=0; $i<sizeof($rs); ++$i)
		{
			$bettingNo  = $rs[$i]['betting_no'];
			
			//rollback cart
			$sql = "update ".$this->db_qz."total_cart set result=0,operdate=now()
							where betting_no = '".$bettingNo."'" ;  
			$this->db->exeSql($sql);			
			
			$sql = "select * from ".$this->db_qz."money_log where state=4 and betting_no='".$bettingNo."'";
			$rsi = $this->db->exeSql($sql);		
	
			//당첨금
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$sn = $rsi[$j]['sn'];
				$memberSn = $rsi[$j]['member_sn'];
				$amount = $rsi[$j]['amount'];
					
				$sql = "update ".$this->db_qz."money_log set state=9 where sn=".$sn;
				$this->db->exeSql($sql);
				$this->modifyMoneyProcess($memberSn, -$amount, 8, $bettingNo);
			}
			
			//낙첨,다폴더 마일리지, 추천인 낙첨 마일리지
			$sql = "select * from ".$this->db_qz."mileage_log where (state=4 or state=3 or state=12) and betting_no='".$bettingNo."'";
			$rsi = $this->db->exeSql($sql);		
	
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$sn = $rsi[$j]['sn'];
				$memberSn = $rsi[$j]['member_sn'];
				$amount = $rsi[$j]['amount'];
				
				$sql = "update ".$this->db_qz."mileage_log set state=9 where sn=".$sn;
				$this->db->exeSql($sql);
				$this->modifyMileageProcess($memberSn, -$amount, "8", "경기재입력[배팅번호:".$bettingNo."]");
			}
		}
 	}
	
	/**
	*--------------------------------------------------------------------
 	*
 	* member process
 	*
 	*--------------------------------------------------------------------
 	*/
 	
 	function modifyVirtualMoneyProcess($sn, $amount, $status, $statusMessage, $memo='')
	{
		$before = $this->memberModel->getMemberField($sn, "virtual_money");
		
		$add = "virtual_money = virtual_money +(".$amount.")";
		$sql = "update ".$this->db_qz."member set ".$add." where sn=".$sn;
		$this->db->exeSql($sql);
		
		$after 	= $this->memberModel->getMemberField($sn, "virtual_money");
		
		//$this->moneyModel->addMoneyLog($sn, $amount, $before, $after, $status, $statusMessage, $memo);
	}
	
	//▶ 회원 돈 정보 갱신
	function modifyMoneyProcess($sn, $amount, $status, $statusMessage, $memo='')
	{
		$before = $this->memberModel->getMemberField($sn, "g_money");
		
		$add = "g_money = g_money +(".$amount.")";
		$sql = "update ".$this->db_qz."member set ".$add." where sn=".$sn;
		$this->db->exeSql($sql);
		
		$after 	= $this->memberModel->getMemberField($sn, "g_money");
		
		$this->moneyModel->addMoneyLog($sn, $amount, $before, $after, $status, $statusMessage, $memo);
	}
	
	//▶ 마일리지
	function modifyMileageProcess($sn, $amount, $status, $statusMessage, $rate=0, $winCount='', $memo='')
	{
		$level = $this->memberModel->getMemberField($sn, 'mem_lev');

		
		$bettingNo='0';
		if($rate==0)
		{
			//1=충전, 2=추천인, 3=다폴더,4=낙첨,5=이벤트,7=관리자수정
			switch($status)
			{
				case '1': 
				{
					$logo = $this->memberModel->getMemberField($sn, 'logo');
					$rate = $this->configModel->first_charge_rate($logo, $level);
					$max =  (int)$this->configModel->max_charge_rate($logo, $level);
				} break;

				case '400': 
				{
					$logo = $this->memberModel->getMemberField($sn, 'logo');
					$rate = $this->configModel->every_charge_rate($logo, $level);
					$max =  (int)$this->configModel->max_charge_rate($logo, $level);
				} break;

				case '2': break;
				case '3': break;
				case '4': 
				{
					$rate = $this->configModel->getLevelConfigField($level, 'lev_bet_failed_mileage_rate');
					$bettingNo = $statusMessage;
					$statusMessage = "낙첨|게임번호[".$bettingNo."]";
				} break;
				case '5':
				case '6':
				case '7': 
				case '8': $rate = 100; break;
				default : $rate = 0;
			}
		}
		//다폴더
		if($status=='3')
		{
			$bettingNo = $statusMessage;
			$statusMessage=sprintf("%d게임|다폴더[배팅번호:%s]", $winCount, $bettingNo);
		}
		//추천인 낙첨 마일리지
		if($status=='12')
		{
			$items = split(",", $statusMessage);
			$bettingNo = $items[0];
			$statusMessage = "추천인 낙첨마일리지|게임번호[".$bettingNo."]|".$items[1];
		}
		
		if($rate<=0) return 0;
		
		$amount  = (int)($amount*$rate/100);

		$accrued = (int)$this->memberModel->getMemberField($sn, "accruedPoint");
        
        // 충전시 보너스 포인트가 10만점을 넘지 않도록 조정
        if($status == '1' && $amount >= $max )
        {
        	$amount = $max;
        }

        // 매충시 보너스 포인트가 10만점을 넘지 않도록 조정
        if($status == '400' && ($accrued + $amount) >= $max )
		{

			$amount = $max - $accrued;
		}
		
		$before = $this->memberModel->getMemberField($sn, "point");
		
		if($status=='1' || $status=='400')
		{
			$add = "point = point +(".$amount."), accruedPoint = accruedPoint +(".$amount.")";
			$sql = "update ".$this->db_qz."member set ".$add." where sn = ".$sn;
		}

		else
		{
			$add = "point = point +(".$amount.")";
			$sql = "update ".$this->db_qz."member set ".$add." where sn = ".$sn;
		}

		$this->db->exeSql($sql);
		
		$after 	= $this->memberModel->getMemberField($sn, "point");
		
		$sql = "insert into ".$this->db_qz."mileage_log(member_sn, amount, before_mileage, after_mileage, regdate, state, status_message, rate, betting_no, log_memo) 
						values (".$sn.",".$amount.",".$before.",".$after.",now(),".$status.",'".$statusMessage."',".$rate.",'".$bettingNo."','".$memo."')";
		
		/*				
		$sql = "insert into ".$this->db_qz."mileage_log(member_sn,amount,before_mileage,after_mileage,regdate,state,status_message,rate,betting_no) 
					values (".$sn.",".$amount.",".$before.",".$after.",now(),".$status.",'".$statusMessage."',".$rate.",'".$bettingNo."')";
		*/
				
		return $this->db->exeSql($sql);
	}
	
	/**
	*--------------------------------------------------------------------
 	*
 	* exchange process
 	*
 	*--------------------------------------------------------------------
 	*/
	
	//▶ 환전요청
	//▶ 환전 요청의 경우 사용자의 금액을 빼줘야 한다. 더이상 게임 진행 불가.
	function exchangeReqProcess($memberSn, $amount,$passwd='')
	{
		if($amount<=0) return -2;
		
		$rs = $this->memberModel->getMemberRow($memberSn,"g_money,bank_name,bank_account,bank_member,exchange_pass");
		if($rs['g_money'] < $amount) return -3;
		
		if($passwd!='' && $passwd!=$rs['exchange_pass']) {return -1;}
		
		$this->modifyMoneyProcess($memberSn, -$amount, '2','환전요청');
		
		$sql = "insert into ".$this->db_qz."exchange_log(member_sn,amount,bank,bank_account,bank_owner, regdate, logo)
				values(".$memberSn.",".$amount.",'".$rs['bank_name']."','".$rs['bank_account']."','".$rs['bank_member']."', now(),'".$this->logo."')";
				
		return $this->db->exeSql($sql);
	}
	
	//▶ 환전요청 상태로 이동 
	function exchange_rollbackReqProcess($sn)
	{
		$sql = "update ".$this->db_qz."exchange_log set agree_amount=0, before_money=0, after_money=0, operdate=null, state=0
				where sn=".$sn;
				
		return $this->db->exeSql($sql);
	}
	
	//▶ 환전취소
	function exchangeCancelProcess($sn)
	{
		$rs = $this->moneyModel->getExchangeState($sn);
		$state = $rs['state'];
		
		if($state=='' || $state==3) return -2;
		
		$rs = $this->moneyModel->getExchangeRow($sn);
		if($rs['sn']=='') 	return -1;
		
		$memberSn  	 = $rs['member_sn'];
		$amount		 = $rs['amount'];
		$agreeAmount = $rs['agree_amount'];
		
		if($state==1 || $state==3) { $amount=$agreeAmount;}
		
		$this->modifyMoneyProcess($memberSn, $amount, '2','환전취소');
		
		$sql = "delete from ".$this->db_qz."exchange_log
				where sn=".$sn;
			
		$rs = $this->db->exeSql($sql);
		
		return ($rs>0)? 1:0;
	}
	
	//▶ 환전
	function exchangeProcess($sn)
	{	
		$rs = $this->moneyModel->getExchangeState($sn);
		$state = $rs['state'];
		
		if($state!=0) return -1;
		
		$rs = $this->moneyModel->getExchangeRow($sn);
		if($rs['sn']=='') 	return -1;
		
		$memberSn  	 = $rs['member_sn'];
		$amount		 = $rs['amount'];
	
		//환전신청시 이미 금액은 가감한다.
		$after = $before = $this->memberModel->getMemberField($memberSn, "g_money");
	
		$sql = "update ".$this->db_qz."exchange_log set agree_amount=".$amount.", before_money=".$before.", after_money=".$after.", operdate=now(), state=3
						where sn=".$sn;
				
		$rs = $this->db->exeSql($sql);
		
		//환전 처리와 승인을 동시에 하는걸로 바뀜. 이전대로 하려면 이 주석을 풀면 됨
		//return ($rs>0)? 1:0;
		return $this->exchangeConfirmProcess($sn);
	}
	
	//▶ 환전 최종승인
	function exchangeConfirmProcess($sn)
	{	
		$rs = $this->moneyModel->getExchangeRow($sn);
		$state = $rs['state'];
		
		if($state!=3) return -1;
		
		$sql = "update ".$this->db_qz."exchange_log set operdate=now(), state=1 where sn=".$sn;
		
		//추천인 마일리지
		//$this->recommendMileage($rs['member_sn'], -$rs['agree_amount']);
				
		$rs = $this->db->exeSql($sql);
		
		return ($rs>0)? 1:0;
	}
	
	
	/**
	*--------------------------------------------------------------------
 	*
 	* charge process
 	*
 	*--------------------------------------------------------------------
 	*/
	//▶ 충전요청 삭제
	function delchargeReq($sn)
	{
		$sql = "delete from ".$this->db_qz."charge_log
					where sn = ".$sn;
					
		return $this->db->exeSql($sql);
	}
	
	//▶ 충전요청
	function chargeReqProcess($member_sn, $amount)
	{
		//미처리 요청이 있을 경우, 에러를 리턴한다.
		$sql = "select count(*) as cnt from ".$this->db_qz."charge_log where state=0 and member_sn=".$member_sn;
		$rows = $this->db->exeSql($sql);
		if($rows[0]['cnt'] > 0)
		{
			return -1;
		}
		
		$charge_rows = $this->getRows("*", $this->db_qz."charge_log", "member_sn=".$member_sn);

		$rows = $this->memberModel->getMemberRow($member_sn, "bank_name,bank_account,bank_member");
		
		if(sizeof($charge_rows)==0)
		{
			$sql = "insert into ".$this->db_qz."charge_log(member_sn,amount,bank,bank_account,bank_owner, regdate, logo, bonus)
							values(".$member_sn.",".$amount.",'".$rows['bank_name']."','".$rows['bank_account']."','".$rows['bank_member']."', now(),'".$this->logo."', 1)";
		}
		else
		{
			$sql = "insert into ".$this->db_qz."charge_log(member_sn,amount,bank,bank_account,bank_owner, regdate, logo)
							values(".$member_sn.",".$amount.",'".$rows['bank_name']."','".$rows['bank_account']."','".$rows['bank_member']."', now(),'".$this->logo."')";
		}
				
		return $this->db->exeSql($sql);
	}
	
	//▶ 충전
	function chargeProcess($sn, $memberSn, $amount, $bonus=0)
	{
		$sql = "select count(*) from ".$this->db_qz."charge_log where sn=".$sn;
		$rs = $this->db->exeSql($sql);
		
		if(sizeof($rs)<=0) 
			return -1;
		
		
		
		$sql = "select count(*) as cnt from ".$this->db_qz."exchange_log where date(now())=date(operdate) and state=1 and member_sn=".$memberSn;
		$rsi = $this->db->exeSql($sql);

		//환전 내역이 있는지?
		if($rsi[0]['cnt'] <= 0)
		{
			$sql = "select count(*) as cnt from ".$this->db_qz."charge_log where date(now())=date(operdate) and state=1 and member_sn=".$memberSn;
			$rs = $this->db->exeSql($sql);

			//금일 첫 충전 마일리지
			if($rs[0]['cnt'] <= 0)
			{
				$this->modifyMileageProcess($memberSn, $amount, '1', '첫충전');
			}

			else
			{
				$sql = "select g_money from ".$this->db_qz."member where sn=".$memberSn;
				$rsx = $this->db->exeSql($sql);

				if((int)$rsx[0]['g_money'] <= 5000)
				{
					$this->modifyMileageProcess($memberSn, $amount, '400', '매충전');
				}
			}
		}

		else
		{
			$sql = "select count(*) as cnt from ".$this->db_qz."charge_log where date(now())=date(operdate) and state=1 and member_sn=".$memberSn;
			$rs = $this->db->exeSql($sql);

			//금일 첫 충전 마일리지
			if($rs[0]['cnt'] <= 0)
			{
				$this->modifyMileageProcess($memberSn, $amount, '1', '환전 후 첫충전');
			}
		}
		
		
		//추천인 마일리지
		//$this->recommendMileage($memberSn, $amount);
	
		$before = $this->memberModel->getMemberField($memberSn, "g_money");
		$this->modifyMoneyProcess($memberSn, $amount, '1',"충전");
		$after 	= $this->memberModel->getMemberField($memberSn, "g_money");
		
		//관리자 보너스 지급
		if($bonus>0)
		{
			$this->modifyMoneyProcess($memberSn, $bonus, '7',"충전 관리자 보너스");
		}
		
		//충전 마일리지 적립
		$sql = "select count(*) as cnt from ".$this->db_qz."charge_log where date(regdate)=date(now()) and state=1";
		$rs = $this->db->exeSql($sql);
		
		$sql = "update ".$this->db_qz."charge_log set agree_amount=".$amount.", before_money=".$before.", after_money=".$after.", bonus=".$bonus.",operdate=now(), state=1
				where sn=".$sn;
				
		$rs = $this->db->exeSql($sql);
		
		return ($rs>0)? 1:0;
	}
	
	//▶ 추천인 마일리지
	function recommendMileage($sn, $amount)
	{
		$uid = $this->memberModel->getMemberField($sn,'uid');
		
		//1차 추천인 마일리지
		
		$rs = $this->partnerModel->joinRecommendRate($sn, 1);
		$recommendSn = $rs['recommend_sn'];
		$rate = $rs['rate'];
		
		if($rate > 0)
		{
			$this->modifyMileageProcess($recommendSn, $amount, "2", $uid."|추천인|", $rate);
			
			/*
			//2차 추천인 검색
			$rs = $this->partnerModel->joinRecommendRate($sn, 2);
			$recommendSn = $rs['recommend_sn'];
			$rate = $rs['rate'];
			if($rate > 0)
			{
				$this->modifyMileageProcess($recommendSn, $amount, "2", $uid."|2차추천인|", $rate);
				
				//3차 추천인 검색
				$rs = $this->partnerModel->joinRecommendRate($sn, 3);
				$recommendSn = $rs['recommend_sn'];
				$rate = $rs['rate'];
				$this->modifyMileageProcess($recommendSn, $amount, "2", $uid."|3차추천인|", $rate);
			}
			*/
		}
	}
	
	//▶ 추천인 낙첨 마일리지
	function recommendFailedGameMileage($sn, $amount, $bettingNo)
	{
		$uid = $this->memberModel->getMemberField($sn,'uid');
	
		$rs = $this->partnerModel->joinRecommendRate($sn, 1);
		$recommendSn = $rs['recommend_sn'];
		$recommend_status = $rs['recommend_status'];
		$rate = $rs['rate'];
		
		$status_message = $bettingNo.",".$uid;
		
		if($rate > 0 && $recommend_status != "S")
		{
			$this->modifyMileageProcess($recommendSn, $amount, "12", $status_message, $rate);
		}
	}
	
	//▶ 충전취소
	function chargeCancelProcess($sn)
	{
		$rs = $this->moneyModel->getChargeState($sn);
		if($rs!=1) return -1;
	
		$rs = $this->moneyModel->getChargeRow($sn);
		if($rs['sn']=='') return -1;
		if($rs['state']!=1) return -2;
		
		$memberSn 	= $rs['member_sn'];
		$amount		= $rs['agree_amount'];
	
		$before = $this->memberModel->getMemberField($memberSn, "g_money");
		$this->modifyMoneyProcess($memberSn, -$amount, '1',"충전취소");
		$after 	= $this->memberModel->getMemberField($memberSn, "g_money");
	
		$sql = "update ".$this->db_qz."charge_log 
				set agree_amount=0, before_money=".$after.", after_money=0, operdate=null, state=0
					where sn=".$sn;
				
		$rs = $this->db->exeSql($sql);
		
		return ($rs>0)? 1:0;
	}
}
?>