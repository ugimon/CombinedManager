<?php /* Template_ 2.2.3 2016/03/07 11:27:11 C:\inetpub\web\3. Poten\www\vhost.manager\_template\content\live\popup_betting_list.html */
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);
$TPL_virtual_list_1=empty($TPL_VAR["virtual_list"])||!is_array($TPL_VAR["virtual_list"])?0:count($TPL_VAR["virtual_list"]);?>
<script language="JavaScript">
	function betting_exception(detail_sn, betting_position, betting_sn)
	{
		if(confirm("[적특] 처리하시겠습니까?"))
		{
			document.location = "/LiveGame/popup_betting_exception?betting_sn="+betting_sn+"&detail_sn="+detail_sn+"betting_position="+betting_position;
		}
	}
</script>

</head>

<body>

<div class="wrap">
	<h3>일반배팅</h3>
	<table border="0" cellspacing="1" class="tableStyle_gameList" summary="게임별 배팅현황">
	<thead>
		<tr>					
			<th>배팅번호</th>
			<th>아이디</th>
			<th>닉네임</th>
			<th>배팅금액</th>
			<th>배당율</th>
			<th>예상배당</th>
			<th>게임결과</th>
			<th>당첨금액</th>					
			<th>배팅날짜</th>
			<!--<th>배팅취소</th>-->
			<th>배팅IP</th>
		</tr>
	</thead>
	<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
		<tr class="gameGoing">
			<td><?php echo $TPL_V1["betting_no"]?></td>			    
			<td><a href="javascript:open_window('/member/popup_detail?idx=<?php echo $TPL_V1["member_sn"]?>',1024,600)"><?php echo $TPL_V1["uid"]?></a></td>					
			<td><?php echo $TPL_V1["nick"]?></td>				    
			<td><?php echo number_format($TPL_V1["betting_money"],0)?></td>
			<td><?php echo $TPL_V1["odd"]?></td>
			<td><?php echo number_format(bcmul($TPL_V1["betting_money"],bcmul($TPL_V1["odd"],1,2),0),0)?></td>
			<td>
<?php if($TPL_V1["betting_result"]=='WIN'){?><font color=red>적  중</font>
<?php }elseif($TPL_V1["betting_result"]=='LOS'){?>실  패
<?php }elseif($TPL_V1["betting_result"]=='CANCEL'){?>적  특
<?php }else{?>경기중
<?php }?>
			</td>
			<td><?php echo number_format($TPL_V1["prize"],0)?></td>
			<td><?php echo $TPL_V1["reg_time"]?></td> 
			<!--<td><input type="button" value="적특"  class="btnStyle3" onClick="betting_exception(<?php echo $TPL_VAR["detail_sn"]?>, <?php echo $TPL_VAR["betting_position"]?>,<?php echo $TPL_V1["betting_sn"]?>)"></td>-->
			<td><?php echo $TPL_V1["ip"]?></td>
		</tr>
<?php }}?>	
	</tbody>
	</table>
	
	<h3>가상배팅</h3>
	<table border="0" cellspacing="1" class="tableStyle_gameList" summary="게임별 배팅현황">
	<thead>
		<tr>					
			<th>배팅번호</th>
			<th>아이디</th>
			<th>닉네임</th>
			<th>배팅금액</th>
			<th>배당율</th>
			<th>예상배당</th>
			<th>게임결과</th>
			<th>당첨금액</th>					
			<th>배팅날짜</th>
			<!--<th>배팅취소</th>-->
			<th>배팅IP</th>
		</tr>
	</thead>
	<tbody>
<?php if($TPL_virtual_list_1){foreach($TPL_VAR["virtual_list"] as $TPL_V1){?>
		<tr class="gameGoing">
			<td><?php echo $TPL_V1["betting_no"]?></td>			    
			<td><a href="javascript:open_window('/member/popup_detail?idx=<?php echo $TPL_V1["member_sn"]?>',1024,600)"><?php echo $TPL_V1["uid"]?></a></td>					
			<td><?php echo $TPL_V1["nick"]?></td>				    
			<td><?php echo number_format($TPL_V1["betting_money"],0)?></td>
			<td><?php echo $TPL_V1["odd"]?></td>
			<td><?php echo number_format(bcmul($TPL_V1["betting_money"],bcmul($TPL_V1["odd"],1,2),0),0)?></td>
			<td>
<?php if($TPL_V1["betting_result"]=='WIN'){?><font color=red>적  중</font>
<?php }elseif($TPL_V1["betting_result"]=='LOS'){?>실  패
<?php }elseif($TPL_V1["betting_result"]=='CANCEL'){?>적  특
<?php }else{?>경기중
<?php }?>
			</td>
			<td><?php echo number_format($TPL_V1["prize"],0)?></td>
			<td><?php echo $TPL_V1["reg_time"]?></td> 
			<!--<td><input type="button" value="적특"  class="btnStyle3" onClick="betting_exception(<?php echo $TPL_VAR["detail_sn"]?>, <?php echo $TPL_VAR["betting_position"]?>,<?php echo $TPL_V1["betting_sn"]?>)"></td>-->
			<td><?php echo $TPL_V1["ip"]?></td>
		</tr>
<?php }}?>	
	</tbody>
	</table>
	
		
	<div id="pages">
		<?php echo $TPL_VAR["pagelist"]?>

	</div>
</div>