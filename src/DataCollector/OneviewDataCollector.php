<?php

namespace App\DataCollector;

use Exception;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OneviewDataCollector
{
    private string $sessionId = '';

    public function fetchVmHosts(): array
    {
        $output = shell_exec('powershell.exe -executionpolicy bypass -NoProfile -command "& {"C:\Users\tbouts\Documents\scripts\GetVsphere.ps1"; exit $err}"');
        $data = json_decode(mb_convert_encoding($output, 'UTF-8'), true);
        if (is_array($data)) {
            return $data;
        }
        throw new Exception("An error occurred while trying to execute script GetVsphere.ps1 : " . $output, 500);
    }

    public function connect(): void
    {
        $http = HttpClient::create();
        $response = $http->request(
            'POST',
            'https://oneview.infra.cloud.local/rest/login-sessions',
            [
                'json' => [
                    'userName' => 'admin',
                    'password' => '5IqMSevX'
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Api-Version' => 2200
                ],
                'verify_peer' => false
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            $data = $response->toArray(false);
            if (array_key_exists('errorCode', $data)) {
                throw new \Exception("An error occurred while connecting to oneview api : " . $data['details'], 500);
            }

            if (!array_key_exists('sessionID', $data)) {
                throw new \Exception("An unknown error occurred while connecting to oneview api.", 500);
            }

            $this->sessionId = $data['sessionID'];
        } else {
            throw new HttpException($statusCode, "An error occurred while connecting to oneview api.");
        }
    }

    public function fetchData()
    {
        if (!$this->sessionId) {
            $this->connect();
        }

        $vmhosts = $this->fetchVmHosts();

        $http = HttpClient::create();
        $response = $http->request(
            'GET',
            'https://oneview.infra.cloud.local/rest/server-hardware?start=0&count=500',
            [
                'json' => [],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Api-Version' => 2200,
                    'Auth' => $this->sessionId
                ],
                'verify_peer' => false
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            $data = $response->toArray(false);
            if (array_key_exists('errorCode', $data)) {
                throw new \Exception("An error occurred while fetching data from oneview api : " . $data['details'], 500);
            }

            if (!array_key_exists('members', $data)) {
                throw new \Exception("An unknown error occurred while fetching data from oneview api.", 500);
            }

            $hosts = [];

            foreach ($data['members'] as $member) {
                $tmp_array = [];
                $tmp_array['ip'] = $tmp_array['additional_ip'] = $tmp_array['os'] = $tmp_array['dns'] = $tmp_array['tools'] = $tmp_array['description'] = $tmp_array['source_ip'] = $tmp_array['source_os'] = $tmp_array['source_dns'] = null;
                $tmp_array['name'] = strtolower($member['serverName']);
                if (preg_match('/(.*)\..*/U', $tmp_array['name'], $matches)) {
                    $tmp_array['name'] = $matches[1];
                    $tmp_array['dns'] = $member['serverName'];
                    $tmp_array['source_dns'] = 'oneview';
                }
                if (preg_match('/esx|simpli/', $tmp_array['name'])) {
                    if ($key = array_search($tmp_array['name'], array_column($vmhosts, 'name')) or $key = array_search($tmp_array['dns'], array_column($vmhosts, 'name'))) {
                        $tmp_array['os'] = 'VSphere ' . $vmhosts[$key]['version'] . '-' . $vmhosts[$key]['build'];
                        $tmp_array['ip'] = $vmhosts[$key]['ip'];
                        $tmp_array['source_os'] = 'vpshere';
                        $tmp_array['source_ip'] = 'vpshere';
                    }
                }
                $tmp_array['memory'] = $member['memoryMb'];
                $tmp_array['cores'] = $member['processorCoreCount'];
                $tmp_array['cpu'] = $member['processorCount'];
                $tmp_array['powerstate'] = $member['powerState'] === 'On' ? 1 : 0;
                $tmp_array['model'] = $member['model'];
                $tmp_array['uuid'] = $member['uuid'];
                $hosts[] = $tmp_array;
            }

            return $hosts;

        } else {
            throw new HttpException($statusCode, "An error occurred while fetching data from oneview api.");
        }
    }
}