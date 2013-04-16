<?php 
/**
 * add.php
 * Adds a new TOTP or HOTP secret into the database
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

require('common.inc.php'); ?><!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <title>Yubikey OATH - Add account</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script src="jquery-1.9.1.min.js"></script>
    <script type="text/javascript"><!--
        var submitting = false;

        $(document).ready(function() {
            $(document).ajaxError(function() {
                submitting = false;
                alert('A network error occured. Please try again.');
            });

            $('form').submit(function(e) { e.preventDefault(); });

            $('input[name=otp]').keypress(function(e) {
                if (submitting) return;
                if (e.which == 13) {
                    submitting = true;
                    $.post('request-add.php', $('form').serialize(),
                        function(ret) {
                            if (ret.success) window.location.href = ret.redirect;
                            else {
                                submitting = false;
                                alert(ret.msg);
                            }
                        }, 'json');
                }
            }).click(function() {
                $(this).select();
            });
        });
    // --></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head><body><div id="topmost">
<h1>
    <a class="back" href="./"></a>
    Yubikey OATH - Add account
</h1>
<form method="post" action="request-add.php">
    <div id="msg"></div>
    <?=text_input('type', 'Type: TOTP/HOTP', 'TOTP')?><br />
    <?=text_input('name', 'Account Name')?><br />
    <?=text_input('secret', 'Secret (Base32)')?><br />
    <?=password_input('otp', 'Yubikey OTP')?><br />
</form>
</div></body></html>
