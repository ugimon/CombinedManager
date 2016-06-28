<?php 


class LeagueController extends WebServiceController 
{
	//▶ 리그 목록
	function listAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/league/list.html");
		
		$model  	= $this->getModel("LeagueModel");
		
		$act		 	= $this->request("act");
		$category	= $this->request("category");
		
		$ph_path		 = "/upload/league/";
		$new_ph_path = "../vhost.user/upload/league/";
		
		if($act=="del")
		{
			$arrmemidx = $this->request("y_id");
			
			$str="";
	
			for($i=0;$i<count($arrmemidx);$i++)
			{
				$str=$str.$arrmemidx[$i].",";
			}
			
			$str=substr($str,0,strlen($str)-1);
			
			$model->delProcess($str);
		}
		else if($act=="delete")
		{
			$idx = $this->request("idx");
			if($idx=="")
				throw new Lemon_ScriptException("","","script","history.back();");
			
			$model->delProcess($idx);
		}
		
		$keyword = $this->request("username");
		
		//keyword
		if($keyword!="")										$where.= " name like '%".$keyword."%'";
		
		if($keyword!="" && $category!="")		$where.= " and kind='".$category."'";
		else if($category!="")							$where.= " kind='".$category."'";

		$conf = Lemon_Configure::readConfig('config');
		if($conf['site']!='')
			$UPLOAD_URL = $conf['site']['upload_url'];
			
		$page_act= "username=".$keyword."&category=".$category;
		
		$total 			= $model->getTotal($where);
		$pageMaker 	= $this->displayPage($total, 10, $page_act);
		$list 			= $model->getList($where, $pageMaker->first, $pageMaker->listNum);
		
		$categoryList = $model->getCategoryList();
		
		$this->view->assign('category', $category);
		$this->view->assign('username', $keyword);
		$this->view->assign('category_list', $categoryList);
		$this->view->assign('UPLOAD_URL', $UPLOAD_URL);
		$this->view->assign('list', $list);
		
		$this->display();
	}
	
	//▶ 리그 추가
	function popup_addAction()
	{
		$this->popupDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$leagueModel = $this->getModel('LeagueModel');
		$categoryList = $leagueModel->getCategoryList();
		
		$this->view->define("content","content/league/popup.add.html");
		
		$this->view->assign('category_list',$categoryList);
		$this->display();
	}
	
	function addProcessAction()
	{
		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		
		$leagueModel = $this->getModel('LeagueModel');
		
		$category 		= $this->request('category');
		$league 			= $this->request('league_name');
		$nationSn 		= $this->request('nation_sn');
		$uploadFile 	= $_FILES["upLoadFile"];
		
		if($uploadFile=="") 
		{
			$leagueModel->add($category, $league, '', $nationSn);
		}
		else
		{
			$leagueImage = $this->copyFile();
			$leagueModel->add($category, $league, $leagueImage, $nationSn);
		}
		throw new Lemon_ScriptException("","","script","alert('추가 되였습니다.');opener.document.location.reload(); self.close();");
	}
	
	//▶ 리그 편집
	function popup_editAction()
	{
		$this->popupDefine();
	
		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/league/popup.edit.html");
			
		$leagueModel = $this->getModel("LeagueModel");
		$leagueSn		 = $this->request("league_sn");
		$mode				 = Trim($this->request("mode"));
		
		$conf = Lemon_Configure::readConfig('config');
			
		if($conf['site']!='')
		{
			$UPLOAD_URL	 = $conf['site']['upload_url'];
			$SITE_DOMAIN = $conf['site']['site_domain'];
		}
			
		if($mode=="edit")
		{	
			$leagueSn		 = $this->request("league_sn");
			$kind				 = $this->request("league_kind");
			$name				 = $this->request("league_name");
			$alias			 = $this->request("alias_league_name");
			$nationflag	 = $this->request("nationflag");
			$viewStyle	 = $this->request("view_style");
			$linkUrl		 = $this->request("link_url");
			$uploadImage = $_FILES["upLoadFile"];
			
			$currentImage = $leagueModel->getLeagueImage($leagueSn);

			if($uploadImage["error"]==4)
			{
				$leagueModel->modify($leagueSn, $kind, $name, $currentImage[0]['lg_img'], $viewStyle, $linkUrl, $alias);
			}
			else
			{
				$lg_img 			= $leagueModel->getLeagueImage($leagueSn);				
				$leagueImage 	= $this->copyFile($lg_img);	
				$leagueModel->modify($leagueSn, $kind, $name, $leagueImage, $viewStyle, $linkUrl, $alias);
			}
				
			throw new Lemon_ScriptException("","","script","alert('수정 되였습니다.');opener.document.location.reload(); self.close();");
		}
		else
		{
			$item = $leagueModel->getListBySn($leagueSn);
				
			$nationCode = $item['nation_sn'];
			$item['nation_image'] = $leagueModel->getNationImage($nationCode);
		}	

		$categoryList = $leagueModel->getCategoryList();
		
		$this->view->assign('SITE_DOMAIN', $SITE_DOMAIN);
		$this->view->assign('UPLOAD_URL', $UPLOAD_URL);
		$this->view->assign('category_list',$categoryList);
		$this->view->assign('league_sn', $leagueSn);
		$this->view->assign('item', $item);
		
		$this->display();
	}
	
	//▶ 국가 리스트
	function popup_nationlistAction()
	{
		$this->popupDefine();
	
		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/league/popup.nation_list.html");
			
		$model  	= $this->getModel("LeagueModel");
			
		$conf = Lemon_Configure::readConfig('config');
			
		if($conf['site']!='') {$UPLOAD_URL = $conf['site']['upload_url'];}
			
		$idx 	= $this->request("idx");
		$mode	= Trim($this->request("mode"));
			
		if($mode=="") {$mode = "list";}
		
		if($mode=="list") {$list = $model->getNationList();}
		else if($mode=="link")
		{
			$name = $this->request("emblemname");
			$list = $model->getNationByName($name);
		}
	
		$this->view->assign('UPLOAD_URL', $UPLOAD_URL);
		$this->view->assign('name', $name);
		$this->view->assign('list', $list);
		$this->display();
	}
	
	function ajaxLeagueListAction()
	{
		$category = $this->request("category");
		$leagueModel = $this->getModel("LeagueModel");
		
		if($category!='')
			$where = "kind='".$category."'";
		$leagueModel->ajaxList($where);
	}
	
	function copyFile($leagueImage='')
	{
		$conf = Lemon_Configure::readConfig('config');
		if($conf['site']!='')
		{
			$srcUri		= $conf['site']['local_upload_url']."/upload/league/";
			$dstUri 	= $conf['site']['upload_path']."/upload/league/";
			$dstUri_f = $conf['site']['upload_path_f']."/upload/league/";
		}

		if($leagueImage!='')
		{
			if(file_exists($srcUri.$leagueImage) && file_exists($dstUri.$leagueImage))
			{	
				unlink($srcUri.$leagueImage);
				unlink($dstUri.$leagueImage);
			}
		}
		
		$obj = new upload;
		$obj->ph_path = $srcUri;
		$obj->get_ph_tmpname($_FILES['upLoadFile']['tmp_name']);
		$obj->get_ph_type($_FILES['upLoadFile']['type']);
		$obj->get_ph_size($_FILES['upLoadFile']['size']);
		$obj->get_ph_name($_FILES['upLoadFile']['name']);
		$obj->save();
		
		$image = substr($obj->ph_name, strlen($srcUri));
		
		if(!copy($srcUri.$image, $dstUri.$image))
		{
			throw new Lemon_ScriptException("복사오류입니다. 관리자에게 문의해 주세요.");					
			exit;
		}
		
		if(!copy($srcUri.$image, $dstUri_f.$image))
		{
			throw new Lemon_ScriptException("복사오류입니다. 관리자에게 문의해 주세요.");					
			exit;
		}
		
		return $image;
	}
	
	function category_listAction()
	{
		$this->commonDefine();

		if(!$this->auth->isLogin())
		{
			$this->redirect("/login");
			exit;
		}
		$this->view->define("content","content/league/category_list.html");
		
		$act = $this->req->request("act");
		$leagueModel = $this->getModel("LeagueModel");
		
		if($act=="modify")
		{
			$categorySn 	= $this->request("category_sn");
			$categoryName = $this->request("modify_name");
			$leagueModel->modifyCategory($categorySn, $categoryName);
		}
		else if($act=="delete")
		{
			$categorySn	= $this->request("category_sn");
			if($categorySn!="")
				$leagueModel->deleteCategory($categorySn);
		}
		
		$list = $leagueModel->getCategoryList();
		
		$this->view->assign('list', $list);
		
		$this->display();
	}
}

?>