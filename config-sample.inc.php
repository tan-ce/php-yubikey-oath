<?php
/**
 * config.inc.php
 * Configuration settings for the application
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

// Yubikey API ID
define('YK_API_ID', 1);
// Yubikey API key
define('YK_API_KEY', '');
// Yubikey validation server
define('YK_VAL_URL', 'http://api.yubico.com/wsapi/2.0/verify');
// Yubikey public ID
define('YK_PUB_ID', 'your yk id');
// Secret for generating intermediate tokens
define('SECRET', 'place a long random string here');

// Database settings
define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_NAME', '');
define('DB_PASS', '');
