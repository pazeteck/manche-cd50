<?php

namespace App\DataCollector;

use App\Tools\Nslookup;

class ManagerDataCollector
{
    public function fetchLdapData(): array
    {
        $ldap = new LdapDataCollector();
        return $ldap->fetchData();
    }

    public function fetchZabbixData(): array
    {
        $zabbix = new ZabbixDataCollector();
        return $zabbix->fetchData();
    }

    public function fetchOneviewData(): array
    {
        $oneview = new OneviewDataCollector();
        return $oneview->fetchData();
    }

    public function fetchVmwareData()
    {
        $vmware = new VmwareDataCollector();
        return $vmware->fetchData();
    }

    public function mergeHostsFromVmwareAndOneview(): array
    {
        return array_merge($this->fetchOneviewData(), $this->fetchVmwareData());
    }

    public function mergeHostsWithDataFromLdap(array $hosts): array
    {
        $ldap = $this->fetchLdapData();
        foreach ($hosts as &$host) {
            if (
                $key = array_search($host['name'], array_column($ldap, 'name')) or
                ($host['ip'] && $key = array_search($host['ip'], array_column($ldap, 'ip'))) or
                ($host['dns'] && $key = array_search($host['dns'], array_column($ldap, 'dns')))
            ) {
                $host['ip'] = $ldap[$key]['ip'];
                $host['source_ip'] = 'ldap';
                $host['dns'] = $ldap[$key]['dns'];
                $host['source_dns'] = 'ldap';
                $host['os'] = str_replace('?', '', $ldap[$key]['os']);
                $host['source_os'] = 'ldap';
            }
        }

        return $hosts;
    }

    public function mergeHostsWithDataFromZabbix(array $hosts): array
    {
        $zabbix = $this->fetchZabbixData();
        foreach ($hosts as &$host) {
            if (
                $key = array_search($host['name'], array_column($zabbix, 'name')) or
                ($host['ip'] && $key = array_search($host['ip'], array_column($zabbix, 'ip'))) or
                ($host['dns'] && $key = array_search($host['dns'], array_column($zabbix, 'dns'))) or
                ($host['dns'] && $key = array_search($host['dns'], array_column($zabbix, 'name')))
            ) {
                if ((!$host['ip'] || $host['source_ip'] === 'vmware') && $zabbix[$key]['ip']) {
                    $host['ip'] = $zabbix[$key]['ip'];
                    $host['source_ip'] = 'zabbix';
                }
                if ((!$host['os'] || $host['source_os'] === 'vmware') && $zabbix[$key]['os']) {
                    $host['os'] = $zabbix[$key]['os'];
                    $host['source_os'] = 'zabbix';
                }
                if ((!$host['dns'] || $host['source_dns'] === 'vmware' || $host['source_dns'] === 'oneview') && $zabbix[$key]['dns']) {
                    $host['dns'] = $zabbix[$key]['dns'];
                    $host['source_dns'] = 'zabbix';
                }
                $host['is_monitored'] = true;
            } else {
                $host['is_monitored'] = false;
            }
        }

        return $hosts;
    }

    public function findHostIp(array $hosts): array
    {
        foreach ($hosts as &$host) {
            if (!$host['ip'] || !$host['dns'] || $host['source_dns'] === 'vmware') {
                if ($nslookup = Nslookup::findHost($host['name'])) {
                    $host['dns'] = $nslookup['dns'];
                    if (!$host['ip']) {
                        $host['ip'] = $nslookup['ip'];
                        $host['source_ip'] = 'nslookup';
                    }
                    $host['additional_ip'] = $nslookup['additional_ip'];
                    $host['source_dns'] = 'nslookup';
                }
            }
        }

        return $hosts;
    }

    public function get(): array
    {
        $hosts = $this->mergeHostsFromVmwareAndOneview();
        $hosts = $this->mergeHostsWithDataFromLdap($hosts);
        $hosts = $this->mergeHostsWithDataFromZabbix($hosts);
        return $this->findHostIp($hosts);
    }
}