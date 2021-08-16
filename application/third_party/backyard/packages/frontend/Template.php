<?php

/**
 * 後花園 - 前端版面處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Template extends \backyard\core\Metadata
{

    /**
     * 取得所有樣版
     * 
     * @param string $metadataType 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Metadata[]
     */
    public function list($metadataType = 'template', $toArray = false)
    {
        $templates = array();
        $responses = parent::list($metadataType);
        if ($toArray) {
            $templates = $responses;
        } else {
            foreach ($responses as $response) {
                $templates[] = new \backyard\packages\frontend\Template($response->toArray());
            }
        }

        return $templates;
    }

    /**
     * 取得指定樣版
     * 
     * @param array $code 代碼
     * @param string $metadataType 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Metadata
     */
    public function get($code, $metadataType = 'template', $toArray = false)
    {
        if (isset($code) && trim($code) != '') {
            $response = parent::get($code, $metadataType);
            if ($toArray) {
                return $response;
            } else {
                return new \backyard\packages\frontend\Template($response->toArray());
            }
        } else {
            return new \backyard\packages\frontend\Template(array());
        }
    }

    /**
     * 取得此版面的所有組件
     * 
     * @return Widget[]
     */
    public function getWidgets()
    {
        $widgets = array();
        if(is_string($this->data['widgets'])){
            $this->data['widgets'] = array();
        }
        foreach ($this->data['widgets'] as $widget) {
            $new_widget = get_instance()->backyard->widget->get($widget['code']);
            if ($new_widget->isExists()) {
                $new_widget->desktop = $widget['desktop'];
                $new_widget->pad = $widget['pad'];
                $new_widget->mobile = $widget['mobile'];
                $widgets[] = $new_widget;
            }
        }
        return $widgets;
    }
}
