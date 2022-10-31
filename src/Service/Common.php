<?php
namespace App\Service;

/**
 * 公用方法
 *
 *
 *
 */
class Common
{ 
    protected static $debug;

    /**
     * geo helper 地址转换为坐标
     * @param $address
     * @return bool|string
     */
    public function geoHelperAddress($address, $merchant_id = '')
    {
        try {
            // FIXME 可以把merchant_id也加入到Key中作為關鍵字查詢，避免address查不到頻繁而呼叫另外一個服務。
            $cackeKey = 'cache-address-'.$address;

            // 從獲取座標
            $userLocation = redisx()->get($cackeKey);
            if ($userLocation) {
                return $userLocation;
            }

            $key = 'time=' . time();

            // requestLog：寫日志
            // FIXME 寫入日誌參數太多，可以針對這個函數封裝一個專用的日誌函數。
            requestLog('Backend', 'Thrift', 'Http', 'phpgeohelper\\Geocoding->convert_addresses', 'https://geo-helper-hostr.ks-it.co',  [[$address, $key]]);

            // getThriftService： 獲取 Thrift 服務
            $geoHelper = ServiceContainer::getThriftService('phpgeohelper\\Geocoding');
            $param = json_encode([[$address, $key]]);

            // 調用接口，以地址獲取座標
            // FIXME 這裡可以直接重構成一行，或者抽出來成為getThriftResponse函數。
            $response = $geoHelper->convert_addresses($param);
            $response = json_decode($response, true);

            if ($response['error'] == 0) {
                responseLog('Backend', 'phpgeohelper\\Geocoding->hksf_addresses', 'https://geo-helper-hostr.ks-it.co', '200', '0',  $response);
                // FIXME 第三方服務無法保證回傳格式，建議封裝成檢查陣列存在函數，比如說getArrayKey如果找不到回傳預設值。
                $data = $response['data'][0];
                $coordinate = $data['coordinate'];

                // 如果返回 '-999,-999'，表示調用接口失敗，那麼直接使用商家位置的座標
                if ($coordinate == '-999,-999') {
                    // FIXME 一樣可以針對這個日誌封裝一個專用物件，統一這個服務的日誌操作。
                    infoLog('geoHelper->hksf_addresses change failed === ' . $address);
                    if ($merchant_id) {
                        $sMerchant = new Merchant();
                        $res = $sMerchant->get_merchant_address($merchant_id);
                        $user_location = $res['latitude'] . ',' . $res['longitude'];
                        // FIXME 這邊算是有找到Merchant資料了，應該也要存入快取之中。
                        return $user_location;
                    }
                    infoLog('geoHelper->hksf_addresses change failed === merchant_id is null' . $merchant_id);
                    return false;
                }
                // FIXME 這邊是成功取得Thrift 服務的回傳，但又不能保證coordinate回傳的格式嗎？如果是的話需要寫註解提醒，或者封裝為Thrift的處理函數。
                if (!isset($data['error']) && (strpos($coordinate,',') !== false)) {
                    $arr = explode(',', $coordinate);
                    $user_location = $arr[1] . ',' . $arr[0];

                    // set cache
                    // FIXME 建議任何設置快取的地方都需要加上過期時間，並且加上一個隨機數避免快取服務雪崩。
                    // FIXME 除了過期時間，也應該幫快取服務設計淘汰策略，避免使用的記憶體空間不足。
                    // FIXME 如果會有高併發的疑慮，應該加上原子鎖讓有限的request到服務後面查詢資料並放回緩存就好。
                    redisx()->set($cackeKey, $user_location);
                    return $user_location;
                }
            }
            responseLog('Backend', 'phpgeohelper\\Geocoding->hksf_addresses', 'https://geo-helper-hostr.ks-it.co', '401', '401',  $response);

            // FIXME 找不到的結果也可以放到快取中，避免被惡意穿透。
            return false;
        } catch (\Throwable $t) {
            criticalLog('geoHelperAddress critical ==' . $t->getMessage());
            // FIXME 統一回傳格式為boolean，應該修改為false。
            return 0;
        }
    }

    // 回调状态过滤
    public static function checkStatusCallback($order_id, $status)
    {
        // FIXME 可以使用switch case 統一判斷status，並且在default加上找不到對應狀態碼時的例外處理。
        // FIXME 可以將狀態碼使用常數命名給予意義方便閱讀，比如說const CAN_CALLBACK = 900;
        // FIXME 建議將輸入輸出型別定義好，比如說回傳是boolean時就回傳true or false，回傳是字串可以顯性的將格式轉換寫出來，例covertStatusToString()。

        // 是900 可以回调
        if ($status == 900) {
            return 1;
        }
        // backend状态为 909 915 916 时 解锁工作单 但不回调
        $code_arr = ['909', '915', '916'];
        if (in_array($status, $code_arr)) {
            infoLog('checkStatusCallback backend code is 909 915 916');
            return 0;
        }

        $open_status_arr = ['901' => 1, '902' => 2, '903' => 3];
        return $order_id.'-'.$open_status_arr[$status];
    }
}
