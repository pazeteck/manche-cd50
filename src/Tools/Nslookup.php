<?php

namespace App\Tools;

class Nslookup
{
    public static function findHost(string $host)
    {
        $output = shell_exec('nslookup -timeout=2 -retry=1 -srchlist="cg50.fr/reseau.cg50.fr/cloud.local/ressources.cloud.local/colleges.cloud.local/id.cloud.local/infra.cloud.local/linux.infra.cloud.local" ' . $host);
        if (preg_match('/.*Nom\s:\s{4}(.*)\nAddress(?:es)?:\s{2}(.*)/s', $output, $matches)) {
            $ips = explode(',', preg_replace(['/\n/','/\t\s{2}/'], ['',','], $matches[2]));
            $dns = strtolower($matches[1]);
            $ip = $ips[0];
            $additional_ip = array_slice($ips, 1) ?: null;
            return [
                'dns' => $dns,
                'ip' => $ip,
                'additional_ip' => $additional_ip
            ];
        }
    }
}