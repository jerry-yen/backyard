<?php

/**
 * 後花園 - 前端組件處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Widget extends \backyard\core\Metadata
{
    /**
     * @var View路徑
     */
    private $viewPath = '';

    /**
     * 取得所有組件
     * 
     * @param string $metadataType 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Widget[]
     */
    public function list($metadataType = 'widget', $toArray = false)
    {
        $widgets = array();
        $responses = parent::list($metadataType, $toArray);
        if ($toArray) {
            $widgets = $responses;
        } else {
            foreach ($responses as $response) {
                $widgets[] = new \backyard\packages\frontend\Widget($response->toArray());
            }
        }
        return $widgets;
    }

    /**
     * 取得指定組件
     * 
     * @param array $code 代碼
     * @param string $metadataType 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Widget
     */
    public function get($code, $metadataType = 'widget', $toArray = false)
    {
        if (isset($code) && trim($code) != '') {
            $response = parent::get($code, $metadataType);
            if ($toArray) {
                return $response;
            } else {
                return new \backyard\packages\frontend\Widget($response->toArray());
            }
        } else {
            return new \backyard\packages\frontend\Widget(array());
        }
    }

    /**
     * 取得資料集
     * 
     * @return Dataset
     */
    public function getDataset()
    {
        if (isset($this->data['dataset']) && trim($this->data['dataset']) != '') {
            return get_instance()->backyard->dataset->get($this->data['dataset']);
        }

        return new \backyard\core\Dataset(array());
    }

    /**
     * 修正HTML內容中的資源檔的路徑
     * 
     * @param string $content HTML內容
     * 
     * @return string
     */
    private function refinePathInHtmlContent($content)
    {
        // 待處理：根據 htaccess 環境變數的設定，來決定資源檔取代的路徑
        // $path = new \backyard\libraries\Path();
        // echo $path->relative($this->viewPath);
        $content = str_replace('{adminlte}', '/adminlte', $content);
        $inputs = get_instance()->backyard->getInputs();
        if (isset($inputs['uri'])) {
            $content = str_replace('{uri}', $inputs['uri'], $content);
        }
        return $content;
    }

    /**
     * 取得組件所需的Script
     * 
     * @return string
     */
    public function getScript()
    {
        $code = $this->data['setting']['code'];
        // 取得View基本路徑
        get_instance()->backyard->config->loadConfigFile('frontend');
        $this->viewPath = get_instance()->backyard->config->getConfig('frontend')['viewPath'];

        // 取得組件內容
        if (file_exists($this->viewPath . 'widgets/' . $code . '/script.js')) {
            $content = file_get_contents($this->viewPath . 'widgets/' . $code . '/script.js');
            $content = $this->refinePathInHtmlContent($content);
            $content = str_replace('{code}', $code, $content) . "\r\n";

            return $content;
        } else {
            return '找不到' . $code . '元件腳本(' . $this->viewPath . 'widgets/' . $code . '/script.js)';
        }
    }

    /**
     * 取得組件所需的Style
     * 
     * @return string
     */
    public function getStyle()
    {
        $code = $this->data['setting']['code'];
        // 取得View基本路徑
        get_instance()->backyard->config->loadConfigFile('frontend');
        $this->viewPath = get_instance()->backyard->config->getConfig('frontend')['viewPath'];

        // 取得組件內容
        if (file_exists($this->viewPath . 'widgets/' . $code . '/style.css')) {
            $content = file_get_contents($this->viewPath . 'widgets/' . $code . '/style.css');
            $content = $this->refinePathInHtmlContent($content);
            $content = str_replace('{code}', $code, $content) . "\r\n";

            return $content;
        } else {
            return '';
        }
    }

    /**
     * 取得組件模組清單
     */
    public function getModules()
    {
        // 取得View基本路徑
        get_instance()->backyard->config->loadConfigFile('frontend');
        $this->viewPath = get_instance()->backyard->config->getConfig('frontend')['viewPath'];

        get_instance()->backyard->config->loadConfigFile('widget');
        $widgets = get_instance()->backyard->config->getConfig('widgets');
        return $widgets;
    }

    /**
     * 取得組件HTML語法
     */
    public function getHTML()
    {
        // 取得View基本路徑
        get_instance()->backyard->config->loadConfigFile('frontend');
        $this->viewPath = get_instance()->backyard->config->getConfig('frontend')['viewPath'];

        // 組件代碼
        $code = $this->data['code'];

        // 組件名稱
        $widget = $this->data['setting']['code'];

        // 取得組件內容
        if (file_exists($this->viewPath . 'widgets/' . $widget . '/template.php')) {
            $content = file_get_contents($this->viewPath . 'widgets/' . $widget . '/template.php');
            $content = $this->refinePathInHtmlContent($content);
            $content = str_replace('{code}', $code, $content);
            return str_replace('{userType}', get_instance()->backyard->getUserType(), $content);
        } else {
            return '找不到' . $widget . '組件介面(' . $this->viewPath . 'widgets/' . $widget . '/template.php)';
        }
    }

    /**
     * 取得組件擴充的後設資料
     */
    public function getMetadata($code)
    {
        // 取得View基本路徑
        get_instance()->backyard->config->loadConfigFile('frontend');
        $this->viewPath = get_instance()->backyard->config->getConfig('frontend')['viewPath'];

        // 取得組件內容
        if (file_exists($this->viewPath . 'widgets/' . $code . '/metadata.php')) {
            include_once($this->viewPath . 'widgets/' . $code . '/metadata.php');
            return $metadata;
        }

        return array();
    }
}
