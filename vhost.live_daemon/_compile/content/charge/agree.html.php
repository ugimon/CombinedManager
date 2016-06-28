<?php /* Template_ 2.2.3 2013/05/30 17:07:32 D:\www\vhost.manager\_template\content\charge\agree.html */?>
<script language="JavaScript">
		function This_Check()
		{
			if (document.frm.amount.value =="")
			{
				alert(" 금액을 입력하세요"); 
				document.frm.amount.focus();
				return;
	
			}
			else
			{
				frm.action = "/charge/agree";
				frm.submit();
			}
		}
	</script>
</head>

<body>

<div id="wrap_pop">
	<div id="pop_title">
		<h1>충전 신청 처리</h1>
		<p><img src="/img/btn_s_close.gif" onclick="window.close()" title="창닫기"></p>
	</div>

	<form name="frm" method="get">
		<input type="hidden" name="mode" value='process'>
		<input type="hidden" name='charge_sn' value='<?php echo $TPL_VAR["chargeSn"]?>'>

		<table cellspacing="1" class="tableStyle_membersWrite">
		<legend class="blind">충전 승인</legend>
			<tr>
			  <th nowrap width="35%">아이디</th>
			  <td><?php echo $TPL_VAR["uid"]?></td>
			</tr>
			<tr>
			  <th>입금액</th>
			  <td><input type="text" name='amount' size="15" readonly  class="inputStyle2" value='<?php echo number_format($TPL_VAR["amount"],0)?>'></td>
			</tr>
			<tr>
			  <th>보너스</th>
			  <td><input type=text name='bonus' size="15" onkeyUp="javascript:this.value=FormatNumber(this.value);" class="inputStyle2" value='0'>
				보너스한도 <?php echo number_format($TPL_VAR["maxBonus"],0)?>(10%)</td>
			</tr>
		</table>
		
		<input type="button" value="충 전" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="This_Check();">
		<input type="button" value="닫 기" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="self.close()">
	</form>

</div>

</body>
</html>