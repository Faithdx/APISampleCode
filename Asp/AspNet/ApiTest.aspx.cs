using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Collections.Specialized;
using System.Net;
using System.Security.Cryptography;
using System.Text;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace AspNet
{
    public partial class ApiTest1 : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            
        }

        string secretkey = "9Rwkkl8hTiech/KSEinXPfwWiDYQfeJA1KubmDgi34ay4dutIfm0oUj7oqxQlmnA";
        string apikey = "8ee3dc19bb124e7ea7eb0433c4f90ffc";
        protected async void Button1_Click(object sender, EventArgs e)
        {
            var res = await GetReport(apikey,secretkey);
            Label1.Text= res;
        }



        static async Task<string> GetReport(string apikey, string secretkey)
        {
            string url = "https://localhost:7087/SmsApi/v1/QueryReport";
            string date = "2022-07-15";

            Dictionary<string, string> values = new Dictionary<string, string>();
            values.Add("sendDate", date);
            string data_json = JsonConvert.SerializeObject(values);

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

        protected void Asp_Click(object sender, EventArgs e)
        {
            Response.Redirect("Test.asp");
        }
    }
}