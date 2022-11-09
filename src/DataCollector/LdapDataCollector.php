<?php

namespace App\DataCollector;

use Exception;
use http\Exception\RuntimeException;

class LdapDataCollector
{
    public function fetchData(): array
    {
        $output = shell_exec('powershell.exe -executionpolicy bypass -NoProfile -command "& {"C:\Users\tbouts\Documents\scripts\GetAdServers.ps1"; exit $err}"');
        $data = json_decode(mb_convert_encoding($output, 'UTF-8'), true);
        if (is_array($data)) {
            return $data;
        }
        throw new Exception("An error occurred while trying to execute script GetAdServers.ps1 : " . $output, 500);
    }
}