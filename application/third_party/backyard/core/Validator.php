<?php

/**
 * 後花園 - 欄位(資料)驗證
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */


namespace backyard\core;

require_once(APPPATH . '/third_party/backyard/validators/System.php');

class Validator
{
    /**
     * @var 驗證失敗訊息
     */
    private $invalid = array();

    /**
     * 驗證
     * 
     * @param string $fieldType 要驗證的欄位類型(form:表單輸入驗證,table:清單輸入驗證,search:搜尋輸入驗證)
     * @param array $dataset 資料集後設資料
     * @param array $inputs 輸入資料
     * 
     * @return array status(success:成功,failed:失敗), message[$key:欄位變數](錯誤訊息)
     */
    public function checkInputs($dataset, $inputs)
    {
        if (!isset($dataset['fields'])) {
            return array('status' => 'failed', 'fields' => array(), 'message' => '資料集錯誤');
        }

        if(is_string($dataset['fields'])){
            $dataset['fields'] = json_decode($dataset['fields'], true);
        }

        if (!isset($dataset['fields']) || $dataset['fields'] == '') {
            return array('status' => 'failed', 'fields' => array(), 'message' => '資料集錯誤');
        }

        $fields = $dataset['fields'];

        $buildinFields = array(
            'id'        => array('input' => false, 'name' => '識別碼', 'validators' => array('uuid', 'length{32,40}')),             // 識別碼
            'parent_id' => array('input' => false, 'name' => '上層識別碼', 'validators' => array('uuid', 'length{32,40}')),         // 上層識別碼
            'domain_id' => array('input' => false, 'name' => '網域識別碼', 'validators' => array('uuid', 'length{32,40}')),         // 網域識別碼
            'member_id' => array('input' => false, 'name' => '使用者識別碼', 'validators' => array('uuid', 'length{32,40}')),       // 使用者識別碼
            'visibility' => array('input' => false, 'name' => '可見度', 'validators' => array('number', 'range{0,5}')),             // 可見度
            'level'     => array('input' => false, 'name' => '層數', 'validators' => array('number')),                              // 層數
            'created_at' => array('input' => false, 'name' => '建置時間', 'validators' => array('datetime')),                       // 建置時間
            'updated_at' => array('input' => false, 'name' => '更新時間', 'validators' => array('datetime')),                       // 更新時間
            'sorted_at' => array('input' => false, 'name' => '排序時間', 'validators' => array('datetime')),                        // 排序時間
            'sequence'  => array('input' => false, 'name' => '排列順序', 'validators' => array('number')),                          // 排列順序
            'top_at'    => array('input' => false, 'name' => '置頂時間', 'validators' => array('datetime')),                         // 置頂時間
            'code'    => array('input' => false, 'name' => '模組代碼', 'validators' => array('length{3,30}'))                       // 模組代碼
        );

        $flag = true;
        $this->invalid = array();
        $validFields = array();
       
        foreach ($fields as $field) {

            if (isset($buildinFields[$field['dbVariable']])) {
                $buildinFields[$field['dbVariable']]['input'] = true;
            }

            if (!isset($inputs[$field['frontendVariable']])) {
                $inputs[$field['frontendVariable']] = '';
            }
            $validFields[$field['dbVariable']] = $inputs[$field['frontendVariable']];
            // 欄位名稱
            $name = $field['name'];
            // 欄位前端變數名稱
            $variable = $field['frontendVariable'];
            // 欄位值
            $value = $inputs[$field['frontendVariable']];

            $flag = $flag && $this->validate($name, $variable, $value, $field['validator']);
        }

        foreach ($buildinFields as $key => $field) {
            if ($field['input'] != false) {
                continue;
            }

            if (!isset($inputs[$key])) {
                continue;
            }

            $validFields[$key] = $inputs[$key];

            $flag = $flag && $this->validate($field['name'], $key, $inputs[$key], $field['validators']);
        }

        return array('status' => $flag ? 'success' : 'failed', 'code' => 'validator', 'fields' => $validFields, 'message' => $this->invalid);
    }

    /**
     * 驗證
     * 
     * @param string $name 欄位名稱
     * @param string $variable 變數
     * @param string $value 值
     * @param array $validators 驗證器
     * 
     * @param boolean
     */
    private function validate($name, $variable, $value, $validators)
    {
        $flag = true;

        // 驗證
        foreach ($validators as $validator) {

            $params = array();

            // 格式分析 (有些驗證指令，會有參數，有些沒有，例如：system.length{5,10} 或 system.required)
            if (preg_match('/(.*?)\{(.*?)\}/i', $validator, $res)) {
                $validator = $res[1];
                $params = explode(',', $res[2]);
            } else {
                $params = array();
            }

            $parts = explode('.', $validator);

            // 未指定類別
            if (count($parts) == 1) {
                $parts[1] = $parts[0];
                $parts[0] = 'System';
            } else {
                $parts[0] = ucfirst($parts[0]);
            }

            // 宣告驗證類別
            $classPath = '\\backyard\\validators\\' . $parts[0];
            $validatorClass = new $classPath();

            // 確認驗證函數是否存在
            if (method_exists($validatorClass, $parts[1])) {
                $res = $validatorClass->{$parts[1]}($name, $value, $params);
                $flag = $flag & ($res['status'] == 'success');

                // 驗證失敗，記錄失敗訊息
                if ($res['status'] == 'failed') {
                    $this->invalid[$variable] = $res['message'];
                    break;
                }
            } else {
                $flag = $flag & false;
                $this->invalid[$variable] = '找不到驗證器';
                break;
            }
        }

        return $flag;
    }
}
