<?php

/**
 * 後花園 - 前端選單處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Menu extends \backyard\Package
{
    /**
     * @var View路徑
     */
    private $viewPath = '';

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
        return $content;
    }

    /**
     * 取得選單後設資料
     */
    public function getMetadata()
    {
        $menu = $this->backyard->getUser()->getMetadataOfMenu();
        return $menu;
    }

    /**
     * 取得整個頁面所需要的Javascript
     * 
     * @param string $code 頁面代碼
     * 
     * @return string
     */
    public function getScript($code)
    {

        $widgetScripts = array();
        $componentScripts = array();

        // 取得 View 路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];

        // 取得頁面後設資料
        $pageMetadata = $this->getMetadata($code);
        foreach ($pageMetadata['metadata']['widgets'] as $widget) {

            // 取得組件後設資料
            $widgetMetadata = $this->backyard->widget->getMetadata($widget['code']);
            $widgetName = $widgetMetadata['metadata']['widget'];
            if (isset($widgetScripts[$widgetName])) {
                continue;
            }

            // 取得組件Script內容
            $scriptPath = $this->viewPath . '/widgets/' . $widgetName . '/script.js';
            if (!file_exists($scriptPath)) {
                continue;
            }

            $widgetScript = file_get_contents($scriptPath) . "\r\n";
            $widgetScript .= $this->readLibraries($this->viewPath . '/widgets/' . $widgetName . '/libraries.json');
            $widgetScripts[$widgetName] = $widgetScript;

            // 取得資料集後設資料
            $datasetCode = $widgetMetadata['metadata']['dataset'];
            $fieldDataset = $this->backyard->dataset->getItem($datasetCode);
            foreach ($fieldDataset['dataset']['fields'] as $field) {
                // 取得元件Script內容
                $scriptPath = $this->viewPath . '/components/' . $field['component'] . '/component.js';
                if (!file_exists($scriptPath)) {
                    continue;
                }

                $componentScript = file_get_contents($scriptPath) . "\r\n";
                $componentScript .= $this->readLibraries($this->viewPath . '/components/' . $field['component'] . '/libraries.json');
                $componentScripts[$field['component']] = $componentScript;
            }
        }

        return implode("\r\n", $widgetScripts) . "\r\n" . implode("\r\n", $componentScripts);
    }

    /**
     * 載入引用的函式/套件
     * 
     * @param string $libraryJSONFile 函式庫路徑
     */
    private function readLibraries($libraryJSONFile)
    {
        $script = '';
        if (file_exists($libraryJSONFile)) {
            $libraries = json_decode(file_get_contents($libraryJSONFile), true);

            foreach ($libraries as $libraryName) {
                $libraryPath = $this->viewPath . '/libraries/' . $libraryName . '/' . $libraryName . '.js';
                if (file_exists($libraryPath)) {
                    $script .= file_get_contents($libraryPath) . "\r\n";
                }
            }
        }
        return $script;
    }

    /**
     * 取得整個頁面所需要的CSS
     * 
     * @param string $code 頁面代碼
     * 
     * @return string
     */
    public function getCSS($code)
    {

        $widgetStyles = array();
        $componentStyles = array();

        // 取得 View 路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];

        // 取得頁面後設資料
        $pageMetadata = $this->getMetadata($code);
        foreach ($pageMetadata['metadata']['widgets'] as $widget) {

            // 取得組件後設資料
            $widgetMetadata = $this->backyard->widget->getMetadata($widget['code']);
            $widgetName = $widgetMetadata['metadata']['widget'];
            if (isset($widgetStyles[$widgetName])) {
                continue;
            }

            // 取得元件Style內容
            $widgetStyle = '';
            $stylePath = $this->viewPath . '/widgets/' . $widgetName . '/style.css';
            if (file_exists($stylePath)) {
                $widgetStyle = file_get_contents($stylePath) . "\r\n";
            }
            $widgetStyle .= $this->readCSSLibraries($this->viewPath . '/widgets/' . $widgetName . '/libraries.json');
            $widgetStyles[$widgetName] = $widgetStyle;

            // 取得資料集後設資料
            $datasetCode = $widgetMetadata['metadata']['dataset'];
            $fieldDataset = $this->backyard->dataset->getItem($datasetCode);
            foreach ($fieldDataset['dataset']['fields'] as $field) {
                // 取得元件Style內容
                $componentStyle = '';
                $stylePath = $this->viewPath . '/components/' . $field['component'] . '/component.css';
                if (file_exists($stylePath)) {
                    $componentStyle = file_get_contents($stylePath) . "\r\n";
                }
                $componentStyle .= $this->readCSSLibraries($this->viewPath . '/components/' . $field['component'] . '/libraries.json');
                $componentStyles[$field['component']] = $componentStyle;
            }
        }

        return implode("\r\n", $widgetStyles) . "\r\n" . implode("\r\n", $componentStyles);
    }

    /**
     * 載入引用的函式/套件
     * 
     * @param string $libraryJSONFile 函式庫路徑
     */
    private function readCSSLibraries($libraryJSONFile)
    {
        $style = '';
        if (file_exists($libraryJSONFile)) {
            $libraries = json_decode(file_get_contents($libraryJSONFile), true);

            foreach ($libraries as $libraryName) {
                $libraryPath = $this->viewPath . '/libraries/' . $libraryName . '/' . $libraryName . '.css';
                if (file_exists($libraryPath)) {
                    $style .= file_get_contents($libraryPath) . "\r\n";
                }
            }
        }
        return $style;
    }

    /**
     * 取得頁面HTML語法
     * 
     * @param string $code 模組代碼
     */
    public function render($code)
    {
        // 取得View基本路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];

        // 取得頁面後設資料
        $page = $this->getMetadata($code);
        if($page['status'] != 'success'){
            return $page['message'];
        }
        $content = file_get_contents($this->viewPath . '/full.html');
        $content = $this->refinePathInHtmlContent($content);
        $content = str_replace('{pageTitle}', $page['metadata']['name'], $content);
        $content = str_replace('{code}', $page['metadata']['code'], $content);

        return $content;
    }
}
