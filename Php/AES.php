  <?php
  class Aes
  {

    /**
     * @var string 秘钥
     * AES-128-CBC key 长度 16 位,IV 16位
     * AES-192-CBC key 长度 24 位,IV 16位
     * AES-256-CBC key 长度 32 位,IV 16位
     */

    protected $securityKey;
    protected $method;

    /**
     * @var string 偏移量
     */
    protected $iv;

    /**
     * Aes constructor.
     * @param string $securityKey
     * @param string $method
     * @param string $iv
     */
    public function __construct(string $securityKey, string $method = 'AES-256-CBC')
    {
      if (empty($securityKey)) {
        throw new RuntimeException('秘钥不能为空');
      }
      $bkey = $this->getbytes(base64_decode($securityKey));
      $aeskey = $this->tostr(array_slice($bkey, 0, 32));
      // $aeskeyBase = base64_encode($aeskey);
      $aesiv = $this->tostr(array_slice($bkey, -16));
      // $aesivBase = base64_encode($aesiv);

      $this->securityKey = $aeskey;
      if (false === $this->isSupportCipherMethod($method)) {
        throw new RuntimeException('暂不支持该加密方式');
      }
      $this->method = $method;

      $this->iv = $this->initializationVector($method, $aesiv);
    }


    /**
     * 加密
     * @param string $plainText 明文
     * @return bool|string
     */
    public function encrypt(string $plainText)
    {
      $originData = (openssl_encrypt($this->addPkcs7Padding($plainText, 16), $this->method, $this->securityKey, OPENSSL_NO_PADDING, $this->iv));
      return $originData === false ? false : base64_encode($originData);
    }

    /**
     * 解密
     * @param string $cipherText 密文
     * @return bool|string
     */
    public function decrypt(string $cipherText)
    {
      $str = base64_decode($cipherText);
      $data = openssl_decrypt($str, $this->method, $this->securityKey, OPENSSL_NO_PADDING, $this->iv);
      return $data === false ? false : $this->stripPKSC7Padding($data);
    }

    /**
     * 初始化向量
     * @param string $method
     * @param string $iv
     * @return false|string
     */
    private function initializationVector(string $method, string $iv = '')
    {
      $originIvLen = openssl_cipher_iv_length($method);
      if (false === $originIvLen) {
        return '';
      }
      $currentIvLen = strlen($iv);
      if ($originIvLen === $currentIvLen) {
        $outIv = $iv;
      } elseif ($currentIvLen < $originIvLen) {
        $outIv = $iv . str_repeat("\0", $originIvLen - $currentIvLen);
      } elseif ($currentIvLen > $originIvLen) {
        $outIv = substr($iv, 0, $originIvLen);
      } else {
        $outIv = str_repeat("\0", $originIvLen);
      }
      return $outIv;
    }

    /**
     * 填充算法
     * @param string $source
     * @return string
     */
    private function addPKCS7Padding($source): string
    {
      $source = trim($source);
      $block = 16;

      $pad = $block - (strlen($source) % $block);
      if ($pad <= $block) {
        $char = chr($pad);
        $source .= str_repeat($char, $pad);
      }
      return $source;
    }

    /**
     * 是否支持该加密方式
     * @param string $method
     * @return bool
     */
    private function isSupportCipherMethod(string $method): bool
    {
      $method = strtolower($method);
      if (in_array($method, openssl_get_cipher_methods(), true)) {
        return true;
      }
      return false;
    }

    /**
     * 移去填充算法
     * @param string $source
     * @return string
     */
    private function stripPKSC7Padding($source): string
    {
      $char = substr($source, -1);
      $num = ord($char);
      if ($num === 62) return $source;
      $source = substr($source, 0, -$num);
      return $source;
    }

    /**

     * 转换一个string字符串为byte数组

     * @param $str 需要转换的字符串

     * @param $bytes 目标byte数组

     */
    private static function getbytes($str)
    {
      $len = strlen($str);
      $bytes = array();
      for ($i = 0; $i < $len; $i++) {
        if (ord($str[$i]) >= 128) {
          $byte = ord($str[$i]) - 256;
        } else {
          $byte = ord($str[$i]);
        }
        $bytes[] =  $byte;
      }
      return $bytes;
    }

    /**

     * 将字节数组转化为string类型的数据

     * @param $bytes 字节数组

     * @param $str 目标字符串

     * @return 一个string类型的数据

     */

    private static function tostr($bytes)
    {
      $str = '';
      foreach ($bytes as $ch) {
        $str .= chr($ch);
      }
      return $str;
    }
  }
