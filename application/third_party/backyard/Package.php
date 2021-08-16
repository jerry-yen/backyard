<?php

/**
 * 後花園 - 套件
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard;

class Package
{
    /**
     * @var Backyard 後花園主程式
     */
    protected $backyard = null;

    /**
     * 建構子
     */
    public function __construct(&$backyard)
    {

        $this->backyard = &$backyard;
    }
}
