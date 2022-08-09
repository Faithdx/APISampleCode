    <?php
    require_once 'AES.php';
    $securityKey = "9Rwkkl8hTiech/KSEinXPfwWiDYQfeJA1KubmDgi34ay4dutIfm0oUj7oqxQlmnA";
    $aesTool = new Aes($securityKey, 'AES-256-CBC');

    $apikey = "8ee3dc19bb124e7ea7eb0433c4f90ffc"; //修改为您的apikey,登录官网后获取

    $ch = curl_init();

    /* 设置验证方式 */
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept:text/plain;charset=utf-8',
        'Content-Type:application/x-www-form-urlencoded', 'charset=utf-8'
    ));
    /* 设置返回结果为流 */
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /* 设置超时时间*/
    curl_setopt($ch, CURLOPT_TIMEOUT, 3000);

    /* 设置通信方式 */
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // 获取用户信息
    $json_data = get_user($ch, $apikey);
    $array = json_decode($json_data, true);

    // 获取发送报告
    // $paramsStr = '{"sendDate":"2022-07-15"}';
    // $cipherText = $aesTool->encrypt($paramsStr);

    // $data = array('header' => $apikey, 'data' => $cipherText);
    // $json_data = GetReport($ch, $data);
    // $array = json_decode($json_data, true);

    // 获取发送详情
    // $paramsStr = '{"orderid":"14"}';
    // $cipherText = $aesTool->encrypt($paramsStr);

    // $data = array('header' => $apikey, 'data' => $cipherText);
    // $json_data = GetReportDetail($ch, $data);
    // $array = json_decode($json_data, true);

    // 发送短信
    // $paramsStr = '{"type":0,"apikind":1,"countrycode":82, "reservedtype":0,"reservedtime":"", "mobile":"01057420006","content":"The is a test", "contacts":"01057420000,01057420001"}';
    // $cipherText = $aesTool->encrypt($paramsStr);

    // $data = array('header' => $apikey, 'data' => $cipherText);
    // $json_data = UserSendSms($ch, $data);
    // $array = json_decode($json_data, true);
    echo '
<pre>';
    print_r($array);

    /************************************************************************************/
    //获得账户信息
    function get_user($ch, $apikey)
    {
        curl_setopt($ch, CURLOPT_URL, 'https://localhost:7087/SmsApi/v1/QueryBalance');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('apikey' => $apikey)));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        checkErr($result, $error);
        return $result;
    }
    // 获取发送报告
    function GetReport($ch, $data)
    {
        curl_setopt($ch, CURLOPT_URL, 'https://localhost:7087/SmsApi/v1/QueryReport');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        checkErr($result, $error);
        return $result;
    }
    // 获取发送详情
    function GetReportDetail($ch, $data)
    {
        curl_setopt($ch, CURLOPT_URL, 'https://localhost:7087/SmsApi/v1/QueryReportDetail');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        checkErr($result, $error);
        return $result;
    }
    // 发送短信
    function UserSendSms($ch, $data)
    {
        curl_setopt($ch, CURLOPT_URL, 'https://localhost:7087/SmsApi/v1/SendSms');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        checkErr($result, $error);
        return $result;
    }

    function checkErr($result, $error)
    {
        if ($result === false) {
            echo 'Curl error: ' . $error;
        } else {
            //echo '操作完成没有任何错误';
        }
    }

    ?>