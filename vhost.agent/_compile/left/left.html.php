<?php /* Template_ 2.2.3 2016/06/27 06:50:12 C:\inetpub\combined_manager\vhost.agent\_template\left\left.html */?>
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
			<div class="notice">
				<marquee onmouseout="this.start();" onmouseover="this.stop();"  scrollamount="2" scrolldelay="-1">
				  환영합니다.
				</marquee>
			</div>
			<p class="m_menu" onclick="mainchgna('4')"><a href="#">게임 관리</a></p>
			<div id="left_menu04">
				<ul>
					<li><a href="javascript:void(0)" onclick="javascript:window.open('/Ladder/','사다리','width=650,height=350')">사다리</a></li>
					<li><a href="javascript:void(0)" onclick="javascript:window.open('/Daridari/','다리다리','width=650,height=350')">다리다리</a></li>
					<li><a href="javascript:void(0)" onclick="javascript:window.open('/Race/','달팽이레이스','width=650,height=350')">달팽이레이스</a></li>
					<li><a href="javascript:void(0)" onclick="javascript:window.open('/PowerBall/','파워볼','width=650,height=350')">파워볼</a></li>
				</ul>
			</div>
			<p class="m_menu" onclick="mainchgna('11')"><a href="#"><font color='orange''>라이브 게임관리</a></p>
			<div id="left_menu11">
				<ul>
					<li><a href='/LiveGame/reload_today_game'>게임 리로드</a></li>
					<li><a href='#' onclick="window.open('/LiveGame/collect','','resizable=yes scrollbars=yes top=5 left=5 width=1100 height=650')">게임등록</a></li>
				</ul>
			</div>