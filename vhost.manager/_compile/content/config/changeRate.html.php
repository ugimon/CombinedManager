<?php /* Template_ 2.2.3 2016/03/07 10:27:12 C:\inetpub\combined_manager\vhost.manager\_template\content\config\changeRate.html */?>
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

<form id="frm" name="frm" method="post" action="?">
	<input type="hidden" id="mode" name="mode" value="save">
	<div class="wrap" id="members" style="width:1100px;">

		<div id="route">
			<h5>관리자 시스템 > 시스템 관리 > <b>기본 환수율 설정</b></h5>
		</div>

		<h3>일반</h3>
		
		<table cellspacing="1" class="tableStyle_members">
			<legend class="blind">일반</legend>
			<thead>
				<tr>
					<th scope="col" class="lineRow">구분</th>
					<th scope="col" class="lineRow">승무패</th>
					<th scope="col" class="lineRow">핸디캡</th>
					<th scope="col" class="lineRow">언더오버</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>환수율</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[0]" size="6" value="<?php echo $TPL_VAR["list"][0]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[0]" size="6" value="<?php echo $TPL_VAR["list"][0]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[0]" size="6" value="<?php echo $TPL_VAR["list"][0]["away_rate"]?>" />
					</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[1]" size="6" value="<?php echo $TPL_VAR["list"][1]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[1]" size="6" value="<?php echo $TPL_VAR["list"][1]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[1]" size="6" value="<?php echo $TPL_VAR["list"][1]["away_rate"]?>" />
					</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[2]" size="6" value="<?php echo $TPL_VAR["list"][2]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[2]" size="6" value="<?php echo $TPL_VAR["list"][2]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[2]" size="6" value="<?php echo $TPL_VAR["list"][2]["away_rate"]?>" />
					</td>
				</tr>
				<tr>
					<td>연동설정</td>
					<td>
						<input type="checkbox" name="allow_rate_change[0]" <?php if($TPL_VAR["list"][0]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[0]" <?php if($TPL_VAR["list"][0]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[0]" <?php if($TPL_VAR["list"][0]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[0]" <?php if($TPL_VAR["list"][0]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
					<td>
						<input type="checkbox" name="allow_rate_change[1]" <?php if($TPL_VAR["list"][1]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[1]" <?php if($TPL_VAR["list"][1]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[1]" <?php if($TPL_VAR["list"][1]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[1]" <?php if($TPL_VAR["list"][1]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
					<td>
						<input type="checkbox" name="allow_rate_change[2]" <?php if($TPL_VAR["list"][2]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[2]" <?php if($TPL_VAR["list"][2]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[2]" <?php if($TPL_VAR["list"][2]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[2]" <?php if($TPL_VAR["list"][2]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
				</tr>
			</tbody>
		</table>
		<br/><br/>

		<h3>스페셜</h3>
		
		<table cellspacing="1" class="tableStyle_members">
			<legend class="blind">스페셜</legend>
			<thead>
				<tr>
					<th scope="col" class="lineRow">구분</th>
					<th scope="col" class="lineRow">승무패</th>
					<th scope="col" class="lineRow">핸디캡</th>
					<th scope="col" class="lineRow">언더오버</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>환수율</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[3]" size="6" value="<?php echo $TPL_VAR["list"][3]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[3]" size="6" value="<?php echo $TPL_VAR["list"][3]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[3]" size="6" value="<?php echo $TPL_VAR["list"][3]["away_rate"]?>" />
					</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[4]" size="6" value="<?php echo $TPL_VAR["list"][4]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[4]" size="6" value="<?php echo $TPL_VAR["list"][4]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[4]" size="6" value="<?php echo $TPL_VAR["list"][4]["away_rate"]?>" />
					</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[5]" size="6" value="<?php echo $TPL_VAR["list"][5]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[5]" size="6" value="<?php echo $TPL_VAR["list"][5]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[5]" size="6" value="<?php echo $TPL_VAR["list"][5]["away_rate"]?>" />
					</td>
				</tr>
				<tr>
					<td>연동설정</td>
					<td>
						<input type="checkbox" name="allow_rate_change[3]" <?php if($TPL_VAR["list"][3]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[3]" <?php if($TPL_VAR["list"][3]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[3]" <?php if($TPL_VAR["list"][3]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[3]" <?php if($TPL_VAR["list"][3]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
					<td>
						<input type="checkbox" name="allow_rate_change[4]" <?php if($TPL_VAR["list"][4]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[4]" <?php if($TPL_VAR["list"][4]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[4]" <?php if($TPL_VAR["list"][4]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[4]" <?php if($TPL_VAR["list"][4]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
					<td>
						<input type="checkbox" name="allow_rate_change[5]" <?php if($TPL_VAR["list"][5]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[5]" <?php if($TPL_VAR["list"][5]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[5]" <?php if($TPL_VAR["list"][5]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[5]" <?php if($TPL_VAR["list"][5]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
				</tr>
			</tbody>
		</table>
		<br/><br/>

		<h3>멀티</h3>
		
		<table cellspacing="1" class="tableStyle_members">
			<legend class="blind">멀티</legend>
			<thead>
				<tr>
					<th scope="col" class="lineRow">구분</th>
					<th scope="col" class="lineRow">승무패</th>
					<th scope="col" class="lineRow">핸디캡</th>
					<th scope="col" class="lineRow">언더오버</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>환수율</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[6]" size="6" value="<?php echo $TPL_VAR["list"][6]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[6]" size="6" value="<?php echo $TPL_VAR["list"][6]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[6]" size="6" value="<?php echo $TPL_VAR["list"][6]["away_rate"]?>" />
					</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[7]" size="6" value="<?php echo $TPL_VAR["list"][7]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[7]" size="6" value="<?php echo $TPL_VAR["list"][7]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[7]" size="6" value="<?php echo $TPL_VAR["list"][7]["away_rate"]?>" />
					</td>
					<td>
						<input style="width:80px;text-align:center;" type="text" name="home_rate[8]" size="6" value="<?php echo $TPL_VAR["list"][8]["home_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="draw_rate[8]" size="6" value="<?php echo $TPL_VAR["list"][8]["draw_rate"]?>" />
						<input style="width:80px;text-align:center;" type="text" name="away_rate[8]" size="6" value="<?php echo $TPL_VAR["list"][8]["away_rate"]?>" />
					</td>
				</tr>
				<tr>
					<td>연동설정</td>
					<td>
						<input type="checkbox" name="allow_rate_change[6]" <?php if($TPL_VAR["list"][6]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[6]" <?php if($TPL_VAR["list"][6]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[6]" <?php if($TPL_VAR["list"][6]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[6]" <?php if($TPL_VAR["list"][6]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
					<td>
						<input type="checkbox" name="allow_rate_change[7]" <?php if($TPL_VAR["list"][7]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[7]" <?php if($TPL_VAR["list"][7]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[7]" <?php if($TPL_VAR["list"][7]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[7]" <?php if($TPL_VAR["list"][7]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
					<td>
						<input type="checkbox" name="allow_rate_change[8]" <?php if($TPL_VAR["list"][8]["allow_rate_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 배당연동
						<input type="checkbox" name="allow_base_change[8]" <?php if($TPL_VAR["list"][8]["allow_base_change"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 기준점연동
						<input type="checkbox" name="allow_betting_auto[8]" <?php if($TPL_VAR["list"][8]["allow_betting_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 발매연동
						<input type="checkbox" name="allow_magam_auto[8]" <?php if($TPL_VAR["list"][8]["allow_magam_auto"]=='Y'){?>checked="checked"<?php }?> value='Y' /> 마감연동
					</td>
				</tr>
			</tbody>
		</table>
		<br/><br/>
		<input type="submit" class="btnStyle1" value="적용" style="float:right;"/>
	</div>
</form>