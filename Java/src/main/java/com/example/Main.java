package com.example;

import org.apache.http.HttpEntity;
import org.apache.http.NameValuePair;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.message.BasicNameValuePair;
import org.apache.http.util.EntityUtils;

import java.io.IOException;
import java.net.URISyntaxException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Dictionary;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.google.gson.Gson;

public class Main {
    // 查账户信息的http地址
    private static String URI_GET_USER_INFO = "https://localhost:7087/SmsApi/v1/QueryBalance";
    private static String URI_GET_REPORTLIST = "https://localhost:7087/SmsApi/v1/Queryreport";
    private static String URI_GET_REPORTDETAIL = "https://localhost:7087/SmsApi/v1/QueryReportDetail";
    private static String URI_GET_SENDSMS = "https://localhost:7087/SmsApi/v1/SendSms";

    public static void main(String[] args) throws IOException, URISyntaxException {
        System.out.println("Hello World!");

        String apikey = "8ee3dc19bb124e7ea7eb0433c4f90ffc";

        /**************** 查账户信息调用 *****************/
        // System.out.println(Main.getUserInfo(apikey));

        /**************** 查报告调用 *****************/
        System.out.println(Main.userSendSms(apikey));
        /**************** 查报告调用 *****************/
        // System.out.println(Main.getReport(apikey));
    }

    /**
     * 获取账户信息
     *
     * @return json格式字符串
     * @throws java.io.IOException
     */
    public static String getUserInfo(String apikey) throws IOException, URISyntaxException {
        Map<String, String> params = new HashMap<String, String>();
        params.put("apikey", apikey);
        return post(URI_GET_USER_INFO, params);
    }

    /**
     * 获取发送报告
     *
     * @return json格式字符串
     * @throws java.io.IOException
     */
    public static String getReport(String apikey) throws IOException, URISyntaxException {
        Map<String, String> dataDic = new HashMap<String, String>();
        dataDic.put("sendDate", "2022-07-15");
        Gson gsonData = new Gson();
        String data = gsonData.toJson(dataDic);

        String enDataDic = "";
        try {
            enDataDic = AesUtils.encryptAES(data);
        } catch (Exception e) {
            e.printStackTrace();
        }

        Map<String, String> params = new HashMap<String, String>();
        params.put("header", apikey);
        params.put("data", enDataDic);

        return post(URI_GET_REPORTLIST, params);
    }

    /**
     * 发送短信
     *
     * @return json格式字符串
     * @throws java.io.IOException
     */
    public static String userSendSms(String apikey) throws IOException, URISyntaxException {
        Map<String, String> dataDic = new HashMap<String, String>();
        dataDic.put("type", "0");
        dataDic.put("apikind", "1");
        dataDic.put("countrycode", "82");
        dataDic.put("reservedtype", "0");
        dataDic.put("reservedtime", null);
        dataDic.put("mobile", "01057420006");
        dataDic.put("content", "这是个测试");
        dataDic.put("sendDate", "01057420000,01057420001");

        Gson gsonData = new Gson();
        String data = gsonData.toJson(dataDic);

        String enDataDic = "";
        try {
            enDataDic = AesUtils.encryptAES(data);
        } catch (Exception e) {
            e.printStackTrace();
        }

        Map<String, String> params = new HashMap<String, String>();
        params.put("header", apikey);
        params.put("data", enDataDic);

        return post(URI_GET_REPORTLIST, params);
    }

    public static String post(String url, Map<String, String> paramsMap) {
        CloseableHttpClient client = HttpClients.createDefault();
        String responseText = "";
        CloseableHttpResponse response = null;
        try {
            HttpPost httpPost = new HttpPost(url);
            httpPost.addHeader("Content-Type", "application/x-www-form-urlencoded");
            if (paramsMap != null && paramsMap.size() > 0) {
                List<NameValuePair> paramList = new ArrayList<NameValuePair>();
                for (Map.Entry<String, String> param : paramsMap.entrySet()) {
                    NameValuePair pair = new BasicNameValuePair(param.getKey(), param.getValue());
                    paramList.add(pair);
                }
                httpPost.setEntity(new UrlEncodedFormEntity(paramList, "UTF-8"));
            }

            response = client.execute(httpPost);
            HttpEntity entity = response.getEntity();
            if (entity != null) {
                responseText = EntityUtils.toString(entity);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            try {
                response.close();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
        return responseText;
    }
}