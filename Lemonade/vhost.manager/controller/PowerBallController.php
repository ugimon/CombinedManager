<?
/*
* Index Controller
*/
class PowerBallController extends WebServiceController 
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
		
		$this->view->define("content","content/powerBall/index.html");
		$this->display();
	}
	
	function powerBallListenerAction()
	{
		$this->popupDefine('ladder');
		
		if(!$this->auth->isLogin())
		{
			$this->loginAction();
			exit;
		}
		
		$power_ball_games = $this->parsePowerBallAction();
		
		echo json_encode($power_ball_games);
	}
	
	function parsePowerBallAction()
	{
		//경기 결과 uri
		$url = "http://www.nlotto.co.kr/power.do?method=powerWinNoList";
	
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
		
		$begin_pos = strpos($data, "<table class=\"tblType1_1\"");
		if($begin_pos ===false)
		{
			return -1;
		}
		$end_pos = strpos($data, "</table>", $begin_pos+1);
		$table_html = substr($data, $begin_pos, $end_pos-$begin_pos);
		
		$begin_pos = strpos($table_html, "<tbody>");
		if($begin_pos ===false)
		{
			return -1;
		}
		$end_pos = strpos($table_html, "</tbody>", $begin_pos+1);
		$tbody_html = substr($table_html, $begin_pos, $end_pos-$begin_pos);
		
		$begin_pos = strpos($tbody_html, "<tr>");
		while( $begin_pos!==false)
		{
			$end_pos = strpos($tbody_html, "</tr>", $begin_pos+1);
			$tr_row = substr($tbody_html, $begin_pos, $end_pos-$begin_pos);
			
			//tr 내의 td 파싱 -Begin ------------------------------------
			
			//날짜
			$td_begin_pos = strpos($tr_row, "<td>")+strlen("<td>");
			$td_end_pos = strpos($tr_row, "</td>", $td_begin_pos+1);
			
			$date = trim(substr($tr_row, $td_begin_pos, $td_end_pos-$td_begin_pos));
			if($date[0]!="2")
				break;
				
			$game['date'] = $date;
			
			//회차
			$td_begin_pos = strpos($tr_row, "<td>", $td_end_pos+1)+strlen("<td>");
			$td_end_pos = strpos($tr_row, "</td>", $td_begin_pos+1);
			$game['th'] = trim(substr($tr_row, $td_begin_pos, $td_end_pos-$td_begin_pos));
			
			//당첨번호
			$td_begin_pos = strpos($tr_row, "<td>", $td_end_pos+1)+strlen("<td>");
			$td_end_pos = strpos($tr_row, "</td>", $td_begin_pos+1);
			$td_string = trim(substr($tr_row, $td_begin_pos, $td_end_pos-$td_begin_pos));
			
			$text_begin_pos = strpos($td_string, "(")+2; //+2
			$game['balls'] = trim(substr($td_string, $text_begin_pos, 10));
			
			//파워볼
			$td_begin_pos = strpos($tr_row, "<td>", $td_end_pos+1)+strlen("<td>");
			$td_end_pos = strpos($tr_row, "</td>", $td_begin_pos+1);
			$img = trim(substr($tr_row, $td_begin_pos, $td_end_pos-$td_begin_pos));
			
			if( strpos($img, "powerball_ball00.gif"))	$game['powerball'] = 0;
			else if( strpos($img, "powerball_ball01.gif"))	$game['powerball'] = 1;
			else if( strpos($img, "powerball_ball02.gif"))	$game['powerball'] = 2;
			else if( strpos($img, "powerball_ball03.gif"))	$game['powerball'] = 3;
			else if( strpos($img, "powerball_ball04.gif"))	$game['powerball'] = 4;
			else if( strpos($img, "powerball_ball05.gif")) 	$game['powerball'] = 5;
			else if( strpos($img, "powerball_ball06.gif")) 	$game['powerball'] = 6;
			else if( strpos($img, "powerball_ball07.gif")) 	$game['powerball'] = 7;
			else if( strpos($img, "powerball_ball08.gif")) 	$game['powerball'] = 8;
			else if( strpos($img, "powerball_ball09.gif")) 	$game['powerball'] = 9;
			
			//숫자합
			$td_begin_pos = strpos($tr_row, "<td>", $td_end_pos+1)+strlen("<td>");
			$td_end_pos = strpos($tr_row, "</td>", $td_begin_pos+1);
			$game['sum_of_ball'] = trim(substr($tr_row, $td_begin_pos, $td_end_pos-$td_begin_pos));
			
			//홀짝
			$td_begin_pos = strpos($tr_row, "<td>", $td_end_pos+1)+strlen("<td>");
			$td_end_pos = strpos($tr_row, "</td>", $td_begin_pos+1);
			$td_html = trim(substr($tr_row, $td_begin_pos, $td_end_pos-$td_begin_pos));
			
			if( strpos($td_html, "Even"))
				$game['odd_even'] = "even";
			else
				$game['odd_even'] = "odd";
				
			//대중소
			$td_begin_pos = strpos($tr_row, "<td>", $td_end_pos+1)+strlen("<td>");
			$td_end_pos = strpos($tr_row, "</td>", $td_begin_pos+1);
			$td_html = trim(substr($tr_row, $td_begin_pos, $td_end_pos-$td_begin_pos));
			if( strpos($td_html, "81"))
				$game['big_small'] = "big";
			else if( strpos($td_html, "15"))
				$game['big_small'] = "small";
			else if( strpos($td_html, "65"))
				$game['big_small'] = "middle";
				
			$games[] = $game;
							
			//tr 내의 td 파싱 -End ------------------------------------
			
			
			if($end_pos!==false)
				$begin_pos = strpos($tbody_html, "<tr>", $end_pos+1);
		}
		
		$power_ball_model = $this->getModel("PowerBallModel");
		$process_model = $this->getModel("ProcessModel");
		
		if( count($games) > 0)
		{
			$last_th = $games[0]['th'];
			
			//게임등록
			$next_th = $last_th+1;
			
			if($power_ball_model->isRegisterGame($next_th)==0)
			{
				$power_ball_model->RegisterGame($next_th);
			}
			if($power_ball_model->isRegisterGame($next_th+1)==0)
			{
				$power_ball_model->RegisterGame($next_th+1,1);
			}
			
			//게임마감
			if($power_ball_model->isAccountGame($last_th)==0)
			{
				$power_ball_model->accountGame($last_th, $games[0]);
			}
			
		}
	
	
		return $games;
	}
	
	function SaveFile($data)
	{
		$data .="\r\n";
		$f = @fopen("debug_powerball_html.txt", 'a+');
		if (!$f) {
			return false;
		} else {
			$bytes = fwrite($f, $data);
			fclose($f);
		}
	}
}
?>
