<?php /* Template_ 2.2.3 2016/06/28 09:47:51 C:\inetpub\combined_manager\vhost.manager\_template\left\left.html */?>
<script>
function mainchgna(vii)
    {
        if(document.getElementById("left_menu0"+vii).style.display=="")
        {
           document.getElementById("left_menu0"+vii).style.display="none";
        }
        else
        {
           document.getElementById("left_menu0"+vii).style.display="";
        }
    }
</script>
			<h2><span class="blind">메인메뉴</span></h2>
			<p class="m_menu" onclick="mainchgna('1')"><a href="#">입금/출금 관리</a></p>
			<div id="left_menu01">
				<ul>
					<!--
					<li><a href='/charge/list'>입금</a></li>
					<li><a href='/exchange/list'>출금</a></li>
					-->

					<li><a href='/charge/finlist_edit'>입금</a></li>
					<li><a href='/exchange/finlist_edit'>출금</a></li>
					<li><a href='/log/moneyloglist'>머니내역</a></li>
					<li><a href='/log/mileageloglist'>마일리지내역</a></li>

				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('2')"><a href="#">회원 관리</a></p>
			<div id="left_menu02">
				<ul>
					<li><a href='/member/list'>회원목록</a></li>
		    	<li><a href='/member/loginlist'>접속기록</a></li>
<!--          <li><a href='javascript:;alert("점검중!")'>접속기록</a></li>  -->
					<li><a href='/memo/list'>쪽지관리</a></li>
					<li><a href="javascript:void(0)" onclick="javascript:window.open('/memo/popup_adminwrite','memo','width=650,height=350')">쪽지쓰기</a></li>
					<!--<li><a href="javascript:void(0)" onclick="javascript:window.open('/member/issue_virtual_money','memo','width=650,height=350')">가상머니지급</a></li>-->
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('3')"><a href="#">게시판 관리</a></p>
			<div id="left_menu03">
				<ul>
					<li><a href='/board/list?province=5'>자유게시판</a></li>
					<li><a href='/board/list?province=2'>공지사항</a></li>
					<li><a href='/board/list?province=7'>이벤트</a></li>
					<li><a href='/board/questionlist'>고객센터</a></li>
					<li><a href='/board/write'>게시물쓰기</a></li>
					<li><a href='/board/site_rule_edit?type=1'>회원약관 수정</a></li>
					<li><a href='/board/site_rule_edit?type=2'>배팅규정 수정</a></li>
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('4')"><a href="#">게임 관리</a></p>
			<div id="left_menu04">
				<ul>
					<li><a href='/game/gamelist'>게임관리</a></li>
					<li><a href='/game/betlist'>배팅현황</a></li>
					<li><a href='/game/betcancellist'>배팅취소현황</a></li>
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('11')"><a href="#"><font color='eclipse'>라이브 게임관리</a></p>
			<div id="left_menu11">
				<ul>
					<li><a href='/LiveGame/reload_today_game'>게임 리로드</a></li>
					<li><a href='#' onclick="window.open('/LiveGame/collect','','resizable=yes scrollbars=yes top=5 left=5 width=1100 height=650')">게임등록</a></li>
					<li><a href='/LiveGame/game_list'>게임관리</a></li>
					<li><a href='/LiveGame/betting_list'>배팅현황</a></li>
					<!--<li><a href='/LiveGame/virtual_betting_list'>가상배팅현황</a></li>-->
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('9')"><a href="#"><font color='eclipse'>게임 등록</font></a></p>
			<div id="left_menu09">
				<ul>
					<li><a href='/gameUpload/'>게임관리</a></li>
					<li><a href='/gameUpload/result_list'>게임마감</a></li>
					<li><a href='/gameUpload/remote_list'>연동관리</a></li>
					<li><a href='/league/list'>리그관리</a></li>
					<li><a href='/league/category_list'>종목관리</a></li>
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('5')"><a href="#">총판 관리</a></p>
			<div id="left_menu05">
				<ul>
					<li><a href='/partner/list'>총판목록</a></li>
<!--			<li><a href='/partner/memolist'>총판쪽지</a></li>  -->
<!--			<li><a href='/partner/accounting'>총판정산</a></li>  -->
<!--			<li><a href='/partner/memoadd'>쪽지쓰기</a></li>  -->
					<li><a href='/partner/childlist'>하부총판목록</a></li>
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('6')"><a href="#">통계</a></p>
			<div id="left_menu06">
				<ul>
					<li><a href='/stat/site'>사이트현황</a></li>
					<li><a href='/stat/money'>입/출금통계</a></li>
					<li><a href='/stat/bet'>배팅 통계</a></li>
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('7')"><a href="#">시스템 관리</a></p>
			<div id="left_menu07">
				<ul>
					<li><a href='/config/globalconfig?logo=mini' >기본 설정</a></li>
					<li><a href='/config/level?logo=mini'>등급 설정</a></li>

					<li><a href='/config/point?logo=mini'>포인트설정</a></li>

					<li><a href='/config/bet?logo=mini'>배팅 설정</a></li>
					<li><a href='/config/changeRate'>환수율 설정</a></li>
					<li><a href='/config/popuplist'>팝업 설정</a></li>

          <li style="width: 150px;"><hr></li>

					<li><a href='/config/dataexcel'>DB추출</a></li>
                    <li><a href='/config/gamedataexcel'>게임DB추출</a></li>
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('8')"><a href="#">관리자</a></p>
			<div id="left_menu08">
				<ul>
					<li><a href='/stat/adminlog'>로그인 내역</a></li>
					<li><a href='/config/admin_ip'>아이피 관리</a></li>
					<!--<li><a href='/stat/siteaccount'>사이트 정산</a></li>-->
				</ul>
			</div>