<?
/*
* Index Controller
*/
class RaceController extends WebServiceController 
{
	//▶ 달팽이 경주 자동 처리
	function indexAction()
	{
		$this->popupDefine('race');
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		
		$this->view->define("content","content/race/auto.html");
		$this->display();
	}
	
	function raceListenerAction()
	{
		$this->popupDefine('race');
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		
		$race_data = $this->parseRaceAction();
		
		$game_model					= $this->getModel("GameModel");
		$league_model 				= $this->getModel("LeagueModel");
		$process_model				= $this->getModel("ProcessModel");
		$race_model				= $this->getModel("RaceModel");
			
		//오늘날짜
		$now_date = date("Y-m-d");
		$now_time = date("H:i");
		
		$now_hour	= date("H");
		$now_min	= date("i");
		
		//현재회차
		$now_cnt = floor(($now_hour*60+$now_min)/5);
		
		$is_game_made_280 = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=500 and gameDate='".$now_date."' and home_team='280회차 네팽이' ");
		
		//게임생성
		if(count($is_game_made_280)<=0)
		{
			for($i=$now_cnt+1; $i < 288; $i++)
			{
				$now_hour = floor($i*5/60);
				$now_minute = $i*5-$now_hour*60;
				
				if($now_hour<10)		{$now_hour = "0".$now_hour;}
				if($now_minute<10)	{$now_minute = "0".$now_minute;}
				
				$is_game_made = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=500 and gameDate='".$now_date."' and home_team='".$i."회차 네팽이'");
				
				if( count($is_game_made) <=0)
				{
					$game_model->addChild($parentSn,'기타',500,$i."회차 네팽이",$i."회차 드팽이",$now_date,$now_hour,$now_minute,'','0',1,7,'2.70','2.70','2.70');
				}
			}
		}


		//게임마감
		$edit_game = $game_model->getRow('*', $game_model->db_qz."child", "league_sn=500 and gameDate='".$now_date."' and home_team='".$race_data["th"]."회차 네팽이'");
		
		if(sizeof($edit_game)!=0 && $edit_game['kubun'] != 1 && $edit_game['home_score'] =='' && $edit_game['away_score'] =='')
		{
			//결과
			if(strstr($race_data["result"], "p1"))
			{
				$home_score = '1';
				$away_score = '0';
			}
			else if(strstr($race_data["result"], "p2"))
			{
				$home_score = '0';
				$away_score = '0';
			}
			else if(strstr($race_data["result"], "p3"))
			{
				$home_score = '0';
				$away_score = '1';
			}
			
			$process_model->resultGameProcess($edit_game["sn"], $home_score, $away_score);
		}
		
		//$mis_game_list = $race_model->misGameList(date("Y-m-d"));

		echo json_encode($mis_game_list);
	}
	
	function parseRaceAction()
	{
		$url = "http://named.com/games/racing/pop/race_result.php";
	
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
		
		if($data===false)
			$this->SaveFile($err);
		
		$begin_pos = strpos($data, "<ul class=\"result_bd\">");
		if($begin_pos ===false)
		{
			return -1;
		}
		$end_pos = strpos($data, "</ul>", $begin_pos+1);
		
		$div_html = substr($data, $begin_pos, $end_pos-$begin_pos);
		
		// game unit
		$pos = $begin_pos+1;
		$li_begin = strpos($data, "<li", $pos+1);
		$li_end = strpos($data, "</li", $pos+1);
		$li_html = substr($data, $li_begin, $li_end-$li_begin);
		
		// 회차
		$th_span_begin = strpos($li_html, "<span class=\"num\">")+strlen("<span class=\"num\">");
		$th_span_end = strpos($li_html, "</span>");
		$race_data['th'] = substr($li_html, $th_span_begin, $th_span_end-$th_span_begin);
		
		// 결과
		$em_begin = strpos($li_html, "<em class=\"")+strlen("<em class=\"");
		$race_data['result'] = substr($li_html, $em_begin, 2);
	
		return $race_data;
	}
	
	function SaveFile($data)
	{
		$data .="\r\n";
		$f = @fopen("snail_race_debug.txt", 'a+');
		if (!$f) {
			return false;
		} else {
			$bytes = fwrite($f, $data);
			fclose($f);
		}
	}
}
?>
