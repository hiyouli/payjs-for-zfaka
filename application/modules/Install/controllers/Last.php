<?php

/*
 * 功能：安装模块
 * Author:资料空白
 * Date:20180626
 */

class LastController extends BasicController
{

	public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
		if(file_exists(INSTALL_LOCK)){
			$data = array();
			$this->getView()->assign($data);
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
    }

}