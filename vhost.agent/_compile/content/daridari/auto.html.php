<?php /* Template_ 2.2.3 2016/06/22 18:51:58 C:\inetpub\combined_manager\vhost.agent\_template\content\daridari\auto.html */?>
<script language="JavaScript">
	window.onload = function ()
	{
		ladder_listener();
		begin_ladder_listener();
	}

	function begin_ladder_listener()
	{
		window.setInterval(ladder_listener, 10000);
	}

	function ladder_listener()
	{
		$.ajax({
			type: "POST",
			url:"/Daridari/daridariListener",
			dataType:'json',
			success : function(result) {
				//alert(result);
				//on_ladder_listener(result);
				displayResultMsg(result);
			}
		});
	}
	function displayResultMsg(result)
	{
		if(result['flag']) {
			$("#result_msg").html("<br>"+result['msg']+"("+result['date']+")");
		}
	}
	function on_ladder_listener(result)
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
		<h1>다리다리 게임 진행현황</h1>
	</div>

	<table cellspacing="1" class="tableStyle_membersWrite" summary="사다리">
	<legend class="blind">다리다리 게임 진행현황</legend>
		<tr>
		  <th>마감 누락게임</th>
		  <td id='mis_games'>
		  </td>
		</tr>
	</table>
	<span id="result_msg"></span>
</div>

</body>
</html>