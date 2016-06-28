<?php /* Template_ 2.2.3 2016/03/07 10:27:14 C:\inetpub\combined_manager\vhost.manager\_template\header\top.html */?>
<div id="wrap_gMenu">
			<p id="logo"><img src="/img/top_logo.gif" alt="관리자 메인"><h1 class="blind"><a href="/">관리자 메인</a></h1></p>
			<ul>
				<li class="user"><b><?php echo $TPL_VAR["sess_member_id"]?></b> 님</li>
				<li><a href="/logout">로그아웃</a></li>
				<li><a href="javascript:void(0)" onclick="javascript:open_window('/etc/killiplist?act=list',604,400);">ip차단관리</a></li>
				<li><a href="javascript:void(0)" onclick="javascript:open_window('/etc/changepasswd',410, 210);">비밀번호관리</a></li>
			</ul>
		</div>

		<div id="topMenu">
			<ul>
				<li><img src="/img/topMenu_icon.gif" alt="현황정보"></li>
				<li id="top_newmember">회원가입</li>
				<li id="top_question">고객센터</li>
				<li id="top_Withdrawal">출금신청</li>
				<li id="top_richer">입금신청</li>
				<li>총입금 : <?php echo number_format($TPL_VAR["today_charge"],0)?></li>
				<li>총출금 : <?php echo number_format($TPL_VAR["today_exchange"],0)?></li>
				<li>총보유머니 : <?php echo number_format($TPL_VAR["totalmemberMoney"],0)?>원</li>
				<li>총보유포인트 : <?php echo number_format($TPL_VAR["totalmemberMileage"],0)?>원</li>
				<li>배팅대기 : <?php echo number_format($TPL_VAR["currentBetting"],0)?>원</li>
				<li id="top_live">라이브데몬: ()</li>
				<li id="top_va">
				</li>
				<li id="top_vb">
				</li>
				<li id="top_vc">
				</li>
				<li id="top_vd">
				</li>
				<li id="top_ve">
				</li>
				<li id="top_vf">
				</li>
				<li id="top_vg">
				</li>		
				<li id="top_vh">
				</li>
			</ul>
		</div>