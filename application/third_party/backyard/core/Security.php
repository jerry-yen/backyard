<?php

/**
 * 後花園 - 安全性處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Security extends \backyard\Package
{

    /**
     * 取得目前的IP
     * 
     * @return string
     */
    private function getIP()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = "0.0.0.0";
        }
        return $cip;
    }

    /**
     * IP 過濾器
     * 
     * @param string $ip 由程式指定要過濾的IP (option)
     * 
     * @return array
     */
    public function filterIPs($ip = null)
    {
        // 取得目前IP
        if (is_null($ip)) {
            $currentIp = $this->getIP();
        }
        // 取得指定IP
        else {
            $currentIp = $ip;
        }
        $currentIpLong = ip2long($currentIp);

        // 取得設定檔中的IP
        $this->backyard->config->loadConfigFile('iptable');

        // 取得的IP設定就算是單一，也要把他當作是個範圍
        // 111.252.82.* => 111.252.82.*-111.252.82.*
        // explode('-', $ip)
        // 第一區，* = 0
        // 第二區，* = 255

        $deny = $this->backyard->config->getConfig('ip_deny');
        $allow = $this->backyard->config->getConfig('ip_allow');

        if (count($deny) > 0) {
            // 被禁止的IP
            foreach ($deny as $item) {
                $parts = explode('-', $item);
                if (count($parts) < 2) {
                    $parts[1] = $parts[0];
                }
                $parts[0] = str_replace('*', '0', $parts[0]);
                $parts[1] = str_replace('*', '255', $parts[1]);

                $start = ip2long($parts[0]);
                $end = ip2long($parts[1]);

                if ($start <= $currentIpLong && $currentIpLong <= $end) {
                    return array('status' => 'deny');
                }
            }

            return array('status' => 'allow');
        } elseif (count($allow) > 0) {
            // 被允許的IP
            foreach ($allow as $item) {
                $parts = explode('-', $item);
                if (count($parts) < 2) {
                    $parts[1] = $parts[0];
                }
                $parts[0] = str_replace('*', '0', $parts[0]);
                $parts[1] = str_replace('*', '255', $parts[1]);

                $start = ip2long($parts[0]);
                $end = ip2long($parts[1]);

                if ($start <= $currentIpLong && $currentIpLong <= $end) {
                    return array('status' => 'allow');
                }
            }

            return array('status' => 'deny');
        } else {
            // 沒有允許、沒有禁止，預設為通過
            return array('status' => 'allow');
        }
    }
}
