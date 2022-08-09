import requests
import base64
from Crypto.Cipher import AES
# from Crypto.Util.Padding import pad
# import time
import json


class SmsApiTest:

    def GetBalance():
        post_url = 'https://localhost:7087/SmsApi/v1/QueryBalance'
        request_param = {'apikey': apikey}
        res = SmsApiTest.request_post(post_url, request_param)
        print(res)

    def GetReport():
        post_url = 'https://localhost:7087/SmsApi/v1/QueryReport'
        p_json = {'sendDate': '2022-07-15'}

        e = a.aes_encrypt(json.dumps(p_json))
        request_param = {
            'header': '8ee3dc19bb124e7ea7eb0433c4f90ffc',
            'data': e
        }
        res = SmsApiTest.request_post(post_url, request_param)
        print(res)

    def GetReportDetail():
        post_url = 'https://localhost:7087/SmsApi/v1/QueryReportDetail'
        p_json = {'orderid': '14'}

        e = a.aes_encrypt(json.dumps(p_json))
        request_param = {
            'header': '8ee3dc19bb124e7ea7eb0433c4f90ffc',
            'data': e
        }
        res = SmsApiTest.request_post(post_url, request_param)
        print(res)

    def SendSms():
        post_url = 'https://localhost:7087/SmsApi/v1/SendSms'
        p_json = {
            'type': '0',
            'apikind': '1',
            'countrycode': '82',
            'reservedtype': '0',
            'reservedtime': 'null',
            'mobile': '01057420006',
            'content': 'this is a test',
            'contacts': '0057420000,01057420001'
        }

        e = a.aes_encrypt(json.dumps(p_json))
        request_param = {
            'header': '8ee3dc19bb124e7ea7eb0433c4f90ffc',
            'data': e
        }
        res = SmsApiTest.request_post(post_url, request_param)
        print(res)

    def request_post(url, param):
        fails = 0
        while True:
            try:
                if fails >= 20:
                    break

                req = requests.post(url=url,
                                    data=param,
                                    timeout=1000,
                                    verify=False)
                if req.status_code == 200:
                    text = req.json()
                else:
                    continue
            except Exception:
                fails += 1
                print('网络连接出现问题, 正在尝试再次请求: ', fails)
            else:
                break
        return text


class Encrypt:

    def __init__(self, aeskey, iv):
        self.key = aeskey
        self.iv = iv

    # @staticmethod
    def pkcs7padding(self, text):
        """明文使用PKCS7填充 """
        bs = 16
        length = len(text)
        bytes_length = len(text.encode('utf-8'))
        padding_size = length if (bytes_length == length) else bytes_length
        padding = bs - padding_size % bs
        padding_text = chr(padding) * padding
        self.coding = chr(padding)
        return text + padding_text

    def aes_encrypt(self, content):
        """ AES加密 """
        cipher = AES.new(self.key, AES.MODE_CBC, self.iv)
        # 处理明文
        content_padding = self.pkcs7padding(content)
        # 加密
        encrypt_bytes = cipher.encrypt(content_padding.encode('utf-8'))
        # 重新编码
        result = str(base64.b64encode(encrypt_bytes), encoding='utf-8')
        return result

    def aes_decrypt(self, content):
        """AES解密 """
        cipher = AES.new(self.key, AES.MODE_CBC, self.iv)
        content = base64.b64decode(content)
        text = cipher.decrypt(content).decode('utf-8')
        return text.rstrip(self.coding)


if __name__ == '__main__':
    apikey = '8ee3dc19bb124e7ea7eb0433c4f90ffc'
    key = '9Rwkkl8hTiech/KSEinXPfwWiDYQfeJA1KubmDgi34ay4dutIfm0oUj7oqxQlmnA'
    bkey = bytearray(base64.b64decode(key))
    aeskey = bkey[0:32]
    iv = bkey[32:]
    a = Encrypt(aeskey, iv)

    req = SmsApiTest
    # 获取余额
    # res = req.GetBalance()
    # 获取报告
    # res = req.GetReport()
    # 获取报告详情
    # res = req.GetReportDetail()
    # 发送短信
    res = req.SendSms()
