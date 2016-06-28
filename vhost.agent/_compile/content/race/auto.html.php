<?php /* Template_ 2.2.3 2016/03/03 06:41:42 C:\inetpub\combined_manager\vhost.agent\_template\content\race\auto.html */?>
<script language="JavaScript">
	window.onload = function ()
	{
		race_listener();
		begin_race_listener();
	}
	
	function begin_race_listener()
	{
		window.setInterval('race_listener()', 30000);
	}
			
	function race_listener()
	{
		$.ajax({
			type: "POST",
			url:"/Race/raceListener",
			dataType:'json',
			success : function(result) {
				on_race_listener(result);
			}
		});
	}
	
	function on_race_listener(result)
	{
		var mis_games = '';
		if( result.length <=0)
			return;
		for(i=0; i<result.length-1; i++)
		{
			game = result[i]['home_team'].split(" ");
			mis_games += game[0]+"<br>";
		}
		
		game = result[result.length-1]['home_team'].split(" ");
		mis_games += "<font color='#CC3D3D'><b>"+game[0]+" [진행중...]</b></font>";
		$("#mis_games").html(mis_games);
	}
</script>
	
</head>
<body>

<div id="wrap_pop">
	<div id="pop_title">
		<h1>달팽이레이스 진행현황</h1>
	</div>

	<table cellspacing="1" class="tableStyle_membersWrite" summary="달팽이레이스">
	<legend class="blind">달팽이레이싱 진행현황</legend>
		<tr>
		  <th>마감 누락게임</th>
		  <td id='mis_games'>
		  </td>
		</tr>
	</table>
</div>

</body>
</html>