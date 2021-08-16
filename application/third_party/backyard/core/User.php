<?php

/**
 * 後花園 - 用戶類別
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class User extends \backyard\core\Item
{
    /**
     * 登入
     * 
     * @param array $inputs 帳號密碼參數
     * 
     * @return User
     */
    public function login($inputs = array())
    {
        $this->data['url'] = '';
        $this->data['message'] = '';
        $this->data['status'] = 'failed';

        if (trim($inputs['account']) == '' || trim($inputs['password']) == '') {
            $this->data['message'] = '請輸入帳號或密碼';
            return new User('', $this->data);
        }



        // 開發者登入
        if (get_instance()->backyard->getUserType() == 'master') {
            $metadata = get_instance()->backyard->metadata->get('information', 'information');

            if ($inputs['account'] == $metadata->account && $inputs['password'] == $metadata->password) {

                get_instance()->session->set_userdata('userType', 'master');
                get_instance()->session->set_userdata('userId', '00000000-0000-0000-0000-000000000000');

                $this->data['url'] = '/master/information';
                $this->data['status'] = 'success';
            } else {
                $this->data['message'] = '帳號或密碼錯誤';
            }
        }

        // 管理者
        else {

            // 確認是否為總管理者
            $isAdmin = false;

            // 檢視所有開發者平台所設定的管理者帳號
            $adminAccounts = get_instance()->backyard->metadata->list('account');
            foreach ($adminAccounts as $account) {
                if ($inputs['account'] == $account->account && $inputs['password'] == $account->password) {
                    get_instance()->session->set_userdata('userType', 'admin');
                    get_instance()->session->set_userdata('userId', $account->id);


                    $this->data['status'] = 'success';
                    $isAdmin = true;

                    get_instance()->backyard->loadPackage('frontend');
                    // 取得登入頁面 - 暫想不到什麼好機制，直接取得選單的第一個頁面
                    $pages = get_instance()->backyard->page->list();
                    if (count($pages) > 0) {
                        if (substr($pages[0]->uri, 0, 1) != '/') {
                            $pages[0]->uri = '/' . $pages[0]->uri;
                        }
                        $this->data['url'] = $pages[0]->uri;
                    } else {
                        $this->data['message'] = '後台尚未設定任何頁面';
                    }
                    break;
                }
            }

            if (!$isAdmin) {
                $this->data['message'] = '帳號或密碼錯誤';
            }
        }

        return new User($this->table, $this->data);
    }

    public function logout()
    {
    }

    public function isLogin()
    {
    }
}
