<?php

namespace App\DataCollector;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ZabbixDataCollector
{
    private string $sessionId = '';

    public function connect(): void
    {
        $http = HttpClient::create();
        $response = $http->request(
            'GET',
            'https://zabbix-mepi.infra.cloud.local/zabbix/api_jsonrpc.php',
            [
                'json' => [
                    "jsonrpc" => "2.0",
                    "method" => "user.login",
                    "params" => [
                        "username" => "api",
                        "password" => "Qzrtwag31*$"
                    ],
                    "id" => 1
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'verify_peer' => false
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            $data = $response->toArray(false);
            if (array_key_exists('error', $data)) {
                throw new \Exception("An error occurred while connecting to zabbix api : " . $data['error']['data'], 500);
            }

            if (!array_key_exists('result', $data)) {
                throw new \Exception("An unknown error occurred while connecting to zabbix api.", 500);
            }

            $this->sessionId = $data['result'];
        } else {
            throw new HttpException($statusCode, "An error occurred while connecting to zabbix api.");
        }
    }

    public function fetchData()
    {
        if (!$this->sessionId) {
            $this->connect();
        }

        $http = HttpClient::create();
        $response = $http->request(
            'GET',
            'https://zabbix-mepi.infra.cloud.local/zabbix/api_jsonrpc.php',
            [
                'json' => [
                    "jsonrpc" => "2.0",
                    "method" => "host.get",
                    "params" => [
                        "output" => [
                            "host"
                        ],
                        "selectInventory" => [
                            "os"
                        ],
                        "selectInterfaces" => [
                            "ip",
                            "dns"
                        ]
                    ],
                    "id" => 1,
                    "auth" => $this->sessionId
                ],
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'verify_peer' => false
            ]
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 400) {
            $data = $response->toArray(false);
            if (array_key_exists('error', $data)) {
                throw new \Exception("An error occurred while fetching data from zabbix api : " . $data['error']['data'], 500);
            }

            if (!array_key_exists('result', $data)) {
                throw new \Exception("An unknown error occurred while fetching data from zabbix api.", 500);
            }

            $hosts = [];

            foreach ($data['result'] as $result) {
                $tmp_array = [];
                $tmp_array['ip'] = $tmp_array['dns'] = null;
                $tmp_array['name'] = strtolower($result['host']);
                $tmp_array['os'] = array_key_exists('os', $result['inventory']) && $result['inventory']['os'] ? str_replace('Microsoft ', '', $result['inventory']['os']) : null;
                if (array_key_exists(0, $result['interfaces'])) {
                    $tmp_array['ip'] = $result['interfaces'][0]['ip'] ?: null;
                    $tmp_array['dns'] = $result['interfaces'][0]['dns'] ?: null;
                }
                $hosts[] = $tmp_array;
            }

            return $hosts;

        } else {
            throw new HttpException($statusCode, "An error occurred while fetching data from zabbix api.");
        }
    }
}