Installation Instructions
=========================
1. Rename config-sample.inc.php to config.inc.php
2. Change the settings in config.inc.php
3. Create database with the schema provided in schema.sql
4. Done!

Requirements
============
MySQL, PHP PEAR, PHP curl

Using with NFC (Yubikey NEOs)
=============================
If the URL for these scripts is http://yourserver.com/oath/,
then set the NDEF data in the Yubikey NEO to:
http://yourserver.com/oath/index.php?otp=

This will allow you to tap your Yubikey NEO to your phone
and directly bring you to your TOTP/HOTP codes.

Acknowledgements
================
* Yubico.php
Yubico AB
http://www.yubico.com

* base32.php
Shannon Wynter
http://fremnet.net

* CSS/HTML Android Holo theme
Vezquex
http://vezquex.com/projects/holo-css/

* PHP Google Authenticator
(Used as reference for my own functions)
devicenull
https://github.com/devicenull/PHP-Google-Authenticator
