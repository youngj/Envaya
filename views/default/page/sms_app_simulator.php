<html>
<head>
<title>EnvayaSMS Request Simulator</title>
<style type='text/css'>
body
{
    font-family:sans-serif;
}
.smsTable
{
    margin-bottom:10px;
}
.smsTable th 
{
    width:200px;
    text-align:right;
    font-size:12px;
}

.smsTable td, .smsTable th
{
    padding:2px;
}
</style>
</head>
<body>
<h2>EnvayaSMS Request Simulator</h2>
<div style='float:left;width:400px'>
<table class='smsTable'>
<tr><th>Server URL</th><td><input id='server_url' type='text' size='40' /></td></tr>
<tr><th>Phone Number</th><td><input id='phone_number' type='text' /></td></tr>
<tr><th>Password</th><td><input id='password' type='password' /></td></tr>
<tr><th>Log Messages</th><td><textarea id='log'  style='width:250px'></textarea></td></tr>
<tr><th>Action</th><td><select id='action' onchange='actionChanged()' onkeypress='actionChanged()'>
    <option value='incoming'>incoming</option>
    <option value='outgoing'>outgoing</option>
    <option value='send_status'>send_status</option>
    <option value='device_status'>device_status</option>
    <option value='test'>test</option>
</select></td></tr>
</table>

<div id='action_incoming'>
<h4>Parameters for action=incoming:</h4>
<table class='smsTable'>
<tr><th>From Phone Number</th><td><input id='from' type='text' /></td></tr>
<tr><th>Message Type</th><td><select id='message_type'>
    <option value='sms'>sms</option>
    <option value='mms'>mms</option>
</select></td></tr>
<tr><th>Message</th><td><textarea id='message' style='width:250px'></textarea></td></tr>
<tr><th>Timestamp</th><td><input id='timestamp' type='text' /></td></tr>
</table>
</div>
<div id='action_outgoing' style='display:none'>
<h4>Parameters for action=outgoing:</h4>
(None)
</div>
<div id='action_send_status'  style='display:none'>
<h4>Parameters for action=send_status:</h4>
<table class='smsTable'>
<tr><th>Server ID</th><td><input id='id' type='text' /></td></tr>
<tr><th>Status</th><td><select id='status'>
    <option value='sent'>sent</option>
    <option value='failed'>failed</option>
    <option value='queued'>queued</option>    
</select></td></tr>
<tr><th>Error Message</th><td><input id='error' type='text' size='50' /></td></tr>
</table>
</div>
<div id='action_test'  style='display:none'>
<h4>Parameters for action=test:</h4>
(None)
</div>
<div id='action_device_status'  style='display:none'>
<h4>Parameters for action=device_status:</h4>
<table class='smsTable'>
<tr><th>Status</th><td><select id='device_status'>
    <option value='power_connected'>power_connected</option>
    <option value='power_disconnected'>power_disconnected</option>
    <option value='battery_low'>battery_low</option>    
    <option value='battery_okay'>battery_okay</option>    
</select></td></tr>
</table>
</div>


<script type='text/javascript'>

function $(id) { return document.getElementById(id); }

function actionChanged()
{      
    setTimeout(function() {
        var action = $('action').value;
    
        var options = $('action').options;
        for (var i = 0; i < options.length; i++)
        {
            var option = options[i].value;
            $('action_' + option).style.display = (action == option) ? 'block' : 'none';
        }
    }, 1);
}

function performAction() {
    
    var server_url = $('server_url').value;
    var password = $('password').value;
    var action = $('action').value;
    
    var params = {
        version: '13',
        phone_number: $('phone_number').value,
        action: action,
        log: $('log').value
    };  
    
    if (action == 'incoming')
    {
        params.message_type = $('message_type').value;
        params.message = $('message').value;
        params.from = $('from').value;
        params.timestamp = $('timestamp').value;
    }
    else if (action == 'send_status')
    {
        params.id = $('id').value;
        params.status = $('status').value;
        params.error = $('error').value;
    }
    else if (action == 'device_status')
    {
        params.status = $('device_status').value;
    }
            
    var xhr = (window.ActiveXObject && !window.XMLHttpRequest) ? new ActiveXObject("Msxml2.XMLHTTP") : new XMLHttpRequest();

    xhr.onreadystatechange = function()
    {
        if (xhr.readyState == 4)
        {            
            $('response').appendChild(document.createTextNode("HTTP " + xhr.status + " " + xhr.statusText + "\n" +
                xhr.getAllResponseHeaders() + "\n" + xhr.responseText));
        }
    };  
    
    var keyArr = [];
    var paramArr = [];
    for (var name in params)
    {
        keyArr.push(name);
        paramArr.push(name + '=' + encodeURIComponent(params[name]));
    }
    var paramStr = paramArr.join('&');
    
    keyArr.sort();    
    var signatureInput = server_url;
    for (var i = 0; i < keyArr.length; i++)
    {
        signatureInput += "," + keyArr[i] + "=" + params[keyArr[i]];
    }
    signatureInput += "," + password;
            
    var signature = Crypto.util.bytesToBase64(Crypto.SHA1(
        Crypto.charenc.UTF8.stringToBytes(signatureInput), { asBytes: true }));

    xhr.open("POST", server_url, true);
    
    var requestHeaders = {
        "Content-Type": "application/x-www-form-urlencoded",
        "Content-Length": paramStr.length,
        "X-Request-Signature": signature
    }
    
    var request = $('request');
    request.innerHTML = "POST " + server_url + "\n";
    
    for (var name in requestHeaders)
    {
        xhr.setRequestHeader(name, requestHeaders[name]);        
        request.appendChild(document.createTextNode(name + ": " + requestHeaders[name] + "\n"));
    }
    
    request.appendChild(document.createTextNode("\n" + paramStr));    
    
    $('response').innerHTML = "";
    
    xhr.send(paramStr);    
}

$('server_url').value = location.href.replace("test.html","");
$('timestamp').value = new Date().getTime();

</script>

<script type='text/javascript'>

/*
 * Crypto-JS v2.3.0
 * http://code.google.com/p/crypto-js/
 * Copyright (c) 2011, Jeff Mott. All rights reserved.
 * http://code.google.com/p/crypto-js/wiki/License
 */
if (typeof Crypto == "undefined" || ! Crypto.util)
{
(function(){

var base64map = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";

// Global Crypto object
var Crypto = window.Crypto = {};

// Crypto utilities
var util = Crypto.util = {

	// Bit-wise rotate left
	rotl: function (n, b) {
		return (n << b) | (n >>> (32 - b));
	},

	// Bit-wise rotate right
	rotr: function (n, b) {
		return (n << (32 - b)) | (n >>> b);
	},

	// Swap big-endian to little-endian and vice versa
	endian: function (n) {

		// If number given, swap endian
		if (n.constructor == Number) {
			return util.rotl(n,  8) & 0x00FF00FF |
			       util.rotl(n, 24) & 0xFF00FF00;
		}

		// Else, assume array and swap all items
		for (var i = 0; i < n.length; i++)
			n[i] = util.endian(n[i]);
		return n;

	},

	// Generate an array of any length of random bytes
	randomBytes: function (n) {
		for (var bytes = []; n > 0; n--)
			bytes.push(Math.floor(Math.random() * 256));
		return bytes;
	},

	// Convert a byte array to big-endian 32-bit words
	bytesToWords: function (bytes) {
		for (var words = [], i = 0, b = 0; i < bytes.length; i++, b += 8)
			words[b >>> 5] |= bytes[i] << (24 - b % 32);
		return words;
	},

	// Convert big-endian 32-bit words to a byte array
	wordsToBytes: function (words) {
		for (var bytes = [], b = 0; b < words.length * 32; b += 8)
			bytes.push((words[b >>> 5] >>> (24 - b % 32)) & 0xFF);
		return bytes;
	},

	// Convert a byte array to a hex string
	bytesToHex: function (bytes) {
		for (var hex = [], i = 0; i < bytes.length; i++) {
			hex.push((bytes[i] >>> 4).toString(16));
			hex.push((bytes[i] & 0xF).toString(16));
		}
		return hex.join("");
	},

	// Convert a hex string to a byte array
	hexToBytes: function (hex) {
		for (var bytes = [], c = 0; c < hex.length; c += 2)
			bytes.push(parseInt(hex.substr(c, 2), 16));
		return bytes;
	},

	// Convert a byte array to a base-64 string
	bytesToBase64: function (bytes) {

		// Use browser-native function if it exists
		if (typeof btoa == "function") return btoa(Binary.bytesToString(bytes));

		for(var base64 = [], i = 0; i < bytes.length; i += 3) {
			var triplet = (bytes[i] << 16) | (bytes[i + 1] << 8) | bytes[i + 2];
			for (var j = 0; j < 4; j++) {
				if (i * 8 + j * 6 <= bytes.length * 8)
					base64.push(base64map.charAt((triplet >>> 6 * (3 - j)) & 0x3F));
				else base64.push("=");
			}
		}

		return base64.join("");

	},

	// Convert a base-64 string to a byte array
	base64ToBytes: function (base64) {

		// Use browser-native function if it exists
		if (typeof atob == "function") return Binary.stringToBytes(atob(base64));

		// Remove non-base-64 characters
		base64 = base64.replace(/[^A-Z0-9+\/]/ig, "");

		for (var bytes = [], i = 0, imod4 = 0; i < base64.length; imod4 = ++i % 4) {
			if (imod4 == 0) continue;
			bytes.push(((base64map.indexOf(base64.charAt(i - 1)) & (Math.pow(2, -2 * imod4 + 8) - 1)) << (imod4 * 2)) |
			           (base64map.indexOf(base64.charAt(i)) >>> (6 - imod4 * 2)));
		}

		return bytes;

	}

};

// Crypto character encodings
var charenc = Crypto.charenc = {};

// UTF-8 encoding
var UTF8 = charenc.UTF8 = {

	// Convert a string to a byte array
	stringToBytes: function (str) {
		return Binary.stringToBytes(unescape(encodeURIComponent(str)));
	},

	// Convert a byte array to a string
	bytesToString: function (bytes) {
		return decodeURIComponent(escape(Binary.bytesToString(bytes)));
	}

};

// Binary encoding
var Binary = charenc.Binary = {

	// Convert a string to a byte array
	stringToBytes: function (str) {
		for (var bytes = [], i = 0; i < str.length; i++)
			bytes.push(str.charCodeAt(i) & 0xFF);
		return bytes;
	},

	// Convert a byte array to a string
	bytesToString: function (bytes) {
		for (var str = [], i = 0; i < bytes.length; i++)
			str.push(String.fromCharCode(bytes[i]));
		return str.join("");
	}

};

})();
}


/*
 * Crypto-JS v2.3.0
 * http://code.google.com/p/crypto-js/
 * Copyright (c) 2011, Jeff Mott. All rights reserved.
 * http://code.google.com/p/crypto-js/wiki/License
 */
(function(){

// Shortcuts
var C = Crypto,
    util = C.util,
    charenc = C.charenc,
    UTF8 = charenc.UTF8,
    Binary = charenc.Binary;

// Public API
var SHA1 = C.SHA1 = function (message, options) {
	var digestbytes = util.wordsToBytes(SHA1._sha1(message));
	return options && options.asBytes ? digestbytes :
	       options && options.asString ? Binary.bytesToString(digestbytes) :
	       util.bytesToHex(digestbytes);
};

// The core
SHA1._sha1 = function (message) {

	// Convert to byte array
	if (message.constructor == String) message = UTF8.stringToBytes(message);
	/* else, assume byte array already */

	var m  = util.bytesToWords(message),
	    l  = message.length * 8,
	    w  =  [],
	    H0 =  1732584193,
	    H1 = -271733879,
	    H2 = -1732584194,
	    H3 =  271733878,
	    H4 = -1009589776;

	// Padding
	m[l >> 5] |= 0x80 << (24 - l % 32);
	m[((l + 64 >>> 9) << 4) + 15] = l;

	for (var i = 0; i < m.length; i += 16) {

		var a = H0,
		    b = H1,
		    c = H2,
		    d = H3,
		    e = H4;

		for (var j = 0; j < 80; j++) {

			if (j < 16) w[j] = m[i + j];
			else {
				var n = w[j-3] ^ w[j-8] ^ w[j-14] ^ w[j-16];
				w[j] = (n << 1) | (n >>> 31);
			}

			var t = ((H0 << 5) | (H0 >>> 27)) + H4 + (w[j] >>> 0) + (
			         j < 20 ? (H1 & H2 | ~H1 & H3) + 1518500249 :
			         j < 40 ? (H1 ^ H2 ^ H3) + 1859775393 :
			         j < 60 ? (H1 & H2 | H1 & H3 | H2 & H3) - 1894007588 :
			                  (H1 ^ H2 ^ H3) - 899497514);

			H4 =  H3;
			H3 =  H2;
			H2 = (H1 << 30) | (H1 >>> 2);
			H1 =  H0;
			H0 =  t;

		}

		H0 += a;
		H1 += b;
		H2 += c;
		H3 += d;
		H4 += e;

	}

	return [H0, H1, H2, H3, H4];

};

// Package private blocksize
SHA1._blocksize = 16;

SHA1._digestsize = 20;

})();

</script>

<br />
<input type='button' value='Perform Action' onclick='performAction()' />
</div>
<div style='float:left;width:500px;padding:2px;'>
<pre id="request" style='background-color:#eef;white-space:pre-wrap;word-wrap:break-word'></pre>
<pre id="response" style='background-color:#efe;white-space:pre-wrap;word-wrap:break-word'></pre>
</div>
</body>
</html>