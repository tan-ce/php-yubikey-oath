<?php
/**
 * common.inc.php
 * Common functions
 *
 * @author Tan Chee Eng
 * @version 1.0
 * @copyright Copyright &copy; 2013 Tan Chee Eng
 * @link http://blog.tan-ce.com/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */
require_once('base32.php');
require_once('Yubico.php');

if (!@include('config.inc.php')) {
    echo 'Please configure the application before continuing';
    exit;
}

function hotp($key, $ctr, $digits) {
    $ctr_ary = Array();
    while($ctr != 0) {
        $ctr_ary[] = chr($ctr & 0xFF);
        $ctr >>= 8;
    }
    $ctr = str_pad(join(array_reverse($ctr_ary)), 8, "\000", STR_PAD_LEFT);

    $raw = unpack('C*', hash_hmac('sha1', $ctr, $key, true));
    $offset = ($raw[20] & 0xf) + 1;

    $P = ($raw[$offset] & 0x7f) << 24;
    $P |= ($raw[$offset + 1] & 0xff) << 16;
    $P |= ($raw[$offset + 2] & 0xff) << 8;
    $P |= ($raw[$offset + 3] & 0xff);

    $code = str_repeat('0', $digits).strval($P);
    return substr($code, -$digits);
}

function totp($key, $digits) {
    $ctr = floor(time() / 30);
    return hotp($key, $ctr, $digits);
}

function otp_qr_url($name, $secret, $type = 'totp') {
    $b32 = new Base32();
    $totp_url = "otpauth://$type/$name?secret=".$b32->fromString($secret);
    return 'https://chart.googleapis.com/chart?chs=200x200&chld=H|0&cht=qr&chl='.
            rawurlencode($totp_url);
}
        
function strToHex($string)
{
    $hex='';
    for ($i=0; $i < strlen($string); $i++)
    {
        $hex .= dechex(ord($string[$i]));
    }
    return $hex;
}

function qr($name, $secret) {
    $b32 = new Base32();
    $secret = $b32->toString($secret);
    echo '<img src="'.otp_qr_url($name, $secret).'">';
    for ($i = 0; $i < 8; $i++) echo '<br>';
}

function verify_yubikey($otp) {
    $yk_pub_id = YK_PUB_ID;
    if (substr($otp, 0, strlen($yk_pub_id)) != $yk_pub_id) {
        return 'Authentication failed: Yubikey not recognized';
    }

    $yubi = new Auth_Yubico(YK_API_ID, YK_API_KEY, 0);
    $yubi->addURLPart(YK_VAL_URL);
    $auth = $yubi->verify($otp);
    if (PEAR::isError($auth)) {
        return 'Authentication failed: '.$auth->getMessage();
    }
    
    return true;
}

function generate_auth_token() {
    $ts = strval(time());
    $sig = base64_encode(hash_hmac('sha512', $ts, SECRET, true));
    return json_encode(array('ts' => $ts, 'sig' => $sig));
}

function verify_auth_token($auth_token, $timeout = 100) {
    // Can the token be parsed?
    $t = json_decode($auth_token);
    if (is_null($t) || !isset($t->ts) || !isset($t->sig)) {
        return 'Bad auth token';
    }

    // Is the token valid?
    $expected_hash = base64_encode(hash_hmac('sha512', strval($t->ts), SECRET, true));
    if ($expected_hash !== $t->sig) {
        return "Invalid Token";
    }

    // Is the token too old?
    $now = time();
    if ($now - intval($t->ts) > $timeout) {
        return "Expired Token";
    }

    return true;
}

function text_input($name, $desc = '', $default = '') {
    return '<span class="holo"><input type="text" name="'.$name.
            '" value="'.$default.'" placeholder="'.$desc.'" /></span>';
}

function password_input($name, $desc, $default = '') {
    return '<span class="holo"><input type="password" name="'.$name.
            '" value="'.$default.'" placeholder="'.$desc.'" /></span>';
}

function radio_input($name, $options, $default = 0) {
    if (!is_array($options)) {
        echo 'radio_input: $options must be an array';
        exit;
    }

    foreach($options as $value=>$desc) {
        $id = 'holo-radio-' . $name . '-' . $value;
        echo '<div class="holo-radio">'.
             '<input type="radio" id="'.$id.'" name="'.$name.'" value="'.$value.'" />'.
             '<label for="'.$id.'">'.$desc.'</label>'.
             '</div>';
    }
}

function db_get_instance() {
    $db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}
