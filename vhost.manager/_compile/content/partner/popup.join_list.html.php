<?php /* Template_ 2.2.3 2016/04/28 10:28:41 C:\inetpub\web\5. Armand De\www\vhost.manager\_template\content\partner\popup.join_list.html */?>
<script>
	
function ajax_uid_check()
{
	var uid = $('#uid').val();
	if(uid.length <= 0)
	{
		$("#ajax_message").html("아이디 입력");
		return false;
	}
	else
	{
		$.ajaxSetup({async:false});
		var param={uid:uid};
		
		$.post("/partner/addCheckAjax", param, on_uid_check);
	}
}

function on_uid_check(result)
{
	if(result==1)
	{
		$("#ajax_message").html("<font color='red'>사용 불가</font>");
	}
	else
	{
		$("#ajax_message").html("<font color='blue'>사용 가능</font>");
	}
}

function doSubmit()
{
	if($("#uid").val().length <=0)
	{
		alert("아이디를 입력하세요");
		$("#uid").focus();
		return false;
	}
	if($("#name").val().length <=0)
	{
		alert("이름을 입력하세요");
		$("#name").focus();
		return false;
	}
	if($("#phone").val().length <=0)
	{
		alert("연락처를 입력하세요");
		$("#phone").focus();
		return false;
	}
	if($("#passwd").val().length <=0)
	{
		alert("비밀번호를 입력하세요");
		$("#passwd").focus();
		return false;
	}

 
	
	//정산률 추가 2016.02.01
	if(!$.isNumeric($("#default_rate").val()))
	{
		alert("정산률을 숫자로 입력하세요");
		$("#default_rate").focus();
		return false;
	}
	
	if(!$.isNumeric($("#nc_rate").val()))
	{
		alert("낙첨수수료를 숫자로 입력하세요");
		$("#nc_rate").focus();
		return false;
	}
	if(!$.isNumeric($("#wb_rate").val()))	{
		alert("웹게임배팅 포인트를 숫자로 입력하세요");
		$("#wb_rate").focus();
		return false;
	}
	if(!$.isNumeric($("#sb_rate").val()))
	{
		alert("스포츠배팅 포인트를 숫자로 입력하세요");
		$("#sb_rate").focus();
		return false;
	}
	
	document.form1.submit();
}
</script>

<div class="wrap">
	<div id="route">
		<h5>관리자 시스템 > 총판 관리 > <b>총판 등록</b></h5>
	</div>

	<h3>총판 등록</h3>
	<form id="form1" name="form1" method="post" action="?act=add">
		<table cellspacing="1" class="tableStyle_pop">
		<legend class="blind">총판 등록</legend>
			<tr>
				<th>아이디</th>
				<td><input name="uid" type="text" id="uid" onblur="ajax_uid_check();" /></td>
				<th>비밀번호</th>
				<td><input name="passwd" type="text" id="passwd"/></td>
		 </tr>
		 <tr>
				<th>이름</th>
				<td><input name="name" type="text" id="name"/></td>
				<th>전화번호</th>
				<td><input name="phone" type="text" id="phone"/></td>
		 </tr>
		 <tr>
				<th>메모</th>
				<td><input name="memo" type="text" id="memo"/></td>
				<th>사이트</th>
				<td>
					<select name="logo">
						<option value="">전체</option>
						<option value="totobang">킹덤</option>
						<option value="eclipse">이클</option>
						<option value="poten2">포텐2</option>
					</select>
				</td>
				<!--<td><span id="ajax_message"></span></td>-->
		 </tr>
		 <tr>
				<th>정산률</th>
				<td><input name="default_rate" type="text" id="default_rate" class="inputStyle3" value="0"/>%</td>
				<th>낙첨수수료</th>
				<td><input name="nc_rate" type="text" id="nc_rate" class="inputStyle3" value="0" />%</td>
		 </tr>
		  <tr>
				<th>웹게임배팅 포인트</th>
				<td><input name="wb_rate" type="text" id="wb_rate" class="inputStyle3" value="0" />%</td>
				<th>스포츠배팅 포인트</th>
				<td><input name="sb_rate" type="text" id="sb_rate" class="inputStyle3" value="0" />%</td>
		 </tr>
		</table>
	
		<div id="wrap_btn">
			<input type="button" name="open" value="등록" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="doSubmit();"/>
		</div>
	</form>
</div>