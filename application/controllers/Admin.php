<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/autoload.php');

class Admin extends CI_Controller
{

	public $backyard = null;
	/**
	 * 建構子
	 */
	public function __construct()
	{
		parent::__construct();
		$this->backyard = new \backyard\Backyard();
		$this->backyard->setUser('admin');
		$this->backyard->loadPackage('frontend');
		print_r($this->backyard->page->getTemplates('listcode'));
		//echo $this->backyard->page->getHTML('listcode');
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
	public function page($code = null)
	{
		// 將頁面導到登入頁
		if (is_null($code)) {
			header('location: /index.php/admin');
		}

		// 是否已登入
		$member_id = $this->session->userdata('backyard_member_login');
		if ($member_id == '') {
			header('location: /index.php/admin');
			exit;
		}

		$this->backyard->loadPackage('frontend');
		$htmlContent = $this->backyard->page->getHTML($code);
		echo $htmlContent;
	}
}
