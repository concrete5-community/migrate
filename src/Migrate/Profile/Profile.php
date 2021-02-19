<?php

namespace A3020\Migrate\Profile;

final class Profile
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $token;

    /** @var string */
    protected $handle;

    /** @var array */
    protected $databases = [];

    /**
     * Create a new Profile object.
     *
     * @param string $handle
     *
     * @return Profile
     */
    public static function createByHandle($handle)
    {
        $obj = new static();

        return $obj->setHandle($handle);
    }

    /**
     * Set the profile's properties.
     *
     * @param array $data
     *
     * @return Profile
     */
    public function setProperties($data)
    {
        $properties = ['url', 'token', 'databases'];
        foreach ($data as $key => $value) {
            if (in_array($key, $properties)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'url' => $this->getUrl(),
            'token' => $this->getToken(),
            'databases' => $this->getDatabases(),
        ];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return Profile
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return Profile
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string $handle
     *
     * @return Profile
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * @return array
     */
    public function getDatabases()
    {
        return $this->databases;
    }

    /**
     * @param array $databases
     *
     * @return Profile
     */
    public function setDatabases(array $databases)
    {
        $this->databases = $databases;

        return $this;
    }

    /**
     * @param string $database
     *
     * @return $this
     */
    public function addDatabase($database)
    {
        $this->databases[] = $database;

        return $this;
    }
}
