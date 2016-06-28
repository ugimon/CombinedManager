<?php /* Template_ 2.2.3 2016/03/03 07:42:07 C:\inetpub\web\5. Armand De\www\vhost.live_daemon\_template\header\top.html */?>
<div id="wrap_gMenu">
			<p id="logo"><img src="/img/top_logo.gif" alt="관리자 메인"><h1 class="blind"><a href="/">관리자 메인</a></h1></p>
			<ul>
				<li class="user"><b><?php echo $TPL_VAR["sess_member_id"]?></b> 님</li>
				<li><a href="/logout">로그아웃</a></li>
				<li><a href="javascript:void(0)" onclick="javascript:open_window('/etc/killiplist?act=list',604,400);">ip차단관리</a></li>
				<li><a href="javascript:void(0)" onclick="javascript:open_window('/etc/changepasswd',410, 210);">비밀번호관리</a></li>
			</ul>
		</div>