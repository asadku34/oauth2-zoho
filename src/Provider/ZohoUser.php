<?php

namespace Asad\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class ZohoUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->response['ZUID'];
    }

    public function getZUID()
    {
        return $this->response['ZUID'];
    }

    /**
     * Get preferred display name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->response['Display_Name'];
    }

    /**
     * Get preferred first name.
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->getResponseValue('First_Name');
    }

    /**
     * Get preferred last name.
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->getResponseValue('Last_Name');
    }

    /**
     * Get email address.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getResponseValue('Email');
    }

    /**
     * Get user data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }

    private function getResponseValue($key)
    {
        if (array_key_exists($key, $this->response)) {
            return $this->response[$key];
        }
        return null;
    }
}
