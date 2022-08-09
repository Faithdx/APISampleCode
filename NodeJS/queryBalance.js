var request = require('request').defaults({rejectUnauthorized: false});
var data = {
    apikey: "97d568ee656a429ba2f6b60e5ca55694"
}
request({
    url:'https://localhost:7087/SmsApi/v1/QueryBalance',
    method: "POST",
    json: true,
    form: data
}, function(error, response, body) {
    if (!error && response.statusCode == 200) {
        console.log(body);
    }
}); 