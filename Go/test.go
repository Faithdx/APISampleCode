package main

import (
	"bytes"
	"crypto/aes"
	"crypto/cipher"
	"encoding/base64"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"net/url"
	"strings"
)

func main() {
	apikey := "8ee3dc19bb124e7ea7eb0433c4f90ffc"
	key := "9Rwkkl8hTiech/KSEinXPfwWiDYQfeJA1KubmDgi34ay4dutIfm0oUj7oqxQlmnA"

	// 获取发送短息
	// SendSmsPost(apikey, key)

	// 获取余额
	// GetBalance(apikey)

	// 获取发送报告
	GetReport(apikey, key)

	// 获取发送详情
	// GetReportDetail(apikey, key)

}

func SendSmsPost(apikey, secretkey string) {
	url_post := "https://localhost:7087/SmsApi/v1/SendSms"

	bkey, err := base64.StdEncoding.DecodeString(secretkey)
	if err != nil {
		log.Print("error:", err)
		return
	}

	orignData := []byte("{'type':'0','apikind':'1','countrycode':'82','reservedtype':'0','reservedtime':'null','mobile':'01057420006','content':'this is a test','contacts':'0057420000,01057420001',}")
	log.Print("原文：", string(orignData))

	encrypted := AesEncryptCBC(orignData, bkey)
	encydata := base64.StdEncoding.EncodeToString(encrypted)
	log.Println("密文(base64)：", encydata)
	decrypted := AesDecryptCBC(encrypted, bkey)
	log.Println("解密结果：", string(decrypted))

	urlValues := url.Values{}
	urlValues.Add("header", apikey)
	urlValues.Add("data", encydata)

	resp, _ := http.PostForm(url_post, urlValues)
	body, _ := ioutil.ReadAll(resp.Body)

	fmt.Println(string(body))
}

func GetReport(apikey, secretkey string) {
	url_post := "https://localhost:7087/SmsApi/v1/QueryReport"

	bkey, err := base64.StdEncoding.DecodeString(secretkey)
	if err != nil {
		log.Print("error:", err)
		return
	}

	orignData := []byte("{'sendDate':'2022-07-15'}")
	log.Print("原文：", string(orignData))

	encrypted := AesEncryptCBC(orignData, bkey)
	encydata := base64.StdEncoding.EncodeToString(encrypted)
	log.Println("密文(base64)：", encydata)
	decrypted := AesDecryptCBC(encrypted, bkey)
	log.Println("解密结果：", string(decrypted))

	urlValues := url.Values{}
	urlValues.Add("header", apikey)
	urlValues.Add("data", encydata)

	resp, _ := http.PostForm(url_post, urlValues)
	body, _ := ioutil.ReadAll(resp.Body)

	fmt.Println(string(body))
}

func GetReportDetail(apikey, secretkey string) {
	url_post := "https://localhost:7087/SmsApi/v1/QueryReportDetail"

	bkey, err := base64.StdEncoding.DecodeString(secretkey)
	if err != nil {
		log.Print("error:", err)
		return
	}

	orignData := []byte("{'orderid':14}")
	log.Print("原文：", string(orignData))

	encrypted := AesEncryptCBC(orignData, bkey)
	encydata := base64.StdEncoding.EncodeToString(encrypted)
	log.Println("密文(base64)：", encydata)
	decrypted := AesDecryptCBC(encrypted, bkey)
	log.Println("解密结果：", string(decrypted))

	urlValues := url.Values{}
	urlValues.Add("header", apikey)
	urlValues.Add("data", encydata)

	resp, _ := http.PostForm(url_post, urlValues)
	body, _ := ioutil.ReadAll(resp.Body)

	fmt.Println(string(body))
}

func GetBalance(apikey string) {
	url := "https://localhost:7087/SmsApi/v1/QueryBalance"

	method := "POST"
	payload := strings.NewReader("apikey=" + apikey)
	client := &http.Client{}
	req, err := http.NewRequest(method, url, payload)

	if err != nil {
		fmt.Println(err)
		return
	}
	req.Header.Set("Content-Type", "application/x-www-form-urlencoded")

	res, err := client.Do(req)
	if err != nil {
		fmt.Println(err)
		return
	}
	defer res.Body.Close()

	body, err := ioutil.ReadAll(res.Body)
	if err != nil {
		fmt.Println(err)
		return
	}
	fmt.Println(string(body))
}

func AesEncryptCBC(origData []byte, key []byte) (encrypted []byte) {
	// 分组秘钥
	// NewCipher该函数限制了输入k的长度必须为16, 24或者32
	aesKey := key[0:32]
	block, _ := aes.NewCipher(aesKey)
	blockSize := block.BlockSize() // 获取秘钥块的长度
	aesIv := key[32:48]
	origData = PKCS7Padding(origData, blockSize)      // 补全码
	blockMode := cipher.NewCBCEncrypter(block, aesIv) // 加密模式
	encrypted = make([]byte, len(origData))           // 创建数组
	blockMode.CryptBlocks(encrypted, origData)        // 加密
	return encrypted
}
func AesDecryptCBC(encrypted []byte, key []byte) (decrypted []byte) {
	aeskey := key[0:32]
	block, _ := aes.NewCipher(aeskey) // 分组秘钥
	// blockSize := block.BlockSize()    // 获取秘钥块的长度
	aesIv := key[32:48]
	blockMode := cipher.NewCBCDecrypter(block, aesIv) // 加密模式
	decrypted = make([]byte, len(encrypted))          // 创建数组
	blockMode.CryptBlocks(decrypted, encrypted)       // 解密
	decrypted = PKCS7UnPadding(decrypted)             // 去除补全码
	return decrypted
}

//使用PKCS7进行填充
func PKCS7Padding(ciphertext []byte, blockSize int) []byte {
	padding := blockSize - len(ciphertext)%blockSize
	padtext := bytes.Repeat([]byte{byte(padding)}, padding)
	return append(ciphertext, padtext...)
}

func PKCS7UnPadding(origData []byte) []byte {
	length := len(origData)
	unpadding := int(origData[length-1])
	return origData[:(length - unpadding)]
}
