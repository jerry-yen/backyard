<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/autoload.php');
require_once(APPPATH . '/third_party/codeigniter-restserver-master/src/RestController.php');
require_once(APPPATH . '/third_party/codeigniter-restserver-master/src/Format.php');

ini_set('display_errors', true);
error_reporting(E_ALL);

class Api extends \chriskacerguis\RestServer\RestController
{

    public $backyard = null;
    private $inputs = array();

    /**
     * 建構子
     */
    public function __construct()
    {
        parent::__construct();
        $this->backyard = new \backyard\Backyard();
        $userType = $this->input->get('userType');
        if (isset($userType)) {
            $this->backyard->setUser($userType);
        } else {
            $this->backyard->setUser('master');
        }
        $this->inputs = $this->backyard->getInputs();
    }

    /**
     * 取得頁面資訊
     * 
     * @param string $pageAction 資訊類型(metadata|script|style)
     */
    public function page_get($pageAction)
    {
        $this->backyard->loadPackage('frontend');

        if (isset($this->inputs['userType'])) {
            $this->backyard->setUser(trim($this->inputs['userType']));
        }



        // 根據 URI 取得頁面
        $uri = $this->inputs['uri'];
        $page = $this->backyard->page->getByURI($uri);

        if ($pageAction == 'metadata') {
            $this->response($page->getAllPageMetadata(), 200);
        }

        if ($pageAction == 'script') {
            header('Content-Type: application/javascript');
            echo $page->getAllPageScript();
        }

        if ($pageAction == 'style') {
            header("Content-type: text/css");
            echo $page->getAllPageStyle();
        }
    }

    /**
     * 取得組件清單
     * 
     * @return string JSON
     */
    public function widgets_get()
    {
        $this->backyard->loadPackage('frontend');
        $this->backyard->setUser('admin');
        $widgets = $this->backyard->widget->list('widget', true);
        $this->response(array('status' => 'success', 'items' => $widgets), 200);
    }


    /**
     * 取得組件資訊
     * 
     * @param string $widgetAction 資訊類型(html)
     * @param string $code 組件代碼
     * 
     * @return string 組件HTML
     */
    public function widget_get($widgetAction, $code = '')
    {
        $this->backyard->loadPackage('frontend');

        if ($widgetAction == 'html') {
            $widget = $this->backyard->widget->get($code);
            if (!$widget->isExists()) {
                echo "找不到組件!";
            } else {
                echo $widget->getHTML();
            }
        } else if ($widgetAction == 'metadata') {
            $metadata = $this->backyard->widget->getMetadata($code);
            $this->response(array('status' => 'success', 'metadata' => $metadata), 200);
        } else if ($widgetAction == 'modules') {
            $widgets = $this->backyard->widget->getModules();
            $this->response(array('status' => 'success', 'items' => $widgets), 200);
        }
    }

    /**
     * 取得後設資料資訊
     * 
     * @param string $metadataType 後設資料類型
     * 
     * @return string JSON
     */
    public function metadatas_get($metadataType)
    {
        $this->backyard->setUser('admin');
        $metadatas = $this->backyard->metadata->list($metadataType, true);
        $this->response(array('status' => 'success', 'items' => $metadatas), 200);
    }

    /**
     * 取得後設資料資訊
     * 
     * @param string $metadataType 後設資料類型
     * 
     * @return string JSON
     */
    public function metadatas_put($metadataType)
    {
        $this->backyard->setUser('admin');
        foreach ($this->inputs['condition'] as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $data = array_merge(array('id' => $value), $this->inputs['value'][$key]);
            $this->backyard->metadata->update($metadataType, $data, true);
        }
        $this->response(array('status' => 'success'), 200);
    }

    /**
     * 取得後設資料資訊
     * 
     * @param string $metadataType 後設資料類型
     * @param string $code 後設資料代碼
     * 
     * @return string JSON
     */
    public function metadata_get($metadataType, $code = '')
    {
        $this->backyard->setUser('admin');
        if (isset($this->inputs['id'])) {
            $metadata = $this->backyard->metadata->getById($this->inputs['id'], true);
        } else {
            $metadata = $this->backyard->metadata->get($code, $metadataType, true);
        }

        $this->response(array('status' => 'success', 'item' => $metadata), 200);
    }

    /**
     * 新增後設資料
     * 
     * @param string $metadataType 後設資料類型
     * @param string $code 後設資料代碼
     * 
     * @return string JSON
     */
    public function metadata_post($metadataType, $code = '')
    {
        if (trim($code) != '') {
            $this->inputs['code'] = $code;
        }
        $this->backyard->setUser('admin');
        //unset($this->inputs[$metadataType]);
        $response =  $this->backyard->metadata->insert($metadataType, $this->inputs);
        if (is_array($response)) {
            $this->response($response, 200);
        } else {
            $this->response(array('status' => 'success', 'id' => $response), 200);
        }
    }

    /**
     * 更新後設資料
     * 
     * @param string $metadataType 後設資料類型
     * @param string $code 後設資料代碼
     * 
     * @return string JSON
     */
    public function metadata_put($metadataType)
    {
        $this->backyard->setUser('admin');
        $response = $this->backyard->metadata->update($metadataType, $this->inputs);
        if (is_array($response)) {
            $this->response($response, 200);
        } else {
            $this->response(array('status' => 'success'), 200);
        }
    }

    /**
     * 刪除後設資料
     * 
     * @param string $metadataType 後設資料類型
     * @param string $code 後設資料代碼
     * 
     * @return string JSON
     */
    public function metadata_delete($metadataType)
    {
        $id = $this->inputs['id'];
        $this->backyard->setUser('admin');
        $this->backyard->metadata->delete($metadataType, $id);
        $this->response(array('status' => 'success'), 200);
    }

    /**
     * 取得資料集資訊清單
     * 
     * @return string JSON
     */
    public function datasets_get()
    {
        $this->backyard->setUser('admin');
        $datasets = $this->backyard->dataset->list('dataset', true);
        $this->response(array('status' => 'success', 'items' => $datasets), 200);
    }


    /**
     * 新增資料集資訊
     * 
     * @param string $metadataType 後設資料類型
     * @param string $code 後設資料代碼
     * 
     * @return string JSON
     */
    public function dataset_post($code = '')
    {
        if (trim($code) != '') {
            $this->inputs['code'] = $code;
        }
        $this->backyard->setUser('admin');
        $response =  $this->backyard->dataset->insert('dataset', $this->inputs);
        if (is_array($response)) {
            $this->response($response, 200);
        } else {
            $this->response(array('status' => 'success', 'id' => $response), 200);
        }
    }

    /**
     * 更新資料集資訊
     * 
     * @param string $metadataType 後設資料類型
     * @param string $code 後設資料代碼
     * 
     * @return string JSON
     */
    public function dataset_put()
    {
        $this->backyard->setUser('admin');
        $response = $this->backyard->dataset->update('dataset', $this->inputs);
        if (is_array($response)) {
            $this->response($response, 200);
        } else {
            $this->response(array('status' => 'success'), 200);
        }
    }

    /**
     * 刪除資料集資訊
     * 
     * @param string $metadataType 後設資料類型
     * 
     * @return string JSON
     */
    public function dataset_delete()
    {
        $id = $this->inputs['id'];
        $this->backyard->setUser('admin');
        $this->backyard->dataset->delete('dataset', $id);
        $this->response(array('status' => 'success'), 200);
    }


    /**
     * 取得元件
     * @param string $code 元件名稱
     */
    public function component_get($code)
    {
        $this->backyard->loadPackage('frontend');
        $script = $this->backyard->component->getScript($code);
        header('Content-Type: application/javascript');
        echo $script;
    }

    /**
     * 開發者系統登入
     */
    public function master_login_post()
    {
        $this->backyard->setUser('master');
        $user = $this->backyard->user->login($this->inputs);
        $this->response($user->toArray(), 200);
    }

    /**
     * 系統登入
     */
    public function login_post()
    {
        $this->backyard->setUser('admin');
        $user = $this->backyard->user->login($this->inputs);
        $this->response($user->toArray(), 200);
    }

    /**
     * 取得項目
     */
    public function item_get($code)
    {
        get_instance()->backyard->setUser('admin');
        unset($this->inputs[$code]);
        $response = $this->backyard->item->get($code, $this->inputs);
        $this->response(array('status' => 'success', 'item' => $response->toArray()), 200);
    }

    /**
     * 新增項目
     */
    public function item_post($code)
    {
        get_instance()->backyard->setUser('admin');
        unset($this->inputs[$code]);
        $response = $this->backyard->item->insert($code, $this->inputs);
        if (is_array($response)) {
            $this->response($response, 200);
        } else {
            $this->response(array('status' => 'success', 'id' => $response), 200);
        }
    }

    /**
     * 更新項目
     */
    public function item_put($code)
    {
        get_instance()->backyard->setUser('admin');
        unset($this->inputs[$code]);
        $response = $this->backyard->item->update($code, $this->inputs);
        if (is_array($response)) {
            $this->response($response, 200);
        } else {
            $this->response(array('status' => 'success'), 200);
        }
    }

    /**
     * 取得項目清單
     */
    public function items_get($code)
    {
        get_instance()->backyard->setUser('admin');
        unset($this->inputs[$code]);
        $responses = $this->backyard->item->list($code, $this->inputs);
        $this->response(array('status' => 'success', 'items' => $responses), 200);
    }

    /**
     * 刪除項目
     */
    public function item_delete($code)
    {
        get_instance()->backyard->setUser('admin');
        unset($this->inputs[$code]);
        $response = $this->backyard->item->delete($code, $this->inputs);
        $this->response(array('status' => 'success'), 200);
    }

    /**
     * 更新項目清單
     */
    public function items_put($code)
    {
        $this->backyard->setUser('admin');
        foreach ($this->inputs['condition'] as $key => $value) {
            if ($key == 0) {
                continue;
            }
            $data = array_merge(array('id' => $value), $this->inputs['value'][$key]);
            $this->backyard->item->update($code, $data, true);
        }
        $this->response(array('status' => 'success'), 200);
    }

    /**
     * 檔案上傳
     */
    public function file_post()
    {
        $response = $this->backyard->file->upload($this->inputs);
        $this->response($response, 200);
    }

    /**
     * 取得檔案清單
     */
    public function files_get()
    {
        $response = $this->backyard->file->getItems($this->inputs);
        $this->response($response, 200);
    }

    



    ///////////////////////////////////////////////////////////////////////



    
}
