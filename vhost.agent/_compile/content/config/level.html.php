<?php /* Template_ 2.2.3 2013/11/28 15:25:54 D:\www\vhost.manager\_template\content\config\level.html */
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
		/* 등급별 다폴더 마일리지를 사용하는 경우

		var folder_bonus3 						= $('#tr_'+$sn+' input:text:eq(11)').val();
		var folder_bonus4 						= $('#tr_'+$sn+' input:text:eq(12)').val();
		var folder_bonus5 						= $('#tr_'+$sn+' input:text:eq(13)').val();
		var folder_bonus6 						= $('#tr_'+$sn+' input:text:eq(14)').val();
		var folder_bonus7 						= $('#tr_'+$sn+' input:text:eq(15)').val();
		var folder_bonus8 						= $('#tr_'+$sn+' input:text:eq(16)').val();
		var folder_bonus9 						= $('#tr_'+$sn+' input:text:eq(17)').val();
		var folder_bonus10 						= $('#tr_'+$sn+' input:text:eq(18)').val();
		var bank 											= $('#tr_'+$sn+' input:text:eq(19)').val();
		var bank_account 							= $('#tr_'+$sn+' input:text:eq(20)').val();
		var bank_owner 								= $('#tr_'+$sn+' input:text:eq(21)').val();
		var min_charge 								= $('#tr_'+$sn+' input:text:eq(22)').val();
		var min_exchange 							= $('#tr_'+$sn+' input:text:eq(23)').val();
		var recommend_limit 					= $('#tr_'+$sn+' input:text:eq(24)').val();
		*/
		
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
		
		/*
		$('#folder_bonus3').val(folder_bonus3);
		$('#folder_bonus4').val(folder_bonus4);
		$('#folder_bonus5').val(folder_bonus5);
		$('#folder_bonus6').val(folder_bonus6);
		$('#folder_bonus7').val(folder_bonus7);
		$('#folder_bonus8').val(folder_bonus8);
		$('#folder_bonus9').val(folder_bonus9);
		$('#folder_bonus10').val(folder_bonus10);
		*/
		$('#bank').val(bank);
		$('#bank_account').val(bank_account);
		$('#bank_owner').val(bank_owner);
		$('#min_charge').val(min_charge);
		$('#min_exchange').val(min_exchange);
		$('#recommend_limit').val(recommend_limit);
		$('#domain_name').val(domain_name);
				
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
	
	<form id="frm" name="frm" method="post" action="?">
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
	<!--
	<input type="hidden" id="folder_bonus3" name="folder_bonus3" value="">
	<input type="hidden" id="folder_bonus4" name="folder_bonus4" value="">
	<input type="hidden" id="folder_bonus5" name="folder_bonus5" value="">
	<input type="hidden" id="folder_bonus6" name="folder_bonus6" value="">
	<input type="hidden" id="folder_bonus7" name="folder_bonus7" value="">
	<input type="hidden" id="folder_bonus8" name="folder_bonus8" value="">
	<input type="hidden" id="folder_bonus9" name="folder_bonus9" value="">
	<input type="hidden" id="folder_bonus10" name="folder_bonus10" value="">
	-->
	<input type="hidden" id="bank" name="bank" value="">
	<input type="hidden" id="bank_account" name="bank_account" value="">
	<input type="hidden" id="bank_owner" name="bank_owner" value="">
	<input type="hidden" id="min_charge" name="min_charge" value="">
	<input type="hidden" id="min_exchange" name="min_exchange" value="">
	<input type="hidden" id="recommend_limit" name="recommend_limit" value="">
	<input type="hidden" id="domain_name" name="domain_name" value="">
	
	<table cellspacing="1" class="tableStyle_members">
		<legend class="blind">레벨목록</legend>
		<thead>
			<tr>
				<th scope="col" class="lineRow">레벨</th>
				<th scope="col" class="lineRow">레벨이름</th>
				<th scope="col" class="lineRow">배팅최소</th>
				<th scope="col" colspan="2">배팅최대</th>
				<th scope="col" colspan="2">배팅상한</th>
				<th scope="col" colspan="2">단폴최대</th>
				<th scope="col" class="lineRow">충전+(%)</th>
				<th scope="col" class="lineRow">낙첨+(%)</th>
				<th scope="col" class="lineRow">추천+(%)</th>
				<!--<th scope="col" class="lineRow">폴더보너스+(%)</th>-->
				<th scope="col" class="lineRow">은행명</th>
				<th scope="col" class="lineRow">계좌번호</th>
				<th scope="col" class="lineRow">예금주</th>
				<th scope="col" class="lineRow">최소입금</th>
				<th scope="col" class="lineRow">최소출금</th>
				<th scope="col" class="lineRow">추천제한</th>
				<th scope="col" class="lineRow">도메인</th>
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
				<td></td>
				<td></td>
				<td></td>
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
					<td><input style="width:20px" type="text" class="w120" size="5" value="<?php echo $TPL_V1["lev_charge_mileage_rate"]?>" />%</td>
					<td><input style="width:20px" type="text" class="w120" size="5" value="<?php echo $TPL_V1["lev_bet_failed_mileage_rate"]?>" />%</td>
					<td>
						<input style="width:20px" type="text" class="w120" type="text" class="w120" size="2" value="<?php echo $TPL_V1["lev_join_recommend_mileage_rate_1"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>%
					</td>
					<!--
					<td>
						3 : <input style="width:20px;text-align:right;" type="text"  class="w120" size="2" value="<?php echo $TPL_V1["lev_folder_bonus_3"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>% /
						4 : <input style="width:20px;text-align:right;" type="text"  class="w120" size="2" value="<?php echo $TPL_V1["lev_folder_bonus_4"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>% /
						5 : <input style="width:20px;text-align:right;" type="text"  class="w120" size="2" value="<?php echo $TPL_V1["lev_folder_bonus_5"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>% /
						6 : <input style="width:20px;text-align:right;" type="text"  class="w120" size="2" value="<?php echo $TPL_V1["lev_folder_bonus_6"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>% /
						7 : <input style="width:20px;text-align:right;" type="text"  class="w120" size="2" value="<?php echo $TPL_V1["lev_folder_bonus_7"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>% /
						8 : <input style="width:20px;text-align:right;" type="text"  class="w120" size="2" value="<?php echo $TPL_V1["lev_folder_bonus_8"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>% /
						9 : <input style="width:20px;text-align:right;" type="text"  class="w120" size="2" value="<?php echo $TPL_V1["lev_folder_bonus_9"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>% /
						10 : <input style="width:20px;text-align:right;" ype="text" class="w120" size="2" value="<?php echo $TPL_V1["lev_folder_bonus_10"]?>" onkeypress="javascript:pressNumberCheck();" style="IME-MODE: disabled;"/>
					</td>
					-->
					
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
					
					<td>
						<select style="width:100px" name="domain">
							<option value="<?php echo $TPL_V1["url"]?>" <?php if($TPL_V1["lev_domain"]==""){?>selected<?php }?>>도메인</option>
<?php if($TPL_domain_list_2){foreach($TPL_V1["domain_list"] as $TPL_V2){?>
							<option value="<?php echo $TPL_V1["url"]?>" <?php if($TPL_V1["lev_domain"]==$TPL_V2["url"]){?>selected<?php }?>><?php echo $TPL_V1["url"]?></option>
<?php }}?>
					</td>
					
					<td>
						<input type="button" class="btnStyle3" value="적용" onclick="onSave(<?php echo $TPL_V1["Id"]?>);"/></a>
					</td>
				</tr>
<?php }}?>
		</tbody>
	</table>
	
	<span id="op1"></span>
	</form>
	
	<div id="wrap_btn">
		<p class="left">
			<input type="button" name="open" value="추가" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="onEdit('add')"/>
			<input type="button" name="open" value="제거" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="onEdit('del')"/>
			
		</p>
	</div>	
</div>