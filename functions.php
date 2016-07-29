<?php

namespace PackageTemplate
{
    
    function page($page)
    {
        return "http://127.0.0.1:4444/" . $page . '.php';
    }
    
    function getRoot()
    {
        return __DIR__ . DIRECTORY_SEPARATOR;
    }
    
    function kill($pid)
    {
        return stripos(php_uname('s'), 'win') > -1
            ? exec("taskkill /F /T /PID $pid")
            : exec("kill -9 $pid");
    }
    
    function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir)))
        {
            if (($file != '.') && ($file != '..'))
            {
                if (is_dir($src . '/' . $file))
                {
                    copyDirectory($src . '/' . $file, $dst . '/' . $file);
                }
                else
                {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    
    function downloadFile($link, $file = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_REFERER, $link);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($curl);
        curl_close($curl);
        
        file_put_contents(
            is_null($file)
                ? basename($link)
                : $file, $content, LOCK_EX
        );
    }
    
    function executeCommands($commands)
    {
        $comandsCount = count($commands);
        
        for ($i = 0; $i < $comandsCount; $i++)
        {
            if ($i == 0)
            {
                echo "\n\n";
            }
            if (!empty($commands[$i]['description']))
            {
                echo $commands[$i]['description'] . "\n\n";
            }
            
            if (!empty($commands[$i]['command']))
            {
                echo passthru($commands[$i]['command']) . "\n\n";
            }
            
            if (!empty($commands[$i]['callback']))
            {
                call_user_func(($commands[$i]['callback']));
            }
            if ($i == $comandsCount - 1)
            {
                echo "\n\n";
            }
        }
    }
    
    function removePath($path, $callback = null)
    {
        if (file_exists($path))
        {
            
            if (is_file($path))
            {
                if (is_null($callback) || (is_callable($callback) && $callback($path) === true))
                {
                    @unlink($path);
                }
            }
            else
            {
                
                $iterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new \RecursiveIteratorIterator(
                    $iterator, \RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $file)
                {
                    if ($file->isDir())
                    {
                        if (is_null($callback) || (is_callable($callback) && $callback($file->getRealPath()) === true))
                        {
                            @rmdir($file->getRealPath());
                        }
                    }
                    else
                    {
                        if (is_null($callback) || (is_callable($callback) && $callback($file->getRealPath()) === true))
                        {
                            @unlink($file->getRealPath());
                        }
                    }
                }
                if (is_null($callback) || (is_callable($callback) && $callback($path) === true))
                {
                    @rmdir($path);
                }
            }
            
        }
    }
    
}
