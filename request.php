<?php 
/**
 * request.php
 * Retrieves token codes based on the secrets stored in the database
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

extract($_POST);

require_once('common.inc.php');

// Did the user bring a token?
if (!isset($auth)) {
    echo json_encode(array('auth' => 0));
    return;
}

// Check the auth token
if (verify_auth_token($auth) !== true) {
    echo json_encode(array('auth' => 0));
    return;
}

// $_POST['hotp_id'] may or may not be set.
// No error checking necessary

// Calculate the counter value to use
$now = time();
$ctr = floor($now / 30);

$codes = array();

$totp_ctr = floor($now / 30); 
$db = db_get_instance();
$b32 = new Base32();
foreach($db->query('SELECT * FROM secrets') as $s) {
    $code = array(
            'id' => $s['id'],
            'type' => $s['type'],
            'name' => $s['name']
        );
    try {
        if ($s['type'] == 'TOTP') {
            $code['code'] = hotp($b32->toString($s['secret']), $totp_ctr, 6);
        } else if ($s['type'] == 'HOTP') {
            // Only generate a token if asked for it
            if (isset($hotp_id) && $hotp_id == $s['id']) {
                $code['code'] = hotp($b32->toString($s['secret']), $s['counter'], 6);
                // Increment the counter
                $query = $db->prepare('UPDATE secrets SET counter = counter + 1 WHERE id = ?');
                $query->execute(array($s['id']));
            } else {
                $code['code'] = '_ _ _ _ _ _';
            }
        }
    } catch (Exception $e) {
        $code['code'] = '(Error generating code)';
    }

    $codes[] = $code;
}

$ret = array('auth' => 1);
$ret['countdown'] = 30;
$ret['codes'] = $codes;

echo json_encode($ret);
