<?php

/**
 * 後花園 - 前端組件處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Component extends \backyard\core\Metadata
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
     * 取得元件所需的Script
     * 
     * @param string $code 元件代碼
     * 
     * @return string
     */
    public function getScript($code = '')
    {
        $code = ($code != '') ? $code : $this->data['component'];

        // 取得View基本路徑
        get_instance()->backyard->config->loadConfigFile('frontend');
        $this->viewPath = get_instance()->backyard->config->getConfig('frontend')['viewPath'];

        // 取得組件內容
        if (file_exists($this->viewPath . 'components/' . $code . '/component.js')) {
            $content = file_get_contents($this->viewPath . 'components/' . $code . '/component.js');
            $content = $this->refinePathInHtmlContent($content);
            $content = str_replace('{code}', $code, $content) . "\r\n";

            $libraryContent = $this->getScriptLibraries($this->viewPath . 'components/' . $code . '/libraries.json');

            return $content . "\r\n" . $libraryContent;
        } else {
            return '找不到' . $code . '元件腳本(' . $this->viewPath . 'components/' . $code . '/component.js)';
        }
    }

    /**
     * 取得元件Script所需的第三方套件
     * 
     * @param string $libraryJSONFile 套件設定檔路徑
     * 
     * @return string
     */
    private function getScriptLibraries($libraryJSONFile)
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
                $parts = explode('.', $libraryName);
                $ext = end($parts);
                if ($ext == 'js') {
                    $libraryPath = $this->viewPath . '/libraries/' . $libraryName;
                    if (file_exists($libraryPath)) {
                        $script .= file_get_contents($libraryPath) . "\r\n";
                    }
                }
            }
        }
        return $script;
    }

    /**
     * 取得元件所需的Style
     * 
     * @return string
     */
    public function getStyle()
    {
        $code = $this->data['component'];

        // 取得View基本路徑
        get_instance()->backyard->config->loadConfigFile('frontend');
        $this->viewPath = get_instance()->backyard->config->getConfig('frontend')['viewPath'];

        // 取得組件內容
        if (file_exists($this->viewPath . 'components/' . $code . '/component.css')) {
            $content = file_get_contents($this->viewPath . 'components/' . $code . '/component.css');
            $content = $this->refinePathInHtmlContent($content);
            $content = str_replace('{code}', $code, $content) . "\r\n";

            $libraryContent = $this->getStyleLibraries($this->viewPath . 'components/' . $code . '/libraries.json');

            return $content . "\r\n" . $libraryContent;
        } else {
            return '';
        }
    }

    /**
     * 取得元件CSS所需的第三方套件
     * 
     * @param string $libraryJSONFile 套件設定檔路徑
     * 
     * @return string
     */
    private function getStyleLibraries($libraryJSONFile)
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
                $parts = explode('.', $libraryName);
                $ext = end($parts);
                if ($ext == 'css') {
                    $libraryPath = $this->viewPath . '/libraries/' . $libraryName;
                    if (file_exists($libraryPath)) {
                        $style .= file_get_contents($libraryPath) . "\r\n";
                    }
                }
            }
        }
        return $style;
    }
}
