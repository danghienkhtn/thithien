#!/bin/bash
php_bin_path=/usr/bin/php
php_ini_path=/etc/php.ini
document_root=/usr/local/src/www/Portal/server/Crontab

$php_bin_path -c $php_ini_path $document_root/account-info.php
