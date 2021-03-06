<?php /* Template_ 2.2.3 2016/06/27 07:04:02 C:\inetpub\combined_manager\vhost.manager\_template\content\gameUpload\popup.game_upload.html */
$TPL_category_list_1=empty($TPL_VAR["category_list"])||!is_array($TPL_VAR["category_list"])?0:count($TPL_VAR["category_list"]);?>
<script src="/js/jquery-1.7.1.min.js"></script>
<script>
	String.prototype.Trim = function() { return this.replace(/(^\s*)|(\s*$)/g, ""); }
	var rowIndex=1;
	var select_obj=0;
		
	function save()
	{
	    var object = document.getElementsByName('league[]');
	    if(object.length==0)
	    {
	        alert("경기정보가 없습니다");
	        return false;
	    }
	    for(var i=0;i<object.length;i++)
	    {
				if(object[i].value=="")
				{
	      	alert("리그를 선택하세요");
	        object[i].focus();
	        object[i].style.backgroundColor = "#EFEFEF";
	        return false;
	        break;
				}
	    }
	    var object = document.getElementsByName('gameDate[]');
	    for(var i=0;i<object.length;i++)
	    {
	    	if(object[i].value=="")
	    	{
	      	alert("날자를 입력하세요");
	        object[i].focus();
	        object[i].style.backgroundColor = "#EFEFEF";
	        return false;
	        break;
				}
	    }
	    var object = document.getElementsByName('HomeTeam[]');
	    for(var i=0;i<object.length;i++)
	    {
	    	if(object[i].value=="")
	    	{
	      	alert("홈팀이름을 입력하세요");
	        object[i].focus();
	        object[i].style.backgroundColor = "#EFEFEF";
	        return false;
	        break;
	      }
	    }
	    var object = document.getElementsByName('AwayTeam[]');
	    for(var i=0;i<object.length;i++){
	        if(object[i].value==""){
	            alert("원정팀이름을 입력하세요");
	            object[i].focus();
	            object[i].style.backgroundColor = "#EFEFEF";
	            return false;
	            break;
	        }
	    }
	}

	function listupLeague(obj)
	{
		if( obj==null)
			return;
			
		$.ajaxSetup({async:false});
		
		if(obj!=null)	var param={category:obj.value};
		else					var param={category:""};
		
		select_obj=obj;

		$.post("/league/ajaxLeagueList?", param, onListupLeague, "json");
	}
	
	function onListupLeague(jsonText)
	{
	    var innerHTML ="<option value=''>리그선택</option>";
	  /*  
	    for(i=0; i<jsonText.length; ++i)
	    {
	    	var data = jsonText[i];
	    	innerHTML += "<option value="+data.sn+">"+data.name+"</option>";
	    }
	    */
	    
	    var object = document.getElementsByName("kind[]")
	    
	    for(var i=0;i<object.length;i++)
	    {
	    	if(object[i]==select_obj)
	    	{
	    		var league_object = document.getElementsByName("league[]");
	    		
	    		while(league_object[i].options.length > 0)
						league_object[i].options.remove(0);
					
					if(jsonText!=null)
					{
		    		for(j=0; j<jsonText.length; ++j)
				    {
				    	var data = jsonText[j];
				    	league_object[i].add(new Option(data.name, data.sn));
				    }
				  }
	    		break;
	    	}
	    }

	   	return true;
	}

	function onCopy(obj, special_type_index, game_type_index)
	{
 		var clickedRow =$(obj).parent().parent();
		var newRow = clickedRow.clone();
		
		// 종목
		var selected_value = $('#kind option:selected').val();
		newRow.find('#kind').val(selected_value);
		
		// 일반,스페셜,라이브배팅
		if( special_type_index==-1)
		{
			selected_value = $('#special_type option:selected').val();
			newRow.find('#special_type').val(selected_value);
		}
		else
		{
			selected_value = $('#special_type option:eq('+special_type_index+')').val();
			newRow.find('#special_type').val(selected_value);
		}
		// 승무패, 핸디캡, 언더오버
		if( game_type_index==-1)
		{
			selected_value = $('#game_type option:selected').val();
			newRow.find('#game_type').val(selected_value);
		}
		else
		{
			selected_value = $('#game_type option:eq('+game_type_index+')').val();
			newRow.find('#game_type').val(selected_value);
		}
		
		selected_value = $('#league option:selected').val();
		newRow.find('#league').val(selected_value);
		
		selected_value = $('#game_hour option:selected').val();
		newRow.find('#game_hour').val(selected_value);
		
		selected_value = $('#game_time option:selected').val();
		newRow.find('#game_time').val(selected_value);
		//select copy - end
		
		newRow.insertAfter(clickedRow);
		return newRow;
	}
	
	function onDelete(obj)
	{
 		var clickedRow =$(obj).parent().parent();
		clickedRow.remove();
	}
	
	function onCopyAll(obj)
	{
		//라이브 - 승무패, 핸디, 언더오버	
		
		newRow = onCopy(obj, 2, 3);
		obj=newRow.find('#type_copy').context;
		
		newRow = onCopy(obj, 2, 2);
		obj=newRow.find('#type_copy').context;
		
		newRow = onCopy(obj, 2, 1);
		obj=newRow.find('#type_copy').context;
		
		//스페셜 - 승무패, 핸디, 언더오버
		newRow = onCopy(obj, 1, 3);
		obj=newRow.find('#type_copy').context;
		
		newRow = onCopy(obj, 1, 2);
		obj=newRow.find('#type_copy').context;
		
		newRow = onCopy(obj, 1, 1);
		obj=newRow.find('#type_copy').context;
		
		//일   반 - 핸디, 언더오버
		newRow = onCopy(obj, 0, 3);
		obj=newRow.find('#type_copy').context;
		
		newRow = onCopy(obj, 0, 2);
		obj=newRow.find('#type_copy').context;
	}

</script>

</head>

<body onload="listupLeague(null);">

<div id="wrap_pop">

	<div id="pop_title">
		<h1>경기등록</h1><p><a href="#"><img src="/img/btn_s_close.gif" onclick="self.close();"></a></p>
	</div>

	<form name="form1" method="post" action="/gameUpload/gameuploadProcess" onsubmit="return save()">
		<input type="hidden" name="pidx" value="<?php echo $TPL_VAR["pidx"]?>">
		<table id="table_list" cellspacing="1" class="tableStyle_normal">
			<thead>
				<tr>
					<th>대분류</th>
					<th>배팅방식</th>
					<th>종목</th>
					<th>리그선택</th>
					<th>경기일시</th>
					<th>홈팀</th>
					<th>원정팀</th>
					<th>승배당</th>
					<th>무배당</th>
					<th>패배당</th>
					<th>관리</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select name='special_type[]' align='center' id='special_type'>
							<option name='normal' value=0>일반</option>
							<option name='spcial' value=1>스페셜</option>
							<option name='multi' value=2>라이브배팅</option>
							<option name='ladder' value=5>사다리</option>
							<option name='powerball' value=6>파워볼</option>
							<option name='snailrace' value=7>달팽이</option>
							<option name='daridari' value=8>다리다리</option>
						</select>
					</td>
					<td>
						<select name='gametype[]' align='center' id='game_type'>
							<option value='0'>지정안함</option>
							<option value='1' selected>승무패</option><option value='2'>핸디캡</option><option value='4'>언더오버</option><option value='5'>승패+스페셜</option>
						</select>
					</td>
					<td>
						<select name='kind[]' align='center' onChange="listupLeague(this);" id='kind'>
							<option value=''>종목선택</option>
<?php if($TPL_category_list_1){foreach($TPL_VAR["category_list"] as $TPL_V1){?>
								<option value="<?php echo $TPL_V1["name"]?>" <?php if($TPL_VAR["select_category"]==$TPL_V1["name"]){?> selected<?php }?>><?php echo $TPL_V1["name"]?></option>
<?php }}?>
						</select>
					</td>
					<td>
						<select name="league[]" id='league'>
						</select>
					</td>
					<td><input type="text" id="gameDate" name="gameDate[]" size="10" maxlength="10" onclick="new Calendar().show(this);"  readonly="readonly" style="border:1px #97ADCE solid;">&nbsp;<?php echo $TPL_VAR["gameHour"]?><?php echo $TPL_VAR["gameTime"]?></td>
					<td><input type="text" id="HomeTeam" name="HomeTeam[]" size="11"></td>
					<td><input type="text" id="AwayTeam" name="AwayTeam[]" size="11"></td>
					<td><input type="text" id="type_a" name="type_a[]" size="5" onkeyup='this.value=this.value.replace(/[^0-9.]/gi,"")'></td>
					<td><input type="text" id="type_b" name="type_b[]" size="5"></td>
					<td><input type="text" id="type_c" name="type_c[]" size="5" onkeyup='this.value=this.value.replace(/[^0-9.]/gi,"")'></td>
					<td>
						<input name='del' type='button' id='del' value='삭제' class="btnStyle3" onClick="onDelete(this);">
						<input name='copy' type='button' id='copy' value='복사' class="btnStyle3" onClick="onCopy(this, -1, -1)">
						<input name='type_copy' type='button' id='type_copy' value='타입복사' onClick="onCopyAll(this);" class="btnStyle3">
					</td>
				</tr>
				<tr class="gameAdd">
					<td colspan="11">
						<input type="checkbox" name="kubun" value="0">&nbsp;&nbsp;발매&nbsp;&nbsp;
						<p class="btn"><input name="submit" type="submit" value="경기올리기" class="Qishi_submit_a"></p>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
</body>
</html>