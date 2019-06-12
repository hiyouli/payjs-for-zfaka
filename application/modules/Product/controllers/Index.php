<?php
/*
 * 功能：产品模块－默认首页
 * Author:资料空白
 * Date:20180509
 */

class IndexController extends ProductBasicController
{
	private $m_products_type;
    public function init()
    {
        parent::init();
		$this->m_products_type = $this->load('products_type');
    }

    public function indexAction()
    {
		if(file_exists(INSTALL_LOCK)){
			$data = array();
			$products_type = $this->m_products_type->Where(array('active'=>1,'isdelete'=>0))->Order(array('sort_num' => 'DESC'))->Select();
			$data['products_type'] = $products_type;
			$data['title'] = "购买商品";
			if($this->tpl){
				$this->display($this->tpl, $data);
				return FALSE;
			}else{
				$this->getView()->assign($data);
			}
		}else{
			$this->redirect("/install/");
			return FALSE;
		}
    }
}