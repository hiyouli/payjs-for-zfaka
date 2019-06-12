<?php
/*
 * 功能：会员首页
 * Author:资料空白
 * Date:20180604
 */
class IndexController extends BasicController
{

	public function init()
	{
        parent::init();
	}

	public function indexAction()
	{
		if(file_exists(INSTALL_LOCK)){
			$this->redirect("/product/");
			return FALSE;
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
	}
}