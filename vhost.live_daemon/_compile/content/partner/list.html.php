<?php /* Template_ 2.2.3 2014/01/07 19:44:08 D:\www\vhost.manager\_template\content\partner\list.html */
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<script>document.title = '총판관리-총판목록';</script>
<script>
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
	
	function toggle(id)
	{
		$( "#"+id ).slideToggle(100);
	}	
	
</script>

<div class="wrap">

	<div id="route">
		<h5>관리자 시스템 > 총판 관리 > <b>총판 목록</b></h5>
	</div>

	<h3>총판 목록</h3>

	<form id="form1" name="form1" method="post" action="?act=delete_user">
	<table cellspacing="1" class="tableStyle_members" summary="총판 목록">
	<legend class="blind">총판 목록</legend>
	<thead>
		<tr>
			<th scope="col">사이트</th>
			<th scope="col">아이디</th>
			<th scope="col">이름</th>
			<th scope="col">회원수</th>
			<th scope="col">입금회원</th>
			<th scope="col">입금금액</th>
			<th scope="col">출금금액</th>
			<th scope="col">상태</th>
			<th scope="col">가입날자</th>
			<th scope="col">처리</th>
		</tr>
	</thead>
	<tbody>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){
$TPL_item_2=empty($TPL_V1["item"])||!is_array($TPL_V1["item"])?0:count($TPL_V1["item"]);?>
			<tr>
				<td><?php if($TPL_V1["logo"]=='totobang'){?>킹덤<?php }elseif($TPL_V1["logo"]=='orange'){?>아레나<?php }?></td>
				<td><a href="javascript:open_window('/partner/memberDetails?idx=<?php echo $TPL_V1["Idx"]?>',640,440)"><?php echo $TPL_V1["rec_id"]?></a></td>
				<td onclick="toggle('d_<?php echo $TPL_V1["Idx"]?>')"><?php echo $TPL_V1["rec_name"]?></td>
				<td onclick="toggle('d_<?php echo $TPL_V1["Idx"]?>')"><?php echo $TPL_V1["member_count"]?></td>
				<td onclick="toggle('d_<?php echo $TPL_V1["Idx"]?>')"><?php echo $TPL_V1["charge_count"]?></td>
				<td onclick="toggle('d_<?php echo $TPL_V1["Idx"]?>')"><?php echo number_format($TPL_V1["charge_sum"],0)?></td>
				<td onclick="toggle('d_<?php echo $TPL_V1["Idx"]?>')"><?php echo number_format($TPL_V1["exchange_sum"],0)?></td>
				<td>
<?php if($TPL_V1["status"]==0){?><font color='red'>정지</font>
<?php }elseif($TPL_V1["status"]==1){?>정상
<?php }elseif($TPL_V1["status"]==2){?>신청
<?php }?>
				</td>
				<td><?php echo $TPL_V1["reg_date"]?></td>
				<td>
<?php if($TPL_V1["status"]==1){?><a href='?act=stop&id=<?php echo $TPL_V1["rec_id"]?>&send=0'><img src='/img/btn_s_stop.gif' title='정지'></a>						
<?php }else{?><a href='?act=stop&id=<?php echo $TPL_V1["rec_id"]?>&send=1'><img src='/img/btn_s_normal.gif' title='정상'></a>
<?php }?>&nbsp;<a href="javascript:void(0)" onclick="go_del('?act=del&id=<?php echo $TPL_V1["rec_id"]?>');return false;"><img src="/img/btn_s_del.gif" title="삭제"></a>&nbsp;
						<a href="javascript:void(0);" onclick="open_window('/partner/memoadd_acc?toid=<?php echo $TPL_V1["rec_id"]?>',650,450)"><img src="/img/btn_s_memo.gif" title="메모"></a>
				</td>
			</tr>			
			
			<tr id="d_<?php echo $TPL_V1["Idx"]?>" style="display:none;" class="gameDetail">
				<td colspan="12">					
					<table cellspacing="1" id="d_<?php echo $TPL_V1["Idx"]?>">
						<tr bgcolor="#ade8a0">				  
							<th>ID</th>
							<th>닉네임</th>
							<th>보유금액</th>						
							<th>회원등급</th>
							<th>가입일</th>
							<th>가입IP</th>
							<th>상태</th>
							<th>입금</th>
							<th>출금</th>
							<th>배팅</th>
						</tr>
<?php if($TPL_item_2){foreach($TPL_V1["item"] as $TPL_V2){?>														
						<tr bgcolor="#ede8e8">				
							<td><?php echo $TPL_V2["uid"]?></td>
							<td><?php echo $TPL_V2["nick"]?></td>
							<td><?php echo number_format($TPL_V2["g_money"],0)?>원</td>
							<td><?php echo $TPL_VAR["arr_mem_lev"][$TPL_V2["mem_lev"]]?></td>
							<td><?php echo $TPL_V2["regdate"]?></td>
							<td><?php echo $TPL_V2["mem_ip"]?></td>
							<td>
<?php if($TPL_V2["mem_status"]=='N'){?>정상
<?php }elseif($TPL_V2["mem_status"]=='S'){?>정지
<?php }elseif($TPL_V2["mem_status"]=='B'){?>불량
<?php }elseif($TPL_V2["mem_status"]=='W'){?>신규
<?php }elseif($TPL_V2["mem_status"]=='D'){?>탈퇴
<?php }?>
							</td>
							<td><?php echo number_format($TPL_V2["charge_money"],0)?>원</td>
							<td><?php echo number_format($TPL_V2["exchange_money"],0)?>원</td>
							<td><?php echo number_format($TPL_V2["bet_money"],0)?>원</td>
						</tr>															
<?php }}?>
					</table>				
				</td>
			</tr>			
<?php }}?>
	
	</tbody>
	</table>
</form>
	<div id="pages">
		<?php echo $TPL_VAR["pagelist"]?>

	</div>

	<div id="wrap_btn">
  	<input type="submit" name="del_Submit" value="총판 등록" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="javascript:window.open('/partner/popup_join','memo','width=650,height=350')"/>
  </div>

</div>