package com.example;

import java.lang.reflect.Array;
import java.security.Security;
import java.util.Arrays;

import javax.crypto.Cipher;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;

import org.apache.commons.codec.binary.Base64;

import org.bouncycastle.jce.provider.BouncyCastleProvider;

public class AesUtils {

    private static final String key = "9Rwkkl8hTiech/KSEinXPfwWiDYQfeJA1KubmDgi34ay4dutIfm0oUj7oqxQlmnA";
    private static final String iv = "EJ9iIPhzB4I5UDfs";

    static {
        try {
            Security.addProvider(new BouncyCastleProvider());
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * @author miracle.qu
     * @Description AES算法加密明文
     * @param data 明文
     * @return 密文
     */
    public static String encryptAES(String data) throws Exception {
        try {
            byte[] aesKey = AesUtils.decode(key);
            byte[] bkey = Arrays.copyOf(aesKey, 32);
            byte[] bIV = Arrays.copyOfRange(aesKey, 32, 48);

            Cipher cipher = Cipher.getInstance("AES/CBC/PKCS7Padding");
            SecretKeySpec keyspec = new SecretKeySpec(bkey, "AES");
            IvParameterSpec ivspec = new IvParameterSpec(bIV);

            // SecretKeySpec keyspec = new SecretKeySpec(key.getBytes("UTF-8"), "AES");
            // CBC模式，需要一个向量iv，可增加加密算法的强度
            // IvParameterSpec ivspec = new IvParameterSpec(iv.getBytes("UTF-8"));

            cipher.init(Cipher.ENCRYPT_MODE, keyspec, ivspec);
            byte[] encrypted = cipher.doFinal(data.getBytes("UTF-8"));
            // BASE64做转码。
            return AesUtils.encode(encrypted).trim();
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    /**
     * @author miracle.qu
     * @Description AES算法解密密文
     * @param data 密文
     * @return 明文
     */
    public static String decryptAES(String data) throws Exception {
        try {
            // 先用base64解密
            byte[] encrypted1 = AesUtils.decode(data);

            byte[] aesKey = AesUtils.decode(key);
            byte[] bkey = Arrays.copyOf(aesKey, 32);
            byte[] bIV = Arrays.copyOfRange(aesKey, 32, 48);

            Cipher cipher = Cipher.getInstance("AES/CBC/PKCS7Padding");
            SecretKeySpec keyspec = new SecretKeySpec(bkey, "AES");
            IvParameterSpec ivspec = new IvParameterSpec(bIV);
            
            // SecretKeySpec keyspec = new SecretKeySpec(key.getBytes("UTF-8"), "AES");
            // IvParameterSpec ivspec = new IvParameterSpec(iv.getBytes("UTF-8"));

            cipher.init(Cipher.DECRYPT_MODE, keyspec, ivspec);

            byte[] original = cipher.doFinal(encrypted1);
            String originalString = new String(original);
            return originalString.trim();
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    /**
     * 编码
     * 
     * @param byteArray
     * @return
     */
    public static String encode(byte[] byteArray) {
        return new String(new Base64().encode(byteArray));
    }

    /**
     * 解码
     * 
     * @param base64EncodedString
     * @return
     */
    public static byte[] decode(String base64EncodedString) {
        return new Base64().decode(base64EncodedString);
    }
}
