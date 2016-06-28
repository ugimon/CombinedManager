<?php /* Template_ 2.2.3 2016/04/28 09:33:02 C:\inetpub\combined_manager\vhost.manager\_template\content\stat\bet.html */
$TPL_league_list_1=empty($TPL_VAR["league_list"])||!is_array($TPL_VAR["league_list"])?0:count($TPL_VAR["league_list"]);
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<div class="wrap">

	<div id="route">
		<h5>관리자 시스템 > 통계 > <b>배팅 통계</b></h5>
	</div>

	<h3>배팅 통계</h3>

	<div id="search2">
		<div>
			<form action="?" method="GET" name="form2" id="form2">
				<span class="icon">사이트</span>
				<select name="filter_logo">
					<option value=""  <?php if($TPL_VAR["filter_logo"]==""){?>  selected <?php }?>>전체</option>
					<option value="totobang"  <?php if($TPL_VAR["filter_logo"]=="totobang"){?>  selected <?php }?>>포텐</option>
					<option value="poten2" <?php if($TPL_VAR["filter_logo"]=="poten2"){?> selected <?php }?>>포텐2</option>
					<option value="eclipse" <?php if($TPL_VAR["filter_logo"]=="eclipse"){?> selected <?php }?>>이클</option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<span class="icon">배팅타입</span>
				<select name="filter_category">
					<option value=""  <?php if($TPL_VAR["filter_category"]==""){?>  selected <?php }?>>전체</option>
					<option value="normal"  <?php if($TPL_VAR["filter_category"]=="normal"){?>  selected <?php }?>>조합배팅</option>
					<option value="special" <?php if($TPL_VAR["filter_category"]=="special"){?> selected <?php }?>>스페셜</option>
					<option value="live" <?php if($TPL_VAR["filter_category"]=="live"){?> selected <?php }?>>라이브</option>
					<option value="soccer_live" <?php if($TPL_VAR["filter_category"]=="soccer_live"){?> selected <?php }?>>축구실시간</option>
					<option value="ladder" <?php if($TPL_VAR["filter_category"]=="ladder"){?> selected <?php }?>>사다리</option>
					<option value="powerball" <?php if($TPL_VAR["filter_category"]=="powerball"){?> selected <?php }?>>파워볼</option>
                    <option value="dalpang" <?php if($TPL_VAR["filter_category"]=="dalpang"){?> selected <?php }?>>달팽이</option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<span class="icon">리그</span>
				<select name="filter_league">
					<option value=""  <?php if($TPL_VAR["filter_league"]==""){?>  selected <?php }?>>전체</option>
<?php if($TPL_league_list_1){foreach($TPL_VAR["league_list"] as $TPL_V1){?>
					<option value="<?php echo $TPL_V1["sn"]?>"  <?php if($TPL_VAR["filter_league"]==$TPL_V1["sn"]){?>  selected <?php }?>><?php echo $TPL_V1["name"]?></option>
<?php }}?>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<span class="icon">날짜</span><input name="begin_date" type="text" id="begin_date" class="date" value="<?php echo $TPL_VAR["begin_date"]?>" maxlength="20" onclick="new Calendar().show(this);" /> ~ 
				<input name="end_date" type="text" id="end_date" class="date" value="<?php echo $TPL_VAR["end_date"]?>" maxlength="20" onclick="new Calendar().show(this);" />
				<input name="Submit4" type="image" src="/img/btn_search.gif" class="imgType" title="검색" />
			</form>
		</div>
	</div>

	<form id="form1" name="form1" method="post" action="?act=delete_user" />
	<table cellspacing="1" class="tableStyle_normal add" summary="배팅 통계" />
	<legend class="blind">배팅 통계</legend>
	<thead>
		<tr>
		  <th scope="col">날짜</th>
		  <th scope="col">배팅총액</th>
		  <th scope="col">참여인원</th>
		  <th scope="col">배팅건수</th>
		  <th scope="col">대기총액</th>
		  <th scope="col">대기건수</th>
		  <th scope="col">당첨총액</th>
		  <th scope="col">당첨건수</th>
		  <th scope="col">낙첨총액</th>
		  <th scope="col">낙첨건수</th>
		  <th scope="col">적특총액</th>
		  <th scope="col">적특건수</th>
		  <th scope="col">배팅수익</th>
		</tr>
	</thead>
	<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
			<tr>
				<td><?php echo $TPL_V1["regdate"]?>(<?php echo $TPL_V1["date_name"]?>)</td>
				<td><font color='#4374D9'><?php echo number_format($TPL_V1["total_bet_money"],0)?></font></td>
				<td><font color='#4374D9'><?php echo number_format($TPL_V1["total_member_count"],0)?></font></td>
				<td><font color='#4374D9'><?php echo number_format($TPL_V1["total_bet_cnt"],0)?></font></td>
				<td><?php echo number_format($TPL_V1["0"]["bet_money"],0)?></td>
				<td><?php echo number_format($TPL_V1["0"]["bet_cnt"],0)?></td>
				<td><font color='#CC3D3D'><?php echo number_format($TPL_V1["1"]["win_money"],0)?></font></td>
				<td><font color='#CC3D3D'><?php echo number_format($TPL_V1["1"]["win_cnt"],0)?></font></td>
				<td><font color='#4374D9'><?php echo number_format($TPL_V1["2"]["bet_money"],0)?></font></td>
				<td><font color='#4374D9'><?php echo number_format($TPL_V1["2"]["bet_cnt"],0)?></font></td>
				<td><font color='#9FC93C'><?php echo number_format($TPL_V1["4"]["win_money"],0)?></font></td>
				<td><font color='#9FC93C'><?php echo number_format($TPL_V1["4"]["win_cnt"],0)?></font></td>
				<td><?php if($TPL_V1["total_bet_money"]-$TPL_V1["1"]["win_money"]-$TPL_V1["4"]["win_money"]>0){?><font color='#4374D9'><?php }else{?><font color='#CC3D3D'><?php }?>
					<?php echo number_format($TPL_V1["total_bet_money"]-$TPL_V1["1"]["win_money"]-$TPL_V1["4"]["win_money"],0)?></font>
				</td>
			 </tr>
<?php }}?>
	</tbody>
	<tfoot>
		<tr>
			<td>합계</td>
			<td><font color='#4374D9'><?php echo number_format($TPL_VAR["totalList"]["total_bet_money"],0)?></font></td>
			<td><font color='#4374D9'><?php echo number_format($TPL_VAR["totalList"]["total_member_count"],0)?></font></td>
			<td><font color='#4374D9'><?php echo number_format($TPL_VAR["totalList"]["total_bet_cnt"],0)?></font></td>
			<td><?php echo number_format($TPL_VAR["totalList"]["total_bet_money_0"],0)?></td>
			<td><?php echo number_format($TPL_VAR["totalList"]["total_bet_cnt_0"],0)?></td>
			<td><font color='#CC3D3D'><?php echo number_format($TPL_VAR["totalList"]["total_win_money_1"],0)?></font></td>
			<td><font color='#CC3D3D'><?php echo number_format($TPL_VAR["totalList"]["total_win_cnt_1"],0)?></font></td>
			<td><font color='#4374D9'><?php echo number_format($TPL_VAR["totalList"]["total_lose_money"],0)?></font></td>
			<td><font color='#4374D9'><?php echo number_format($TPL_VAR["totalList"]["total_lose_cnt"],0)?></font></td>
			<td><font color='#9FC93C'><?php echo number_format($TPL_VAR["totalList"]["total_win_money_4"],0)?></font></td>
			<td><font color='#9FC93C'><?php echo number_format($TPL_VAR["totalList"]["total_win_cnt_4"],0)?></font></td>
			<td><?php if($TPL_VAR["totalList"]["total_win_money"]>0){?><font color='#4374D9'><?php }else{?><font color='#CC3D3D'><?php }?><?php echo number_format($TPL_VAR["totalList"]["total_win_money"],0)?></font></td>
		</tr>
	</tfoot>
	</table>
	</form>

</div>