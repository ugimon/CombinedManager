<?php /* Template_ 2.2.3 2016/03/07 10:27:14 C:\inetpub\combined_manager\vhost.manager\_template\content\partner\popup_partner_member_list.html */
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<div class="wrap">
	<div id="route">
		<h5>관리자 시스템 > 총판 관리 > <b>총판 회원목록</b></h5>
	</div>

	<h3><?php echo $TPL_VAR["partner_name"]?> 총판 회원목록</h3>
		<table cellspacing="1" class="tableStyle_members" summary="총판 회원목록">
		<thead>
			<tr>
				<th scope="col">ID</th>
				<th scope="col">닉네임</th>
				<th scope="col">보유금액</th>						
				<th scope="col">회원등급</th>
				<th scope="col">가입일</th>
				<th scope="col">가입IP</th>
				<th scope="col">상태</th>
				<th scope="col">입금</th>
				<th scope="col">출금</th>
				<th scope="col">배팅</th>
			</tr>
		</thead>
		<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
			<tr>
				<td><?php echo $TPL_V1["uid"]?></td>
				<td><?php echo $TPL_V1["nick"]?></td>
				<td><?php echo number_format($TPL_V1["g_money"],0)?>원</td>
				<td><?php echo $TPL_VAR["arr_mem_lev"][$TPL_V1["mem_lev"]]?></td>
				<td><?php echo $TPL_V1["regdate"]?></td>
				<td><?php echo $TPL_V1["mem_ip"]?></td>
				<td>
<?php if($TPL_V1["mem_status"]=='N'){?>정상
<?php }elseif($TPL_V1["mem_status"]=='S'){?>정지
<?php }elseif($TPL_V1["mem_status"]=='B'){?>불량
<?php }elseif($TPL_V1["mem_status"]=='W'){?>신규
<?php }elseif($TPL_V1["mem_status"]=='D'){?>탈퇴
<?php }elseif($TPL_V1["mem_status"]=='G'){?>테스터
<?php }?>
				</td>
				<td><?php echo number_format($TPL_V1["charge_money"],0)?>원</td>
				<td><?php echo number_format($TPL_V1["exchange_money"],0)?>원</td>
				<td><?php echo number_format($TPL_V1["bet_money"],0)?>원</td>
			</tr>
<?php }}?>
		</tbody>
		</table>
		
	<div id="pages">
		<?php echo $TPL_VAR["pagelist"]?>

	</div>
</div>