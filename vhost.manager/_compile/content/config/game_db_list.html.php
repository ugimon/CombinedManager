<?php /* Template_ 2.2.3 2016/03/07 10:27:12 C:\inetpub\combined_manager\vhost.manager\_template\content\config\game_db_list.html */
$TPL_categoryList_1=empty($TPL_VAR["categoryList"])||!is_array($TPL_VAR["categoryList"])?0:count($TPL_VAR["categoryList"]);
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<script>document.title = '게임관리-게임관리';</script>

<script>
	function addall()
	{
		var intChildIdx="";
		var   c   =   document.getElementsByName("intChildIdx");   
		for(i=0;i<c.length;i++)   
		{   
			if(c[i].checked == true )   
		    {   
				var val=c[i].value;
				var rate1=document.getElementById(val+"_rate1").value;
				var rate3=document.getElementById(val+"_rate3").value;
				if(rate1=="0.00" || rate3=="0.00")
				{
					alert(val+" -  배당이 틀립니다.확인하세요.");
					return;
				}
		        intChildIdx += c[i].value+"\,";   
			}   
		} 
		if(intChildIdx.length>0)
		{
			intChildIdx=intChildIdx.substring(0,(intChildIdx.length)-1);  	
			url="/game/modifyStausProcess?mode=edit&idx="+intChildIdx+"&play=0";
			team_betting(url);		
		}
		else
		{
			alert("발매경기를 선택!");
		 	return;
		 }
	}
	
	function delall()
	{
		var intChildIdx="";
		var   c   =   document.getElementsByName("intChildIdx");   
		for(i=0;i<c.length;i++)   
		{   
		      if(c[i].checked == true )   
		      {   
		                intChildIdx += c[i].value+"\,";   
		      }   
		 } 
		 if(intChildIdx.length>0)
		 {
			 intChildIdx=intChildIdx.substring(0,(intChildIdx.length)-1);  				
			 document.location="/game/delchildProcess?idx="+intChildIdx+"&type=<?php echo $TPL_VAR["type"]?>";
			
		 }else
		 {
		 	alert("발매경기를 선택!");
		 	return;
		 }
	}
	function checkAll()
	{
		var   c   =   document.getElementsByName("intChildIdx");
		for( i=0;i<c.length;i++)
		{
			c[i].checked=true;
		}  
	}
	function clearAll()
	{
		var   c   =   document.getElementsByName("intChildIdx");
		for(i=0;i<c.length;i++)
		{
		    
			c[i].checked=false;
		}  	
	}
	function team_betting(url)
	{
		window.open(url,'','resizable=no width=520 height=210');
		//alert(url);
	}
	function team_betting2(url)
	{
		window.open(url,'','resizable=no width=520 height=240');
		//alert(url);
	}
	function go_delete(url)
	{
		if(confirm("정말 삭제하시겠습니까?  "))
		{
			document.location = url;
		}
		else{return;}
	}
		
	function onCheckbox(frm)
	{
		if(frm.money_option.checked==true)
		{
			frm.money_option.value=1
		}
		else
			frm.money_option.value=0
		frm.submit();
	}
	
	function onDeadLine(child_sn)
	{
		if(confirm("게임시간을 변경 하시겠습니까?"))
		{
			param="child_sn="+child_sn+"&act=deadline_game&state=<?php echo $TPL_VAR["state"]?>&game_type=<?php echo $TPL_VAR["gameType"]?>&categoryName=<?php echo $TPL_VAR["categoryName"]?>&special_type=<?php echo $TPL_VAR["special_type"]?>&perpage=<?php echo $TPL_VAR["perpage"]?>&begin_date=<?php echo $TPL_VAR["begin_date"]?>&end_date=<?php echo $TPL_VAR["end_date"]?>&filter_team_type=<?php echo $TPL_VAR["filter_team_type"]?>&filter_team=<?php echo $TPL_VAR["filter_team"]?>&money_option=<?php echo $TPL_VAR["money_option"]?>&page=<?php echo $TPL_VAR["page"]?>";
			document.location="/config/gamedataexcel?"+param;
		}
		else
		{
			return;
		}
	}

	function saveExcel()
	{
	    var excelForm = document.frmSrh;
	    excelForm.action = "/config/getExcel";
	    excelForm.submit();
	    excelForm.action = "/config/gamedataexcel";
	}
</script>
</head>

<div class="wrap">

	<div id="route">
		<h5>관리자 시스템 - 항목 보기</h5>
	</div>
	<h3>항목 보기</h3>	
	
	<div id="search">
		<form name=frmSrh method=post action="/config/gamedataexcel">
			<input type="hidden" name="search" value="search">				
			<input type="hidden" name="type" value="<?php echo $TPL_VAR["type"]?>">
			<input type="hidden" name="category_name" value="">
			
			<div class="betList_option">
				
				<!--<span>출력</span>-->
				<input name="perpage" type="hidden" id="perpage"  class="sortInput" onkeyup="if(event.keyCode !=37 && event.keyCode != 39) value=value.replace(/\D/g,'');" maxlength="3" size="5" value="9999" onmouseover="this.focus()">
				
				<span class="icon">설정</span>
				<input type="radio" name="state" value=0 <?php if($TPL_VAR["state"]==0){?>checked<?php }?> class="radio">전체
				<input type="radio" name="state" value=1 <?php if($TPL_VAR["state"]==1){?>checked<?php }?> class="radio">종료
				<input type="radio" name="state" value=20 <?php if($TPL_VAR["state"]==20){?>checked<?php }?> onClick="submit()" class="radio">발매(배팅가능)
				<input type="radio" name="state" value=21 <?php if($TPL_VAR["state"]==21){?>checked<?php }?> onClick="submit()" class="radio">발매(배팅마감)
				<input type="radio" name="state" value=3 <?php if($TPL_VAR["state"]==3){?>checked<?php }?> class="radio">대기
				&nbsp;&nbsp;
				
				<span class="icon">정렬</span>
				<select name="special_type" onchange="submit()">
					<option value="">대분류</option>
					<option value="1"  <?php if($TPL_VAR["special_type"]==1){?>  selected <?php }?>>일반</option>
					<option value="2"  <?php if($TPL_VAR["special_type"]==2){?>  selected <?php }?>>스페셜</option>
					<option value="4"  <?php if($TPL_VAR["special_type"]==4){?>  selected <?php }?>>멀티</option>
					<option value="5"  <?php if($TPL_VAR["special_type"]==5){?>  selected <?php }?>>사다리</option>
					<option value="6"  <?php if($TPL_VAR["special_type"]==6){?>  selected <?php }?>>파워볼</option>
					<option value="7"  <?php if($TPL_VAR["special_type"]==7){?>  selected <?php }?>>달팽이</option>
				</select>
				
				<select name="game_type" onchange="submit()">
					<option value="">종류</option>
					<option value="1"  <?php if($TPL_VAR["gameType"]==1){?>  selected <?php }?>>승무패</option>
					<option value="2"  <?php if($TPL_VAR["gameType"]==2){?>  selected <?php }?>>핸디캡</option>
					<option value="4"  <?php if($TPL_VAR["gameType"]==4){?>  selected <?php }?>>언더오버</option>
				</select>
				
				<select name="categoryName" onchange="submit()">
					<option value="">종목</option>
<?php if($TPL_categoryList_1){foreach($TPL_VAR["categoryList"] as $TPL_V1){?>
						<option value="<?php echo $TPL_V1["name"]?>"  <?php if($TPL_VAR["categoryName"]==$TPL_V1["name"]){?>  selected <?php }?>><?php echo $TPL_V1["name"]?></option>
<?php }}?>
				</select>
				
				<!--<input type="checkbox" name="money_option" value="" <?php if($TPL_VAR["money_option"]==1){?>checked<?php }?> onClick="onCheckbox(this.form)"><font color='red'> 배팅금액 0↑</font>-->

			</div>
			<div class="wrap_search">
				
				<!-- 기간 필터 -->
				<span class="icon">날짜</span><input name="begin_date" type="text" id="begin_date" class="date" value="<?php echo $TPL_VAR["begin_date"]?>" maxlength="20" onclick="new Calendar().show(this);"/>&nbsp;~
				<input name="end_date" type="text" id="end_date" class="date" value="<?php echo $TPL_VAR["end_date"]?>" maxlength="20" onclick="new Calendar().show(this);" />
				
				<!-- 팀검색, 리그검색 -->
				<select name="filter_team_type">
					<option value="home_team" <?php if($TPL_VAR["filter_team_type"]=="home_team"){?> selected<?php }?>>홈팀</option>
					<option value="away_team" <?php if($TPL_VAR["filter_team_type"]=="away_team"){?> selected<?php }?>>원정팀</option>
					<option value="league" 		<?php if($TPL_VAR["filter_team_type"]=="league"){?> selected<?php }?>>리그명</option>
				</select>
				<input type=text" size=10 name="filter_team" value="<?php echo $TPL_VAR["filter_team"]?>" class="name">
				
				<!-- 배팅총액 검색-->
				배팅총액 <input type=text" size=10 name="filter_betting_total" value="<?php echo $TPL_VAR["filter_betting_total"]?>" onkeypress="javascript:pressNumberCheck();" class="name" style="IME-MODE: disabled;">만원 이상
				
				<!-- 검색버튼 -->
				<input name="Submit4" type="image" src="/img/btn_search.gif" class="imgType" title="검색" />

                <!-- 엑셀 저장 버튼 -->
                <button name="SubmitExcel" type="button" class="saveExcel" title="엑셀저장" value="엑셀저장" onclick="saveExcel();">엑셀 저장</button>
			</div>
		</form>
	</div>
	
	<form id="form1" name="form1" method="post" action="?">
  	<input type="hidden" name="act" value="delete">  	
		<table cellspacing="1" class="tableStyle_gameList">
			<legend class="blind">게임별 상세항목</legend>
			<thead>
				<tr>
					<th scope="col">번호</th>
					<th scope="col">진행상태</th>
					<th scope="col">대분류</th>
					<th scope="col">종류</th>
					<th scope="col">종목</th>
					<th scope="col">경기일시</th>
					<th scope="col">리그</th>
					<th scope="col" colspan="2">승(홈팀)</th>
					<th scope="col">무</th>
					<th scope="col" colspan="2">패(원정팀)</th>
					<th scope="col">스코어</th>
					<th scope="col">이긴 팀</th>
					<th scope="col">배당관리</th>
					<th scope="col">배팅수정</th>
					<th scope="col">마감</th>
					<th scope="col">홈배팅(낙첨제외)</th>
					<th scope="col">무배팅(낙첨제외)</th>
					<th scope="col">원정배팅(낙첨제외)</th>
				</tr>
			</thead>
			<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
<?php if(is_null($TPL_V1["kubun"])){?>
					<tr>	
<?php }elseif($TPL_V1["kubun"]==0){?>
 					<tr class="gameGoing">
<?php }elseif($TPL_V1["kubun"]==1){?>		
 					<tr class="gameEnd">	
<?php }?>
						<td>
<?php if($TPL_VAR["type"]==3){?>
								<input type='checkbox' name='intChildIdx' value='<?php echo $TPL_V1["child_sn"]?>'><font color=blue><?php echo $TPL_V1["child_sn"]?></font>
<?php }else{?>
								<font color='blue'><?php echo $TPL_V1["child_sn"]?></font>
<?php }?>
							
<?php if(is_null($TPL_V1["kubun"])){?>
								<input type='button' class='btnStyle_s' value='발매' onclick=open_window('/game/modifyStausProcess?mode=edit&idx=<?php echo $TPL_V1["child_sn"]?>&play=0','300','200')>
<?php }?>
						</td>
						<td>
<?php if(is_null($TPL_V1["kubun"])){?> <img src="/img/icon_gameStand.gif">
<?php }elseif($TPL_V1["kubun"]==0){?><img src="/img/icon_gameGoing.gif">
<?php }elseif($TPL_V1["kubun"]==1){?><img src="/img/icon_gameEnd.gif">
<?php }?>
						<!-- 12.10.29 "대기중" 게임일 때 <img src="/img/icon_gameStand.gif"> / "진행중" 게임일 때 <img src="/img/icon_gameGoing.gif"> / "완료" 게임일 때 <img src="/img/icon_gameEnd.gif"> -->
					</td>
					<td>
<?php if($TPL_V1["special"]==0){?>일반
<?php }elseif($TPL_V1["special"]==1){?>스페셜
<?php }elseif($TPL_V1["special"]==2){?>멀티
<?php }elseif($TPL_V1["special"]==5){?>사다리
<?php }elseif($TPL_V1["special"]==6){?>파워볼
<?php }elseif($TPL_V1["special"]==7){?>달팽이
<?php }?>
					</td>
					<td>
<?php if($TPL_V1["type"]==1){?>
								<span class="victory">승무패</span>
<?php }elseif($TPL_V1["type"]==2){?>
								<span class="handicap">핸디캡</span>
<?php }elseif($TPL_V1["type"]==4){?>
								<span class="underover">언더오버</span>
<?php }?>
						</td>
						<td><?php echo $TPL_V1["sport_name"]?></td>
						<td><?php echo sprintf("%s %s:%s",substr($TPL_V1["gameDate"],5),$TPL_V1["gameHour"],$TPL_V1["gameTime"])?></td>
						<!--<td><?php echo $TPL_V1["regDate"]?></td>-->
						<td><?php echo $TPL_V1["league_name"]?></td>
						<td class="homeName">
							<a href=javascript:team_betting2("/game/popup_gamedetail?child_sn=<?php echo $TPL_V1["child_sn"]?>"); style='cursor:hand' onmousemove="showpup('<?php echo $TPL_V1["home_team"]?>&nbsp;&nbsp;VS&nbsp;&nbsp;<?php echo $TPL_V1["away_team"]?>')" onmouseout='hidepup()'>
								<?php echo mb_strimwidth($TPL_V1["home_team"],0,20,"..","utf-8")?>

							</a>
						</td>
						<td><?php echo $TPL_V1["home_rate"]?></td>
						<td><?php if($TPL_V1["draw_rate"]=='1.00'&&$TPL_V1["type"]==1||$TPL_V1["draw_rate"]=='0'&&$TPL_V1["type"]==2){?>VS<?php }else{?><?php echo $TPL_V1["draw_rate"]?><?php }?></td>
						<td><?php echo $TPL_V1["away_rate"]?></td>
						<td class="awayName">
							<a href=javascript:team_betting2("/game/popup_gamedetail?child_sn=<?php echo $TPL_V1["child_sn"]?>"); style='cursor:hand' onmousemove="showpup('<?php echo $TPL_V1["home_team"]?>&nbsp;&nbsp;VS&nbsp;&nbsp;<?php echo $TPL_V1["away_team"]?>')" onmouseout='hidepup()'>
								<?php echo mb_strimwidth($TPL_V1["away_team"],0,20,"..","utf-8")?>

							</a>
						</td>
						<td><?php echo $TPL_V1["home_score"]?>:<?php echo $TPL_V1["away_score"]?></td>
						<td>
<?php if($TPL_V1["win"]==1){?> 홈승
<?php }elseif($TPL_V1["win"]==2){?> 원정승
<?php }elseif($TPL_V1["win"]==3){?> 무승부
<?php }elseif($TPL_V1["win"]==4){?> 취소/적특
<?php }else{?> &nbsp;
<?php }?>
						</td>
						<!--<td>
							<input type='hidden' id='<?php echo $TPL_V1["child_sn"]?>_home_rate' value='<?php echo $TPL_V1["home_rate"]?>'>
							<input type='checkbox' <?php if($TPL_V1["win"]==1){?> checked <?php }?>><?php echo $TPL_V1["home_rate"]?>

							<input type='hidden' id='<?php echo $TPL_V1["child_sn"]?>_draw_rate' value='<?php echo $TPL_V1["draw_rate"]?>'>
							<input type='checkbox' <?php if($TPL_V1["win"]==3){?> checked <?php }?>><?php echo $TPL_V1["draw_rate"]?>

							<input type='hidden' id='<?php echo $TPL_V1["child_sn"]?>_away_rate' value='<?php echo $TPL_V1["away_rate"]?>'>
							<input type='checkbox' <?php if($TPL_V1["win"]==2){?> checked <?php }?>><?php echo $TPL_V1["away_rate"]?>

						</td>-->
						<td>
							<input type='button' class='btnStyle4' value='배당수정' onclick=open_window('/game/modifyrate?idx=<?php echo $TPL_V1["child_sn"]?>&gametype=<?php echo $TPL_V1["type"]?>&mode=edit','650','350')>
						</td>
						<td>
<?php if($TPL_V1["special"]==2&&$TPL_V1["result"]!=1){?>
								<input type="button" class="btnStyle3" value="마감" onclick="onDeadLine(<?php echo $TPL_V1["child_sn"]?>)";>&nbsp;
<?php }?>
						</td>
						<td><?php echo $TPL_V1["betting_count"]?></td>
						<td><a href="#" onclick="open_window('/game/popup_bet_list?child_sn=<?php echo $TPL_V1["child_sn"]?>&select_no=1','1024','600')"><?php echo number_format($TPL_V1["home_total_betting"],0)?>(<?php echo number_format($TPL_V1["active_home_total_betting"])?>)</a></td>
						<td><a href="#" onclick="open_window('/game/popup_bet_list?child_sn=<?php echo $TPL_V1["child_sn"]?>&select_no=3','1024','600')"><?php echo number_format($TPL_V1["draw_total_betting"])?>(<?php echo number_format($TPL_V1["active_draw_total_betting"])?>)</a></td>
						<td><a href="#" onclick="open_window('/game/popup_bet_list?child_sn=<?php echo $TPL_V1["child_sn"]?>&select_no=2','1024','600')"><?php echo number_format($TPL_V1["away_total_betting"])?>(<?php echo number_format($TPL_V1["active_away_total_betting"])?>)</a></td>
					</tr>
<?php }}?>
<?php if($TPL_VAR["type"]==3){?>
		    	<tr height="26">
						<td  colspan="9">
							<input type="button" value="전체선택" onclick="checkAll()" class="input">
							<input type="button" value="선택해제" onclick="clearAll()" class="input">
							<input type="button" value="선택발매" onclick="addall()" class="input">
							<input type="button" value="선택삭제" onclick="delall()" class="input">
						</td>
					</tr>
<?php }?>
	  </table>
	  
	  <div id="pages">
			<?php echo $TPL_VAR["pagelist"]?>

		</div>
	
	</form>
</div>