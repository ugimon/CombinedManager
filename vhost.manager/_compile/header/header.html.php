<?php /* Template_ 2.2.3 2016/03/07 10:27:14 C:\inetpub\combined_manager\vhost.manager\_template\header\header.html */?>
﻿<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>관리자 시스템</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="keywords" content="">
		<meta http-equiv="imagetoolbar" content="no">
		<link rel="shortcut icon" href="img/favicon.ico">
		<link rel="stylesheet" href="/css/default.css" type="text/css">

		<script src="/js/selectAll.js"></script>
		<script src="/js/js.js"></script>
		<script src="/js/is_show.js"></script>
		<script src="/js/lendar.js"></script>
		<script src="/js/common.js"></script>
		<script src="/js/jBeep.min.js" type="text/javascript"></script>
		<script src="/js/jquery-1.11.3.min.js"></script>
		
		<script language="javascript">
			function refreshSms()
			{
				var oBao=null;
				if(window.ActiveXObject)
				{                //IE
					oBao = new ActiveXObject("Microsoft.XMLHTTP");
				}
				else if(window.XMLHttpRequest)
				{    //IE가 아닐경우
					oBao = new XMLHttpRequest();
				}

				oBao.open("POST","/etc/sms",false); 
				oBao.send(); 
				var rs = unescape(oBao.responseText);
			
				if(rs>0)
				{
					$("#ve").html("<div style='display:none;'><embed src='/voice/regcode.mp3' loop=false hidden='true' volume='100'></embed></div>");
				}
				$("#top_sms").html("<a href='/etc/smslist'>인증문자:("+rs+")건</a>");
			}
			
			function refreshContent() 
			{

			    $.ajax({
			        url: "/etc/refresh",
			        success: function(data) {
			            console.log(data);

			            var arrResult = data.split("###");

			            for (var i = 0; i < arrResult.length; i++) {
			                arrTmp = arrResult[i].split("||");
			                charge_count = arrTmp[0];
			                charge_alarm = arrTmp[1];
			                exchange_count = arrTmp[2];
			                exchange_alarm = arrTmp[3];
			                board_count = arrTmp[4];
			                question_count = arrTmp[5];
			                new_member_count = arrTmp[6];
			                new_memo_count = arrTmp[7];
			                total_memo_count = arrTmp[8];
			                recommend_count = arrTmp[9];
			                question_alarm = arrTmp[10];
			                question_sn = arrTmp[11];
			                new_member_alarm = arrTmp[12];
			                live_flag = arrTmp[13];
			            }
			            if (charge_count > 0) {
			                if (charge_alarm == 1) {
			                    jBeep('/voice/in.mp3');
			                    //$("#top_va").html("<div style='display:none;'><embed src='/voice/in.mp3' loop=false hidden='true' volume='100'></embed></div>");
			                }
			                $("#top_richer").html("<a href='/charge/finlist_edit'><font color=#ffff00>입금신청: (" + charge_count + ")건</font></a>");
			            }
			            else $("#top_richer").html("<a href='/charge/finlist_edit'>입금신청: (" + charge_count + ")건</a>");

			            if (exchange_count > 0) {
			                if (exchange_alarm == 1) {
			                    jBeep('/voice/out.mp3');
			                    //$("#top_vb").html("<div style='display:none'><embed src='/voice/out.wav' loop=false hidden='true' volume='100'></embed></div>");
			                }


			                $("#top_Withdrawal").html("<a href='/exchange/finlist_edit'><font color=#ffff00>출금신청: (" + exchange_count + ")건</font></a>");
			            }
			            else $("#top_Withdrawal").html("<a href='/exchange/finlist_edit'>출금신청: (" + exchange_count + ")건</a>");

			            $("#top_bbs").html("<a href='/board/config'>게시판: (" + board_count + ")건</a>");

			            if (question_count > 0) {
			                if (question_alarm == 1) {
			                    jBeep('/voice/center.mp3');
			                }
			                $("#top_question").html("<a href='/board/questionview?idx=" + question_sn + "'><font color=#ffff00>고객센터: (" + question_count + ")건</font></a>");
			            }
			            else {
			                $("#top_question").html("<a href='/board/questionlist'>고객센터: (" + question_count + ")건</a>");
			            }

			            if (new_member_alarm == 'on') {
			                jBeep('/voice/newmember.mp3');
			                $("#top_newmember").html("<a href='/member/list?act=levelup'><font color=#ffff00>회원가입: (" + new_member_count + ")건</font></a>");
			            }
			            else {
			                $("#top_newmember").html("<a href='/member/list?act=levelup'>회원가입: (" + new_member_count + ")건</a>");
			            }
			            $("#top_newmemo").html("<a href='/memo/list'>쪽지: (" + new_memo_count + "/" + total_memo_count + ")건</a>");
			            $("#top_partner").html("<a href='/partner/join'>파트너신청: (" + recommend_count + ")건</a>");

			            if (live_flag == 'OFF') {
			                //jBeep('/voice/regcode.mp3');					
			            }
			            $("#top_live").html("라이브데몬: <font color=yellow>(" + live_flag + ") </font>");
			        }
			    });

				
			}
			
			function beginTimer() 
			{ 
				timer = window.setInterval("refreshContent()",1000*8);
			}

	</script>