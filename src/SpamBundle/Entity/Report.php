<?php

namespace Perform\SpamBundle\Entity;

use Symfony\Component\HttpFoundation\Request;

class Report
{
    /**
     * @var uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string|null
     */
    protected $ip;

    /**
     * @var string|null
     */
    protected $userAgent;

    /**
     * Set the ip and user agent from a request, if available.
     *
     * @param Request|null $request
     */
    public function addRequestDetails(Request $request = null)
    {
        if (!$request) {
            return;
        }

        $ip = $request->getClientIp();
        if ($ip) {
            $this->ip = $ip;
        }

        $userAgent = $request->headers->get('User-Agent');
        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }
    }

    /**
     * @return uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $type
     *
     * @return SpamType
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return\DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string|null $ip
     *
     * @return Report
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string|null $userAgent
     *
     * @return Report
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }
}
