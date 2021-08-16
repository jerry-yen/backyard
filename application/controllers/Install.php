<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/Backyard.php');
require_once(APPPATH . '/third_party/backyard/Package.php');

class Install extends CI_Controller
{

	public function index()
	{
		$this->backyard = new \backyard\Backyard();
		$this->backyard->data->install();
		echo '資料庫設定表單建置完成';
	}
}
