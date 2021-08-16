<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/autoload.php');

class Master extends CI_Controller
{

	public $backyard = null;
	/**
	 * 建構子
	 */
	public function __construct()
	{
		parent::__construct();
		$this->backyard = new \backyard\Backyard();
		$this->backyard->setUser('master');

		$this->backyard->loadPackage('frontend');
		$page = $this->backyard->page->get('email');
		$templates = $page->getTemplates();
		foreach ($templates as $template) {
			$widgets = $template -> getWidgets();
			foreach($widgets as $widget){
				$dataset = $widget -> getDataset();
				$components = $dataset -> getComponents();
				foreach($components as $component){
					echo $component -> getScript();
				}

			}
			
		}
		exit;
	}

	/**
	 * 登入頁面
	 */
	public function index()
	{
		$this->login();
	}

	public function login()
	{
		$this->backyard->loadPackage('frontend');
		$htmlContent = $this->backyard->page->renderLoginPage();
		echo $htmlContent;
	}

	/**
	 * 載入頁面
	 */
	public function page($type = null)
	{
		// 將頁面導到登入頁
		if (is_null($type)) {
			header('location: /index.php/master/');
			exit;
		}

		// 是否已登入
		$member_id = $this->session->userdata('backyard_master_login');
		if ($member_id == '') {
			header('location: /index.php/master/');
			exit;
		}

		$this->backyard->loadPackage('frontend');
		$htmlContent = $this->backyard->page->render($type);
		echo $htmlContent;
	}
}
