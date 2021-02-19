<?php

namespace A3020\Migrate\Client;

use A3020\Migrate\Profile\Profile;
use Exception;

final class Client
{
    /** @var Profile */
    protected $profile;

    protected $numberOfRequests = 0;

    /**
     * @param array $postData
     * @param array $options
     *
     * @return string
     *
     * @throws Exception
     */
    public function post($postData, array $options = [])
    {
        $this->numberOfRequests++;

        $options = $options + [
            'verify' => false,
            'timeout' => 10,
        ];

        $postData['token'] = $this->getProfile()->getToken();

        $ch = curl_init($this->getEndpoint());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $options['verify']);
        curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        if ($statusCode !== 200) {
            $result = json_decode($response, true);

            throw new Exception('Code: ' . $statusCode . '. Message: '. $result['error']);
        }

        return $response;
    }

    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;
    }

    private function getProfile()
    {
        return $this->profile;
    }

    /**
     * The URL we try to connect to
     *
     * @return string
     */
    private function getEndpoint()
    {
        $url = $this->getProfile()->getUrl();

        if (strpos($url, '/ccm/system/migrate') === false) {
            return rtrim($url, '/') . '/ccm/system/migrate';
        }

        return $url;
    }

    /**
     * @return int
     */
    public function getNumberOfRequests()
    {
        return $this->numberOfRequests;
    }
}
