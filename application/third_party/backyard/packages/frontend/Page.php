<?php

/**
 * 後花園 - 前端頁面處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Page extends \backyard\core\Metadata
{

    /**
     * @var View路徑
     */
    private $viewPath = '';

    /**
     * 取得所有頁面
     * 
     * @param string $metadata_type 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Page[]
     */
    public function list($metadataType = 'page', $toArray = false)
    {
        $pages = array();
        $responses = parent::list($metadataType);
        if ($toArray) {
            $pages = $responses;
        } else {
            foreach ($responses as $response) {
                $pages[] = new \backyard\packages\frontend\Page($response->toArray());
            }
        }

        return $pages;
    }

    /**
     * 根據頁面代碼取得頁面
     * 
     * @param string $code 頁面代碼
     * @param string $metadata_type 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Page
     */
    public function get($code, $metadataType = 'page', $toArray = false)
    {
        if (isset($code) && trim($code) != '') {
            $response = parent::get($code, $metadataType);
            if ($toArray) {
                return $response;
            } else {
                return new \backyard\packages\frontend\Page($response->toArray());
            }
        } else {
            return new \backyard\packages\frontend\Page(array());
        }
    }

    /**
     * 根據網址路徑取得頁面
     * 
     * @param string $uri 網址路徑
     * 
     * @return Page
     */
    public function getByURI($uri)
    {
        $pages = $this->list();

        foreach ($pages as $page) {
            if (substr($page->uri, 0, 1) == '/') {
                $page->uri = substr($page->uri, 1);
            }
            if ($page->uri == $uri) {
                return $page;
            }
        }

        return array();
    }

    /**
     * 取得此頁面的所有版面
     * 
     * @param string $code 頁面代碼
     * 
     * @return Template[]
     */
    public function getTemplates()
    {
        $templates = get_instance()->backyard->template->list();

        $template = get_instance()->backyard->template->get($this->data['code'], 'page');
        if ($template != array()) {
            $templates[] = $template;
        }
        return $templates;
    }

    /**
     * 取得頁面的HTML
     * 
     * @param string $pageTheme 版面主題(路徑)
     * 
     * @return string
     */
    public function getHTML($pageTheme = null)
    {

        // 處理版面主題的變數預設值
        if (is_null($pageTheme)) {
            $pageTheme = $this->getTheme();
        }

        // 取得View基本路徑
        get_instance()->backyard->config->loadConfigFile('frontend');
        $this->viewPath = get_instance()->backyard->config->getConfig('frontend')['viewPath'];

        $content = file_get_contents($this->viewPath . '/theme/' . $pageTheme . '.html');
        $content = $this->refinePathInHtmlContent($content);

        return $content;
    }

    /**
     * 取得路徑，並確認是否設定為預設變數
     * 
     * @return string
     */
    private function getUri()
    {
        if (!isset($this->data['uri']) || trim($this->data['uri']) == '') {
            return 'page/' . $this->data['code'];
        }

        return trim($this->data['uri']);
    }

    /**
     * 取得版面主題，並確認是否設定為預設變數
     * 
     * @return string
     */
    private function getTheme()
    {
        if (!isset($this->data['theme']) || trim($this->data['theme']) == '') {
            return 'page/adminlte';
        }

        return trim($this->data['theme']);
    }

    /**
     * 取得整個頁面的 Metadata
     * 
     * 頁面 → 版面 → 組件 → 資料集 → 元件
     * 逐層取得所有 metadata
     * 一口氣交給前端運用
     * 
     * @return array
     */
    public function getAllPageMetadata()
    {

        $metadata = array();

        // 取得系統資訊
        $information = parent::get('information', 'information');
        $metadata = $information->toArray();

        // 為防止資料傳輸時，不小時出現帳密，因此先拿掉帳號密碼
        unset($metadata['account']);
        unset($metadata['password']);


        // 取得頁面中所有樣版
        $allTemplates = $this->getTemplates();
        $templates = array();
        foreach ($allTemplates as $template) {

            if (!$template->isExists()) {
                continue;
            }

            // 取得樣版中所有組件
            $allWidgets = $template->getWidgets();
            $widgets = array();
            foreach ($allWidgets as $widget) {

                if (!$widget->isExists()) {
                    continue;
                }

                // 取得組件中的資料集
                $dataset = $widget->getDataset();

                // 取得資料集中的所有元件
                $allComponents = $dataset->getComponents();
                $components = array();
                foreach ($allComponents as $component) {

                    if (!$component->isExists()) {
                        continue;
                    }

                    $tmpComponent = $component->toArray();
                    $components[$tmpComponent['dbVariable']] = $tmpComponent;
                }

                $tmpWidget = $widget->toArray();
                $tmpWidget['dataset'] = $dataset->toArray();
                $tmpWidget['components'] = $components;

                $widgets[$tmpWidget['code']] = $tmpWidget;
            }

            $tmpTemplate = $template->toArray();
            $tmpTemplate['widgets'] = $widgets;

            if (isset($tmpTemplate['uri'])) {
                $templates['page'] = $tmpTemplate;
            } else {
                $templates[$tmpTemplate['code']] = $tmpTemplate;
            }
        }

        $metadata['templates'] = $templates;

        return $metadata;
    }

    /**
     * 取得整頁所需要的 script
     * 
     * 頁面 → 版面 → 組件 → 資料集 → 元件
     * 逐層取得所有 script
     * 一口氣交給前端運用
     * 
     * @return string
     */
    public function getAllPageScript()
    {
        $scripts = array();

        // 取得頁面中所有樣版
        $templates = $this->getTemplates();
        foreach ($templates as $template) {

            if (!$template->isExists()) {
                continue;
            }

            // 取得樣版中所有組件
            $widgets = $template->getWidgets();
            foreach ($widgets as $widget) {

                if (!$widget->isExists()) {
                    continue;
                }

                // 取得組件中的資料集
                $dataset = $widget->getDataset();

                // 取得資料集中的所有元件
                $components = $dataset->getComponents();
                foreach ($components as $component) {

                    if (!$component->isExists()) {
                        continue;
                    }
                    $tmpComponent = $component->toArray();

                    // 不要重複載入
                    if (!isset($scripts['component_' . $tmpComponent['component']])) {
                        $scripts['component_' . $tmpComponent['component']] = $component->getScript();
                    }
                }

                $tmpWidget = $widget->toArray();

                // 不要重複載入
                if (!isset($scripts['widget_' . $tmpWidget['code']])) {
                    $scripts['widget_' . $tmpWidget['code']] = $widget->getScript();
                }
            }
        }

        return implode("\r\n", $scripts);
    }

    /**
     * 取得整頁所需要的 style
     * 
     * 頁面 → 版面 → 組件 → 資料集 → 元件
     * 逐層取得所有 style
     * 一口氣交給前端運用
     * 
     * @return string
     */
    public function getAllPageStyle()
    {

        $styles = array();

        // 取得頁面中所有樣版
        $templates = $this->getTemplates();
        foreach ($templates as $template) {

            if (!$template->isExists()) {
                continue;
            }

            // 取得樣版中所有組件
            $widgets = $template->getWidgets();
            foreach ($widgets as $widget) {

                if (!$widget->isExists()) {
                    continue;
                }

                // 取得組件中的資料集
                $dataset = $widget->getDataset();

                // 取得資料集中的所有元件
                $components = $dataset->getComponents();
                foreach ($components as $component) {

                    if (!$component->isExists()) {
                        continue;
                    }
                    $tmpComponent = $component->toArray();

                    // 不要重複載入
                    if (!isset($styles['component_' . $tmpComponent['component']])) {
                        $styles['component_' . $tmpComponent['component']] = $component->getStyle();
                    }
                }

                $tmpWidget = $widget->toArray();

                // 不要重複載入
                if (!isset($styles['widget_' . $tmpWidget['code']])) {
                    $styles['widget_' . $tmpWidget['code']] = $widget->getStyle();
                }
            }
        }

        return implode("\r\n", $styles);
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
        $content = str_replace(
            '{userType}',
            get_instance()->backyard->getUserType(),
            $content
        );

        $content = str_replace(
            '{uri}',
            $this->getUri(),
            $content
        );
        return $content;
    }

    public function getMetadatas()
    {
        $page = $this->backyard->getUser()->getMetadataOfPages();
        return array('status' => 'success', 'page' => $page['metadata']);
    }



    /**
     * 取得整個頁面所需要的Javascript
     * 
     * @param string $code 頁面代碼
     * 
     * @return string
     */
    public function getComponentScript($code)
    {

        $widgetScripts = array();
        $componentScripts = array();

        // 取得 View 路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];


        // 取得欄位後設資料
        $dataset = $this->backyard->dataset->getItem($code);
        if (isset($dataset['dataset'])) {
            foreach ($dataset['dataset']['fields'] as $field) {
                // 取得元件Script內容
                $scriptPath = $this->viewPath . '/components/' . $field['component'] . '/component.js';
                if (!file_exists($scriptPath)) {
                    continue;
                }

                $componentScript = file_get_contents($scriptPath) . "\r\n";
                $componentScript .= $this->readLibraries($this->viewPath . '/components/' . $field['component'] . '/libraries.json');

                $componentScript = str_replace('{adminlte}', '/adminlte', $componentScript);
                $componentScripts[$field['component']] = $componentScript;
            }
        }

        return implode("\r\n", $componentScripts);
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


        /* 版面各區塊所使用的組件 */

        // Logo 區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('logo');
        if ($template['status'] == 'success') {
            $this->readScripts($template['metadata']['widgets'], $widgetScripts, $componentScripts);
        }
        // 左側區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('leftside');
        $this->readScripts($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 頁頭區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('header');
        $this->readScripts($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 頁底區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('footer');
        $this->readScripts($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 取得頁面後設資料
        $template = $this->getMetadata($code);
        $this->readScripts($template['page']['widgets'], $widgetScripts, $componentScripts);


        return implode("\r\n", $widgetScripts) . "\r\n" . implode("\r\n", $componentScripts);
    }

    private function readScripts($widgets, &$widgetScripts, &$componentScripts)
    {
        foreach ($widgets as $widget) {

            // 取得組件後設資料
            $widgetMetadata = $this->backyard->widget->getMetadata($widget['code']);
            if ($widgetMetadata['status'] != 'success') {
                continue;
            }

            $widgetName = $widgetMetadata['metadata']['widget']['code'];
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

            // 如果沒有指定dataset，就不需要往下處理，因為不是每個組件都會有資料來源
            if ($datasetCode != '') {
                $fieldDataset = $this->backyard->dataset->getItem($datasetCode);

                if (isset($fieldDataset['dataset'])) {
                    foreach ($fieldDataset['dataset']['fields'] as $field) {
                        // 取得元件Script內容
                        $scriptPath = $this->viewPath . '/components/' . $field['component'] . '/component.js';
                        if (!file_exists($scriptPath)) {
                            continue;
                        }

                        $componentScript = file_get_contents($scriptPath) . "\r\n";
                        $componentScript .= $this->readLibraries($this->viewPath . '/components/' . $field['component'] . '/libraries.json');

                        $componentScript = str_replace('{adminlte}', '/adminlte', $componentScript);
                        $componentScripts[$field['component']] = $componentScript;
                    }
                }
            }
        }
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
                    continue;
                }

                $libraryPath = $this->viewPath . '/libraries/' . $libraryName;
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

        /* 版面各區塊所使用的組件 */

        // Logo 區塊
        /*
        $template = $this->backyard->getUser()->getMetadataOfTemplate('logo');
        $this->readCsses($template['metadata']['widgets'], $widgetScripts, $componentScripts);
*/
        // 左側區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('leftside');
        $this->readCsses($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 頁頭區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('header');
        $this->readCsses($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 頁底區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('footer');
        $this->readCsses($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 取得頁面後設資料
        $template = $this->getMetadata($code);
        $this->readCsses($template['page']['widgets'], $widgetStyles, $componentStyles);

        /*
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
        */

        return implode("\r\n", $widgetStyles) . "\r\n" . implode("\r\n", $componentStyles);
    }

    private function readCsses($widgets, &$widgetStyles, &$componentStyles)
    {
        foreach ($widgets as $widget) {
            // 取得組件後設資料
            $widgetMetadata = $this->backyard->widget->getMetadata($widget['code']);
            if ($widgetMetadata['status'] != 'success') {
                continue;
            }
            $widgetName = $widgetMetadata['metadata']['widget']['code'];
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
            if ($datasetCode != '') {
                $fieldDataset = $this->backyard->dataset->getItem($datasetCode);
                if (isset($fieldDataset['dataset']['fields'])) {
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
            }
        }
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
                    continue;
                }

                $libraryPath = $this->viewPath . '/libraries/' . $libraryName;
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
        if ($page['status'] != 'success') {
            return $page['message'];
        }

        $content = file_get_contents($this->viewPath . '/full.html');
        $content = $this->refinePathInHtmlContent($content);
        $content = str_replace('{systemTitle}', $page['information']['title'], $content);
        $content = str_replace('{pageTitle}', $page['page']['name'], $content);
        $content = str_replace('{code}', $page['page']['code'], $content);

        return $content;
    }
}
