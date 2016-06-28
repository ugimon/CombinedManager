<?php /* Template_ 2.2.3 2016/03/07 11:27:11 C:\inetpub\web\5. Armand De\www\vhost.manager\_template\content\game\popup.modify_rate.html */?>
</head>

<?php if($TPL_VAR["bettype"]==4){?>
<script>
	function Edit_Ok()
	{
		var frm = document.EditForm;
		
		if(frm.home_rate.value>=10 || frm.away_rate.value>=10)
		{
			if(confirm('배당이 10 을 초과 하였습니다. \n\n "확인" 클릭시 올림    "취소"클릭시 취소됩니다. ')) {}
			else{return;}
		}
		if (frm.home_rate.value > 19) 
		{
		   alert("승자 배당률을 입력하세요");
		   frm.home_rate.focus();
		   return;
		} 
		if (frm.draw_rate.value < 0.9 ) 
		{
		   alert("입력값에 이상이 있습니다.");
		   frm.draw_rate.focus();
		   return;
		} 
		if (frm.away_rate.value > 19 )
		{
		   alert("패자 배당률을 입력하세요");
		   frm.away_rate.focus();
		   return;
		}
		frm.submit();
	}
</script>
<?php }else{?>
<script>
	function Edit_Ok(){
		var frm = document.EditForm;
		if(frm.home_rate.value>=10 || frm.away_rate.value>=10)
		{
			if(confirm('배당이 10 을 초과 하였습니다. \n\n "확인" 클릭시 올림    "취소"클릭시 취소됩니다. ')){}
			else{return;}
		}
		if (frm.home_rate.value > 19) 
		{
		   alert("승자배당 확인 ");
		   frm.home_rate.focus();
		   return;
		} 
		if (frm.away_rate.value > 19 )
		{
		   alert("패자 배당률을 입력하세요");
		   frm.away_rate.focus();
		   return;
		} 
	
		frm.submit();
	}
</script>
<?php }?>

<body>

<div id="wrap_pop">

<form action="/game/rateProcess" method="post" name="EditForm">
	<input type="hidden" name="idx" value="<?php echo $TPL_VAR["idx"]?>">	
	<input type="hidden" name="mode" value="<?php echo $TPL_VAR["mode"]?>">
	<input type="hidden" name="gametype" value="<?php echo $TPL_VAR["gametype"]?>">
<?php if($TPL_VAR["otherType"]!=0){?>
	<input type="hidden" name="otherType" value="<?php echo $TPL_VAR["otherType"]?>"/>
    <input type="hidden" name="otherSn" value="<?php echo $TPL_VAR["otherSn"]?>"/>
<?php }?>
	
	<div id="pop_title">
		<h1><font size="2" color="red"><?php if($TPL_VAR["gametype"]==1){?>승무패<?php }elseif($TPL_VAR["gametype"]==2){?>핸디캡<?php }elseif($TPL_VAR["gametype"]==3){?>홀 짝<?php }elseif($TPL_VAR["gametype"]==4){?>언더오버<?php }?></font> 배당 수정</h1><p><a href="#"><img src="/img/btn_s_close.gif" onclick="self.close();"></a></p>
	</div>

	<table cellspacing="1" class="tableStyle_modifyRate">
		<tr>
			<th>경기시간</th>
			<td>
				<input name="gameDate" type="text" id="begin_date" class="date" value="<?php echo $TPL_VAR["item"]["gameDate"]?>" size='8' maxlength="20" onclick="new Calendar().show(this);"/>
				<input type='text' size=4 name='gameHour' value='<?php echo $TPL_VAR["item"]["gameHour"]?>'/>
				<input type='text' size=4 name='gameTime' value='<?php echo $TPL_VAR["item"]["gameTime"]?>'/>
			</td>
		</tr>
		<tr>
			<th>리그명</th>
			<td>&nbsp;&nbsp;<?php echo $TPL_VAR["item"]["league_name"]?></td>
		</tr>
		<tr>
			<th>팀명</th>
			<td>&nbsp;&nbsp;<?php echo $TPL_VAR["item"]["home_team"]?> <font color='red'>VS</font> <?php echo $TPL_VAR["item"]["away_team"]?></td>
		</tr>
<?php if($TPL_VAR["otherType"]==2){?>
        <tr>
            <th>핸디캡</th>
            <?php echo $TPL_VAR["otherHtml"]?>

        </tr>
        <tr>
            <th>언더오버</th>
            <?php echo $TPL_VAR["html"]?>

        </tr>
<?php }elseif($TPL_VAR["otherType"]==4){?>
        <tr>
            <th>핸디캡</th>
            <?php echo $TPL_VAR["html"]?>

        </tr>
        <tr>
            <th>언더오버</th>
            <?php echo $TPL_VAR["otherHtml"]?>

        </tr>
<?php }else{?>
        <tr>
            <th><?php if($TPL_VAR["gametype"]==1){?>승무패<?php }elseif($TPL_VAR["gametype"]==2){?>핸디캡<?php }elseif($TPL_VAR["gametype"]==3){?>홀 짝<?php }elseif($TPL_VAR["gametype"]==4){?>언더오버<?php }?></th>
            <?php echo $TPL_VAR["html"]?>

        </tr>
<?php }?>
	</table>

	<div id="wrap_btn">
		<input type="button" value="등록하기" onclick="Edit_Ok()" class="Qishi_submit_a">
		<input type="button" value="  닫 기  " onclick="self.close()" class="Qishi_submit_a">		
	</div>
	
</form>

<script type="text/javascript" language="JavaScript">
function ShowTr() 
{ 
	if(document.all.result.style.display == '') {document.all.result.style.display = 'none';}
	else										{document.all.result.style.display = '';}
}
</script>

</div>
</body>
</html>