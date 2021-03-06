<?php /* Template_ 2.2.3 2016/03/07 11:27:12 C:\inetpub\web\3. Poten\www\vhost.manager\_template\content\partner\member_childRec.html */
$TPL_childlist_1=empty($TPL_VAR["childlist"])||!is_array($TPL_VAR["childlist"])?0:count($TPL_VAR["childlist"]);?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/css/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="/js/js.js"></script>
	<title>하부총판 설정</title>
	<script>
			
		function check()
		{
			var fm=document.frm;
			if(fm.pwd.value !=""){
				if(fm.pwd.value.length<6){
					alert("비밀번호는 6자리 이상입니다");
					fm.pwd.focus();
					return;
				}
			}
			if(fm.rec_name.value=="")
			{
				alert("이름은 필수 입력 항입니다.");
				fm.rec_name.focus();
				return;
			}
			if(fm.default_rate.value=="")
			{
				alert("정산률을 넣어주세요.");
				fm.default_rate.focus();
				return;
			}
			fm.submit();
		}

		function child_add()
		{
			if($("#selected_rec option:selected").val() == 0)
			{
				alert('추가 할 하부총판을 선택하세요.');
				return;
			}
			
			if($("#new_rec_sn").val() != 0)
			{
				if(!confirm('다른 총판의 하부총판입니다. 이동시키겠습니까?'))
				{
					return;
				}
			}
			if($("#new_parent_rate").val()=='')
			{
				alert("상부총판정산률을 입력하세요.");
				$("#new_parent_rate").focus();
				return;
			}

			if(!$.isNumeric($("#new_parent_nc_rate").val()))
			{
				alert("상부총판 낙첨수수료를 숫자로 입력하세요");
				$("#new_parent_nc_rate").focus();
				return false;
			}
			if(!$.isNumeric($("#new_parent_wb_rate").val()))
			{
				alert("상부총판 웹게임 배팅포인트를 숫자로 입력하세요");
				$("#new_parent_wb_rate").focus();
				return false;
			}
			if(!$.isNumeric($("#new_parent_sb_rate").val()))
			{
				alert("상부총판 스포츠 배팅포인트를 숫자로 입력하세요");
				$("#new_parent_sb_rate").focus();
				return false;
			}

			var fm=document.frm;
			fm.submit();
		}

		function modify_child(idx)
		{
			var fm=document.frmChild;
			if( $('input[name="parent_rate_'+idx+'"]').val() == '')
			{
				alert('상부 정산률을 입력하세요.');
				$('input[name="parent_rate_'+idx+'"]').focus();
				return;
			}
			$('input[name="child_rec_sn"]').val(idx);
			$('input[name="parent_rate"]').val($('input[name="parent_rate_'+idx+'"]').val());
			$('input[name="parent_nc_rate"]').val($('input[name="parent_nc_rate_'+idx+'"]').val());
			$('input[name="parent_wb_rate"]').val($('input[name="parent_wb_rate_'+idx+'"]').val());
			$('input[name="parent_sb_rate"]').val($('input[name="parent_sb_rate_'+idx+'"]').val());
			fm.action="?act=modify";
			fm.submit();
		}

		function remove_child(idx)
		{
			var fm=document.frmChild;
			var result = confirm('정말로 삭제 하시겠습니까?');
			if(result) {
				$('input[name="child_rec_sn"]').val(idx);
			
				fm.action="?act=delete";
				fm.submit();
			}
		}

		function go(url)
		{
			var result = confirm('정말로 전환 하시겠습니까?');
			if(result){
				location.href=url;
			}
	  	}

	  	$(document).ready(function(){
	  		$("#selected_rec").change(function() {
	  			$.ajax({
	  				url: "./getSelectedRec" ,
	  				type: "post",
	  				data: "idx="+$("#selected_rec option:selected").val(),
	  				dataType: "json",
	  				success: function(data) {
						console.log(data);
	  					if(data !== null)
	  					{
	  						$("#new_default_rate").html(data.default_rate+" %");
	  						$("#new_parent_rate").val(data.parent_rate);
	  						$("#new_rec_sn").val(data.parent_rec_sn);
							$("#new_nc_rate").html(data.nc_rate+" %");
							$("#new_wb_rate").html(data.wb_rate+" %");
							$("#new_sb_rate").html(data.sb_rate+" %");
	  					}
	  					else
	  					{
	  						$("#new_rec_sn").val(0);
	  					}
	  				}
	  			});
	  		});
	  	});
	</script>
</head>

<body>

<div id="wrap_pop">
	<div id="pop_title">
		<h1>하부총판 설정</h1>
		<p><img src="/img/btn_s_close.gif" onclick="window.close()" title="창닫기"></p>
	</div>

	<h3><?php echo $TPL_VAR["list"]["rec_id"]?> 하부총판</h3>
	<form id="frmChild" name="frmChild" method="post" action="?act=modify">
		<input type="hidden" name="urlidx" value="<?php echo $TPL_VAR["list"]["Idx"]?>">
		<input type="hidden" name="child_rec_sn" value="">
		<input type="hidden" name="parent_rate" value="">
		<input type="hidden" name="parent_nc_rate" value="">
		<input type="hidden" name="parent_wb_rate" value="">
		<input type="hidden" name="parent_sb_rate" value="">
		<table cellspacing="1" class="tableStyle_ChildWrite" summary="하부 총판 정보">
			<legend class="blind">하부 총판 정보</legend>
<?php if($TPL_childlist_1){foreach($TPL_VAR["childlist"] as $TPL_V1){?>
				 
					<tr>
						<th>하부총판</th>
						<td><?php echo $TPL_V1["rec_id"]?></td>
						<th>정산률</th>
						<td>하부 <?php echo $TPL_V1["default_rate"]?>% <br>상부 <input type="input" name="parent_rate_<?php echo $TPL_V1["Idx"]?>" value="<?php echo $TPL_V1["parent_rate"]?>" class="inputStyle3" >%</td>
						<th>낙첨수수료</th>
						<td>하부 <?php echo $TPL_V1["nc_rate"]?>% <br>상부 <input type="input" name="parent_nc_rate_<?php echo $TPL_V1["Idx"]?>" value="<?php echo $TPL_V1["parent_nc_rate"]?>" class="inputStyle3" >%</td>
						<th>웹게임<br>배팅포인트</th>
						<td>하부 <?php echo $TPL_V1["wb_rate"]?>% <br>상부 <input type="input" name="parent_wb_rate_<?php echo $TPL_V1["Idx"]?>" value="<?php echo $TPL_V1["parent_wb_rate"]?>" class="inputStyle3" >%</td>
						<th>스포츠<br>배팅포인트</th>
						<td>하부 <?php echo $TPL_V1["sb_rate"]?>% <br>상부 <input type="input" name="parent_sb_rate_<?php echo $TPL_V1["Idx"]?>" value="<?php echo $TPL_V1["parent_sb_rate"]?>" class="inputStyle3" >%</td>
						<td>
							<input type="button" value="수정" onClick="modify_child(<?php echo $TPL_V1["Idx"]?>)" class="btnStyle1">
							<input type="button" value="삭제" onClick="remove_child(<?php echo $TPL_V1["Idx"]?>)" class="btnStyle1">
						</td>
					</tr>
				 
<?php }}?>
		</table>
	</form>

</div>
</body>
</html>