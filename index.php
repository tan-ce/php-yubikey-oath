<?php
require_once('common.inc.php');
/**
 * index.php
 * Main landing page for the application
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
?><!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
    <title>Yubikey OATH</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script src="jquery-1.9.1.min.js"></script>
    <script type="text/javascript"><!--
        var menu_open = false;  
        var requesting = 0;
        var countdown = 0;
        var token = <?php 
            // See if we came from somewhere where we're already authorized
            if (isset($_GET['auth'])) {
                $result = verify_auth_token($_GET['auth'], 15);
            } else {
                // Otherwise check if the Yubikey OTP checks out
                if (!isset($_GET['otp'])) $result = 'Please login using your Yubikey';
                else $result = verify_yubikey($_GET['otp']);
            }

            if ($result === true) {
                // Generate token by signing the current timestamp
                $auth_token = generate_auth_token();
                echo json_encode(array(
                        'authok' => 1,
                        'auth'  => $auth_token
                    ));
            } else {
                $auth_token = '';
                echo json_encode(array('authok' => 0, 'msg' => $result));
            }
        ?>;

        function make_hotp_click_event(hotp_id) {
            var id = hotp_id;
            return function() {
                request_codes(id);
            };
        }

        var intervalID = false;
        function request_codes(hotp_id) {
            token.hotp_id = hotp_id || -1;

            requesting = 1;
            if (intervalID !== false) clearInterval(intervalID);
            $.post('request.php', token, function (ret) {
                $('#topmost > .entry').remove();
                if (ret.auth != 1) {
                    token.msg = "Login expired. Please re-login.";
                    show_login();
                    return;
                } 
                for (var i = 0; i < ret.codes.length; i++) {
                    var code = ret.codes[i];
                    var elem = $('<div class="entry"></div>')
                            .append($('<div class="entry-name"></div>')
                                .text(code.name))
                            .append($('<div class="entry-code"></div>')
                                .text(code.code))

                    if (code.type == 'HOTP') {
                        elem.click(make_hotp_click_event(code.id));
                        elem.css('cursor', 'pointer');
                    }

                    $('#topmost').append(elem);
                }
                countdown = ret.countdown;
                requesting = 0;
                $('#preamble').text(countdown + ' more second(s)');
                intervalID = setInterval(timer_tick, 1000);
            }, 'json');
        }

        function timer_tick() {
            if (requesting) return;
            var preamble = $('#preamble');
            if (countdown <= 1) {
                preamble.text('Requesting new codes...');
                request_codes();
            } else {
                countdown--;
                preamble.text(countdown + ' more second(s)');
            }
        }

        function show_login() {
            $('#preamble').remove();
            $('#login-msg').text(token.msg);
            $('#login').show();
        }

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

            if (token.authok == 1) {
                request_codes();
            } else {
                show_login();
            }

            $('input[type=text]').keypress(function(e) {
                if (e.which == 13) {
                    $('form').submit();
                }
            });

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
<div id="preamble">Please wait...</div>
<form id="login" method="get" action="index.php">
    <div id="login-msg"></div>
    <?=password_input('otp', 'Yubikey OTP')?><br />
</form>
</div></body></html>

