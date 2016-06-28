<?php /* Template_ 2.2.3 2013/01/02 13:00:58 D:\www\vhost.manager\_template\content\partner\member_details.html */?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="/css/style.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="/js/js.js"></script>
	<title>회원 상세 정보</title>
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
		fm.submit();
	}

	function go(url)
	{
		var result = confirm('정말로 전환 하시겠습니까?');
		if(result){
			location.href=url;
		}
  }
</script>
</head>

<body>

<div id="wrap_pop">
	<div id="pop_title">
		<h1>파트너 상세정보</h1>
		<p><img src="/img/btn_s_close.gif" onclick="window.close()" title="창닫기"></p>
	</div>

	<form id="frm" name="frm" method="post" action="?act=add">
		<input type="hidden" name="urlidx" value="<?php echo $TPL_VAR["list"]["Idx"]?>">	
		<table cellspacing="1" class="tableStyle_membersWrite" summary="회원 정보">
		<legend class="blind">쪽지 쓰기</legend>
			<tr>
			  <th>아이디</th>
			  <td><?php echo $TPL_VAR["list"]["rec_id"]?></td>
			  <th>비밀번호</th>
			  <td><input type="password" name="pwd" value="default" class="inputStyle1"></td>
			</tr>
			<tr>
			  <th>이름</th>
			  <td><input type="input" name="rec_name" value="<?php echo $TPL_VAR["list"]["rec_name"]?>" class="inputStyle2"></td>
			  <th>은행명</th>
			  <td><input type="input" name="rec_bankname" value="<?php echo $TPL_VAR["list"]["rec_bankname"]?>" class="inputStyle2"></td>
			</tr>
			<tr>
				<!--
			  <th>정산비율</th>
			  <td>
<?php if(strpos($TPL_VAR["quanxian"],"1001")>=0){?>
				  	<a href="javascript:open_window('/partner/popup_rate?id=<?php echo $TPL_VAR["list"]["rec_id"]?>&rate=<?php echo $TPL_VAR["list"]["rec_rate"]?>',400,250)"><?php echo $TPL_VAR["list"]["rec_rate"]?>%</a>
<?php }else{?>
				  	<?php echo $TPL_VAR["list"]["rec_rate"]?>%
<?php }?>
			  </td>
			  -->
			  <th>예금주</th>
			  <td colspan="3"><input type="input" name="rec_bankusername" value="<?php echo $TPL_VAR["list"]["rec_bankusername"]?>" class="inputStyle2"></td>
			</tr>
			<tr>
			  <th>총입금액</th>
			  <td><?php echo number_format($TPL_VAR["list"]["charge_sum"],0)?>원</td>
			  <th>총출금액</th>
			  <td><?php echo number_format($TPL_VAR["list"]["exchange_sum"],0)?>원</td>
			</tr>
			<tr>
			  <th>계좌번호</th>
			  <td><input type="input" name="rec_banknum" value="<?php echo $TPL_VAR["list"]["rec_banknum"]?>" class="inputStyle2"></td>
			  <th>가입날짜</th>
			  <td><?php echo $TPL_VAR["list"]["reg_date"]?></td>
			</tr>
			<tr>
			  <th>회원수</th>
			  <td><?php echo $TPL_VAR["list"]["member_count"]?>명</td>
			  <th>입금자수</th>
			  <td><?php echo $TPL_VAR["list"]["charge_count"]?>명</td>
			</tr>
			<!--
			<tr>
			  <th>등급</th>
			  <td colspan="3">
				<select name="rec_lev">
					<option value="1" <?php if($TPL_VAR["list"]["rec_lev"]==1){?> "selected" <?php }?>>1</option>
					<option value="2" <?php if($TPL_VAR["list"]["rec_lev"]==2){?> "selected" <?php }?>>2</option>
					<option value="3" <?php if($TPL_VAR["list"]["rec_lev"]==3){?> "selected" <?php }?>>3</option>
					<option value="4" <?php if($TPL_VAR["list"]["rec_lev"]==4){?> "selected" <?php }?>>4</option>
					<option value="5" <?php if($TPL_VAR["list"]["rec_lev"]==5){?> "selected" <?php }?>>5</option>
					<option value="6" <?php if($TPL_VAR["list"]["rec_lev"]==6){?> "selected" <?php }?>>6</option>
					<option value="7" <?php if($TPL_VAR["list"]["rec_lev"]==7){?> "selected" <?php }?>>7</option>
					<option value="8" <?php if($TPL_VAR["list"]["rec_lev"]==8){?> "selected" <?php }?>>8</option>
					<option value="9" <?php if($TPL_VAR["list"]["rec_lev"]==9){?> "selected" <?php }?>>9</option>
					<option value="10" <?php if($TPL_VAR["list"]["rec_lev"]==10){?> "selected" <?php }?>>10</option>
				</select>
			  </td>
			</tr>
			-->
			<tr>
			  <th>이메일</th>
			  <td><input type="input" name="rec_email" value="<?php echo $TPL_VAR["list"]["rec_email"]?>" class="inputStyle2"></td>
			  <th>핸드폰</th>
			  <td><input type="input" name="rec_phone" value="<?php echo $TPL_VAR["list"]["rec_phone"]?>" class="inputStyle2"></td>
			</tr>
			<tr>
			  <th>메모</th>
			  <td colspan="3">
				<textarea class="input" cols="50" rows="3" name="memo" ><?php echo $TPL_VAR["list"]["memo"]?></textarea>
			  </td>
			</tr>
		</table>
	
		<div id="wrap_btn"><input type="button" value="수정" onClick="check()" class="btnStyle1"></div>
	</form>
</div>
</body>
</html>