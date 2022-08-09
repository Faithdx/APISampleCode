var  crypto = require( 'crypto' );
var request = require('request').defaults({rejectUnauthorized: false});
var algorithm = 'aes-256-cbc';
const base64 = 'he8MTjKMVGSwcKMEqhZgyjy7PAZESXQENVv/eqjaZlXGG8cW2x2WW4J7vLdwuQj7=';
const buff = Buffer.from(base64, 'base64');
var key = buff.slice(0, 32);
var iv = buff.slice(-16);
var data = '{"type":0,"apikind":1,"countrycode":82,"reservedtype":0,"reservedtime":"","mobile":"15603214235","content":"The is a test","contacts":"01022222222,01033333333"}';
function encrypt(data) {
    var  cipher = crypto.createCipheriv(algorithm, key, iv);
    var  cipherChunks = [];
    cipherChunks.push(cipher.update(data, 'utf8', 'base64'));
    cipherChunks.push(cipher.final('base64'));
    return cipherChunks.join(''); 
}
var dataList = {
    header: '97d568ee656a429ba2f6b60e5ca55694',
    data: encrypt(data)
}
request({
    url:'https://localhost:7087/SmsApi/v1/SendSms',
    method: "POST",
    json: true,
    form: dataList
}, function(error, response, body) {
    if (!error && response.statusCode == 200) {
        console.log(body);
    }
}); 