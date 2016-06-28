<?
/*
* Index Controller
*/
class LadderController extends WebServiceController 
{
	//▶ 사다리 게임 자동 처리
	function indexAction()
	{
		$this->popupDefine('ladder');
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		
		$this->view->define("content","content/ladder/auto.html");
		$this->display();
	}
	
	function ladderListenerAction()
	{
		$this->popupDefine('ladder');
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		
		$ladder_data = $this->parseLadder2Action();
		
		$game_model					= $this->getModel("GameModel");
		$league_model 				= $this->getModel("LeagueModel");
		$process_model				= $this->getModel("ProcessModel");
		$ladder_model				= $this->getModel("LadderModel");
			
		//오늘날짜
		$now_date = date("Y-m-d");
		$now_time = date("H:i");
		
		$now_hour	= date("H");
		$now_min	= date("i");
		
		//현재회차
		$now_cnt = floor(($now_hour*60+$now_min)/5);
		
		$is_game_made_280 = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=505 and gameDate='".$now_date."' and home_team='280회차 [홀]' ");
		
		//게임생성
		if(count($is_game_made_280)<=0)
		{
			for($i=$now_cnt+1; $i < 288; $i++)
			{
				$now_hour = floor($i*5/60);
				$now_minute = $i*5-$now_hour*60;
				
				if($now_hour<10)		{$now_hour = "0".$now_hour;}
				if($now_minute<10)	{$now_minute = "0".$now_minute;}
				
				$is_game_made = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=505 and gameDate='".$now_date."' and home_team='".$i."회차 [홀]'");
				
				if( count($is_game_made) <=0)
				{
					$game_model->addChild($parentSn,'기타',505,$i."회차 [홀]",$i."회차 [짝]",$now_date,$now_hour,$now_minute,'','0',1,5,1.95,1,1.95);
					$game_model->addChild($parentSn,'기타',504,$i."회차 [좌측시작]",$i."회차 [우측시작]",$now_date,$now_hour,$now_minute,'','0',1,5,1.95,1,1.95);
					$game_model->addChild($parentSn,'기타',503,$i."회차 [3줄]",$i."회차 [4줄]",$now_date,$now_hour,$now_minute,'','0',1,5,1.95,1,1.95);
					$game_model->addChild($parentSn,'기타',502,$i."회차 [좌3]",$i."회차 [좌4]",$now_date,$now_hour,$now_minute,'','0',1,5,3.60,0,3.60);
					$game_model->addChild($parentSn,'기타',501,$i."회차 [우3]",$i."회차 [우4]",$now_date,$now_hour,$now_minute,'','0',1,5,3.60,0,3.60);
				}
			}
		}


		//게임마감
		$edit_game = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=505 and gameDate='".$now_date."' and home_team='".$ladder_data["th"]."회차 [홀]'");
		$edit_game_start	= $game_model->getRow('*', $game_model->db_qz."child", "league_sn=504 and gameDate='".$now_date."' and home_team='".$ladder_data["th"]."회차 [좌측시작]'");
		$edit_game_line		= $game_model->getRow('*', $game_model->db_qz."child", "league_sn=503 and gameDate='".$now_date."' and home_team='".$ladder_data["th"]."회차 [3줄]'");
		$edit_game_leftline	= $game_model->getRow('*', $game_model->db_qz."child", "league_sn=502 and gameDate='".$now_date."' and home_team='".$ladder_data["th"]."회차 [좌3]'");
		$edit_game_rightline	= $game_model->getRow('*', $game_model->db_qz."child", "league_sn=501 and gameDate='".$now_date."' and home_team='".$ladder_data["th"]."회차 [우3]'");
		
		if(sizeof($edit_game)!=0 && $edit_game['kubun'] != 1 && $edit_game['home_score'] =='' && $edit_game['away_score'] =='' && $ladder_data["result"] !="")
		{
			//홀/짝 결과
			if(strstr($ladder_data["result"], "odd"))
			{
				$home_score = '1';
				$away_score = '0';
			}
			else
			{
				$home_score = '0';
				$away_score = '1';
			}
			$process_model->resultGameProcess($edit_game["sn"], $home_score, $away_score);
			
			//시작점 결과
			if(strstr($ladder_data["start"], "left"))
			{
				$home_score = '1';
				$away_score = '0';
			}
			else
			{
				$home_score = '0';
				$away_score = '1';
			}
			$process_model->resultGameProcess($edit_game_start["sn"], $home_score, $away_score);
			
			//줄 수 결과
			if(strstr($ladder_data["line"], "3"))
			{
				$home_score = '1';
				$away_score = '0';
			}
			else
			{
				$home_score = '0';
				$away_score = '1';
			}
			$process_model->resultGameProcess($edit_game_line["sn"], $home_score, $away_score);

			// 좌3 4 결과
			if(strstr($ladder_data["start"], "left") && strstr($ladder_data["line"], "3"))
			{
				$home_score = '1';
				$away_score = '0';
				$process_model->resultGameProcess($edit_game_leftline["sn"], $home_score, $away_score);
			}
			else if(strstr($ladder_data["start"], "left") && strstr($ladder_data["line"], "4"))
			{
				$home_score = '0';
				$away_score = '1';
				$process_model->resultGameProcess($edit_game_leftline["sn"], $home_score, $away_score);
			}
			else
			{
				// 좌3, 4 해당 없음 
				$process_model->resultGameProcess($edit_game_leftline["sn"], '0', '0');
			}

			// 우3 4 결과
			if(strstr($ladder_data["start"], "right") && strstr($ladder_data["line"], "3"))
			{
				$home_score = '1';
				$away_score = '0';
				$process_model->resultGameProcess($edit_game_rightline["sn"], $home_score, $away_score);
			}
			else if(strstr($ladder_data["start"], "right") && strstr($ladder_data["line"], "4"))
			{
				$home_score = '0';
				$away_score = '1';
				$process_model->resultGameProcess($edit_game_rightline["sn"], $home_score, $away_score);
			}
			else
			{
				// 우3, 4 해당 없음 
				$process_model->resultGameProcess($edit_game_rightline["sn"], '0', '0');
			}
			
		}
		echo "<pre>", var_dump($ladder_data), "</pre>";
		//$mis_game_list = $ladder_model->misGameList(date("Y-m-d"));

		echo json_encode($mis_game_list);
	}
	
	function parseLadderAction()
	{
		$url = "http://www.scoregame.co.kr/?mod=analysPday&iframe=Y";
		
		$data = '';
		
		//데이터 가져오기
		$data = file_get_contents($url);
		
		//데이터 회차별 파싱
		$data = strstr($data, '<div class="ladder-data">');
		$data = strstr($data, "<tbody>");
		$data = substr($data, 7);
		
		$data_array = explode("</tbody>", $data);
		$ladder_result_array = explode("</tr>", $data_array[0]);
		
		//회차, 결과 뽑아내기
		$ladder_data = array();

		$method_th = '/data="?([^"]+)/i';		
		$method_result = '/class="?([^">]+)/i';
		$method_start =  '/class="?([^">]+)/i';;
		$method_line = '/class="?([^">]+)/i';

		$tmp_array = explode("</td>", $ladder_result_array[0]);
			
		preg_match_all($method_th, $tmp_array[0], $th, PREG_SET_ORDER);
		preg_match_all($method_start, $tmp_array[1], $result, PREG_SET_ORDER);
		preg_match_all($method_line, $tmp_array[2], $start, PREG_SET_ORDER);
		preg_match_all($method_result, $tmp_array[3], $line, PREG_SET_ORDER);
		
		//$th[0][1] : 회차
		
		$ladder_data["th"]			= $th[0][1];
		$ladder_data["start"]		= $start[0][1];
		$ladder_data["line"]		= $line[0][1];
		$ladder_data["result"]	= $result[0][1];

		return $ladder_data;
	}
	
	function parseLadder2Action()
	{
//		$url = "http://born.ladderraft.com/ladder.php";

		$url =  "http://named.com/data/json/ladder/result.json";
	
		$json_string = file_get_contents($url);
		$json_decode = json_decode($json_string, true);

		
		$ladder_data["th"] = $json_decode['times'];
		
		if($json_decode['answer'] == "ODD")
		{
			$ladder_data["result"] = "odd";
		}
		else if($json_decode['answer'] == "EVEN")
		{
			$ladder_data["result"] = "even";
		}

		if($json_decode['ladder_type'] == "type1")
		{
			$ladder_data["line"] = "3";
		}
		else if($json_decode['ladder_type'] == "type2")
		{
			$ladder_data["line"] = "4";
		}

		if($json_decode['start_point'] == "first")
		{
			$ladder_data["start"] = "left";
		}
		else if($json_decode['start_point'] == "second")
		{
			$ladder_data["start"] = "right";
		}

/*
		$date_year		= date("Y");
		$date_month	= date("m");
		$date_day		= date("d");
		
		$data = '';
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
		curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 5); 

		$data = curl_exec($ch);
		$err = curl_error($ch);


		if($data===false){
			$this->SaveFile("[".$str_time."]".$err);
			return -1;
		} 
		
		$begin_pos = strpos($data, "<div class=\"game_result\">");
		if($begin_pos ===false)
		{
			return -1;
		}
		$end_pos = strpos($data, "</div>", $begin_pos+1);
		
		$div_html = substr($data, $begin_pos, $end_pos-$begin_pos);
		
		//대기게임
		$pos = $begin_pos+1;
		$li_begin = strpos($data, "<li", $pos+1);
		$li_end = strpos($data, "</li", $pos+1);
		$li_html = substr($data, $li_begin, $li_end-$li_begin);
		
		//최근게임
		$pos = $li_end+1;
		$li_begin = strpos($data, "<li", $pos+1);
		$li_end = strpos($data, "</li", $pos+1);
		$li_html = substr($data, $li_begin, $li_end-$li_begin);
		$pos = $li_begin+1;
		
		//class로 결과 판단
		$class_begin = strpos($li_html, "prev")+5;
		$class_end = strpos($li_html, "\"", $class_begin+1);
		$class = substr($li_html, $class_begin, $class_end-$class_begin);
	
		$splits = split(" ", $class);
		
		if($splits[0]=="row_even")
		{
			$ladder_data["result"] = "even";
			
			if($splits[1]=="row_even_first")
			{
				$ladder_data["line"] = "3";
				$ladder_data["start"] = "left";
			}
			else if($splits[1]=="row_even_second")
			{
				$ladder_data["line"] = "4";
				$ladder_data["start"] = "right";
			}
		}
		else if($splits[0]=="row_odd")
		{
			$ladder_data["result"] = "odd";
			if($splits[1]=="row_odd_first")
			{
				$ladder_data["line"] = "4";
				$ladder_data["start"] = "left";
			}
			else if($splits[1]=="row_odd_second")
			{
				$ladder_data["line"] = "3";
				$ladder_data["start"] = "right";
			}
		}
		
		//회차 구하기
		$th_begin = strpos($li_html, "-")+1;
		$th_end = strpos($li_html, "<", $th_begin);
		$th = substr($li_html, $th_begin, $th_end-$th_begin);
		
		$ladder_data["th"] = $th;
*/		
		return $ladder_data;
	}
	
	function SaveFile($data)
	{
		$data .="\r\n";
		$f = @fopen("ladder_debug.txt", 'a+');
		if (!$f) {
			return false;
		} else {
			$bytes = fwrite($f, $data);
			fclose($f);
		}
	}
}
?>
