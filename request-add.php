<?php
/**
 * request-add.php
 * Does the actual work of adding a TOTP/HOTP secret into the database
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
require('common.inc.php');
function fail($msg) {
    echo json_encode(array(
        'success' => false,
        'msg' => $msg));
    exit;
}

$name = ''; $secret = ''; $type = ''; $otp = '';
extract($_POST, EXTR_IF_EXISTS);

// Sanity checks
if (strlen($name) == 0) {
    fail('You must provide an account name!');
    return;
}
if (strlen($secret) == 0) {
    fail('Secret cannot be empty!');
    return;
}

$type = strtoupper($type);
if (!($type == 'HOTP' || $type == 'TOTP')) {
    fail('Type must be either HOTP or TOTP!');
    return;
}

// Check the token
$result = verify_yubikey($otp);
if ($result !== true) {
    fail($result);
    return;
}

// Insert into the database
$db = db_get_instance();
try {
    $ins = $db->prepare("INSERT INTO `secrets` (`name`, `type`, `secret`) ".
                        "VALUES (?, ?, ?)");
    $ins->execute(array($name, $type, $secret));
} catch (PDOException $ex) {
    fail('Database error: '.$ex->getMessage());
}

// Success
echo json_encode(array(
    'success' => true,
    'redirect' => './?auth='.rawurlencode(generate_auth_token())
    ));
