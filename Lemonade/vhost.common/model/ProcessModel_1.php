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
 		$this->modifyMoneyProcess($sn, -$amount, 3, '����');
 	}
 
	//�� ���
	function bettingCancelProcess($bettingNo)
	{
		//�̹� ����ߴ��� ���� �Ǵ�
		$sql = "select count(betting_no) as cnt from ".$this->db_qz."money_log
						where state=5 and betting_no='".$bettingNo."'";
		$rs = $this->db->exeSql($sql);
		if($rs[0]['cnt']>0)
			return;
						
		$sql = "select member_sn, parent_sn, betting_money, result_money, bonus, result 
						from ".$this->db_qz."total_cart 
						where logo='".$this->logo."' and betting_no='".$bettingNo."'";
		$rs = $this->db->exeSql($sql);
		
		$sn					= $rs[0]["member_sn"];
		$parentIdx	= $rs[0]["parent_sn"];
		$betMoney		= $rs[0]["betting_money"];
		$result			= $rs[0]["result"];
		
		//�����, ���ý���, ���ü���
		/*
		if(0==$result||2==$result||1==$result)
		{
			$sql = "select b.child_sn from ".$this->db_qz."total_betting a, ".$this->db_qz."subchild b
							where a.sub_child_sn=b.sn and a.event='0' and a.betting_no='".$bettingNo."' and a.member_sn =" .$sn;
			$rs = $this->db->exeSql($sql);
			
			for($i=0; $i<sizeof($rs); ++$i)
				$childSn.="'".$rs[$i]['child_sn']."',";
			
			$childSn = substr($childSn,0,strlen($childSn)-1);
			
			$this->modifyMoneyProcess($sn, $betMoney, 5, $bettingNo);
		}
		$this->cartModel->delCart($bettingNo);
		*/
		if(0==$result)
		{
			$this->modifyMoneyProcess($sn, $betMoney, 5, $bettingNo);
			$this->cartModel->delCart($bettingNo);
		}
	}
	
	/**
	*--------------------------------------------------------------------
 	*
 	* game process
 	*
 	*--------------------------------------------------------------------
 	*/
 	//�� ���
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
 	
 	//�� [����] - ����� ���� ���� ����
 	function resultGameProcess($childSn, $homeScore, $awayScore, $winTeam/*Home, Draw, Away, Cancel*/, $gameCancel="")
 	{
		$rs = $this->gameModel->getSubChildRows($childSn, "sn,betting_type,draw_rate");
	 		
		//01. modify subchild
		for($i=0; $i<sizeof($rs); ++$i)
	 	{
	 		$subSn 		= $rs[$i]['sn'];
	 		$betType 	= $rs[$i]['betting_type'];
	 		$drawRate	= $rs[$i]['draw_rate'];
	 		
			if($betType==1) //�¹���
			{
				if($winTeam=="Home")				{$winCode = 1;}
				else if($winTeam=="Away")		{$winCode = 2;}
				else if($winTeam=="Draw")		{$winCode = 3;}
				else if($winTeam=="Cancel")	{$winCode = 4;}
			}
			else if($betType==2) //�ڵ�ĸ
		 	{
		 		if($winTeam=="Home")				{$winCode = 1;}
				else if($winTeam=="Away")		{$winCode = 2;}
				else if($winTeam=="Cancel")	{$winCode = 4;}
			}
			else if($betType==4) //�������
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

				//�����ڿ� ���� ��Ưó���� ���� ��� ��Ư���� ó���� ����� �Ѵ�.
				if($rsi[$j]["home_rate"]=="1.00" && $rsi[$j]["draw_rate"]=="1.00" && $rsi[$j]["away_rate"]=="1.00") $gameCancel="Cancel";
				
				//��������� ��� �ڱ� �ڽ��� ����� ���� �ʰ�, ��ü �����Ѵ�.
				if($gameCancel=="Cancel")
				{
					$winCode=4;
				}
				else
				{
					if($betType==1) //�¹���
					{
						if($homeScore==$awayScore)
						{
							if($drawRate=="1.00")		$winCode = 4;
							else										$winCode = 3;
						}
						else if($homeScore > $awayScore){$winCode = 1;}
						else														{$winCode = 2;}
					}
					/*
					if($betType==1) //�¹���
					{
						if($homeScore > $awayScore)
					 		$winCode = 1;
					 	else if($homeScore < $awayScore)
					 		$winCode = 2;
					 	else if($homeScore == $awayScore)
					 		$winCode = 3;
					}
					*/
					//�ڵ�ĸ, ��������� ������ ������ �Ͼ ��츦 �����, ���ô���� ���������� ó���Ѵ�.
					else if($betType==2) //�ڵ�ĸ
				 	{
				 		if(($homeScore+$selectDrawRate) > $awayScore)
				 			$winCode = 1;
				 		else if(($homeScore+$selectDrawRate) < $awayScore)
				 			$winCode = 2;
				 		else if(($homeScore+$selectDrawRate) == $awayScore)
				 			$winCode = 4;
					}
					else if($betType==4) //�������
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
	 		
 		//���� �� ����
 		//return $this->resultMoneyProcess($childSn);
 	}
 	
 	
 	//�� [�������] - ����� ���� �� ����
 	function accountMoneyProcess($childSn)
 	{
 		$rs = $this->gameModel->getChildRow($childSn, '*');
 		
 		$homeScore = $rs['home_score'];
 		$awayScore = $rs['away_score'];
 		
 		if(is_null($homeScore)||is_null($awayScore))
 			return -1;
 			
 		//�̹� ó���Ȱ������� Ȯ��
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
			
			if($loseCount>0) //��÷
			{
				$sql = "update ".$this->db_qz."total_cart 
								set result=2, operdate=now() 
								where betting_no =".$bettingNo;
				$rsi = $this->db->exeSql($sql);
					
				//��÷ ���ϸ���
				$this->modifyMileageProcess($sn, $betMoney, "4", $bettingNo);
				
				//��õ�� ��÷ ���ϸ���
				$this->recommendFailedGameMileage($sn,$betMoney,$bettingNo);
			}
			
			//����������
			else if($ingGameCount==0)
			{
				$memberSn = $rs[$i]['member_sn'];
				$sql = "select logo from ".$this->db_qz."member where sn=".$memberSn;
				$rsi = $this->db->exeSql($sql);
					
				$logo = $rsi[0]['logo'];
				
				//��� ��ҵ� ����
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
				else if(($winCount+$cancelCount) >= $total) //��÷
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart
									set result=1, operdate=now(), result_money='".$winMoney."' 
									where logo='".$logo."' 
												and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
						
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
					
					//��÷���ڸ޼���
					//$this->sendWinMsgCheck($memberSn);
					
					//������ ���
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
 	
 	//�� ��÷�� ȸ���� ���
	function resultWinListProcess($childSn)
	{
		//�¸��� ������ ������ �־��ش�.
 		$rs = $this->gameModel->getChildRow($childSn, '*');
 		$homeScore = $rs['home_score'];
 		$awayScore = $rs['away_score'];
 		
 		if(is_null($homeScore)||is_null($awayScore))
 			return -1;
 			
 		//�̹� ó���Ȱ������� Ȯ��
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
			
			if($loseCount > 0 || $ingGameCount > 0) //��÷
			{
				//break;
			}
			//����������
			else if($ingGameCount==0)
			{
				//��� ��ҵ� ����
				if(($winCount+$cancelCount) >= $total) //��÷
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
					
					$rs[$i]["result_rate"] 	= $winRate;
					$rs[$i]["result_money"] = $winMoney;
					$rs[$i]["bonus_rate"] 	= 0;
					//������ ���
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
 	
 	//�� [�������] - ����� ���� �� ����
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
			
			if($loseCount>0) //��÷
			{
				$sql = "update ".$this->db_qz."total_cart set result=2 ,operdate=now() where betting_no =".$bettingNo;
				$rsi = $this->db->exeSql($sql);
					
				//��÷ ���ϸ���
				$this->modifyMileageProcess($sn,$betMoney,"4",$bettingNo);
				
				//��õ�� ��÷ ���ϸ���
				$this->recommendFailedGameMileage($sn,$betMoney,$bettingNo);
			}
			
			//����������
			else if($ingGameCount==0)
			{
				$memberSn = $rs[$i]['member_sn'];
				$sql = "select logo from ".$this->db_qz."member where sn=".$memberSn;
				$rsi = $this->db->exeSql($sql);
					
				$logo = $rsi[0]['logo'];
				
				//��� ��ҵ� ����
				if($total==$cancelCount)
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart set result=4, operdate=now(), result_money='".$winMoney."' 
									where logo='".$logo."' and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
				}
				else if(($winCount+$cancelCount) >= $total) //��÷
				{
					$winRate  = bcmul($winRate,1,2);
					$winMoney = bcmul($betMoney,$winRate,0);
						
					$sql = "update ".$this->db_qz."total_cart set result=1, operdate=now(), result_money='".$winMoney."' 
									where logo='".$logo."' and betting_no = '".$bettingNo."'";
					$rsi = $this->db->exeSql($sql);
						
					$this->modifyMoneyProcess($memberSn, $winMoney, 4, $bettingNo);
					//������ ���
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
 	
 	//�� [rollback]
 	function rollbackGameProcess($childSn)
 	{
 		// 01. ��������
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
/*			
 		if($rs[0]['win']==4)
 		{
 			//01. rollback subchild 
 			$sql = "update ".$this->db_qz."subchild set win=null,result=null
							where sn=".$subSn;
			$this->db->exeSql($sql);
			
			//02. rollback total_betting
 			for($j=0; $j<sizeof($rsi); ++$j)
 			{
 				$sn = $rsi[$j]['betting_sn'];
 				$sql = "update ".$this->db_qz."total_betting set result=0
	 							where sub_child_sn=".$subSn;
 			}
	 		$this->db->exeSql($sql);
 		}
 		else
 		{
 			//01. rollback subchild 
 			$sql = "update ".$this->db_qz."subchild set win=null,result=null
							where sn=".$subSn;
							
			$this->db->exeSql($sql);
			
			//02. rollback total_betting
			$sql = "update ".$this->db_qz."total_betting set result=0 where sub_child_sn=".$subSn;
			
			$this->db->exeSql($sql);
		}
*/
		
		// 02. rollback money - ��÷��, ��÷���ϸ���, ���������ϸ���, ��õ�� ��÷ ���ϸ���
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
	
			//��÷��
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$sn = $rsi[$j]['sn'];
				$memberSn = $rsi[$j]['member_sn'];
				$amount = $rsi[$j]['amount'];
					
				$sql = "update ".$this->db_qz."money_log set state=9 where sn=".$sn;
				$this->db->exeSql($sql);
				$this->modifyMoneyProcess($memberSn, -$amount, 8, $bettingNo);
			}
			
			//��÷,������ ���ϸ���, ��õ�� ��÷ ���ϸ���
			$sql = "select * from ".$this->db_qz."mileage_log where (state=4 or state=3 or state=12) and betting_no='".$bettingNo."'";
			$rsi = $this->db->exeSql($sql);		
	
			for($j=0; $j<sizeof($rsi); ++$j)
			{
				$sn = $rsi[$j]['sn'];
				$memberSn = $rsi[$j]['member_sn'];
				$amount = $rsi[$j]['amount'];
				
				$sql = "update ".$this->db_qz."mileage_log set state=9 where sn=".$sn;
				$this->db->exeSql($sql);
				$this->modifyMileageProcess($memberSn, -$amount, "8", "������Է�[���ù�ȣ:".$bettingNo."]");
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
	
	//�� ȸ�� �� ���� ����
	function modifyMoneyProcess($sn, $amount, $status, $statusMessage, $memo='')
	{
		$before = $this->memberModel->getMemberField($sn, "g_money");
		
		$add = "g_money = g_money +(".$amount.")";
		$sql = "update ".$this->db_qz."member set ".$add." where sn=".$sn;
		$this->db->exeSql($sql);
		
		$after 	= $this->memberModel->getMemberField($sn, "g_money");
		
		$this->moneyModel->addMoneyLog($sn, $amount, $before, $after, $status, $statusMessage, $memo);
	}
	
	//�� ���ϸ���
	function modifyMileageProcess($sn, $amount, $status, $statusMessage, $rate=0, $winCount='', $memo='')
	{
		$level 	= $this->memberModel->getMemberField($sn, 'mem_lev');

		$bettingNo='0';
		if($rate==0)
		{
			//1=����, 2=��õ��, 3=������,4=��÷,5=�̺�Ʈ,7=�����ڼ���
			switch($status)
			{
				case '1': $rate = $this->configModel->getLevelConfigField($level, 'lev_charge_mileage_rate'); break;
				case '2': break;
				case '3': break;
				case '4': 
				{
					$rate = $this->configModel->getLevelConfigField($level, 'lev_bet_failed_mileage_rate');
					$bettingNo = $statusMessage;
					$statusMessage = "��÷|���ӹ�ȣ[".$bettingNo."]";
				} break;
				case '5':
				case '6':
				case '7': 
				case '8': $rate = 100; break;
				default : $rate = 0;
			}
		}
		//������
		if($status=='3')
		{
			$bettingNo = $statusMessage;
			$statusMessage=sprintf("%d����|������[���ù�ȣ:%s]", $winCount, $bettingNo);
		}
		//��õ�� ��÷ ���ϸ���
		if($status=='12')
		{
			$bettingNo = $statusMessage;
			$statusMessage = "��õ�� ��÷���ϸ���|���ӹ�ȣ[".$bettingNo."]";
		}
		
		if($rate<=0) return 0;
		
		$amount = (int)($amount*$rate/100);
		
		$before = $this->memberModel->getMemberField($sn, "point");
		
		$add = "point = point +(".$amount.")";
		$sql = "update ".$this->db_qz."member set ".$add." where sn = ".$sn;
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
	
	//�� ȯ����û
	//�� ȯ�� ��û�� ��� ������� �ݾ��� ����� �Ѵ�. ���̻� ���� ���� �Ұ�.
	function exchangeReqProcess($memberSn, $amount,$passwd='')
	{
		if($amount<=0) return -2;
		
		$rs = $this->memberModel->getMemberRow($memberSn,"g_money,bank_name,bank_account,bank_member,exchange_pass");
		if($rs['g_money'] < $amount) return -3;
		
		if($passwd!='' && $passwd!=$rs['exchange_pass']) {return -1;}
		
		$this->modifyMoneyProcess($memberSn, -$amount, '2','ȯ����û');
		
		$sql = "insert into ".$this->db_qz."exchange_log(member_sn,amount,bank,bank_account,bank_owner, regdate, logo)
				values(".$memberSn.",".$amount.",'".$rs['bank_name']."','".$rs['bank_account']."','".$rs['bank_member']."', now(),'".$this->logo."')";
				
		return $this->db->exeSql($sql);
	}
	
	//�� ȯ����û ���·� �̵� 
	function exchange_rollbackReqProcess($sn)
	{
		$sql = "update ".$this->db_qz."exchange_log set agree_amount=0, before_money=0, after_money=0, operdate=null, state=0
				where sn=".$sn;
				
		return $this->db->exeSql($sql);
	}
	
	//�� ȯ�����
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
		
		$this->modifyMoneyProcess($memberSn, $amount, '2','ȯ�����');
		
		$sql = "delete from ".$this->db_qz."exchange_log
				where sn=".$sn;
			
		$rs = $this->db->exeSql($sql);
		
		return ($rs>0)? 1:0;
	}
	
	//�� ȯ��
	function exchangeProcess($sn)
	{	
		$rs = $this->moneyModel->getExchangeState($sn);
		$state = $rs['state'];
		
		if($state!=0) return -1;
		
		$rs = $this->moneyModel->getExchangeRow($sn);
		if($rs['sn']=='') 	return -1;
		
		$memberSn  	 = $rs['member_sn'];
		$amount		 = $rs['amount'];
	
		//ȯ����û�� �̹� �ݾ��� �����Ѵ�.
		$after = $before = $this->memberModel->getMemberField($memberSn, "g_money");
	
		$sql = "update ".$this->db_qz."exchange_log set agree_amount=".$amount.", before_money=".$before.", after_money=".$after.", operdate=now(), state=3
						where sn=".$sn;
				
		$rs = $this->db->exeSql($sql);
		
		return ($rs>0)? 1:0;
	}
	
	//�� ȯ�� ��������
	function exchangeConfirmProcess($sn)
	{	
		$rs = $this->moneyModel->getExchangeRow($sn);
		$state = $rs['state'];
		
		if($state!=3) return -1;
		
		$sql = "update ".$this->db_qz."exchange_log set operdate=now(), state=1 where sn=".$sn;
		
		//��õ�� ���ϸ���
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
	//�� ������û ����
	function delchargeReq($sn)
	{
		$sql = "delete from ".$this->db_qz."charge_log
					where sn = ".$sn." and logo='".$this->logo."'";
					
		return $this->db->exeSql($sql);
	}
	
	//�� ������û
	function chargeReqProcess($member_sn, $amount)
	{		
		$rs = $this->memberModel->getMemberRow($member_sn, "bank_name,bank_account,bank_member");
		
		$sql = "insert into ".$this->db_qz."charge_log(member_sn,amount,bank,bank_account,bank_owner, regdate, logo)
						values(".$member_sn.",".$amount.",'".$rs['bank_name']."','".$rs['bank_account']."','".$rs['bank_member']."', now(),'".$this->logo."')";
				
		return $this->db->exeSql($sql);
	}
	
	//�� ����
	function chargeProcess($sn, $memberSn, $amount, $bonus=0)
	{
		$sql = "select count(*) from ".$this->db_qz."charge_log where sn=".$sn;
		$rs = $this->db->exeSql($sql);
		
		if(sizeof($rs)<=0) return -1;
		
		$sql = "select count(*) as cnt from ".$this->db_qz."charge_log where date(now())=date(operdate) and state=1 and member_sn=".$memberSn;
		$rs = $this->db->exeSql($sql);
		
		//���� ù ���� ���ϸ���
		if($rs[0]['cnt']<=0)	{$this->modifyMileageProcess($memberSn, $amount, '1', '����');}
		
		//��õ�� ���ϸ���
		//$this->recommendMileage($memberSn, $amount);
	
		$before = $this->memberModel->getMemberField($memberSn, "g_money");
		$this->modifyMoneyProcess($memberSn, $amount, '1',"����");
		$after 	= $this->memberModel->getMemberField($memberSn, "g_money");
		
		//������ ���ʽ� ����
		if($bonus>0)
		{
			$this->modifyMoneyProcess($memberSn, $bonus, '7',"���� ������ ���ʽ�");
		}
		
		//���� ���ϸ��� ����
		$sql = "select count(*) as cnt from ".$this->db_qz."charge_log where date(regdate)=date(now()) and state=1";
		$rs = $this->db->exeSql($sql);
		
		$sql = "update ".$this->db_qz."charge_log set agree_amount=".$amount.", before_money=".$before.", after_money=".$after.", bonus=".$bonus.",operdate=now(), state=1
				where sn=".$sn;
				
		$rs = $this->db->exeSql($sql);
		
		return ($rs>0)? 1:0;
	}
	
	//�� ��õ�� ���ϸ���
	function recommendMileage($sn, $amount)
	{
		$uid = $this->memberModel->getMemberField($sn,'uid');
		
		//1�� ��õ�� ���ϸ���
		
		$rs = $this->partnerModel->joinRecommendRate($sn, 1);
		$recommendSn = $rs['recommend_sn'];
		$rate = $rs['rate'];
		
		if($rate > 0)
		{
			$this->modifyMileageProcess($recommendSn, $amount, "2", $uid."|��õ��|", $rate);
			
			/*
			//2�� ��õ�� �˻�
			$rs = $this->partnerModel->joinRecommendRate($sn, 2);
			$recommendSn = $rs['recommend_sn'];
			$rate = $rs['rate'];
			if($rate > 0)
			{
				$this->modifyMileageProcess($recommendSn, $amount, "2", $uid."|2����õ��|", $rate);
				
				//3�� ��õ�� �˻�
				$rs = $this->partnerModel->joinRecommendRate($sn, 3);
				$recommendSn = $rs['recommend_sn'];
				$rate = $rs['rate'];
				$this->modifyMileageProcess($recommendSn, $amount, "2", $uid."|3����õ��|", $rate);
			}
			*/
		}
	}
	
	//�� ��õ�� ��÷ ���ϸ���
	function recommendFailedGameMileage($sn, $amount, $bettingNo)
	{
		$uid = $this->memberModel->getMemberField($sn,'uid');
	
		$rs = $this->partnerModel->joinRecommendRate($sn, 1);
		$recommendSn = $rs['recommend_sn'];
		$rate = $rs['rate'];
		
		if($rate > 0)
		{
			$this->modifyMileageProcess($recommendSn, $amount, "12", $bettingNo, $rate);
			
			/*
			//2�� ��õ�� �˻�
			$rs = $this->partnerModel->joinRecommendRate($sn, 2);
			$recommendSn = $rs['recommend_sn'];
			$rate = $rs['rate'];
			if($rate > 0)
			{
				$this->modifyMileageProcess($recommendSn, $amount, "12", $bettingNo, $rate);
				
				//3�� ��õ�� �˻�
				$rs = $this->partnerModel->joinRecommendRate($sn, 3);
				$recommendSn = $rs['recommend_sn'];
				$rate = $rs['rate'];
				$this->modifyMileageProcess($recommendSn, $amount, "12", $bettingNo, $rate);
			}
			*/
		}
	}
	
	//�� �������
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
		$this->modifyMoneyProcess($memberSn, -$amount, '1',"�������");
		$after 	= $this->memberModel->getMemberField($memberSn, "g_money");
	
		$sql = "update ".$this->db_qz."charge_log 
				set agree_amount=0, before_money=".$after.", after_money=0, operdate=null, state=0
					where sn=".$sn;
				
		$rs = $this->db->exeSql($sql);
		
		return ($rs>0)? 1:0;
	}
}
?>