<?php /* Template_ 2.2.3 2016/03/07 10:27:12 C:\inetpub\combined_manager\vhost.manager\_template\content\config\admin_ip.html */
$TPL_list_1=empty($TPL_VAR["list"])||!is_array($TPL_VAR["list"])?0:count($TPL_VAR["list"]);?>
<script>
	function on_insert()
	{
		document.frm.act.value="insert";
		document.frm.submit();
	}
	
	function on_modify(modify_sn)
	{
		var modify_ip = $("#"+modify_sn+"_ip").val();
		
		document.frm.act.value="modify";
		document.frm.modify_sn.value=modify_sn;
		document.frm.modify_ip.value=modify_ip
		document.frm.submit();
	}
	
	function on_delete(delete_sn)
	{
		document.frm.act.value="delete";
		document.frm.delete_sn.value=delete_sn;
		document.frm.submit();
	}
</script>

<div class="wrap">

	<div id="route">
		<h5>관리자 시스템 > 시스템 관리 > <b>포인트 설정</b></h5>
	</div>

	<h3>아이피 관리</h3>

	<form  method="post"  name="frm" action="/config/admin_ip">
		<input type="hidden" name="act">
		<input type="hidden" name="modify_sn">
		<input type="hidden" name="delete_sn">
		<input type="hidden" name="modify_ip">
		<table cellspacing="1" class="tableStyle_membersWrite" summary="아이피 관리">
			<tr>
				<th>아이피 추가</th>
				<td>
					<input type="text" class="w120" name="insert_ip" size="10" />
					<input type="button" value="추가" onClick="on_insert()">
				</td>
			</tr>
		</table>
		
		<table cellspacing="1" class="tableStyle_membersWrite" summary="아이피 관리">
<?php if($TPL_list_1){foreach($TPL_VAR["list"] as $TPL_V1){?>
			<tr>
				<th>아이피</th>
				<td>
					<input type="text" class="w120" name="<?php echo $TPL_V1["sn"]?>_ip" id="<?php echo $TPL_V1["sn"]?>_ip" size="10" value="<?php echo $TPL_V1["ip"]?>" onKeyUp=" value=value.replace(/[^\d\.]/g,'')" />
					<input type="button" value="수정" onClick="on_modify(<?php echo $TPL_V1["sn"]?>);">
					<input type="button" value="삭제" onClick="on_delete(<?php echo $TPL_V1["sn"]?>);">
				</td>
			</tr>
<?php }}?>
		</table>
	</form>
</div>