<?php /* Template_ 2.2.3 2016/03/07 10:27:14 C:\inetpub\combined_manager\vhost.manager\_template\content\memo\list.html */
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<script>document.title = '회원관리-받은 쪽지함';</script>

<script type="text/javascript" src="/js/selectAll.js"></script>
<script type="text/javascript" src="/js/is_show.js"></script>
<script type="text/javascript" src="/js/js.js"></script>
<script>

var number=<?php if($TPL_VAR["total"]>$TPL_VAR["perpage"]){?> <?php echo $TPL_VAR["perpage"]?> <?php }else{?> <?php echo $TPL_VAR["total"]?> <?php }?>;

function LMYC() {
	
var lbmc;
    for (i=1;i<=number;i++) {
        lbmc = eval('LM' + i);
        lbmc.style.display = 'none';
    }
}
 
function ShowFLT(i) {
	alert(i);
  lbmc = 'LM'+i;
  alert(lbmc);
  /*
  if ($('#lbmc).is(':visible')) {
 	alert("success");
  }
  else {
	alert("fail");
  }
  */
}
</script>

<div class="wrap" id="members_rmemo">
	
	


	<div id="route">
		<h5>관리자 시스템 > 회원 관리 > 쪽지 관리 > <b>받은 쪽지함</b></h5>
	</div>

	<h3>받은 쪽지함</h3>

	<ul id="tab">
		<li><a href="/memo/list" id="members_rmemo">받은쪽지함</a></li>
		<li><a href="/memo/sendlist" id="members_smemo">보낸쪽지함</a></li>
	</ul>
	<div id="search">
		<div class="wrap">
			<form action="?" method="GET" name="form2" id="form2">
				
				<!-- 키워드 검색 -->
				<select name="field">
					<option value="uid" 				<?php if($TPL_VAR["field"]=="member_id"){?> selected <?php }?>>아이디</option>
					<option value="nick" 				<?php if($TPL_VAR["field"]=="nick"){?> selected <?php }?>>닉네임</option>
					<option value="bank_member" <?php if($TPL_VAR["field"]=="bank_member"){?> selected <?php }?>>예금주</option>
				</select>
				<input name="keyword" type="text" id="key" class="name" value="<?php echo $TPL_VAR["keyword"]?>" maxlength="20" onmouseover="this.focus()"/>
				
				<!-- 검색버튼 -->
				<input name="Submit4" type="image" src="/img/btn_search.gif" class="imgType" title="검색" />
			</form>
		</div>
	</div>

	<div id="table_sort">
		<form action="?" method="GET" name="form3" id="form3">
		<span class="icon">출력</span><input name="perpage" type="text" id="perpage"  class="sortInput" onkeyup="if(event.keyCode !=37 && event.keyCode != 39) value=value.replace(/\D/g,'');" maxlength="3" value="<?php echo $TPL_VAR["perpage"]?>" onmouseover="this.focus()" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/\D/g,''))"><input type="image" name="Submitok" src="/img/btn_s_sort.gif" class="imgType" title="정렬"/>
		</form>
	</div>
	
	<form id="form1" name="form1" method="get" action="?">
	<input type="hidden" name="act" value="del">
	<table cellspacing="1" class="tableStyle_normal">
	<legend class="blind">받은 쪽지함 - 제목</legend>
	<thead>
		<tr>
			<th scope="col" class="check"><input type="checkbox" name="chkAll" title="전체선택" onClick="selectAll()"/></th>
			<th width="15%" scope="col">받는이</th>
			<th width="15%" scope="col">보낸이</th>
			<th width="34%" scope="col">제목</th>
			<th width="15%" scope="col">보낸 시간</th>
			<th width="10%" scope="col">상태</th>
			<th width="20%" scope="col">처리</th>
		</tr>
	</thead>
	</table>
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
		<table cellspacing="1" class="tableStyle_normal" summary="받은 쪽지함">
			<legend class="blind">받은 쪽지함 - 내용</legend>
			<tbody>
				<tr onClick="javascript:ShowFLT(<?php echo $TPL_V1["idx"]?>)">
					<td class="check"><input name="y_id[]" type="checkbox" id="y_id" value="<?php echo $TPL_V1["mem_idx"]?>"  onclick="javascript:chkRow(this);"/></td>
					<td width="15%"><?php echo $TPL_V1["toid"]?></td>
					<td width="15%"><?php echo $TPL_V1["fromid"]?></td>
					<td width="34%"><span class="subject"><?php echo mb_strimwidth($TPL_V1["title"],0,36,"..","utf-8")?></span></td>
					<td width="15%"><?php echo $TPL_V1["writeday"]?></td>
					<td width="10%">
<?php if($TPL_V1["newreadnum"]==0){?>
							<font color='red'>미답변</font>
<?php }else{?>
							완료
<?php }?>
					</td>
					<td width="20%"><a href="javascript:open_window('/memo/popup_write?idx=<?php echo $TPL_V1["mem_idx"]?>&userid=<?php echo $TPL_V1["fromid"]?>',650,300)"><img src="/img/btn_s_answer.gif" title="답변"></a>&nbsp;
						<a href="javascript:confirm('정말 삭제하시겠습니까?');location.href='/memo/list?act=onedel&idx=<?php echo $TPL_V1["mem_idx"]?>';">
							<img src="/img/btn_s_del.gif" title="삭제">
						</a>
					</td>
				</tr>
			</tbody>
			</table>
		
			<table id="LM<?php echo $TPL_V1["idx"]?>" style="display:none" class="memo_answer">
				<tr class="line">
					<th valign="top">제목 :</th>
					<td><?php echo mb_strimwidth($TPL_V1["title"],0,36,"..","utf-8")?></td>
				</tr>
				<tr>
					<th valign="top">내용 :</th>
					<td><?php echo $TPL_V1["content"]?></td>
				</tr>
			</table>
<?php }}?>
	<div id="pages">
		<?php echo $TPL_VAR["pagelist"]?>

	</div>
<!--    
	<div id="wrap_btn">
		<p class="left">
			<input type="button" name="open" value="삭  제" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="isChm()"/>
		</p>
	</div>
-->
    </form>    
</div>