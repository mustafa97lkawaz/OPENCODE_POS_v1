<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPcache cleared OK - now try test print';
} else {
    echo 'OPcache not active - Apache should load new file automatically';
}
