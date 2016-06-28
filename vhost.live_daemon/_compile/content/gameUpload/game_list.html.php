<?php /* Template_ 2.2.3 2014/01/31 15:59:22 D:\www\vhost.live_daemon\_template\content\gameUpload\game_list.html */
$TPL_categoryList_1=empty($TPL_VAR["categoryList"])||!is_array($TPL_VAR["categoryList"])?0:count($TPL_VAR["categoryList"]);
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<script>
	function select_delete()
	{
		var child_sn="";
		var sn = document.getElementsByName("child_sn[]");
		
		for(i=0;i<sn.length;i++)   
		{   
			if(sn[i].checked==true)
			{
				if($('#state_'+sn[i].value).val()!=-1)
				{
					alert("대기중인 게임만 삭제가능합니다.");
					return;
				}
				child_sn += sn[i].value+"\,";   
			}   
		}
		if(child_sn.length>0)
		{
			child_sn=child_sn.substring(0,(child_sn.length)-1);
			param="child_sn="+child_sn+"&act=delete_game&state=<?php echo $TPL_VAR["state"]?>&game_type=<?php echo $TPL_VAR["gameType"]?>&categoryName=<?php echo $TPL_VAR["categoryName"]?>&special_type=<?php echo $TPL_VAR["special_type"]?>&perpage=<?php echo $TPL_VAR["perpage"]?>&begin_date=<?php echo $TPL_VAR["begin_date"]?>&end_date=<?php echo $TPL_VAR["end_date"]?>&filter_team_type=<?php echo $TPL_VAR["filter_team_type"]?>&filter_team=<?php echo $TPL_VAR["filter_team"]?>&money_option=<?php echo $TPL_VAR["money_option"]?>";
			document.location="/gameUpload/gamelist?"+param;
		}
		else
		{
			alert("경기를 선택!");
			return;
		}
	}
	
	function select_modify_state()
	{
		var child_sn="";
		var sn = document.getElementsByName("child_sn[]");
		for(i=0;i<sn.length;i++)   
		{   
			if(sn[i].checked==true)
		  {
		  	if($('#state_'+sn[i].value).val()!=-1 && $('#state_'+sn[i].value).val()!=0)
		  	{
		  		alert("완료된 게임은 상태변경이 불가합니다.");
					return;
		  	}
		  	child_sn += sn[i].value+"\,";
		  }   
		}
	
		if(child_sn.length>0)
		{
			state=$('#select_state').val();
			child_sn=child_sn.substring(0,(child_sn.length)-1);  				
		
			param="child_sn="+child_sn+"&new_state="+state+"&act=modify_state&state=<?php echo $TPL_VAR["state"]?>&game_type=<?php echo $TPL_VAR["gameType"]?>&categoryName=<?php echo $TPL_VAR["categoryName"]?>&special_type=<?php echo $TPL_VAR["special_type"]?>&perpage=<?php echo $TPL_VAR["perpage"]?>&begin_date=<?php echo $TPL_VAR["begin_date"]?>&end_date=<?php echo $TPL_VAR["end_date"]?>&filter_team_type=<?php echo $TPL_VAR["filter_team_type"]?>&filter_team=<?php echo $TPL_VAR["filter_team"]?>&money_option=<?php echo $TPL_VAR["money_option"]?>";
			document.location="/gameUpload/gamelist?"+param;
		}
		else
		{
			alert("경기를 선택!");
			return;
		}
	}
	
	function select_all()
	{	
		var check_state = document.form1.check_all.checked;
		for (i=0;i<document.all.length;i++) 
		{
			if (document.all[i].name=="child_sn[]") 
			{
				document.all[i].checked = check_state;
			}
		}
	}
	
	function team_betting(url)
	{
		window.open(url,'','resizable=no width=520 height=210');
		
	}
	function team_betting2(url)
	{
		window.open(url,'','resizable=no width=520 height=240');
	}
	function onDelete(child_sn)
	{
		if(confirm("정말 삭제하시겠습니까?  "))
		{
			param="child_sn="+child_sn+"&act=delete_game&state=<?php echo $TPL_VAR["state"]?>&game_type=<?php echo $TPL_VAR["gameType"]?>&categoryName=<?php echo $TPL_VAR["categoryName"]?>&special_type=<?php echo $TPL_VAR["special_type"]?>&perpage=<?php echo $TPL_VAR["perpage"]?>&begin_date=<?php echo $TPL_VAR["begin_date"]?>&end_date=<?php echo $TPL_VAR["end_date"]?>&filter_team_type=<?php echo $TPL_VAR["filter_team_type"]?>&filter_team=<?php echo $TPL_VAR["filter_team"]?>&money_option=<?php echo $TPL_VAR["money_option"]?>";
			document.location="/gameUpload/gamelist?"+param;
		}
		else
		{
			return;
		}
	}
	function go_rollback(url)
	{
		if(confirm("게임결과와 배당지급이 취소됩니다. 진행하시겠습니까?  "))
		{
			document.location = url;
		}
		else
		{
			return;
		}
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
	
	function onStateChange(child_sn)
	{
		state=$('#state_'+child_sn).val();
		param="child_sn="+child_sn+"&new_state="+state+"&act=modify_state&state=<?php echo $TPL_VAR["state"]?>&game_type=<?php echo $TPL_VAR["gameType"]?>&categoryName=<?php echo $TPL_VAR["categoryName"]?>&special_type=<?php echo $TPL_VAR["special_type"]?>&perpage=<?php echo $TPL_VAR["perpage"]?>&begin_date=<?php echo $TPL_VAR["begin_date"]?>&end_date=<?php echo $TPL_VAR["end_date"]?>&filter_team_type=<?php echo $TPL_VAR["filter_team_type"]?>&filter_team=<?php echo $TPL_VAR["filter_team"]?>&money_option=<?php echo $TPL_VAR["money_option"]?>";
		document.location="/gameUpload/gamelist?"+param;
	}
	
	function onDeadLine(child_sn)
	{
		if(confirm("게임시간을 변경 하시겠습니까?"))
		{
			param="child_sn="+child_sn+"&act=deadline_game&state=<?php echo $TPL_VAR["state"]?>&game_type=<?php echo $TPL_VAR["gameType"]?>&categoryName=<?php echo $TPL_VAR["categoryName"]?>&special_type=<?php echo $TPL_VAR["special_type"]?>&perpage=<?php echo $TPL_VAR["perpage"]?>&begin_date=<?php echo $TPL_VAR["begin_date"]?>&end_date=<?php echo $TPL_VAR["end_date"]?>&filter_team_type=<?php echo $TPL_VAR["filter_team_type"]?>&filter_team=<?php echo $TPL_VAR["filter_team"]?>&money_option=<?php echo $TPL_VAR["money_option"]?>&page=<?php echo $TPL_VAR["page"]?>";
			document.location="/gameUpload/gamelist?"+param;
		}
		else
		{
			return;
		}
	}
</script>
</head>

	<div id="route">
		<h5>관리자 시스템 - 항목 보기</h5>
	</div>
	<h3>항목 보기</h3>

	<div id="search">
		<div class="betList_option">
			<form action="?act=user" method="post" name="form3" id="form3">
				<input type="button" value="-경기등록-" class="Qishi_submit_a" onclick="window.open('/gameUpload/popup_gameupload','','resizable=yes scrollbars=yes top=5 left=5 width=1600 height=650')";>
			</form>
			<form action="?mode=execl_collect" method="post" name="form4" id="form4">
				<input type="button" value="-Excel-수집-" class="Qishi_submit_a" onclick="window.open('/gameUpload/popup_excelupload','','resizable=yes scrollbars=yes top=5 left=5 width=1100 height=650')";>
			</form>
			<form action="?mode=collect" method="post" name="form5" id="form5">
				<input type="button" value="-7m-수집-" class="Qishi_submit_a" onclick="window.open('/gameUpload/collect7m','','resizable=yes scrollbars=yes top=5 left=5 width=1100 height=650')";>
			</form>
			<form action="?mode=bwin_collect" method="post" name="form6" id="form6">
				<input type="button" value="-라이브게임-" class="Qishi_submit_a" onclick="window.open('/LiveGame/collect','','resizable=yes scrollbars=yes top=5 left=5 width=1100 height=650')";>
			</form>
				<input type="button" value="-게임복사-" class="Qishi_submit_a" onclick="window.open('/gameUpload/game_copy_list','','resizable=yes scrollbars=yes top=5 left=5 width=1100 height=650')";>
		</div>
		
		<div class="wrap_search">
			<form name=frmSrh method=post action="/gameUpload/gamelist"> 
			<input type="hidden" name="search" value="search">				
			<input type="hidden" name="category_name" value="">
			
			<span>출력</span>
			<input name="perpage" type="text" id="perpage"  class="sortInput" onkeyup="if(event.keyCode !=37 && event.keyCode != 39) value=value.replace(/\D/g,'');" maxlength="3" size="5" value="<?php echo $TPL_VAR["perpage"]?>" onmouseover="this.focus()">

			<span>설정</span>
			<input type="radio" name="state" value=0 <?php if($TPL_VAR["state"]==0){?>checked<?php }?> class="radio">전체
			<input type="radio" name="state" value=1 <?php if($TPL_VAR["state"]==1){?>checked<?php }?> class="radio">종료
			<input type="radio" name="state" value=20 <?php if($TPL_VAR["state"]==20){?>checked<?php }?> class="radio">발매(배팅가능)
			<input type="radio" name="state" value=21 <?php if($TPL_VAR["state"]==21){?>checked<?php }?> class="radio">발매(배팅마감)
			<input type="radio" name="state" value=3 <?php if($TPL_VAR["state"]==3){?>checked<?php }?> class="radio">대기
			&nbsp;
			
			<select name="special_type">
				<option value="">대분류</option>
				<option value="1" <?php if($TPL_VAR["special_type"]==1){?> selected <?php }?>>일반</option>
				<option value="2" <?php if($TPL_VAR["special_type"]==2){?> selected <?php }?>>스페셜</option>
				<option value="4" <?php if($TPL_VAR["special_type"]==4){?> selected <?php }?>>멀티</option>
			</select>
			
			<select name="game_type">
				<option value="">종류</option>
				<option value="1" <?php if($TPL_VAR["gameType"]==1){?> selected <?php }?>>승무패</option>
				<option value="2" <?php if($TPL_VAR["gameType"]==2){?> selected <?php }?>>핸디캡</option>
				<option value="4" <?php if($TPL_VAR["gameType"]==4){?> selected <?php }?>>언더오버</option>
			</select>
			
			<select name="categoryName">
				<option value="">종목</option>
<?php if($TPL_categoryList_1){foreach($TPL_VAR["categoryList"] as $TPL_V1){?>
					<option value="<?php echo $TPL_V1["name"]?>" <?php if($TPL_VAR["categoryName"]==$TPL_V1["name"]){?> selected <?php }?>><?php echo $TPL_V1["name"]?></option>
<?php }}?>
			</select>
			
			<!-- 기간 필터 -->
			<span class="icon">날짜</span><input name="begin_date" type="text" id="begin_date" class="date" value="<?php echo $TPL_VAR["begin_date"]?>" maxlength="20" onclick="new Calendar().show(this);"/>&nbsp;~
			<input name="end_date" type="text" id="end_date" class="date" value="<?php echo $TPL_VAR["end_date"]?>" maxlength="20" onclick="new Calendar().show(this);" />
				
			<!-- 팀검색, 리그검색 -->
			<select name="filter_team_type">
				<option value="home_team" <?php if($TPL_VAR["filter_team_type"]=="home_team"){?> selected<?php }?>>홈팀</option>
				<option value="away_team" <?php if($TPL_VAR["filter_team_type"]=="away_team"){?> selected<?php }?>>원정팀</option>
				<option value="league" 		<?php if($TPL_VAR["filter_team_type"]=="league"){?> selected<?php }?>>리그명</option>
			</select>
			<input type="text" size="10" name="filter_team" value="<?php echo $TPL_VAR["filter_team"]?>" class="name">
			<!-- 검색버튼 -->
			<input name="Submit4" type="image" src="/img/btn_search.gif" class="imgType" title="검색" />
			&nbsp;&nbsp;
			<!--<input type="checkbox" name="money_option" value="" <?php if($TPL_VAR["money_option"]==1){?>checked<?php }?> onClick="onCheckbox(this.form)" class="radio"><font color='red'>배팅금액 0↑</font>-->
			<span class="rightSort">
				<span>선택 항목을</span>
				<select name="select_state" id="select_state">
					<option value=0  <?php if($TPL_VAR["select_state"]==0){?>  selected <?php }?>>발매</option>
					<option value=-1 <?php if($TPL_VAR["select_state"]== -1){?> selected <?php }?>>대기</option>
				</select>
				<input type="button" value="선택상태변경" onclick="select_modify_state();" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'" onmouseout="this.className='Qishi_submit_a'">
				<input type="button" value="선택삭제" onclick="select_delete();" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'" onmouseout="this.className='Qishi_submit_a'">
			</span>
			</form>
		</div>
	</div>
	
	<form id="form1" name="form1" method="post" action="?">
  	<input type="hidden" name="act" value="delete">
		<table cellspacing="1" class="tableStyle_gameList">
		<legend class="blind">항목보기</legend>
			<thead>
	    		<tr>     
	      	<th><input type="checkbox" name="check_all" onClick="select_all()"/> No</th>
					<th>경기일시</th>
					<th>대분류</th>
					<th>종류</th>
					<th>종목</th>
					<th>리그</th>
					<th colspan="2">승(홈팀)</th>
					<th>무</th>
					<th colspan="2">패(원정팀)</th>
					<th>스코어</th>
					<th>이긴 팀</th>
					<th>진행상태</th>
					<th>배당관리</th>
					<th>처리</th>
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
						<td><input type='checkbox' name='child_sn[]' id='child_sn' value='<?php echo $TPL_V1["child_sn"]?>'><font color='blue'> <?php echo $TPL_V1["child_sn"]?></font></td>
						<td><?php echo sprintf("%s %s:%s",substr($TPL_V1["gameDate"],5),$TPL_V1["gameHour"],$TPL_V1["gameTime"])?></td>
						<td>
<?php if($TPL_V1["special"]==0){?>일반
<?php }elseif($TPL_V1["special"]==1){?>스페셜
<?php }elseif($TPL_V1["special"]==2){?>멀티
<?php }?>
						</td>
						<td>
<?php if($TPL_V1["type"]==1){?><span class="victory">승무패<?php if($TPL_V1["special"]==1){?>(스페셜)<?php }elseif($TPL_V1["special"]==2){?>(멀티)<?php }elseif($TPL_V1["special"]==3){?>(고액)<?php }?></span>
<?php }elseif($TPL_V1["type"]==2){?><span class="handicap">핸디캡<?php if($TPL_V1["special"]==1){?>(스페셜)<?php }elseif($TPL_V1["special"]==2){?>(멀티)<?php }elseif($TPL_V1["special"]==3){?>(고액)<?php }?></span>
<?php }elseif($TPL_V1["type"]==4){?><span class="underover">언더오버<?php if($TPL_V1["special"]==1){?>(스페셜)<?php }elseif($TPL_V1["special"]==2){?>(멀티)<?php }elseif($TPL_V1["special"]==3){?>(고액)<?php }?></span>
<?php }?>
						</td>
						<td><?php echo $TPL_V1["sport_name"]?></td>
						<td><?php echo $TPL_V1["league_name"]?></td>
						<td>
							
<?php if($TPL_V1["total_betting"]>0){?>
						  	<font color="blue">
						  		<a href=javascript:team_betting2("/gameUpload/popup_gamedetail?child_sn=<?php echo $TPL_V1["child_sn"]?>"); style='cursor:hand' onmousemove="showpup('<?php echo $TPL_V1["home_team"]?>&nbsp;&nbsp;VS&nbsp;&nbsp;<?php echo $TPL_V1["away_team"]?>')" onmouseout='hidepup()'>
						  			<b><?php echo mb_strimwidth($TPL_V1["home_team"],0,20,"..","utf-8")?></b>
						  		</a>
						  	</font>
<?php }else{?>
						  	<a href=javascript:team_betting2('/gameUpload/popup_gamedetail?child_sn=<?php echo $TPL_V1["child_sn"]?>'); style='cursor:hand' onmousemove="showpup('<?php echo $TPL_V1["home_team"]?>&nbsp;&nbsp;VS&nbsp;&nbsp;<?php echo $TPL_V1["away_team"]?>')" onmouseout='hidepup()'>
						  		<?php echo mb_strimwidth($TPL_V1["home_team"],0,20,"..","utf-8")?>

							</a>
<?php }?>
						  
						  <!--
						  <a href=javascript:team_betting2('/gameUpload/popup_gamedetail?child_sn=<?php echo $TPL_V1["child_sn"]?>'); style='cursor:hand' onmousemove="showpup('<?php echo $TPL_V1["home_team"]?>&nbsp;&nbsp;VS&nbsp;&nbsp;<?php echo $TPL_V1["away_team"]?>')" onmouseout='hidepup()'>
						  		<?php echo mb_strimwidth($TPL_V1["home_team"],0,20,"..","utf-8")?>

						  		-->
						</td>
						<td><?php echo $TPL_V1["home_rate"]?></td>
						<td><?php if(($TPL_V1["type"]==1&&$TPL_V1["draw_rate"]=='1.00')||($TPL_V1["type"]==1&&$TPL_V1["draw_rate"]=='1')){?>VS<?php }else{?><?php echo $TPL_V1["draw_rate"]?><?php }?></td>
						<td><?php echo $TPL_V1["away_rate"]?></td>
						<td>
<?php if($TPL_V1["total_betting"]>0){?>
						  	<font color=blue>
						  		<a href=javascript:team_betting2("/gameUpload/popup_gamedetail?child_sn=<?php echo $TPL_V1["child_sn"]?>"); style='cursor:hand' onmousemove="showpup('<?php echo $TPL_V1["home_team"]?>&nbsp;&nbsp;VS&nbsp;&nbsp;<?php echo $TPL_V1["away_team"]?>')" onmouseout='hidepup()'>
						  			<b><?php echo mb_strimwidth($TPL_V1["away_team"],0,20,"..","utf-8")?></b>
						  		</a>
						  	</font>
<?php }else{?>
						  	<a href=javascript:team_betting2('/gameUpload/popup_gamedetail?child_sn=<?php echo $TPL_V1["child_sn"]?>'); style='cursor:hand' onmousemove="showpup('<?php echo $TPL_V1["home_team"]?>&nbsp;&nbsp;VS&nbsp;&nbsp;<?php echo $TPL_V1["away_team"]?>')" onmouseout='hidepup()'>
						  		<?php echo mb_strimwidth($TPL_V1["away_team"],0,20,"..","utf-8")?>

								</a>&nbsp;&nbsp;
<?php }?>
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
						 	
						 	<td>
<?php if($TPL_V1["kubun"]==1){?>
						 		종료
<?php }else{?>
						 		<select name="play" id="state_<?php echo $TPL_V1["child_sn"]?>" onChange="onStateChange(<?php echo $TPL_V1["child_sn"]?>);">
						 			<option value=0  <?php if($TPL_V1["kubun"]==0){?>  selected <?php }?>>발매</option>
									<option value=-1 <?php if($TPL_V1["kubun"]==''){?> selected <?php }?>>대기</option>
								</select>
<?php }?>
							</td>
						<td>
							<input type='button' class='btnStyle4' value="배당수정" onclick=open_window('/gameUpload/modifyrate?idx=<?php echo $TPL_V1["child_sn"]?>&gametype=<?php echo $TPL_V1["type"]?>&mode=edit','650','300')>
						</td>
						<td>
<?php if($TPL_V1["special"]==2&&$TPL_V1["result"]!=1){?>
								<input type="button" class="btnStyle3" value="마감" onclick="onDeadLine(<?php echo $TPL_V1["child_sn"]?>)";>&nbsp;
<?php }?>
							<input type="button" class="btnStyle3" value="수정" onclick="window.open('/gameUpload/popup_modifyresult?mode=edit&idx=<?php echo $TPL_V1["child_sn"]?>&result=<?php echo $TPL_V1["result"]?>','','resizable=no width=650 height=400')";>&nbsp;
<?php if($TPL_V1["result"]!=1){?>
								<input type="button" class="btnStyle3" value="삭제" onclick="onDelete(<?php echo $TPL_V1["child_sn"]?>)">
<?php }else{?>
								<input type='button' class="btnStyle3" value="결과취소" style="color:red" onclick="go_rollback('/gameUpload/cancel_resultProcess?idx=<?php echo $TPL_V1["child_sn"]?>&type=<?php echo $TPL_VAR["type"]?>')")>
<?php }?>
						</td>
					</tr>
<?php }}?>
			</tbody>
		</table>
 		
		<div id="pages">
			<?php echo $TPL_VAR["pagelist"]?>

		</div>
	</form>