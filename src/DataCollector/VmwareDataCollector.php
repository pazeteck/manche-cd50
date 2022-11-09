<?php

namespace App\DataCollector;

use Exception;

class VmwareDataCollector
{
    public function fetchData(): array
    {
        $output = shell_exec('powershell.exe -executionpolicy bypass -NoProfile -command "& {"C:\Users\tbouts\Documents\scripts\GetVms.ps1"; exit $err}"');
        $data = json_decode(mb_convert_encoding($output, 'UTF-8'), true);
        if (is_array($data)) {
            foreach ($data as &$d) {
                if (preg_match('/(.*)\..*/U', $d['name'], $matches)) {
                    $d['dns'] = !$d['dns'] ?: $d['name'];
                    $d['name'] = $matches[1];
                }
                $d['source_ip'] = $d['ip'] ? 'vmware' : null;
                $d['source_dns'] = $d['dns'] ? 'vmware' : null;
                $d['source_os'] = $d['os'] ? 'vmware' : null;
                if ($d['dns'] === $d['name']) {
                    $d['dns'] = null;
                    $d['source_dns'] = null;
                }
            }
            return $data;
        }
        throw new Exception("An error occurred while trying to execute script GetVms.ps1 : " . $output, 500);
    }
}