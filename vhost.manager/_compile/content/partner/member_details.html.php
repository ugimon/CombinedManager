<?php /* Template_ 2.2.3 2016/03/07 10:27:14 C:\inetpub\combined_manager\vhost.manager\_template\content\partner\member_details.html */
$TPL_partner_list_1=empty($TPL_VAR["partner_list"])||!is_array($TPL_VAR["partner_list"])?0:count($TPL_VAR["partner_list"]);?>
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
		if(fm.default_rate.value=="")
		{
			alert("정산률을 넣어주세요.");
			fm.default_rate.focus();
			return;
		}



		//정산률 추가 2016.02.01

<?php if($TPL_VAR["list"]["rec_grd"]==2){?>
		
		if(frm.org_parent_rec_sn.value != frm.parent_rec_sn.value)
		{
			if(!confirm("상부 총판을 변경하시겠습니까?")){
				return;
			}
		}
		if(fm.parent_rate.value=="")
		{
			alert("상부 정산률을 넣어주세요.");
			fm.parent_rate.focus();
			return;
		}

		if(!$.isNumeric(fm.parent_nc_rate.value))
		{
			alert("상부 낙첨수수료를 숫자로 입력하세요");
			fm.parent_nc_rate.focus();
			return ;
		}
		if(!$.isNumeric(fm.parent_wb_rate.value))	{
			alert("상부 웹게임배팅 포인트를 숫자로 입력하세요");
			fm.parent_wb_rate.focus();
			return ;
		}
		if(!$.isNumeric(fm.parent_sb_rate.value))	
		{
			alert("상부 스포츠배팅 포인트를 숫자로 입력하세요");
			fm.parent_sb_rate.focus();
			return ;
		}
<?php }?>

		if(!$.isNumeric(fm.nc_rate.value))
		{
			alert("낙첨수수료를 숫자로 입력하세요");
			fm.nc_rate.focus();
			return ;
		}
		if(!$.isNumeric(fm.wb_rate.value))	{
			alert("웹게임배팅 포인트를 숫자로 입력하세요");
			fm.wb_rate.focus();
			return ;
		}
		if(!$.isNumeric(fm.sb_rate.value))	
		{
			alert("스포츠배팅 포인트를 숫자로 입력하세요");
			fm.sb_rate.focus();
			return ;
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
			  <td <?php if($TPL_VAR["list"]["rec_grd"]!=2){?> colspan=3<?php }?>><input type="input" name="rec_bankusername" value="<?php echo $TPL_VAR["list"]["rec_bankusername"]?>" class="inputStyle2"></td>
<?php if($TPL_VAR["list"]["rec_grd"]==2){?>
			   <th>상부총판</th>
			  <td ><input type=hidden name="org_parent_rec_sn" value="<?php echo $TPL_VAR["list"]["parent_rec_sn"]?>">
				<select name="parent_rec_sn" id="parent_rec_sn">
				<option value="0"> 총    판  
					</option>
<?php if($TPL_partner_list_1){foreach($TPL_VAR["partner_list"] as $TPL_V1){?>
					<option value="<?php echo $TPL_V1["Idx"]?>" <?php if($TPL_V1["Idx"]==$TPL_VAR["list"]["parent_rec_sn"]){?> selected <?php }?>><?php echo $TPL_V1["rec_id"]?>

					</option>
<?php }}?>
					</select></td>

<?php }?>
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
<?php if($TPL_VAR["list"]["rec_grd"]==2){?>
			<tr>
				<th>정산률</th>
				<td>상부<input type="input" name="parent_rate" value="<?php echo $TPL_VAR["list"]["parent_rate"]?>" class="inputStyle3"> %하부<input type="input" name="default_rate" value="<?php echo $TPL_VAR["list"]["default_rate"]?>" class="inputStyle3"> %</td>
				<th>낙첨수수료</th>
				<td>상부<input type="input" name="parent_nc_rate" value="<?php echo $TPL_VAR["list"]["parent_nc_rate"]?>" class="inputStyle3"> %하부<input type="input" name="nc_rate" value="<?php echo $TPL_VAR["list"]["nc_rate"]?>" class="inputStyle3"> %</td>
			</tr>
			<tr>
				<th>웹게임 배팅포인트</th>
				<td>상부<input type="input" name="parent_wb_rate" value="<?php echo $TPL_VAR["list"]["parent_wb_rate"]?>" class="inputStyle3"> %하부<input type="input" name="wb_rate" value="<?php echo $TPL_VAR["list"]["wb_rate"]?>" class="inputStyle3"> %</td>
				<th>스포츠 배팅포인트</th>
				<td>상부<input type="input" name="parent_sb_rate" value="<?php echo $TPL_VAR["list"]["parent_sb_rate"]?>" class="inputStyle3"> %하부<input type="input" name="sb_rate" value="<?php echo $TPL_VAR["list"]["sb_rate"]?>" class="inputStyle3"> %</td>
			</tr>
			<tr>
				<th>상부총판<br/>정산액</th>
				<td><?php echo number_format($TPL_VAR["list"]["parent_rec_account"],0)?>원</td>
				<th rowspan=2>총정산액</th>
				<td rowspan=2>
					<?php echo number_format($TPL_VAR["list"]["total_rec_account"],0)?>원
				</td>
			</tr>
			<tr>
				<th>정산액</th>
				<td><?php echo number_format($TPL_VAR["list"]["rec_account"],0)?>원
				</td>
				 
			</tr>
<?php }else{?>
	 
			<tr>
				<th>정산률</th>
				<td><input type=hidden name="parent_rec_sn" value="<?php echo $TPL_VAR["list"]["parent_rec_sn"]?>"><input type=hidden name="parent_rate" value="<?php echo $TPL_VAR["list"]["parent_rate"]?>"><input type="input" name="default_rate" value="<?php echo $TPL_VAR["list"]["default_rate"]?>" class="inputStyle3"> %</td>
				<th>낙첨수수료</th>
				<td><input type=hidden name="parent_nc_rate" value="<?php echo $TPL_VAR["list"]["parent_nc_rate"]?>"><input type="input" name="nc_rate" value="<?php echo $TPL_VAR["list"]["nc_rate"]?>" class="inputStyle3"> %</td>
			</tr>
			<tr>
				<th>웹게임 배팅포인트</th>
				<td><input type=hidden name="parent_wb_rate" value="<?php echo $TPL_VAR["list"]["parent_wb_rate"]?>"><input type="input" name="wb_rate" value="<?php echo $TPL_VAR["list"]["wb_rate"]?>" class="inputStyle3"> %</td>
				<th>스포츠 배팅포인트</th>
				<td><input type=hidden name="parent_sb_rate" value="<?php echo $TPL_VAR["list"]["parent_sb_rate"]?>"><input type="input" name="sb_rate" value="<?php echo $TPL_VAR["list"]["sb_rate"]?>" class="inputStyle3"> %</td>
			</tr>
			<tr>
				<th>정산액</th>
				<td><?php echo number_format($TPL_VAR["list"]["rec_account"],0)?>원</td>
				<th rowspan=2>총정산액</th>
				<td rowspan=2>
					<?php echo number_format($TPL_VAR["list"]["total_rec_account"],0)?>원
				</td>
			</tr>
			<tr>
				<th>하부총판<br/>정산액</th>
				<td>
					<?php echo number_format($TPL_VAR["list"]["child_rec_account"],0)?>원
					<div style="display:inline;">
						<input style="float:right;margin-right:30px;" type="button" value="설정" onClick="javascript:open_window('/partner/memberChildRec?idx=<?php echo $TPL_VAR["list"]["Idx"]?>',940,440)" class="btnStyle1">
					</div>
				</td>
				 
			</tr>
<?php }?>
			
			 
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