<?php

//Composer classes
include 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

//Load configuration
$config = include 'config.php';

$testResult = null;

$webServerRoot = __DIR__ . DIRECTORY_SEPARATOR . 'package' . DIRECTORY_SEPARATOR . 'pages';
$webServerRouter = __DIR__ . DIRECTORY_SEPARATOR . 'router.php';
$webServerCommand = 'php -S ' . $config['server'] . ' -t "' . $webServerRoot . '" "' . $webServerRouter . '"';

echo $webServerCommand . "\n";

$webServerProcess = proc_open(
    $webServerCommand, [
    ["pipe", "r"],
    ["pipe", "w"],
    ["pipe", "w"],
], $pipesWebServer
);

echo "Webserver loading...";
while (!is_resource($webServerProcess))
{
    echo ".";
}
echo "\n";

$commands = [
    [
        'description' => 'Package testing started...',
    ],
    [
        'callback' => function () use ($config)
        {
            chdir(__DIR__);
        },
    ],
    [
        'callback' => function () use ($config)
        {
            $removes = [
                'tests',
                'codeception.yml',
            ];
            
            foreach ($removes as $remove)
            {
                PackageTemplate\removePath($remove);
            }
        },
    ],
    [
        'command' => 'php codecept.phar bootstrap',
    ],
    [
        'description' => 'Replace testing files...',
        'callback'    => function () use ($config)
        {
            PackageTemplate\copyDirectory('package/tests', 'tests');
            unlink('codeception.yml');
            copy('package/codeception.yml', 'codeception.yml');
        },
    ],
    [
        'command' => 'php codecept.phar build',
    ],
    [
        'description' => 'Testing...',
        'callback'    => function () use ($config, &$testResult)
        {
            $testCommand = 'php codecept.phar run acceptance';
            passthru($testCommand, $acceptanceResult);
            
            $testCommand = 'php codecept.phar run ' . $config['codeceptionArguments'];
            passthru($testCommand, $testResult);
            
            $testResult = intval($acceptanceResult) + intval($testResult);
            
        },
    ],
    [
        /**
         * Restore gitignore from changes by codeception
         */
        'command' => 'git checkout -f .gitignore',
    ],
];

//Executing commands and show output
PackageTemplate\executeCommands($commands);

$pstatus = proc_get_status($webServerProcess);
$pid = $pstatus['pid'];
PackageTemplate\kill($pid);

echo 'Exit code: [' . $testResult . "]\n";

exit($testResult);
