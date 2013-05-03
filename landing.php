<?php
require_once('common.inc.php');
/**
 * landing.php
 * Alternate landing page for the application. Allows OTP to be copied before it is used.
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

$otp = '';
if (isset($_GET['otp'])) $otp = $_GET['otp'];
?><!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <title>Yubikey OATH</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script src="jquery-1.9.1.min.js"></script>
    <script type="text/javascript"><!--
        var menu_open = false;  

        function toggle_menu(e) {
            e.preventDefault();
            if (menu_open) {
                menu_open = false;
                $('#menu').slideUp('fast');
            } else {
                menu_open = true;
                $('#menu').slideDown('fast');
            }
        }
        
        $(document).ready(function() {
            $('.menu').click(toggle_menu);

        });

    // --></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head><body><div id="topmost">
<h1>
    <a class="menu" href="#"></a>
    Yubikey OATH
</h1>
<div id="menu">
    <a class="entry" href="add.php">Add Account</a>
    <a class="entry" href="remove.php?auth=<?=rawurlencode($auth_token)?>">Remove Account</a>
</div>
<form id="login" method="get" action="index.php" style="display:block">
    <div id="login-msg"></div>
    <?=text_input('otp', 'Yubikey OTP', $otp)?><br />
    <input type="submit" value="Generate OATH Codes" class="button" />
</form>
</div></body></html>

