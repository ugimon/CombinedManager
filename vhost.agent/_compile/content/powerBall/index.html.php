<?php /* Template_ 2.2.3 2016/03/03 06:41:42 C:\inetpub\combined_manager\vhost.agent\_template\content\powerBall\index.html */?>
<script language="JavaScript">
	window.onload = function ()
	{
		power_ball_listener();
		begin_power_ball_listener();
	}
	
	function begin_power_ball_listener()
	{
		window.setInterval('power_ball_listener()', 10000);
	}
			
	function power_ball_listener()
	{
		$.ajax({
			type: "POST",
			url:"/PowerBall/powerBallListener",
			dataType:'json',
			success : function(result) {
				on_power_ball_listener(result);
			}
		});
	}
	
	function on_power_ball_listener(result)
	{
		return;
	}
</script>
	
</head>
<body>

<div id="wrap_pop">
	<div id="pop_title">
		<h1>파워볼 게임 진행현황</h1>
	</div>

	<table cellspacing="1" class="tableStyle_membersWrite" summary="사다리">
	<legend class="blind">파워볼 게임 진행현황</legend>
		<tr>
		  <th>현재 회차</th>
		  <td id='mis_games'>
		  </td>
		</tr>
		<tr>
		  <th>정산오류게임</th>
		  <td id='mis_account'>
		  </td>
		</tr>
	</table>
</div>

</body>
</html>