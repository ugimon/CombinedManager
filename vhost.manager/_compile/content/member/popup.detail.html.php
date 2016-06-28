<?php /* Template_ 2.2.3 2016/03/07 10:27:14 C:\inetpub\combined_manager\vhost.manager\_template\content\member\popup.detail.html */
$TPL_partner_list_1=empty($TPL_VAR["partner_list"])||!is_array($TPL_VAR["partner_list"])?0:count($TPL_VAR["partner_list"]);
$TPL_levelList_1=empty($TPL_VAR["levelList"])||!is_array($TPL_VAR["levelList"])?0:count($TPL_VAR["levelList"]);
$TPL_memoList_1=empty($TPL_VAR["memoList"])||!is_array($TPL_VAR["memoList"])?0:count($TPL_VAR["memoList"]);?>
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
		if(fm.email.value.length!=0){
			reg=/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
			if(!reg.test(fm.email.value)){
				alert("이메일 격식이 틀립니다.");
				fm.email.focus();
				return;
			}
		}
		fm.submit();
	}
	
	function goSubmit() {
		var fm=document.frm;
		fm.submit();
	
		opener.document.location.reload();
	}
	
	function go(url){
		var result = confirm('정말로 전환 하시겠습니까?');
		if(result){
			location.href=url;
	
			opener.document.location.reload();
		 
		}
	} 
	
	function del_note(member_sn, note_sn) 
	{
		var result = confirm('정말로 삭제 하시겠습니까?');
		if(result)
		{
			location.href="/member/noteProcess?act=delete&member_sn="+member_sn+"&note_sn="+note_sn;
		}
	}
	
	function add_note(member_sn, content)
	{
		var fm=document.frm;
		content = frm.content.value;
		location.href="/member/noteProcess?act=add&member_sn="+member_sn+"&content="+content;
	}
	
	function modify_note(member_sn, note_sn)
	{
		content = $('#content_'+note_sn).val();
		location.href="/member/noteProcess?act=modify&member_sn="+member_sn+"&content="+content+"&note_sn="+note_sn;
	}
	</script>
</head>
<body>

<div id="wrap_pop">
	<div id="pop_title">
		<h1>회원 상세정보</h1>
		<p><img src="/img/btn_s_close.gif" onclick="window.close()" title="창닫기"></p>
	</div>

	<form name="frm" method="POST" action="?mode=modify">
		<input type="hidden" name="memid" value="<?php echo $TPL_VAR["list"]["uid"]?>">
		<input type="hidden" name="urlidx" value="<?php echo $TPL_VAR["memberSn"]?>">
		<input type="hidden" name="memo" value="<?php echo $TPL_VAR["list"]["memo"]?>">
		<table cellspacing="1" class="tableStyle_membersWrite" summary="회원 정보">
		<legend class="blind">쪽지 쓰기</legend>
			<tr>
			  <th>아이디</th>
			  <td><?php echo $TPL_VAR["list"]["uid"]?></td>
			  <th>비밀번호</th>
			  <td><input type="text" name="pwd" value="<?php echo $TPL_VAR["list"]["upass"]?>" class="w250"></td>
			  <!--<td><input type="text" name="pwd" value="******" class="w250"></td>-->
			</tr>
			<tr>
			  <th>닉네임</th>
			  <td><?php echo $TPL_VAR["list"]["nick"]?></td>
			  <th>이름</th>
			  <td><?php echo $TPL_VAR["list"]["name"]?></td>
			</tr>
			<tr>
			  <th>보유금액 / 가상머니</th>
			  	<td>
<?php if(strpos($TPL_VAR["quanxian"],"1001")>=0&&$TPL_VAR["list"]["mem_status"]!='G'){?>
			  			<a href="javascript:open_window('/member/popup_moneychange?idx=<?php echo $TPL_VAR["list"]["sn"]?>&act=money',400,250)"> <?php echo number_format($TPL_VAR["list"]["g_money"],0)?> / <?php echo number_format($TPL_VAR["list"]["virtual_money"],0)?> </a>
<?php }else{?>
			  			<?php echo number_format($TPL_VAR["list"]["g_money"],0)?> / <?php echo number_format($TPL_VAR["list"]["virtual_money"],0)?>

<?php }?>
			    	</td>
			  <th>배팅금액</th>
			  <td><?php echo number_format($TPL_VAR["list"]["bet_money"],0)?>원</td>
			</tr>
			<tr>
			  <th>은행명</th>
			  <td><input type="text" name="bank_name" value="<?php echo $TPL_VAR["list"]["bank_name"]?>" class="w250"></td>
			  <th>예금주</th>
			  <td><input type="text" name="bank_member" value="<?php echo $TPL_VAR["list"]["bank_member"]?>" class="w250"></td>
			</tr>
			<tr>
			  <th>계좌번호</th>
			  <td><input type="text" name="bank_count" value="<?php echo $TPL_VAR["list"]["bank_account"]?>" onKeyUp="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))" class="w250"/></td>
			  <th>환전비번</th>
			  <td><input type="text" name="exchange_pass" value="<?php echo $TPL_VAR["list"]["exchange_pass"]?>" class="w250"/></td>
			</tr>
			
			<tr>
			  <th>가입일</th>
			  <td><?php echo $TPL_VAR["list"]["regdate"]?></td>
			  <th>로그인시간</th>
			  <td><?php echo $TPL_VAR["list"]["last_date"]?></td>
			</tr>
			<tr>
			  <th>가입아이피</th>
			  <td><?php echo $TPL_VAR["list"]["reg_ip"]?></td>
			  <th>로그인아이피</th>
			  <td>[<?php echo $TPL_VAR["country_code"]?>] <?php echo $TPL_VAR["list"]["mem_ip"]?></td>
			</tr>
			<tr>
			  <th>가입사이트</th>
			  <td></td>
			  <th>총판</th>
			  <td>
					<select name="recommend_sn">
						<option value="" <?php if($TPL_VAR["list"]["recommend_sn"]==""){?> selected <?php }?>>총판</option>
<?php if($TPL_partner_list_1){foreach($TPL_VAR["partner_list"] as $TPL_V1){?>
						<option value=<?php echo $TPL_V1["Idx"]?> <?php if($TPL_VAR["list"]["recommend_sn"]==$TPL_V1["Idx"]){?> selected <?php }?>><?php echo $TPL_V1["rec_id"]?></option>
<?php }}?>
					</select>
			  </td>
			</tr>
			<tr>
			  <th>입금횟수</th>
			  <td><?php echo $TPL_VAR["list"]["charge_cnt"]?>회</td>
			  <th>출금횟수</th>
			  <td><?php echo $TPL_VAR["list"]["exchange_cnt"]?>회</td>
			</tr>
			<tr>
			  <th>입금총액</th>
			  <td><?php echo number_format($TPL_VAR["list"]["charge_money"],0)?>원</td>
			  <th>출금총액</th>
			  <td><?php echo number_format($TPL_VAR["list"]["exchange_money"],0)?>원</td>
			</tr>
			<tr>
			  <th>회원등급</th>
			  <td>
			  	<select name="mem_lev">
<?php if($TPL_levelList_1){foreach($TPL_VAR["levelList"] as $TPL_V1){?>
			  			<option value="<?php echo $TPL_V1["lev"]?>" <?php if($TPL_VAR["list"]["mem_lev"]==$TPL_V1["lev"]){?> selected <?php }?>><?php echo $TPL_V1["lev_name"]?></option>	
<?php }}?>
				</select>
			  </td>
			  <th>상태</th>
			  <td>
			  	<select name="member_status">
				  		<option value="S" <?php if($TPL_VAR["list"]["mem_status"]=="S"){?> selected <?php }?>>정지</option>
				  		<option value="N" <?php if($TPL_VAR["list"]["mem_status"]=="N"){?> selected <?php }?>>정상</option>			  		
				  		<option value="G" <?php if($TPL_VAR["list"]["mem_status"]=="G"){?> selected <?php }?>>테스터</option>
			  	</select>
			  </td>
			</tr>
			<tr>
			  <th>이메일</th>
			  <td><input type="text" name="email" value="<?php echo $TPL_VAR["list"]["email"]?>" class="w250"></td>
			  <th>핸드폰</th>
			  <td><input type="text" name="phone" value="<?php echo $TPL_VAR["list"]["phone"]?>" class="w250" size="15" maxlength="12" onKeyUp="value=value.replace(/[^\d]/g,'')" onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))"></td>
			</tr>
			<tr>
			  <th>회원수익</th>
			  <td><?php echo number_format($TPL_VAR["list"]["benefit"],0)?>원</td>
			  <th>추천인</th>
			  <td><?php echo $TPL_VAR["joiner"]['uid']?> (<?php echo $TPL_VAR["joiner"]['nick']?>)</td>
			</tr>
			<tr>
			  <th>가입시킨 회원(추천인)</th>
			  <td>
			  		<input type="button" onclick="javascript:open_window('/member/popup_joiners?sn=<?php echo $TPL_VAR["list"]["sn"]?>',1000,600)" value="세부정보" class="btnStyle1">
			  		
			  </td>
			</tr>
			<tr>
			  <th>메모</th>
			  <td colspan="3">
					<table border="0" class="tableStyle_memo">
						<tr>
							<th>시간</th>
							<th>내용</th>
							<th>처리</th>
						</tr>
						<tr>
							<td></td>
							<td><input type='text' size='80' name='content'></td>
							<td><a href="javascript:add_note(<?php echo $TPL_VAR["memberSn"]?>);void(0);">[추가]</a></td>
								
						</tr>
<?php if($TPL_memoList_1){foreach($TPL_VAR["memoList"] as $TPL_V1){?>
							<tr>
								<td><?php echo $TPL_V1["regdate"]?></td>
								<td><input type='text' size='80' id='content_<?php echo $TPL_V1["sn"]?>' value='<?php echo $TPL_V1["memo"]?>'></td>
								<td width="10%">
									<a href="javascript:modify_note(<?php echo $TPL_VAR["memberSn"]?>,<?php echo $TPL_V1["sn"]?>);void(0);">[수정]</a>&nbsp;&nbsp;
									<a href="javascript:del_note(<?php echo $TPL_VAR["memberSn"]?>,<?php echo $TPL_V1["sn"]?>);void(0);">[삭제]</a>
								</td>
							</tr>
<?php }}?>
					</table>
			  </td>
			</tr>
		</table>
		<div id="wrap_btn">
			<input type="button" onclick="javascript:open_window('/member/popup_moneychange?idx=<?php echo $TPL_VAR["list"]["sn"]?>&act=money',400,250)" value="충/환전" class="btnStyle1">
			<input type="button" onclick="javascript:open_window('/log/popup_moneyloglist?field=uid&uid=<?php echo $TPL_VAR["list"]["uid"]?>',1000,600)" value="머니내역" class="btnStyle1">
			<input type="button" onclick="javascript:open_window('/log/popup_mileageloglist?field=uid&uid=<?php echo $TPL_VAR["list"]["uid"]?>',1000,600)" value="마일리지내역" class="btnStyle1">
			<input type="button" onclick="javascript:open_window('/charge/popup_charge?field=uid&mem_id=<?php echo $TPL_VAR["list"]["uid"]?>',1000,600)" value="입금내역" class="btnStyle1">
			<input type="button" onclick="javascript:open_window('/exchange/popup_exchange?field=uid&member_sn=<?php echo $TPL_VAR["list"]["sn"]?>',1000,600)" value="출금내역" class="btnStyle1">
			<input type="button" onclick="javascript:open_window('/member/popup_bet?mem_sn=<?php echo $TPL_VAR["memberSn"]?>','1400',600)" value="배팅내역" class="btnStyle1">
			<input type="button" onclick="javascript:open_window('/member/popup_live_game_betting_list?mem_sn=<?php echo $TPL_VAR["memberSn"]?>','1400',600)" value="라이브배팅" class="btnStyle1">
			<input type="button" onclick="javascript:open_window('/member/popup_loginlist?field=member_id&username=<?php echo $TPL_VAR["list"]["uid"]?>',1000,600)" value="접속기록" class="btnStyle1">
			<!--<input type="button" value="메모쓰기" onclick="javascript:open_window('/member/popup_notewrite?idx=<?php echo $TPL_VAR["memberSn"]?>',700,500)" class="btnStyle1">-->
			<input type="button" value="쪽지함" onclick="javascript:open_window('/memo/popup_memo?username=<?php echo $TPL_VAR["list"]["uid"]?>',700,500)" class="btnStyle1">
			<input type="button" value="쪽지쓰기" onclick="javascript:open_window('/memo/popup_write?userid=<?php echo $TPL_VAR["list"]["uid"]?>&phone=<?php echo $TPL_VAR["list"]["phone"]?>',650,300)" class="btnStyle1">			
			<input type="button" value="수정" onClick="goSubmit()" class="btnStyle1">
			<input type="button" onclick="window.close()" value="닫기" class="btnStyle2">
		</div>
	</form>
</div>

</body>
</html>