<?php
echo "disable_functions: " . ini_get('disable_functions') . "\n";
echo "proc_open exists: " . (function_exists('proc_open') ? 'yes' : 'no') . "\n";
echo "exec exists: " . (function_exists('exec') ? 'yes' : 'no') . "\n";
