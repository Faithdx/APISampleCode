<%@  language="VBSCRIPT" codepage="65001" %>
<%
    'Response.CodePage = 65001
    'Response.Charset = "utf-8"
    'Session.CodePage = 65001
%>

<%
    '加密方法
    Function Encrypt(plaintext, secretkey)
    
        Dim AesKey(32)
        Dim IVKey(16) 
        Dim bKeys(48)
        bKeys = Base64Decode(secretkey)
        
        Array.Copy bKeys, AesKey, 32
        Array.Copy bKeys, 32, IVKey, 0, 16
    '
    '    
    '    'esKeyBytes = Slice(KeyBytes,0,31)
    '    'aesIVBytes = Slice(KeyBytes,32,47)
    '
        aes.Key = AesKey
        aes.IV = aesIVBytes
        aes.Padding = 2 'PKCS7
    '    
        Set aesEnc = aes.CreateEncryptor_2()
    '
    '    'Set aesEnc = aes.CreateEncryptor_2((aesKeyBytes),aes.IV)
    '    
        plainBytes = utf8.GetBytes_4(plaintext)
        cipherBytes = aesEnc.TransformFinalBlock((plainBytes), 0, LenB(plainBytes))
        Encrypt = "密文："& B64Encode(cipherBytes) &"<br/>IV: "& B64Encode(aes.IV)
    End Function
        

    Function Slice(arr,starting,ending)
        Dim out_array
        
        If Right(TypeName(arr), 2) = "()" Then
            out_array = Array()

            ReDim Preserve out_array(ending - starting)

            For index = starting To ending                
                out_array(index - starting) = arr(index)
            Next
        Else
            Exit Function
        End If
        Slice = out_array
    End Function

    'byte转base64
    Function B64Encode(bytes)
        blockSize = b64Enc.InputBlockSize
        For offset = 0 To LenB(bytes) - 1 Step blockSize
            length = Min(blockSize, LenB(bytes) - offset)
            b64Block = b64Enc.TransformFinalBlock((bytes), offset, length)
            result = result & utf8.GetString((b64Block))
        Next
        B64Encode = result
    End Function

    'base64转byte
    Function B64Decode(b64Str)
        bytes = utf8.GetBytes_4(b64Str)
        B64Decode = b64Dec.TransformFinalBlock((bytes), 0, LenB(bytes))
    End Function

    '取两个数字中最小的
    Function Min(a, b)
        Min = a
        If b < a Then Min = b
    End Function


    
    Function Base64Decode(b64)

        Dim OutStr() , i , j 
        Const B64_CHAR_DICT = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/="

        If InStr(1, b64, "=") <> 0 Then b64 = Left(b64, InStr(1, b64, "=") - 1)'判断Base64真实长度,除去补位

        Dim length , mods 
        mods = Len(b64) Mod 4
        length = Len(b64) - mods
        ReDim OutStr(length / 4 * 3 - 1 + Switch(mods = 0, 0, mods = 2, 1, mods = 3, 2))
        For i = 1 To length Step 4
            Dim buf(3) 

            For j = 0 To 3
                buf(j) = InStr(1, B64_CHAR_DICT, Mid(b64, i + j, 1)) - 1'根据字符的位置取得索引值
            Next        
            OutStr((i - 1) / 4 * 3) = buf(0) * &H4 + (buf(1) And &H30) / &H10
            OutStr((i - 1) / 4 * 3 + 1) = (buf(1) And &HF) * &H10 + (buf(2) And &H3C) / &H4
            OutStr((i - 1) / 4 * 3 + 2) = (buf(2) And &H3) * &H40 + buf(3)
        Next

        If mods = 2 Then
            OutStr(length / 4 * 3) = (InStr(1, B64_CHAR_DICT, Mid(b64, length + 1, 1)) - 1) * &H4 + ((InStr(1, B64_CHAR_DICT, Mid(b64, length + 2, 1)) - 1) And &H30) / 16
        ElseIf mods = 3 Then
            OutStr(length / 4 * 3) = (InStr(1, B64_CHAR_DICT, Mid(b64, length + 1, 1)) - 1) * &H4 + ((InStr(1, B64_CHAR_DICT, Mid(b64, length + 2, 1)) - 1) And &H30) / 16
            OutStr(length / 4 * 3 + 1) = ((InStr(1, B64_CHAR_DICT, Mid(b64, length + 2, 1)) - 1) And &HF) * &H10 + ((InStr(1, B64_CHAR_DICT, Mid(b64, length + 3, 1)) - 1) And &H3C) / &H4
        End If

        Base64Decode = OutStr'读取解码结果     
    End Function
    
%>


<%
    Set utf8 = CreateObject("System.Text.UTF8Encoding")
    Set b64Enc = CreateObject("System.Security.Cryptography.ToBase64Transform")
    Set b64Dec = CreateObject("System.Security.Cryptography.FromBase64Transform")
    Set aes = CreateObject("System.Security.Cryptography.RijndaelManaged")
    Set mem = CreateObject("System.IO.MemoryStream")

    Dim plaintext, aesKey, macKey,encrypt_res
    secretkey = "9Rwkkl8hTiech/KSEinXPfwWiDYQfeJA1KubmDgi34ay4dutIfm0oUj7oqxQlmnA"
   
    
    'For i = LBound(a) To UBound(a)
    '    a(i) = x
    '    Response.Write a(i)
    'Next

    'b = Slice(a,0,31)


    plaintext="anidf fsf kdf f "  '加密原文
    encrypt_res=Encrypt(plaintext, secretkey)  '加密
    'decrypt_res=Decrypt(encrypt_res, aesKey, macKey)  '解密

    'response.write("明文："&plaintext)
    response.write("<br/>")
    response.write(encrypt_res)
    response.write("<br/>")
    'response.Write(decrypt_res)

%>


<!-- 
    apikey = "8ee3dc19bb124e7ea7eb0433c4f90ffc"
    secretkey = "9Rwkkl8hTiech/KSEinXPfwWiDYQfeJA1KubmDgi34ay4dutIfm0oUj7oqxQlmnA"

    response.contenttype = "text/html;charset=utf-8"
    method = "POST"

    url_get_balance = "https://localhost:7087/SmsApi/v1/QueryBalance"
    data_get_balance = "apikey=" & apikey
        
    response.write GetBody(url_get_balance, data_get_balance)

    Function GetBody(url, data)
        Set https = Server.CreateObject("MSXML2.ServerXMLHTTP")
        With https
            .Open method, url, False
                .setRequestHeader "Content-Type", "application/x-www-form-urlencoded"
                    .Send data

        GetBody = .ResponseBody
        End With
        GetBody = bytetostr(https.ResponseBody, "utf-8")
        Set https = Nothing

    End Function
           
    Function bytetostr(vin,cset)
    dim bs,sr
    set bs = server.createObject("adodb.stream")
        bs.type = 2
        bs.open
        bs.writetext vin
        bs.position = 0
        bs.charset = cset
        bs.position = 2
        sr = bs.readtext
        bs.close
    set bs = nothing
    bytetostr = sr
    end Function 
    -->