<?php
//Turn on implicit flush
ob_implicit_flush(true);

error_reporting(E_ALL);
ini_set('display_errors', 1);

//Change shell directory to current
chdir(__DIR__);

include "functions.php";

return [
    'server'               => "127.0.0.1:4444",
    'packageName'          => "package",
    'codeceptionArguments' => '--skip functional --skip acceptance --coverage-xml --coverage-html --coverage-text --coverage',
];