<?php /* Template_ 2.2.3 2014/01/07 17:54:38 D:\www\vhost.manager\_template\content\board\write.html */
$TPL_muneList_1=empty($TPL_VAR["muneList"])||!is_array($TPL_VAR["muneList"])?0:count($TPL_VAR["muneList"]);
$TPL_bettingItem_1=empty($TPL_VAR["bettingItem"])||!is_array($TPL_VAR["bettingItem"])?0:count($TPL_VAR["bettingItem"]);
$TPL_array_game_1=empty($TPL_VAR["array_game"])||!is_array($TPL_VAR["array_game"])?0:count($TPL_VAR["array_game"]);
$TPL_replyList_1=empty($TPL_VAR["replyList"])||!is_array($TPL_VAR["replyList"])?0:count($TPL_VAR["replyList"]);?>
<script charset="utf-8" src="/web_editor_cocopa/kindeditor.js"></script>

<script>
function len(s) 
{ 
	var l = 0; 
	var a = s.split(""); 
	for (var i=0;i<a.length;i++) 
	{
		if (a[i].charCodeAt(0)<299) {l++;} 
		else {l+=2;} 
	} 
	return l; 
}

function Form_ok() 
{
	if (Form1.title.value == "") 
	{
		alert("제목 입력!!!");
		document.Form1.title.focus();
		return;
	}
	if(Form1.province.value == "")
	{
		alert("분류 선택!!!");
		document.Form1.province.focus();
		return;
	}
	if(Form1.author.value =="")
	{
		alert("작성자 선택!!!");
		document.Form1.author.focus();
		return;
	}
	if(Form1.time.value =="")
	{
		alert("시간 선택!!!");
		document.Form1.time.focus();
		return;
	}
	if(len(Form1.time.value) !=19)
	{
		alert("시간 격식이 틀립니다. 확인하십시오!!!");
		document.Form1.time.focus();
		return;
	}
	
	if (confirm("입력하신 내용을 등록 하시겠습니까 ?"))
	{
		document.Form1.imgsrc.value=KE.util.getpic('on');
		document.Form1.submit();
	}
	else {return;}
}

function check_reply()
{
	if(document.reply.comment.value=="")
	{
		alert("리플 내용을 입력하십시오.!");
		document.reply.comment.focus();
		return;
	}
	if(document.reply.reply_author.value=="")
	{
		alert("리플 닉네임을 입력하십시오.!");
		document.reply.reply_author.focus();
		return;
	}
	document.reply.submit();
}

// chltnwjd
function goDel(delsn, _id)
{
	var result = confirm('삭제 하시겠습니까?');
	if(result) {location.href="?act=delete_comment&sn="+delsn+"&id="+_id;}	
}

function goModify(sn, _id)
{
	var result = confirm('수정하시겠습니까?');
	var content = $('#'+sn+'_content').val();
	if(result) {location.href="?act=modify_comment&sn="+sn+"&content="+content+"&id="+_id;}	
}

function checkNumber(e)
{
	var key = window.event ? e.keyCode : e.which;
	var keychar = String.fromCharCode(key);
	//var el = document.getElementById('test');
	var msg = document.getElementById('msg');
	reg = /\d/;
	var result = reg.test(keychar);
	if(!result)
	{
		msg.innerHTML="<font color='red'>숫자만 입력 가능합니다.</font>";
		return false;
	}
	else
	{
		msg.innerHTML="";
		return true;
	}
}

var xmlHttp;
var re=false;
var re2=false;
var isChckRecommend = false;
var re4=false;

function createXMLHttpRequest()
{
	if(window.XMLHttpRequest)
  {
  	xmlHttp = new XMLHttpRequest();//mozilla브라우저
  }
  else if(window.ActiveXObject)
  {
  	try
    {
    	xmlHttp = new ActiveX0bject("Msxml2.XMLHTTP");//IE얼드버전
    }
    catch(e)
    {}
    try
    {
    	xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");//IE뉴버전
    }
    catch(e)
    {}
    if(!xmlHttp)
    {
    	window.alert("XMLHttpRequest-대상을 업그레이드 할 수가 없습니다.");
      return false;
    }
  }
}

function startRequest()
{
	var author = document.getElementById("author").value;
  
	createXMLHttpRequest();
	xmlHttp.open("GET","/board/addCheckAjax?author="+encodeURIComponent(author),true);
	xmlHttp.onreadystatechange = handleStateChange;
	xmlHttp.send(null);
}

function ajaxReplyNickRequest()
{
	var author = document.getElementById("reply_author").value;
  
	createXMLHttpRequest();
	xmlHttp.open("GET","/board/addCheckAjax?author="+encodeURIComponent(author),true);
	xmlHttp.onreadystatechange = onReplyNickResponse;
	xmlHttp.send(null);
}

function onReplyNickResponse()
{
	if(xmlHttp.readyState==4)
  {
  	if(xmlHttp.status==200)
    {
    	if(xmlHttp.responseText == "true")
    	{
      	document.getElementById("reply_nick_check_message").innerHTML = ' <font color=#ff0000>이미 가입된 닉네임 입니다.</font>';
      	document.reply.reply_author.value="";
				return false;
      }
      else if(xmlHttp.responseText == "false")
      {
      	document.getElementById("reply_nick_check_message").innerHTML = '<font color=blue>사용 가능한 닉네임 입니다.</font>';
				return true;
      }
     }
  }
}

function handleStateChange()
{
	if(xmlHttp.readyState==4)
  {
  	if(xmlHttp.status==200)
    {
    	if(xmlHttp.responseText == "true")
    	{
      	document.getElementById("ckauthor").innerHTML = ' <font color=#ff0000>이미 가입된 닉네임 입니다.</font>';
      	
				re=false;
				return false;
      }
      else if(xmlHttp.responseText == "false")
      {
      	document.getElementById("ckauthor").innerHTML = '<font color=#ff0000>사용 가능한 닉네임 입니다.</font>';
				re=true;
				return true;
      }
     }
  }
}

function excelUpload(_id)
{
	window.open('/board/popup_reply_excelupload?id='+_id,'','resizable=yes scrollbars=yes top=5 left=5 width=1100 height=650');
}

function changeAuthor(val)
{
	if(val!='5')
	{
		document.getElementById('author').value="관리자";
	}
}

</script>



<!-- content begin -->

<div class="wrap" id="write">

	<div id="route">
		<h5>관리자 시스템 > 게시판 관리 > <b>게시글 쓰기</b></h5>
	</div>

	<h3>게시글 쓰기</h3>

	<ul id="tab">
		<li><a href="/board/list?province=5" id="freeboard">자유게시판</a></li>
		<li><a href="/board/list?province=2" id="notice">공지사항</a></li>
		<li><a href="/board/list?province=7" id="event">이벤트</a></li>
		<li><a href="/board/questionlist" id="question_list">고객센터</a></li>
		<li><a href="/board/write" id="write">게시물 쓰기</a></li>
		<li><a href="/board/site_rule_edit?type=1" id="member_rule">회원약관 수정</a></li>
		<li><a href="/board/site_rule_edit?type=2" id="betting_rule">배팅규정 수정</a></li>
	</ul>

	<form action="/board/writeProcess?id=<?php echo $TPL_VAR["id"]?>" method="post" name="Form1">
		<input type="hidden" name="imgsrc"  value="">
		<table cellspacing="1" class="tableStyle_membersWrite">
		<legend class="blind">게시글 쓰기</legend>
			<tr>
				<th>배팅첨부</th>
				<td><a href="#" onclick="javascript:open_window('/board/popup_betting?province=<?php echo $TPL_VAR["province"]?>&perpage=300',1024,600)"><input type="button" value="배팅첨부"></a></td>
			</tr>
			<tr>
				<th>제목</th>
				<td><input name="title" type="text" class="wWhole"  value='<?php echo str_replace("'","\'",$TPL_VAR["list"]["title"])?>' onmouseover="this.focus()"></td>
		  </tr>
		  <tr>
				<th>분류</th>
				<td>
					<select name="province" onchange="changeAuthor(this.value)"><option value="">분류 선택</option>
<?php if($TPL_muneList_1){foreach($TPL_VAR["muneList"] as $TPL_V1){?>
							<option value="<?php echo $TPL_V1["sn"]?>" <?php if($TPL_V1["sn"]==$TPL_VAR["list"]["province"]){?> selected <?php }?>><?php echo $TPL_V1["name"]?></option>
<?php }}?>
					</select>

					<select name="logo">
						<option value="totobang" <?php if($TPL_VAR["list"]["logo"]=='totobang'){?> selected<?php }?>>킹덤</option>
						<option value="orange" <?php if($TPL_VAR["list"]["logo"]=='orange'){?> selected<?php }?>>아레나</option>
					</select>
					<font color='red'>사이트 선택은 [공지사항]일 경우에만 적용됩니다.</font>
				</td>
		  </tr>
		  <tr>
				<th>작성자</th>
				<td>
					<input id="author" name="author" type="text"  class="w250" onblur="startRequest();" value="<?php echo $TPL_VAR["list"]["author"]?>"/>	&nbsp;<span id="ckauthor"></span>
				</td>
		  </tr>
		  <tr>
			<th>시간</th>						
<?php if($TPL_VAR["id"]==""){?>
			<td><input name="time" type="text"  class="w250" value="<?php echo date("Y-m-d H:i:s")?>"/>&nbsp;<font color="red">날자는 꼭 지정된 형식대로 적어주십시오.</font>
<?php }else{?>
			<td><input name="time" type="text"  class="w250" value="<?php echo $TPL_VAR["list"]["time"]?>"/>&nbsp;<font color="red">날자는 꼭 지정된 형식대로 적어주십시오.</font>
<?php }?>
			</td>
		  </tr>
		  <tr>
			<th>조회</th>
			<td><input name="hit" type="text"  value="<?php echo $TPL_VAR["list"]["hit"]?>" class="w60" onkeypress="return checkNumber(event);" onmouseover="this.focus()" onmouseover="this.focus()"/><span id="msg"></span></td>
		  </tr>
		  <tr>
			<th>추천</th>
			<td>
				<input name="top" type="radio"  value="1" <?php if($TPL_VAR["list"]["top"]==1){?> checked="checked" <?php }else{?> checked="checked <?php }?> class="recomInput"/> 추천안함
				<input name="top" type="radio"  value="2" <?php if($TPL_VAR["list"]["top"]==2){?> checked="checked" <?php }?> class="recomInput"/> 추천
				<span style="padding-left:30px;"><font color="red">↓이미지 업로드 경우 이미지 넓이를 무조건 720 이하로 설정하여 주십시오.</font></span
			</td>
		  </tr>
		  <tr>
		  
			<!-- 배팅 리스트 Begin -->
<?php if(sizeof($TPL_VAR["bettingItem"])){?>
<?php if($TPL_bettingItem_1){foreach($TPL_VAR["bettingItem"] as $TPL_V1){
$TPL_item_2=empty($TPL_V1["item"])||!is_array($TPL_V1["item"])?0:count($TPL_V1["item"]);?>
				<table cellspacing="1" class="tableStyle_gameList">		  		
		  		<thead>
	    		<tr>     	      
		      	<th>No</th>
						<th>경기일시</th>
						<th>대분류</th>
						<th>종류</th>
						<th>종목</th>
						<th>리그</th>
						<th colspan="2">승(홈팀)</th>
						<th>무</th>
						<th colspan="2">패(원정팀)</th>
						<th>스코어</th>
						<th>이긴 팀</th>					
		    		</tr>	    		
	 				</thead>
					<tbody>
<?php if($TPL_item_2){foreach($TPL_V1["item"] as $TPL_V2){?>
						<tr>
							<td><?php echo $TPL_V1["betting_no"]?></td>
							<td><?php echo sprintf("%s/%s %s:%s",substr($TPL_V2["gameDate"],5,2),substr($TPL_V2["gameDate"],8,2),$TPL_V2["gameHour"],$TPL_V2["gameTime"])?></td>
							<td>
<?php if($TPL_V2["special"]==0){?>일반
<?php }elseif($TPL_V2["special"]==1){?>스페셜
<?php }elseif($TPL_V2["special"]==2){?>멀티
<?php }elseif($TPL_V2["special"]==3){?>고액
<?php }elseif($TPL_V2["special"]==4){?>이벤트
<?php }?>
							</td>
							<td>
<?php if($TPL_V2["game_type"]==1){?><span class="victory">승무패</span>
<?php }elseif($TPL_V2["game_type"]==2){?><span class="handicap">핸디캡</span>
<?php }elseif($TPL_V2["game_type"]==4){?><span class="underover">언더오버</span>
<?php }?>
							</td>
							<td><?php echo $TPL_V2["sport_name"]?></td>
							<td class="league"><?php echo $TPL_V2["league_name"]?></td>
							<td<?php if($TPL_V2["select_no"]==1){?> style="background:#FFE08C;"<?php }?>><?php echo $TPL_V2["home_team"]?></td>
							<td<?php if($TPL_V2["select_no"]==1){?> style="background:#FFE08C;"<?php }?>><?php echo $TPL_V2["home_rate"]?></td>
							<td<?php if($TPL_V2["select_no"]==3){?> style="background:#FFE08C;"<?php }?>><?php echo $TPL_V2["draw_rate"]?></td>
							<td<?php if($TPL_V2["select_no"]==2){?> style="background:#FFE08C;"<?php }?>><?php echo $TPL_V2["away_team"]?></td>
							<td<?php if($TPL_V2["select_no"]==2){?> style="background:#FFE08C;"<?php }?>><?php echo $TPL_V2["away_rate"]?></td>
							<td><?php echo $TPL_V2["home_score"]?>:<?php echo $TPL_V2["away_score"]?></td>
							<td>
<?php if($TPL_V2["win"]==1){?> 홈승
<?php }elseif($TPL_V2["win"]==2){?> 원정승
<?php }elseif($TPL_V2["win"]==3){?> 무승부
<?php }elseif($TPL_V2["win"]==4){?> 취소/적특
<?php }else{?> &nbsp;
<?php }?>
							</td>
						</tr>
<?php }}?>
					</tbody>
						<tfoot>
							<tr>
								<td colspan="8">
									배팅번호 :<span><b><?php echo $TPL_V1["betting_no"]?></b></span>&nbsp;&nbsp;//&nbsp;&nbsp;구매일시 :<span><?php echo $TPL_V1["regdate"]?></span>&nbsp;&nbsp;//&nbsp;&nbsp;배팅금액 :<span><?php echo number_format($TPL_V1["betting_money"])?></span>&nbsp;&nbsp;//&nbsp;&nbsp;배당률 :<span><?php echo $TPL_V1["result_rate"]?></span>&nbsp;&nbsp;//&nbsp;&nbsp;
									예상금액 :<span><b><?php echo number_format($TPL_V1["win_money"])?>원</b></span>&nbsp;&nbsp;//&nbsp;&nbsp;결과 : <?php if($TPL_V1["result"]==0){?>진행중<?php }elseif($TPL_V1["result"]==2){?>낙첨<?php echo $TPL_V1["result"]==4?>취소<?php }else{?>당첨<?php }?>
								</td>
							</tr>
						</tfoot>
					</table>
<?php }}?>
<?php }?>					
					
			<!-- 배팅 리스트 End -->
			
			<!-- 배팅 리스트 Begin -->
			<!--
<?php if(sizeof($TPL_VAR["bettingItem"])){?>
					<table cellspacing="1" class="tableStyle_gameList">		  		
						<thead>
							<tr>
								<td>경기시간</td>
								<td>타입</td>
								<td>리그</td>
								<td>홈(승)</td>
								<td>VS(무)</td>
								<td>원정(패)</td>
								<td>스코어</td>
								<td>상태</td>
							</tr>
						</thead>
						<tbody>
<?php if($TPL_bettingItem_1){foreach($TPL_VAR["bettingItem"] as $TPL_K1=>$TPL_V1){
$TPL_item_2=empty($TPL_V1["item"])||!is_array($TPL_V1["item"])?0:count($TPL_V1["item"]);?>
<?php if($TPL_item_2){foreach($TPL_V1["item"] as $TPL_V2){?>
									<tr>
										<td><?php echo sprintf("%s/%s %s:%s",substr($TPL_V2["gameDate"],5,2),substr($TPL_V2["gameDate"],8,2),$TPL_V2["gameHour"],$TPL_V2["gameTime"])?></td>
										<td>
<?php if($TPL_V2["game_type"]==1){?>승패
<?php }elseif($TPL_V2["game_type"]==2){?>핸디캡
<?php }elseif($TPL_V2["game_type"]==3){?>스페셜
<?php }elseif($TPL_V2["game_type"]==4){?>멀티
<?php }?>
										</td>
										<td class="league">
											<?php echo $TPL_V2["league_name"]?>

										</td>
										<td <?php if($TPL_V2["select_no"]==1){?>style="border:1 solid #2d050b; background-color:#9FC93C;"<?php }?>><div class=""><span class="name"><?php echo $TPL_V2["home_team"]?></span><span class="rate"><?php echo $TPL_V2["home_rate"]?></span></div></td>
										<td <?php if($TPL_V2["select_no"]==3){?>style="border:1 solid #2d050b; background-color:#9FC93C;"<?php }?>><div class=""><span class="rate"><?php if($TPL_V2["game_type"]==1){?><?php echo $TPL_V1["draw_rate"]?><?php }else{?>(<?php echo $TPL_V2["draw_rate"]?>)<?php }?></span></div></td>
										<td <?php if($TPL_V2["select_no"]==2){?>style="border:1 solid #2d050b; background-color:#9FC93C;"<?php }?>><div class=""><span class="name"><?php echo $TPL_V2["away_team"]?></span><span class="rate"><?php echo $TPL_V2["away_rate"]?></span></div></td>
										<td>[ <?php echo $TPL_V1["home_score"]?>:<?php echo $TPL_V1["away_score"]?> ]</td>
										<td>
<?php if($TPL_V2["result"]==0){?>진행중
<?php }elseif($TPL_V2["result"]==1){?>당첨
<?php }elseif($TPL_V2["result"]==2){?>낙첨
<?php }elseif($TPL_V2["result"]==4){?>취소
<?php }?>
										</td>
									</tr>
<?php }}?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="8">
									배팅번호 :<span><b><?php echo $TPL_K1?></b></span>구매일시 :<span><?php echo $TPL_V1["regdate"]?></span>선택경기 :<span><?php echo $TPL_V1["betting_cnt"]?></span>배팅금액 :<span><?php echo number_format($TPL_V1["betting_money"])?></span>배당률 :<span><?php echo $TPL_V1["result_rate"]?></span>
								</td>
							</tr>
							<tr>
								<td colspan="8" class="info">
									<p>예상금액 :<span><b><?php echo number_format($TPL_V1["win_money"])?>원</b> (+<?php echo $TPL_V1["bonus_rate"]?>% <?php echo number_format($TPL_V1["folder_bonus"])?>포인트)</span>
										결과 : <?php if($TPL_V1["result"]==0){?>진행중<?php }elseif($TPL_V1["result"]==2){?><font color='red'>낙첨</font><?php }elseif($TPL_V1["result"]==4){?>취소<?php }else{?><font color='blue'>당첨</font><?php }?>
									</p>
											
								</td>
							</tr>
						</tfoot>
<?php }}?>
					</table>
<?php }?>
			-->
			<!-- 배팅 리스트 End -->
	
							
<?php if(sizeof($TPL_VAR["array_game"])){?>		  		
		  	<table cellspacing="1" class="tableStyle_gameList">		  		
		  		<input type="hidden" name="cart" value="add">				
		  		<input type="hidden" name="bet_date" value="<?php echo $TPL_VAR["bet_date"]?>">				
		  		<input type="hidden" name="bet_money" value="<?php echo $TPL_VAR["bet_money"]?>">				
		  			&nbsp;&nbsp;구매일시:&nbsp; <?php echo $TPL_VAR["bet_date"]?>

						&nbsp;&nbsp;배팅금액:&nbsp;	<?php echo number_format($TPL_VAR["bet_money"])?>원						
		  		<thead>
	    		<tr>     	      
		      	<th>No</th>
						<th>경기일시</th>
						<th>대분류</th>
						<th>종류</th>
						<th>종목</th>
						<th>리그</th>
						<th colspan="2">승(홈팀)</th>
						<th>무</th>
						<th colspan="2">패(원정팀)</th>
						<th>스코어</th>
						<th>이긴 팀</th>					
		    		</tr>	    		
	 				</thead>
	 				<tbody>
<?php if($TPL_array_game_1){foreach($TPL_VAR["array_game"] as $TPL_V1){?>
	 						<input type="hidden" name="child_sn[<?php echo $TPL_V1["child_sn"]?>]" value="<?php echo $TPL_V1["select_no"]?>">				
	 						<tr>
	 							<td><font color='blue'> <?php echo $TPL_V1["child_sn"]?></font></td>
	 							<td><?php echo sprintf("%s %s:%s",substr($TPL_V1["gameDate"],5),$TPL_V1["gameHour"],$TPL_V1["gameTime"])?></td>
	 							<td>
<?php if($TPL_V1["special"]==0){?>일반
<?php }elseif($TPL_V1["special"]==1){?>스페셜
<?php }elseif($TPL_V1["special"]==2){?>멀티
<?php }elseif($TPL_V1["special"]==3){?>고액
<?php }elseif($TPL_V1["special"]==4){?>이벤트
<?php }?>
								</td>
								<td>
<?php if($TPL_V1["type"]==1){?><span class="victory">승무패</span>
<?php }elseif($TPL_V1["type"]==2){?><span class="handicap">핸디캡</span>
<?php }elseif($TPL_V1["type"]==4){?><span class="underover">언더오버</span>
<?php }?>
								</td>
								<td><?php echo $TPL_V1["sport_name"]?></td>
								<td><?php echo $TPL_V1["league_name"]?></td>
								<td><?php if($TPL_V1["select_no"]==1){?><font color='red'><?php echo mb_strimwidth($TPL_V1["home_team"],0,20,"..","utf-8")?></font><?php }else{?><?php echo mb_strimwidth($TPL_V1["home_team"],0,20,"..","utf-8")?><?php }?></td>
								<td><?php echo $TPL_V1["home_rate"]?></td>
								<td><?php if($TPL_V1["select_no"]==2){?><font color='red'><?php echo $TPL_V1["draw_rate"]?></font><?php }else{?><?php echo $TPL_V1["draw_rate"]?><?php }?></td>
								<td><?php if($TPL_V1["select_no"]==3){?><font color='red'><?php echo mb_strimwidth($TPL_V1["away_team"],0,20,"..","utf-8")?></font><?php }else{?><?php echo mb_strimwidth($TPL_V1["away_team"],0,20,"..","utf-8")?><?php }?></td>
								<td><?php echo $TPL_V1["away_rate"]?></td>
								<td><?php echo $TPL_V1["home_score"]?>:<?php echo $TPL_V1["away_score"]?></td>
								<td>
<?php if($TPL_V1["win"]==1){?> 홈승
<?php }elseif($TPL_V1["win"]==2){?> 원정승
<?php }elseif($TPL_V1["win"]==3){?> 무승부
<?php }elseif($TPL_V1["win"]==4){?> 취소/적특
<?php }else{?> &nbsp;
<?php }?>
								</td>
	 						</tr>
<?php }}?>
	 				</tbody>	
		  	</table>
<?php }?>	
		  	
			<th>내용</th>
			<td>
			<textarea id="on" name="content" cols="80" rows="4" onmouseover="this.focus()">
				<?php echo str_replace("/upload/images/",$TPL_VAR["UPLOAD_URL"],$TPL_VAR["list"]["content"])?>

			</textarea>
			<script>       
				KE.show({   id 			: "on", 
							width 		: "650px",
							height 		: "350px",
							filterMode 	: true,
							resizeMode 	: 1 ,
							items 		: [
											'source','|','fontname', 'fontsize', '|', 'textcolor', 'bgcolor', 'bold', 'italic', 'underline',
											'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
											'insertunorderedlist', '|',  'image', 'link','advtable']
			   			});
			</script>
			</td>
		  </tr>
		</table>
		<div id="wrap_btn">
	      <input type="button" name="ok" value="등  록" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="Form_ok()"/>
	      <input type="reset" name="Submit2" value="초기화" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'"/></td>
	      <input type="button" name="excel_upload" value="엑셀업로드" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="jsvascript:excelUpload('<?php echo $TPL_VAR["id"]?>');"/>
	      <!--
<?php if(empty($TPL_VAR["id"])){?>
	      	<input type="button" name="betting" value="배팅내역첨부" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="javascript:open_window('/board/popup_betting',1024,600)" />
<?php }?>
	      -->
	    </div>
	</form>
	
	<form name="reply" action="?act=reply" method="post">
		<input type="hidden" name="replyid" value="<?php echo $TPL_VAR["id"]?>">
		<table cellspacing="1" class="tableStyle_comment" summary="댓글 쓰기">
			<legend class="blind">댓글 쓰기</legend>
			<tr>
				<th><input name="reply_author" type="text" class="name" value="관리자" onblur="ajaxReplyNickRequest();" /></th>
				<td><textarea  name="comment" class="comment"></textarea></td>
				<td class="btn"><input type="button" name="ok" value="답변" class="Qishi_submit_a" onmouseover="this.className='Qishi_submit_b'"  onmouseout="this.className='Qishi_submit_a'" onclick="check_reply()"></td>
			</tr>
		</table>
	</form>
	<span id="reply_nick_check_message"></span>
	
<?php if(isset($TPL_VAR["id"])){?>
<?php if($TPL_replyList_1){foreach($TPL_VAR["replyList"] as $TPL_V1){?>
			<table cellspacing="1" class="tableStyle_comment" summary="댓글 목록">
				<legend class="blind">댓글 목록</legend>
				<tr>
					<th>
<?php if($TPL_V1["mem_id"]=="관리자 리플"){?>
							<font color='red'><?php echo $TPL_V1["mem_nick"]?></font>
<?php }else{?>
							<?php echo $TPL_V1["mem_nick"]?>

<?php }?>
						
					</th>
					<td><textarea cols="60" rows="2" id='<?php echo $TPL_V1["idx"]?>_content' class="replyContents" <?php if($TPL_V1["mem_id"]!="관리자 리플"){?> readonly <?php }?>><?php echo $TPL_V1["content"]?></textarea></td>
					<td><?php echo $TPL_V1["regdate"]?></td>
					<td>
						<a href="javascript:goModify( <?php echo $TPL_V1["idx"]?>, <?php echo $TPL_VAR["id"]?> );void(0);">[수정]</a>
						<a href="javascript:goDel( <?php echo $TPL_V1["idx"]?>, <?php echo $TPL_VAR["id"]?> );void(0);">[삭제]</a>
					</td>
				</tr>
			</table>
<?php }}?>
<?php }?>
</div>