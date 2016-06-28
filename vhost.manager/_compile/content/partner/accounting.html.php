<?php /* Template_ 2.2.3 2016/04/28 10:28:42 C:\inetpub\combined_manager\vhost.manager\_template\content\partner\accounting.html */
$TPL_partner_list_1=empty($TPL_VAR["partner_list"])||!is_array($TPL_VAR["partner_list"])?0:count($TPL_VAR["partner_list"]);
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<script>
	

function checkAccount(tid, pid)
{
	if(confirm("["+pid+"]총판을 정산하시겠습니까?\r\n※정산일자를 반드시 확인하세요!!!"))
	{
		var id = $("#t_"+tid).find("span.t_pid").text();
		
		var jcnt = $("#t_"+tid).find("span.t_jcount").text();
		var icnt = $("#t_"+tid).find("span.t_icount").text();
		
		var inm = $("#t_"+tid).find("span.t_inmoney").text();
		var outm = $("#t_"+tid).find("span.t_outmoney").text();

		var carm = $("#t_"+tid).find("span.t_carrymoney").text();
		var calm = $("#t_"+tid).find("span.t_calmoney").text();

		var totm = $("#t_"+tid).find("span.t_totalmoney").text();

		var btime = "<?php echo $TPL_VAR["begin_date"]?>";
		var etime = "<?php echo $TPL_VAR["end_date"]?>";

		inm = inm.replace(/,/gi, '');
		outm = outm.replace(/,/gi, '');
		carm = carm.replace(/,/gi, '');
		calm = calm.replace(/,/gi, '');
		totm = totm.replace(/,/gi, '');

		

		$.post("?act=account",
		{
			pid: id,
			j_cnt: jcnt,
			i_cnt: icnt,

			in_money: inm,
			out_money: outm,

			carry_money: carm,
			calc_money: calm,

			total_money: totm,

			begin_time: btime,
			end_time: etime
		}, function(data, status)
		{
			
		});
	}
}

function go_del(url)
{
	if(confirm("정말 삭제하시겠습니까?"))
	{
		document.location = url;
	}
	else
	{
		return;
	}
}

</script>

<div class="wrap" id="partner_accounting">

	<div id="route">
		<h5>관리자 시스템 > 파트너 관리 > <b>총판 정산</b></h5>
	</div>

	<h3>총판 정산</h3>

	<ul id="tab">
		<li><a href="/partner/accounting" id="partner_accounting">정산 대기</a></li>
		<li><a href="/partner/accounting_fin" id="partner_accounting_fin">정산 완료</a></li>
	</ul>

	<div id="search">
		<div class="wrap">
			<form action="?" method="GET" name="form2" id="form2">
				<!-- 기간 필터 -->
				<span class="icon">날짜</span>
				
				<input name="begin_date" type="text" id="begin_date" class="date" value="<?php echo $TPL_VAR["begin_date"]?>" maxlength="20" onclick="new Calendar().show(this);"/>&nbsp;~
				<input name="end_date" type="text" id="end_date" class="date" value="<?php echo $TPL_VAR["end_date"]?>" maxlength="20" onclick="new Calendar().show(this);" />
				&nbsp;&nbsp;&nbsp;&nbsp;
				
				<!-- 총판 필터 -->
				<select name="filter_partner_sn">
					<option value="" <?php if($TPL_VAR["filter_partner_sn"]==""){?> selected <?php }?>>총판</option>
<?php if($TPL_partner_list_1){foreach($TPL_VAR["partner_list"] as $TPL_V1){?>
						<option value=<?php echo $TPL_V1["Idx"]?> <?php if($TPL_VAR["filter_partner_sn"]==$TPL_V1["Idx"]){?> selected <?php }?>><?php echo $TPL_V1["rec_id"]?></option>
<?php }}?>
				</select>
				
				<!-- 검색버튼 -->
				<input name="Submit4" type="image" src="/img/btn_search.gif" class="imgType" />
			</form>
		</div>
	</div>

	<form id="form1" name="form1" method="post" action="?act=delete_user">
	<table cellspacing="1" class="tableStyle_normal" summary="정산 대기 목록">
	<legend class="blind">정산 대기</legend>
	<thead>
		<tr>
			<th scope="col">사이트</th>
			<th scope="col">파트너</th>
			<th scope="col">가입회원</th>
			<th scope="col">입금회원</th>
			<th scope="col">충전금액</th>
			<th scope="col">환전금액</th>
			<th scope="col">수익금액</th>
			<th scope="col">이월금액</th>
			<th scope="col">정산금액</th>
			<th scope="col">웹게임포인트</th>
			<th scope="col">스포츠포인트</th>
			<th scope="col">총 정산금액</th>
			<th scope="col">마지막 정산일</th>
			<th scope="col">처리</th>
		</tr>
	</thead>
	<tbody>	
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
<?php if($TPL_V1["carried"]=='a'){?>
			<tr id="t_<?php echo $TPL_V1["Idx"]?>" style="background: eclipse;">
<?php }else{?>
			<tr id="t_<?php echo $TPL_V1["Idx"]?>">
<?php }?>
				<td><?php if($TPL_V1["logo"]=='totobang'){?>포텐<?php }elseif($TPL_V1["logo"]=='eclipse'){?>이클<?php }elseif($TPL_V1["logo"]=='poten2'){?>포텐2<?php }?></td>
				<td><a href="javascript:open_window('/partner/memberDetails?idx=<?php echo $TPL_V1["Idx"]?>',640,440)"><span class="t_pid"><?php echo $TPL_V1["rec_id"]?></span> (<font color="red"><?php echo number_format($TPL_V1["default_rate"])?></font>%)</td>
				<td><span class="t_jcount"><?php echo number_format($TPL_V1["member_count"],0)?></span>명</td>
				<td><span class="t_icount"><?php echo number_format($TPL_V1["charge_count"],0)?></span>명</td>
				<td><span class="t_inmoney"><?php echo number_format($TPL_V1["charge_sum"],0)?></span></td>
				<td><span class="t_outmoney"><?php echo number_format($TPL_V1["exchange_sum"],0)?></span></td>
				<td>
<?php if($TPL_V1["charge_sum"]-$TPL_V1["exchange_sum"]>0){?>
						<span style="font-weight: bold; color: blue;"><?php echo number_format($TPL_V1["charge_sum"]-$TPL_V1["exchange_sum"],0)?></span>
<?php }else{?>
						<span style="font-weight: bold; color: red;"><?php echo number_format($TPL_V1["charge_sum"]-$TPL_V1["exchange_sum"],0)?></span>
<?php }?>
				</td>
				<td><span style="font-weight: bold; color: red;"><?php echo number_format($TPL_V1["carried"],0)?></span></td>
				<td>
<?php if($TPL_V1["default_rate"]>0){?>
<?php if($TPL_V1["charge_sum"]-$TPL_V1["exchange_sum"]+$TPL_V1["carried"]>0){?>
							<span class="t_calmoney" style="font-weight: bold; color: blue;"><?php echo number_format(($TPL_V1["charge_sum"]-$TPL_V1["exchange_sum"]+$TPL_V1["carried"])/100*$TPL_V1["default_rate"],0)?></span>
<?php }else{?>
							<span class="t_carrymoney" style="font-weight: bold; color: red;"><?php echo number_format($TPL_V1["charge_sum"]-$TPL_V1["exchange_sum"]+$TPL_V1["carried"],0)?></span>
<?php }?>
<?php }else{?>
						<s>해당없음</s>
<?php }?>
				</td>
				<td><?php echo number_format($TPL_V1["rec_wb_account"],0)?></td>
				<td><?php echo number_format($TPL_V1["rec_sb_account"],0)?></td>
				<td style="background: crimson; color: yellow; font-weight: bold;">
<?php if($TPL_V1["default_rate"]>0){?>
<?php if($TPL_V1["charge_sum"]-$TPL_V1["exchange_sum"]+$TPL_V1["carried"]>0){?>
							<span class="t_totalmoney"><?php echo number_format(($TPL_V1["charge_sum"]-$TPL_V1["exchange_sum"]+$TPL_V1["carried"])/100*$TPL_V1["default_rate"],0)?></span>
<?php }else{?>
							-
<?php }?>
<?php }else{?>
						<?php echo number_format($TPL_V1["rec_wb_account"]+$TPL_V1["rec_sb_account"],0)?>

<?php }?>
				</td>
				<td>
<?php if($TPL_V1["carried"]=='a'){?>
<?php }else{?>
					<?php echo $TPL_V1["lastDate"]?>

<?php }?>
				</td>
				<td>
<?php if($TPL_V1["carried"]=='a'){?>
<?php }else{?>
				<a href="javascript:checkAccount(<?php echo $TPL_V1["Idx"]?>, '<?php echo $TPL_V1["rec_id"]?>');"><img src="/img/btn_s_confirm2.gif" title="정산"></a>
<?php }?>
				</td>
			
			</tr>
<?php }}?>	
	</tbody>
	</table>
	</form>

	<div id="pages">
		<?php echo $TPL_VAR["pagelist"]?>

	</div>

</div>