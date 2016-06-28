<?php /* Template_ 2.2.3 2016/06/28 18:53:35 C:\inetpub\combined_manager\vhost.manager\_template\content\config\level.html */
$TPL_logolist_1=empty($TPL_VAR["logolist"])||!is_array($TPL_VAR["logolist"])?0:count($TPL_VAR["logolist"]);
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<script>

	function onEdit($value)
	{
		var $rs = $("#mode");

		if( $value == 'add' )
		{
			$rs.attr('value','add');
		}
		else
		{
			$rs.attr('value','del');
		}

		frm.submit();
	}

	function onSave($sn)
	{
		var lev_name									= $('#tr_'+$sn+' input:text:eq(0)').val();
		var min_money 								= $('#tr_'+$sn+' input:text:eq(1)').val();
		var max_money 								= $('#tr_'+$sn+' input:text:eq(2)').val();
		var max_money_special 				= $('#tr_'+$sn+' input:text:eq(3)').val();
		var max_bonus 								= $('#tr_'+$sn+' input:text:eq(4)').val();
		var max_bonus_special 				= $('#tr_'+$sn+' input:text:eq(5)').val();
		var max_money_single 					= $('#tr_'+$sn+' input:text:eq(6)').val();
		var max_money_single_special 	= $('#tr_'+$sn+' input:text:eq(7)').val();
		var charge_rate 							= $('#tr_'+$sn+' input:text:eq(8)').val();
		var lose_rate 								= $('#tr_'+$sn+' input:text:eq(9)').val();
		var recommend_rate 						= $('#tr_'+$sn+' input:text:eq(10)').val();
		var bank 											= $('#tr_'+$sn+' input:text:eq(11)').val();
		var bank_account 							= $('#tr_'+$sn+' input:text:eq(12)').val();
		var bank_owner 								= $('#tr_'+$sn+' input:text:eq(13)').val();
		var min_charge 								= $('#tr_'+$sn+' input:text:eq(14)').val();
		var min_exchange 							= $('#tr_'+$sn+' input:text:eq(15)').val();
		var recommend_limit 					= $('#tr_'+$sn+' input:text:eq(16)').val();
		var domain_name			 					= $('#tr_'+$sn+' select').val();

		$('#sn').val($sn);
		$('#lev_name').val(lev_name);
		$('#min_money').val(min_money);
		$('#max_money').val(max_money);
		$('#max_money_special').val(max_money_special);
		$('#max_bonus').val(max_bonus);
		$('#max_bonus_special').val(max_bonus_special);
		$('#max_money_single').val(max_money_single);
		$('#max_money_single_special').val(max_money_single_special);
		$('#charge_rate').val(charge_rate);
		$('#lose_rate').val(lose_rate);
		$('#recommend_rate').val(recommend_rate);
		$('#bank').val(bank);
		$('#bank_account').val(bank_account);
		$('#bank_owner').val(bank_owner);
		$('#min_charge').val(min_charge);
		$('#min_exchange').val(min_exchange);
		$('#recommend_limit').val(recommend_limit);
		$('#domain_name').val(domain_name);

		frm.submit();
	}

	function onLadderSave($sn)
	{
	    var ladder_min_betting = $('#tr_ladder_'+$sn+' input:text:eq(0)').val();
	    var ladder_max_betting = $('#tr_ladder_'+$sn+' input:text:eq(1)').val();
	    var ladder_max_prize = $('#tr_ladder_'+$sn+' input:text:eq(2)').val();

	    $('#sn').val($sn);
	    $('#ladder_min_betting').val(ladder_min_betting);
	    $('#ladder_max_betting').val(ladder_max_betting);
	    $('#ladder_max_prize').val(ladder_max_prize);
		$('#mode').val("ladder_save");
		frm.submit();
	}

	function onPowerballSave()
	{

		$('#mode').val("powerball_save");
		frm.submit();
	}

	function onSnailRaceSave($sn)
	{
	    var snail_race_min_betting = $('#tr_snail_'+$sn+' input:text:eq(0)').val();
	    var snail_race_max_betting = $('#tr_snail_'+$sn+' input:text:eq(1)').val();
	    var snail_race_max_prize = $('#tr_snail_'+$sn+' input:text:eq(2)').val();

	    $('#sn').val($sn);
	    $('#snail_race_min_betting').val(snail_race_min_betting);
	    $('#snail_race_max_betting').val(snail_race_max_betting);
	    $('#snail_race_max_prize').val(snail_race_max_prize);
		$('#mode').val("snail_race_save");
		frm.submit();
	}

	$(function()
	{
		$('.quantity').keypress(function(event)
		{
	  		if (event.which && (event.which  > 47 && event.which  < 58 || event.which == 8)) {
	  		}
	  		else{
	    		event.preventDefault();
	  		}
		});
	});
</script>

<div class="wrap" id="members">

	<div id="route">
		<h5>관리자 시스템 > 시스템 관리 > <b>레벨 설정</b></h5>
	</div>

	<h3>레벨목록</h3>

	<div id="search2">
		<div>
			<form action="?" method="GET" name="form2" id="form2">
				<span class="icon2">사이트</span>

<?php if($TPL_logolist_1){foreach($TPL_VAR["logolist"] as $TPL_V1){?>
						<input type="radio" name="logo" value="<?php echo $TPL_V1["name"]?>" <?php if($TPL_VAR["filter_logo"]==$TPL_V1["name"]){?> checked <?php }?>><?php echo $TPL_V1["nick"]?>

<?php }}?>


					<input name="Submit4" type="image" src="/img/btn_search.gif" class="imgType" title="검색" />
			</form>
		</div>
	</div>

	<form id="frm" name="frm" method="post" action="?">
		<input type="hidden" id="logo" name="logo" value="<?php echo $TPL_VAR["filter_logo"]?>">
		<input type="hidden" id="mode" name="mode" value="save">
		<input type="hidden" id="sn" name="sn" value="">
		<input type="hidden" id="lev_name" name="lev_name" value="">
		<input type="hidden" id="min_money" name="min_money" value="">
		<input type="hidden" id="max_money" name="max_money" value="">
		<input type="hidden" id="max_money_special" name="max_money_special" value="">
		<input type="hidden" id="max_money_single" name="max_money_single" value="">
		<input type="hidden" id="max_money_single_special" name="max_money_single_special" value="">
		<input type="hidden" id="max_bonus" name="max_bonus" value="">
		<input type="hidden" id="max_bonus_special" name="max_bonus_special" value="">
		<input type="hidden" id="charge_rate" name="charge_rate" value="">
		<input type="hidden" id="lose_rate" name="lose_rate" value="">
		<input type="hidden" id="recommend_rate" name="recommend_rate" value="">
		<input type="hidden" id="bank" name="bank" value="">
		<input type="hidden" id="bank_account" name="bank_account" value="">
		<input type="hidden" id="bank_owner" name="bank_owner" value="">
		<input type="hidden" id="min_charge" name="min_charge" value="">
		<input type="hidden" id="min_exchange" name="min_exchange" value="">
		<input type="hidden" id="recommend_limit" name="recommend_limit" value="">
        <input type="hidden" id="domain_name" name="domain_name" value="">

        <input type="hidden" id="ladder_min_betting" name="ladder_min_betting" value="">
        <input type="hidden" id="ladder_max_betting" name="ladder_max_betting" value="">
        <input type="hidden" id="ladder_max_prize" name="ladder_max_prize" value="">

        <input type="hidden" id="snail_race_min_betting" name="snail_race_min_betting" value="">
        <input type="hidden" id="snail_race_max_betting" name="snail_race_max_betting" value="">
        <input type="hidden" id="snail_race_max_prize" name="snail_race_max_prize" value="">


		<table cellspacing="1" class="tableStyle_members">
			<legend class="blind">레벨목록</legend>
			<thead>
				<tr>
					<th scope="col" class="lineRow">레벨</th>
					<th scope="col" class="lineRow">이름</th>
					<th scope="col" class="lineRow">배팅최소</th>
					<th scope="col" colspan="2">배팅최대</th>
					<th scope="col" colspan="2">배팅상한</th>
					<th scope="col" colspan="2">단폴최대</th>
					<th scope="col" class="lineRow">충전</th>
					<th scope="col" class="lineRow">낙첨</th>
					<th scope="col" class="lineRow">추천</th>
					<th scope="col" class="lineRow">은행명</th>
					<th scope="col" class="lineRow">계좌번호</th>
					<th scope="col" class="lineRow">예금주</th>
					<th scope="col" class="lineRow">최소입금</th>
					<th scope="col" class="lineRow">최소출금</th>
					<th scope="col" class="lineRow">추천제한</th>
					<th scope="col" class="lineRow">수정</th>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td>일반</td>
					<td>스페셜</td>
					<td>일반</td>
					<td>스페셜</td>
					<td>일반</td>
					<td>스페셜</td>
					<td>(%)</td>
					<td>(%)</td>
					<td>(%)</td>
					<!--<td></td>-->
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</thead>
			<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){
$TPL_domain_list_2=empty($TPL_V1["domain_list"])||!is_array($TPL_V1["domain_list"])?0:count($TPL_V1["domain_list"]);?>
					<tr id="tr_<?php echo $TPL_V1["Id"]?>">
						<td><?php echo $TPL_V1["lev"]?></td>
						<td><input style="width:50px" type="text" class="w120" size="6" value="<?php echo $TPL_V1["lev_name"]?>" /></td>
						<td><input style="width:80px" type="text" class="w120" size="10" value="<?php echo number_format($TPL_V1["lev_min_money"],0)?>"/></td>
						<td><input style="width:80px" type="text" class="w120" size="10" value="<?php echo number_format($TPL_V1["lev_max_money"],0)?>"/></td>
						<td><input style="width:80px" type="text" class="w120" size="10" value="<?php echo number_format($TPL_V1["lev_max_money_special"],0)?>"/></td>
						<td><input style="width:80px" type="text" class="w120" size="10" value="<?php echo number_format($TPL_V1["lev_max_bonus"],0)?>"/></td>
						<td><input style="width:80px" type="text" class="w120" size="10" value="<?php echo number_format($TPL_V1["lev_max_bonus_special"],0)?>"/></td>
						<td><input style="width:80px" type="text" class="w120" size="10" value="<?php echo number_format($TPL_V1["lev_max_money_single"],0)?>"/></td>
						<td><input style="width:80px" type="text" class="w120" size="10" value="<?php echo number_format($TPL_V1["lev_max_money_single_special"],0)?>"/></td>
						<td><input style="width:20px" type="text" class="w120" size="5" value="<?php echo $TPL_V1["lev_charge_mileage_rate"]?>" /></td>
						<td><input style="width:20px" type="text" class="w120" size="5" value="<?php echo $TPL_V1["lev_bet_failed_mileage_rate"]?>" /></td>
						<td>
							<input style="width:20px" type="text" class="w120" type="text" class="w120" size="2" value="<?php echo $TPL_V1["lev_join_recommend_mileage_rate_1"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>
						</td>

						<td>
							<input type="text" class="w120" size="8" value="<?php echo $TPL_V1["lev_bank"]?>"/>
						</td>
						<td>
							<input type="text" class="w120" size="12" value="<?php echo $TPL_V1["lev_bank_account"]?>"/>
						</td>
						<td>
							<input type="text" class="w120" size="6" value="<?php echo $TPL_V1["lev_bank_owner"]?>"/>
						</td>

						<td>
							<input type="text" class="w120" size="6" value="<?php echo number_format($TPL_V1["lev_bank_min_charge"],0)?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>
						</td>

						<td>
							<input type="text" class="w120" size="6" value="<?php echo number_format($TPL_V1["lev_bank_min_exchange"],0)?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>
						</td>

						<td>
							<input type="text" class="w120" size="2" value="<?php echo $TPL_V1["lev_recommend_limit"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>
						</td>
						<!--
						<td>
							<select style="width:100px" name="domain">
								<option value="<?php echo $TPL_V1["url"]?>" <?php if($TPL_V1["lev_domain"]==""){?>selected<?php }?>>도메인</option>
<?php if($TPL_domain_list_2){foreach($TPL_V1["domain_list"] as $TPL_V2){?>
								<option value="<?php echo $TPL_V1["url"]?>" <?php if($TPL_V1["lev_domain"]==$TPL_V2["url"]){?>selected<?php }?>><?php echo $TPL_V1["url"]?></option>
<?php }}?>
						</td>
						-->

						<td>
							<input type="button" class="btnStyle3" value="적용" onclick="onSave(<?php echo $TPL_V1["Id"]?>);"/></a>
						</td>
					</tr>
<?php }}?>
			</tbody>
		</table>

		<br>
		<br>
		<h3>사다리 배팅설정</h3>
		<table cellspacing="1" class="tableStyle_comment">
			<thead>
				<tr>
                    <th scope="col" >레벨</th>
                    <th scope="col">이름</th>
					<th scope="col" class="lineRow">배팅최소</th>
					<th scope="col">배팅최대</th>
					<th scope="col">배팅상한</th>
					<th scope="col" class="lineRow">수정</th>
				</tr>
			</thead>
			<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
                <tr id="tr_ladder_<?php echo $TPL_V1["Id"]?>">
                    <td><?php echo $TPL_V1["lev"]?></td>
                    <td><?php echo $TPL_V1["lev_name"]?></td>
                    <td><input  value="<?php echo number_format($TPL_V1["ladder_min_betting"],0)?>" /></td>
                    <td><input  value="<?php echo number_format($TPL_V1["ladder_max_betting"],0)?>" /></td>
                    <td><input  value="<?php echo number_format($TPL_V1["ladder_max_prize"],0)?>" /></td>
                    <td>
                        <input type="button" class="btnStyle3" value="적용" onclick="onLadderSave(<?php echo $TPL_V1["Id"]?>);" />
                    </td>
                </tr>
<?php }}?>
			</tbody>
		</table>

		<br>
		<br>
		<h3>다리다리 배팅설정</h3>
		<table cellspacing="1" class="tableStyle_comment">
			<thead>
				<tr>
                    <th scope="col">레벨</th>
                    <th scope="col">이름</th>
					<th scope="col" class="lineRow">배팅최소</th>
					<th scope="col">배팅최대</th>
					<th scope="col">배팅상한</th>
					<th scope="col" class="lineRow">수정</th>
				</tr>
			</thead>
			<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
				<tr id="tr_snail_<?php echo $TPL_V1["Id"]?>">
                    <td><?php echo $TPL_V1["lev"]?></td>
                    <td><?php echo $TPL_V1["lev_name"]?></td>
					<td><input  value="<?php echo number_format($TPL_V1["daridari_min_betting"],0)?>"/></td>
					<td><input  value="<?php echo number_format($TPL_V1["daridari_max_betting"],0)?>"/></td>
					<td><input  value="<?php echo number_format($TPL_V1["daridari_max_prize"],0)?>"/></td>
					<td>
						<input type="button" class="btnStyle3" value="적용" onclick="onDaridariSave(<?php echo $TPL_V1["Id"]?>);"/></a>
					</td>
				</tr>
<?php }}?>
			</tbody>
		</table>

		<br>
		<br>
		<h3>달팽이레이스 배팅설정</h3>
		<table cellspacing="1" class="tableStyle_comment">
			<thead>
				<tr>
                    <th scope="col">레벨</th>
                    <th scope="col">이름</th>
					<th scope="col" class="lineRow">배팅최소</th>
					<th scope="col">배팅최대</th>
					<th scope="col">배팅상한</th>
					<th scope="col" class="lineRow">수정</th>
				</tr>
			</thead>
			<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
				<tr id="tr_snail_<?php echo $TPL_V1["Id"]?>">
                    <td><?php echo $TPL_V1["lev"]?></td>
                    <td><?php echo $TPL_V1["lev_name"]?></td>
					<td><input  value="<?php echo number_format($TPL_V1["snail_race_min_betting"],0)?>"/></td>
					<td><input  value="<?php echo number_format($TPL_V1["snail_race_max_betting"],0)?>"/></td>
					<td><input  value="<?php echo number_format($TPL_V1["snail_race_max_prize"],0)?>"/></td>
					<td>
						<input type="button" class="btnStyle3" value="적용" onclick="onSnailRaceSave(<?php echo $TPL_V1["Id"]?>);"/></a>
					</td>
				</tr>
<?php }}?>
			</tbody>
		</table>
		<!--
		<br>
		<br>
		<h3>파워볼 배팅설정</h3>
		<table cellspacing="1" class="tableStyle_comment">
			<thead>
				<tr>
					<th scope="col" class="lineRow">배팅최소</th>
					<th scope="col">배팅최대</th>
					<th scope="col">배팅상한</th>
					<th scope="col" class="lineRow">수정</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input name="powerball_min_betting" value="<?php echo number_format($TPL_VAR["powerball"]["powerball_min_betting"],0)?>"/></td>
					<td><input name="powerball_max_betting" value="<?php echo number_format($TPL_VAR["powerball"]["powerball_max_betting"],0)?>"/></td>
					<td><input name="powerball_max_prize" value="<?php echo number_format($TPL_VAR["powerball"]["powerball_max_prize"],0)?>"/></td>
					<td>
						<input type="button" class="btnStyle3" value="적용" onclick="onPowerballSave();"/></a>
					</td>
				</tr>
			</tbody>
		</table>
		-->

		<span id="op1"></span>
	</form>
	<br>
	<br>
	<br>
	<div id="wrap_btn">
		<p class="left">
			<input type="button" name="open" value="추가" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="onEdit('add')"/>
			<input type="button" name="open" value="제거" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="onEdit('del')"/>

		</p>
	</div>
</div>