<?php
/**
 * request-remove.php
 * Does the actual work of removing a TOTP/HOTP secret from the database
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

$id = ''; $otp = '';
extract($_POST, EXTR_IF_EXISTS);

// Sanity checks
if (strlen($id) == 0) {
    fail('Please choose an account to delete');
    return;
}
if (!is_numeric($id)) {
    fail('That is an invalid account');
    return;
}
$id = intval($id);

// Check the token
$result = verify_yubikey($otp);
if ($result !== true) {
    fail($result);
    return;
}

// DELETE from database
$db = db_get_instance();
try {
    $ins = $db->prepare("DELETE FROM `secrets` WHERE id = ?");
    $ins->execute(array($id));
} catch (PDOException $ex) {
    fail('Database error: '.$ex->getMessage());
}

// Success
echo json_encode(array(
    'success' => true,
    'redirect' => './?auth='.rawurlencode(generate_auth_token())
    ));
