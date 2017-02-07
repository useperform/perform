<?php

namespace Perform\ContactBundle\Entity;

use Symfony\Component\HttpFoundation\Request;

class SpamReport
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
     * @var string
     */
    protected $ip;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var Message
     */
    protected $message;

    public static function createFromRequest(Request $request)
    {
        $report = new static();
        $ip = $request->getClientIp();
        $report->setIp($ip ?: 'Unknown');
        $report->setUserAgent($request->headers->get('User-Agent', 'Unknown'));

        return $report;
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
     * @param string $ip
     *
     * @return SpamReport
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $userAgent
     *
     * @return SpamReport
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return SpamReport
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return\DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param Message $message
     *
     * @return SpamReport
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
