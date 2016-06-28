<?
/*
* Index Controller
*/
class DaridariController extends WebServiceController
{
	//▶ 사다리 게임 자동 처리
	function indexAction()
	{
		$this->popupDefine('daridari');		// 'daridari' 파라미터 받는 코드 없음 무의미

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}

		$this->view->define("content","content/daridari/auto.html");
		$this->display();
	}

	function daridariListenerAction()
	{
		$this->popupDefine('daridari');

		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}

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
		$now_cnt = floor(($now_hour*60+$now_min)/3) + 1;

		// 마지막 회차 게임이 있나 확인
		$is_game_made_last = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=593 and gameDate='".$now_date."' and home_team='480회차 [홀]' ");
		//echo "Game is : ".var_dump($is_game_made)."<br>";

		//게임생성
		if(count($is_game_made_last)<=0)
		{
			//echo '<br>if 1st game is not made, enter at this point.<br>';
			for($i=$now_cnt; $i <= 480; $i++)
			{
				$game_hour = floor($i*3/60);
				$game_minute = ($i*3)-($game_hour*60);

				if($game_hour<10)		{$game_hour = "0".$game_hour;}
				if($game_minute<10)	{$game_minute = "0".$game_minute;}

				$is_game_made = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=593 and gameDate='".$now_date."' and home_team='".$i."회차 [홀]'");

				if( count($is_game_made) <=0)
				{
					$game_model->addChild($parentSn,'기타',593,$i."회차 [홀]",$i."회차 [짝]",$now_date,$game_hour,$game_minute,'','0',1,8,1.95,1,1.95);
					$game_model->addChild($parentSn,'기타',594,$i."회차 [좌측시작]",$i."회차 [우측시작]",$now_date,$game_hour,$game_minute,'','0',1,8,1.95,1,1.95);
					$game_model->addChild($parentSn,'기타',595,$i."회차 [3줄]",$i."회차 [4줄]",$now_date,$game_hour,$game_minute,'','0',1,8,1.95,1,1.95);
					$game_model->addChild($parentSn,'기타',596,$i."회차 [좌3]",$i."회차 [좌4]",$now_date,$game_hour,$game_minute,'','0',1,8,3.60,0,3.60);
					$game_model->addChild($parentSn,'기타',597,$i."회차 [우3]",$i."회차 [우4]",$now_date,$game_hour,$game_minute,'','0',1,8,3.60,0,3.60);
				}
			}
		}

		$dari_data = $this->parseDaridariAction(); // 결과 파싱
		//echo "<br>dari data: ".var_dump($dari_data)."<br>";

		//게임마감
		$edit_game = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=593 and gameDate='".$now_date."' and home_team='".$dari_data["th"]."회차 [홀]'");
		$edit_game_start	= $game_model->getRow('*', $game_model->db_qz."child", "league_sn=594 and gameDate='".$now_date."' and home_team='".$dari_data["th"]."회차 [좌측시작]'");
		$edit_game_line		= $game_model->getRow('*', $game_model->db_qz."child", "league_sn=595 and gameDate='".$now_date."' and home_team='".$dari_data["th"]."회차 [3줄]'");
		$edit_game_leftline	= $game_model->getRow('*', $game_model->db_qz."child", "league_sn=596 and gameDate='".$now_date."' and home_team='".$dari_data["th"]."회차 [좌3]'");
		$edit_game_rightline	= $game_model->getRow('*', $game_model->db_qz."child", "league_sn=597 and gameDate='".$now_date."' and home_team='".$dari_data["th"]."회차 [우3]'");

		if(sizeof($edit_game)!=0 && $edit_game['kubun'] != 1 && $dari_data["result"] !="")
		{
			$resultOk = true;
			//홀/짝 결과
			if(strstr($dari_data["result"], "odd"))
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
			if(strstr($dari_data["start"], "left"))
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
			if(strstr($dari_data["line"], "3"))
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
			if(strstr($dari_data["start"], "left") && strstr($dari_data["line"], "3"))
			{
				$home_score = '1';
				$away_score = '0';
				$process_model->resultGameProcess($edit_game_leftline["sn"], $home_score, $away_score);
			}
			else if(strstr($dari_data["start"], "left") && strstr($dari_data["line"], "4"))
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
			if(strstr($dari_data["start"], "right") && strstr($dari_data["line"], "3"))
			{
				$home_score = '1';
				$away_score = '0';
				$process_model->resultGameProcess($edit_game_rightline["sn"], $home_score, $away_score);
			}
			else if(strstr($dari_data["start"], "right") && strstr($dari_data["line"], "4"))
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
		//echo "<pre>", var_dump($dari_data), "</pre>";
		//$mis_game_list = $ladder_model->misGameList(date("Y-m-d"));
		$result['flag'] = $resultOk;
		$result['date'] = $now_date." ".$now_time;
		$result['msg'] = $dari_data["th"]."회차의 결과가 처리되었습니다.";

		echo json_encode($result);
	}

	function parseDaridariAction()
	{
		$url =  "http://www.named.com/data/json/games/dari/result.json";

		$json_string = file_get_contents($url);
		$json_decode = json_decode($json_string, true);

		$dari_data["th"] = $json_decode['r'];

		if($json_decode['o'] == "ODD")
		{
			$dari_data["result"] = "odd";
		}
		else if($json_decode['o'] == "EVEN")
		{
			$dari_data["result"] = "even";
		}

		if($json_decode['l'] == "3")
		{
			$dari_data["line"] = "3";
		}
		else if($json_decode['l'] == "4")
		{
			$dari_data["line"] = "4";
		}

		if($json_decode['s'] == "LEFT")
		{
			$dari_data["start"] = "left";
		}
		else if($json_decode['s'] == "RIGHT")
		{
			$dari_data["start"] = "right";
		}

		return $dari_data;
	}

	function SaveFile($data)
	{
		$data .="\r\n";
		$f = @fopen("daridari_debug.txt", 'a+');
		if (!$f) {
			return false;
		} else {
			$bytes = fwrite($f, $data);
			fclose($f);
		}
	}
}
?>
