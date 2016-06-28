<?php /* Template_ 2.2.3 2015/12/01 16:10:05 D:\Web\2. php\3. Poten\www\vhost.manager\_template\content\member\popup_issue_virtual_money.html */
$TPL_level_list_1=empty($TPL_VAR["level_list"])||!is_array($TPL_VAR["level_list"])?0:count($TPL_VAR["level_list"]);?>
<script>
	function on_issue()
	{
		var fm=document.form1;
		
		if( fm.amount.value <= 0)
		{
			if(confirm("모든 데이터가 초기화 됩니다. 진행하시겠습니까?  "))
			{
				fm.submit();
			}
			else
			{
				return;
			}
		}
		else 
		{
			fm.submit();
		}
	}
</script>
</head>

<body>

<div id="wrap_pop">
	<div id="pop_title">
		<h1>가상머니 지급</h1>
		<p><img src="/img/btn_s_close.gif" onclick="window.close()" title="창닫기"></p>
	</div>

	<form method="post" name="form1" action="?act=send">	
	<table cellspacing="1" class="tableStyle_membersWrite" summary="가상머니 지급">
		<tr>
		  <th>받는이</th>
		  <td>
			<select name="filter_logo">
				<option value=""  <?php if($TPL_VAR["filter_logo"]==""){?>  selected <?php }?>>전체</option>
				<option value="totobang"  <?php if($TPL_VAR["filter_logo"]=="totobang"){?>  selected <?php }?>>포텐</option>
				<option value="eclipse" <?php if($TPL_VAR["filter_logo"]=="eclipse"){?> selected <?php }?>>이클</option>
				<option value="apple" <?php if($TPL_VAR["filter_logo"]=="apple"){?> selected <?php }?>>포텐2</option>
			</select>
			&nbsp;
			<select name="toid_level">
				<option value="">등급</option>
<?php if($TPL_level_list_1){foreach($TPL_VAR["level_list"] as $TPL_V1){?>
					<option value="<?php echo $TPL_V1["lev"]?>"><?php echo $TPL_V1["lev_name"]?></option>
<?php }}?>
			</select>
		  </td>
		</tr>
		<tr>
		  <th>금액</th>
		  <td bgcolor=#ffffff><input type="text" name="amount" size=10 value="0" class="w250" onkeyUp="javascript:this.value=FormatNumber(this.value);" style="text-align:right;"></td>
		</tr>
		
	</table>
	<div id="wrap_btn">
	  <input type="button" value="지 급" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="on_issue()">
	  &nbsp;
	  <input type="button" value="취 소" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="window.close();">
	</div>
	</form>
</div>

</body>
</html>