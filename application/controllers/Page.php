<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/autoload.php');

ini_set('display_errors', true);
error_reporting(E_ALL);

class Page extends CI_Controller
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
    }

    /**
     * 路由，透過網址(URI)來對應要取的哪個頁面的Metadata
     * 以及要載入哪個主題版面
     */
    public function router()
    {
        $this->load->helper('url');
        $uri = $this->uri->uri_string();

        $parts = explode('/', $uri);
        if ($parts[0] == 'master') {
            $this->backyard->setUser('master');
        } elseif ($parts[0] == 'admin') {
            $this->backyard->setUser('admin');
        } else {
            $this->backyard->setUser('user');
        }

        $page = $this->backyard->page->getByURI($uri);

        if ($page == array()) {
            // 之後可設計成專有的 404 頁面
            echo '404 Page Not Found!';
        } else {
            echo $page->getHTML();
        }
    }
}
