<?php

namespace Perform\Licensing\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Perform\Licensing\Licensing;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LicensingListener
{
    protected $logger;
    protected $key;
    protected $valid;
    protected $domains;

    public function __construct(LoggerInterface $logger, $key, $valid, array $domains)
    {
        $this->logger = $logger;
        $this->key = $key;
        $this->valid = $valid;
        $this->domains = $domains;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->valid) {
            return $this->invalid($event, sprintf(
                'INVALID PROJECT KEY: The project key "%s" is invalid. Please visit https://useperform.com to either create a new project, or to get the correct key for an existing project. Then set the "%s" parameter when you have a valid key.',
                $this->key,
                Licensing::PARAM_PROJECT_KEY
            ));
        }

        // trim root DNS dot and whitespace from the end of the host
        $host = rtrim($event->getRequest()->getHost(), ". \t\n\r\0\x0B");
        if (!in_array($host, $this->domains)) {
            $domainMsg = count($this->domains) === 0 ?
                       'There are no domains registered for this project' :
                       sprintf(count($this->domains) === 1 ?
                               'The only valid domain for this project is "%s"' :
                               'The valid domains for this project are "%s"',
                               implode('", "', $this->domains));

            return $this->invalid($event, sprintf(
                'INVALID PROJECT HOST: The project key "%s" is valid, however it is not valid for the domain "%s". %s. Please visit https://useperform.com to either create a new project for "%s", or to add it to an existing project.',
                $this->key,
                $host,
                $domainMsg,
                $host
            ));
        }
    }

    private function invalid(GetResponseEvent $event, $msg)
    {
        $this->logger->emergency($msg);
        $event->setResponse(new Response(
            file_get_contents(__DIR__.'/../Resources/views/invalid.html'),
            Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
