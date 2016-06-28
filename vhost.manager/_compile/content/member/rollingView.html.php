<?php /* Template_ 2.2.3 2016/03/22 08:09:18 C:\inetpub\combined_manager\vhost.manager\_template\content\member\rollingView.html */
$TPL_levelList_1=empty($TPL_VAR["levelList"])||!is_array($TPL_VAR["levelList"])?0:count($TPL_VAR["levelList"]);
$TPL_chargeList_1=empty($TPL_VAR["chargeList"])||!is_array($TPL_VAR["chargeList"])?0:count($TPL_VAR["chargeList"]);
$TPL_exchangeList_1=empty($TPL_VAR["exchangeList"])||!is_array($TPL_VAR["exchangeList"])?0:count($TPL_VAR["exchangeList"]);
$TPL_betList_1=empty($TPL_VAR["betList"])||!is_array($TPL_VAR["betList"])?0:count($TPL_VAR["betList"]);?>
<style type="text/css">
.betTotalAccount
{
	font-weight: bold;
}

.betAccountTitle
{
	display: inline-block;
	width: 50px;
}
</style>

<body>

<div id="wrap_pop">
	<div id="pop_title">
		<h1>회원 배팅규정 확인</h1>
		<p><img src="/img/btn_s_close.gif" onclick="window.close()" title="창닫기"></p>
	</div>

	<form name="frm" method="POST" action="?mode=modify">
		<input type="hidden" name="memid" value="<?php echo $TPL_VAR["list"]["uid"]?>">
		<input type="hidden" name="urlidx" value="<?php echo $TPL_VAR["memberSn"]?>">
		
		<table cellspacing="1" class="tableStyle_membersWrite" summary="회원 정보">
			<tr>
			  <th>아이디</th>
			  <td><?php echo $TPL_VAR["list"]["uid"]?></td>
			</tr>

			<tr>
			  <th>닉네임</th>
			  <td><?php echo $TPL_VAR["list"]["nick"]?></td>
			</tr>

			<tr>
			  <th>예금주</th>
			  <td><?php echo $TPL_VAR["list"]["bank_member"]?></td>
			</tr>

			<tr>
			  <th>회원등급</th>
			  <td>
			  	<select name="mem_lev">
<?php if($TPL_levelList_1){foreach($TPL_VAR["levelList"] as $TPL_V1){?>
			  			<option value="<?php echo $TPL_V1["lev"]?>" <?php if($TPL_VAR["list"]["mem_lev"]==$TPL_V1["lev"]){?> selected <?php }?>><?php echo $TPL_V1["lev_name"]?></option>	
<?php }}?>
				</select>
			  </td>
			</tr>

			
			<tr>
			  <th>최근충전</th>
			  <td>
			  	<table cellspacing="1" class="tableStyle_normal" summary="입금 내역">
					<thead>
						<tr>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">신청시간</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">처리시간</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">당시금액</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">보유금액</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">신청금액</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">실입금액</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">입금자명</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">상태</td>
						</tr>
					</thead>

					<tbody>
<?php if($TPL_chargeList_1){foreach($TPL_VAR["chargeList"] as $TPL_V1){?>
						<tr class="link_lan" style="padding-left:1px;"  onMouseOver="this.style.backgroundColor='#e0eafe';" onMouseOut="this.style.backgroundColor=''" >
							<td><?php echo $TPL_V1["regdate"]?></td>        
							<td><?php echo $TPL_V1["operdate"]?></td>
							<td><?php echo number_format($TPL_V1["before_money"],0)?></td>
							<td><?php echo number_format($TPL_V1["g_money"],0)?></td>
							<td><?php echo number_format($TPL_V1["amount"],0)?></td>
							<td><?php echo number_format($TPL_V1["agree_amount"],0)?></td>
							<td><?php echo $TPL_V1["bank_owner"]?></td>
							<td>
<?php if($TPL_V1["state"]==0){?>
									<font color='red'>신청</font>
<?php }elseif($TPL_V1["state"]==1){?>
									<font color='blue'>완료</font>
<?php }else{?>
									<font color='yellow'>삭제</font>
<?php }?>
							</td>
					  	</tr>
<?php }}?>
					</tbody>
				</table>
			  </td>
			</tr>

 
			<tr>
			  <th>최근환전</th>
			  <td>
			  	<table cellspacing="1" class="tableStyle_normal" summary="출금 내역">
					<thead>
						<tr>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">신청시간</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">처리시간</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">당시금액</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">보유금액</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">출금금액</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">실출금액</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">예금주</td>
						  <td style="color:black; font-weight:bold; border-top:1px solid #DDD; border-right:1px solid #eaeaea; border-bottom:1px solid #eaeaea;">상태</td>
						</tr>
					</thead>

					<tbody>
<?php if($TPL_exchangeList_1){foreach($TPL_VAR["exchangeList"] as $TPL_V1){?>
						<tr class="link_lan" style="padding-left:1px;"  onMouseOver="this.style.backgroundColor='#e0eafe';" onMouseOut="this.style.backgroundColor=''" >
							<td><?php echo $TPL_V1["regdate"]?></td>        
							<td><?php echo $TPL_V1["operdate"]?></td>
							<td><?php echo number_format($TPL_V1["before_money"],0)?></td>
							<td><?php echo number_format($TPL_V1["g_money"],0)?></td>
							<td><?php echo number_format($TPL_V1["amount"],0)?></td>
							<td><?php echo number_format($TPL_V1["agree_amount"],0)?></td>
							<td><?php echo $TPL_V1["bank_owner"]?></td>
							<td>
<?php if($TPL_V1["state"]==0){?>
									<font color='red'>신청</font>
<?php }elseif($TPL_V1["state"]==1){?>
									<font color='blue'>완료</font>
<?php }else{?>
									<font color='yellow'>삭제</font>
<?php }?>
							</td>
					  	</tr>
<?php }}?>
					</tbody>
				</table>
			  </td>
			</tr>
			
			<tr>
				<th>배팅규정 CASE</th>
				<td><?php echo $TPL_VAR["ncase"]?></td>
			</tr>

			<tr>
<?php if($TPL_chargeList_1){foreach($TPL_VAR["chargeList"] as $TPL_V1){?>
				<th>롤링충족금액</th>
				<td><?php echo number_format($TPL_V1["amount"]*3,0)?> (300% 기준)</td>
<?php }}?>
			</tr>

			<tr>

				<th>현재롤링금액</th>
				<td>
					<div class="betTotalAccount"><span class="betAccountTitle">총&nbsp;&nbsp;&nbsp;합</span> <?php echo number_format($TPL_VAR["totalBet"],0)?>원 (<?php echo $TPL_VAR["totalBetCount"]?>개)</div>
					<div class="betAccount"><span class="betAccountTitle">라이브</span> <?php echo number_format($TPL_VAR["liveBet"],0)?>원 (<?php echo $TPL_VAR["liveBetCount"]?>개)</div>
					<div class="betAccount"><span class="betAccountTitle">사다리</span> <?php echo number_format($TPL_VAR["sadariBet"],0)?>원 (<?php echo $TPL_VAR["sadariBetCount"]?>개)</div>
					<div class="betAccount"><span class="betAccountTitle">파워볼</span> <?php echo number_format($TPL_VAR["powerBet"],0)?>원 (<?php echo $TPL_VAR["powerBetCount"]?>개)</div>
				</td>
				
			</tr>

			<tr>
				<th>배팅내역</th>
				<!--
				<td>
					<input type="button" onclick="javascript:open_window('/member/popup_bet?mem_sn=<?php echo $TPL_VAR["memberSn"]?>','1400',600)" value="배팅내역" class="btnStyle1">
				</td>
				-->

				<td>
					<table cellspacing="1" class="tableStyle_normal" summary="배팅 내역">			
						<legend class="blind">배팅 내역</legend>
						<thead>
							<tr>
								<td width="8%">배팅번호</th>
								<td width="5%">게임</th>
								<td width="8%">당시금액</th>
								<td width="8%">배팅금액</th>
								<td width="5%">배당율</th>
								<td width="8%">예상금액</th>
								<td width="8%">배당금액</th>
								<td width="10%">배팅시간</th>
								<td width="10%">처리시간</th>
							</tr>
						</thead>
					</table>
					<br>
<?php if($TPL_betList_1){foreach($TPL_VAR["betList"] as $TPL_V1){
$TPL_item_2=empty($TPL_V1["item"])||!is_array($TPL_V1["item"])?0:count($TPL_V1["item"]);?>
					<table cellspacing="1" class="tableStyle_normal">
						<tbody>
							<tr id="t_<?php echo $TPL_V1["betting_no"]?>" >
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="8%"><?php echo $TPL_V1["betting_no"]?></td>	
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="5%"><?php echo $TPL_V1["betting_cnt"]?></td>
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="8%"><?php echo number_format($TPL_V1["before_money"],0)?></td>
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="8%"><?php echo number_format($TPL_V1["betting_money"],0)?></td>
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="5%"><?php echo number_format($TPL_V1["result_rate"],2)?></td>
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="8%"><?php echo number_format(($TPL_V1["betting_money"]*$TPL_V1["result_rate"]),0)?></td>
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="8%"><?php echo number_format($TPL_V1["result_money"],0)?></td>
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="10%"><?php echo $TPL_V1["regdate"]?></td>
								<td onclick="toggle('d_<?php echo $TPL_V1["betting_no"]?>')" width="10%"><?php echo $TPL_V1["operdate"]?></td>
							</tr>
						</tbody>
					</table>
					
					<table cellspacing="1" class="tableStyle_memo" id="d_<?php echo $TPL_VAR["list"]["betting_no"]?>" style="display:none;width:90%;margin:0 auto;margin-top:5px;margin-bottom:5px">
						<thead>
							<tr>				  
								<th>게임종류</th>
								<th>게임타입</th>
								<th>경기시간</th>
								<th>리그</th>
								<th colspan="2">홈팀</th>
								<th>무</th>
								<th colspan="2">원정팀</th>
								<th>점수</th>
								<th>배팅</th>
								<th>결과</th>
								<th>상태</th>
							</tr>
						</thead>
						<tbody>
<?php if($TPL_item_2){foreach($TPL_V1["item"] as $TPL_V2){?>
							<tr>				
								<td>
<?php if($TPL_V2["special"]==0){?>
										[일반]
<?php }elseif($TPL_V2["special"]==1){?>
										[스페셜]
<?php }elseif($TPL_V2["special"]==2){?>
										[실시간]
<?php }?>
								</td>
								<td>
<?php if($TPL_V2["game_type"]==1){?>
										[승무패]
<?php }elseif($TPL_V2["game_type"]==2){?>
										[핸디캡]
<?php }elseif($TPL_V2["game_type"]==3){?>
										[홀짝]
<?php }elseif($TPL_V2["game_type"]==4){?>
										[언더오버]
<?php }?>
								</td>
								<td><?php echo $TPL_V2["g_date"]?></td>
								<td><?php echo $TPL_V2["league_name"]?></td>
								<td <?php if($TPL_V2["select_no"]==1){?>bgcolor='#CEF279'<?php }?>><?php echo $TPL_V2["home_team"]?></td>
								<td <?php if($TPL_V2["select_no"]==1){?>bgcolor='#CEF279'<?php }?>><?php echo $TPL_V2["home_rate"]?></td>
								<td <?php if($TPL_V2["select_no"]==3){?>bgcolor='#CEF279'<?php }?> align=center><?php echo $TPL_V2["draw_rate"]?></td>
								<td <?php if($TPL_V2["select_no"]==2){?>bgcolor='#CEF279'<?php }?>><?php echo $TPL_V2["away_rate"]?></td>
								<td <?php if($TPL_V2["select_no"]==2){?>bgcolor='#CEF279'<?php }?>><?php echo $TPL_V2["away_team"]?></td>
								<td><?php echo $TPL_V2["home_score"]?>:<?php echo $TPL_V2["away_score"]?></td>
								<td>
<?php if($TPL_V2["select_no"]==1){?>
										홈팀
<?php }elseif($TPL_V2["select_no"]==2){?>
										원정팀
<?php }else{?>
										무
<?php }?>	
								</td>
								<td>
<?php if($TPL_V2["win"]==1){?>[홈승]
<?php }elseif($TPL_V2["win"]==3){?>[무승부]
<?php }elseif($TPL_V2["win"]==2){?>[원정승]
<?php }elseif($TPL_V2["win"]==4&&($TPL_V2["game_type"]==2||$TPL_V2["game_type"]==4)){?>[적특]
<?php }elseif($TPL_V2["win"]==4){?>[취소]
<?php }?>
								</td>	
								<td>
<?php if($TPL_V2["result"]==0){?>
										<font color=#666666>진행중</font>
<?php }elseif($TPL_V2["result"]==1){?>
										<font color=red>적  중</font>
<?php }elseif($TPL_V2["result"]==2){?>
										<font color=blue>낙 첨</font>
<?php }elseif($TPL_V2["result"]==4){?>
										<font color=green>취 소</font>
<?php }?>
								</td>
							</tr>	
<?php }}?>
						</tbody>
					</table>
<?php }}?>
				</td>
				
			</tr>
		</table>
	</form>
</div>

</body>