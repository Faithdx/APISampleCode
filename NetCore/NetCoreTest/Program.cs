using System;
using System.Collections.Generic;
using System.Collections.Specialized;
using System.Net;
using System.Security.Cryptography;
using System.Text;
using System.Threading.Tasks;

namespace NetCoreTest
{
    internal class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Hello World!");

            string secretkey = "9Rwkkl8hTiech/KSEinXPfwWiDYQfeJA1KubmDgi34ay4dutIfm0oUj7oqxQlmnA";
            string apikey = "8ee3dc19bb124e7ea7eb0433c4f90ffc";

            // 获取余额
            var res = GetUserInfo(apikey);

            // 获取报告
            //var res = GetReport(apikey, secretkey);

            // 获取报告详情
            //var res = GetReportDetail(apikey, secretkey);

            // 发送短信
            //var res = UserSendSms(apikey, secretkey);

            Console.WriteLine(res.Result);
        }

        // 获取报告
        static async Task<string> GetReport(string apikey, string secretkey)
        {
            string url = "https://localhost:7087/SmsApi/v1/QueryReport";
            string date = "2022-07-15";

            Dictionary<string, string> values = new Dictionary<string, string>();
            values.Add("sendDate", date);
            string data_json = Newtonsoft.Json.JsonConvert.SerializeObject(values);

            NameValueCollection nameValues = new NameValueCollection();
            nameValues.Add("Header", apikey);

            try
            {
                string upload_data = await AesEncrypt(secretkey, data_json);
                nameValues.Add("Data", upload_data);
            }
            catch (Exception ex)
            {
                return ex.Message;
            }

            var json = HttpPost(url, nameValues).Result;

            return json;
        }

        // 获取报告
        static async Task<string> GetReportDetail(string apikey, string secretkey)
        {
            string url = "https://localhost:7087/SmsApi/v1/QueryReportDetail";
            string orderid = "14";

            Dictionary<string, string> values = new Dictionary<string, string>();
            values.Add("orderid", orderid);
            string data_json = Newtonsoft.Json.JsonConvert.SerializeObject(values);

            NameValueCollection nameValues = new NameValueCollection();
            nameValues.Add("Header", apikey);

            try
            {
                string upload_data = await AesEncrypt(secretkey, data_json);
                nameValues.Add("Data", upload_data);
            }
            catch (Exception ex)
            {
                return ex.Message;
            }

            var json = HttpPost(url, nameValues).Result;

            return json;
        }

        // 获取账户余额
        static Task<string> GetUserInfo(string apikey)
        {
            string url = "https://localhost:7087/SmsApi/v1/QueryBalance";

            NameValueCollection nameValues = new NameValueCollection();
            nameValues.Add("apikey", apikey);

            var json = HttpPost(url, nameValues);

            return json;
        }

        // 发送短信
        static async Task<string> UserSendSms(string apikey, string secretkey)
        {
            string url = "https://localhost:7087/SmsApi/v1/SendSms";

            Dictionary<string, string> values = new Dictionary<string, string>();
            values.Add("type", "0");
            values.Add("apikind", "1");
            values.Add("countrycode", "82");
            values.Add("reservedtype", "0");
            values.Add("reservedtime", null);
            values.Add("mobile", "01057420006");
            values.Add("content", "发送短信测试");
            values.Add("contacts", "01057420000,01057420001");

            string data_json = Newtonsoft.Json.JsonConvert.SerializeObject(values);

            NameValueCollection nameValues = new NameValueCollection();
            nameValues.Add("Header", apikey);

            try
            {
                string upload_data = await AesEncrypt(secretkey, data_json);
                nameValues.Add("Data", upload_data);
            }
            catch (Exception ex)
            {
                return ex.Message;
            }

            var json = HttpPost(url, nameValues).Result;

            return json;
        }


        static async Task<string> HttpPost(string url, NameValueCollection postDataStr)
        {
            string responseData = "";

            try
            {
                using (var client = new WebClient())
                {
                    byte[] bytes = client.UploadValues(url, postDataStr);
                    responseData = Encoding.UTF8.GetString(bytes);
                }
            }
            catch (Exception ex)
            {
                return ex.Message;
            }
            return responseData;
        }

        public static async Task<string> AesEncrypt(string key, string data)
        {
            try
            {
                byte[] bKey = Convert.FromBase64String(key);
                byte[] aesKey = new byte[32];
                byte[] aesIV = new byte[16];

                Array.Copy(bKey, 0, aesKey, 0, 32);
                Array.Copy(bKey, 32, aesIV, 0, 16);

                AesCryptoServiceProvider aes = new AesCryptoServiceProvider();
                aes.IV = aesIV;
                aes.Key = aesKey;
                aes.Mode = CipherMode.CBC;
                aes.Padding = PaddingMode.PKCS7;
                byte[] bdata = Encoding.UTF8.GetBytes(data);
                using (ICryptoTransform encrypt = aes.CreateEncryptor())
                {
                    byte[] destin = encrypt.TransformFinalBlock(bdata, 0, bdata.Length);
                    return Convert.ToBase64String(destin);
                }
            }
            catch (Exception ex)
            {
                return ex.Message;
            }
        }
    }
}
