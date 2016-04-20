<?php

namespace Admin\Base\Email;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Use swiftmailer and twig templates to send emails easily.
 **/
class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $swift;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $excludedDomains = [];

    /**
     * @var string
     */
    protected $fromAddress;

    public function __construct(\Swift_Mailer $swift, \Twig_Environment $twig, $fromAddress, LoggerInterface $logger = null)
    {
        $this->swift = $swift;
        $this->twig = $twig;
        $this->fromAddress = $fromAddress;
        $this->logger = $logger ?: new NullLogger();
    }

    public function setExcludedDomains(array $excludedDomains)
    {
        $this->excludedDomains = $excludedDomains;
    }

    /**
     * Create a new email from a twig template and send it.
     *
     * Use createMessage() and sendMessage() if you need to customise
     * the message (e.g. add an attachment) before sending.
     *
     * @param string|array $recipient
     * @param string       $subject
     * @param string       $template        The name of twig template, e.g. AdminBaseBundle:Email:test.txt.twig
     * @param array        $templateContext
     *
     * @return \Swift_Message
     */
    public function send($recipient, $subject, $template, array $templateContext = [])
    {
        return $this->sendMessage($this->createMessage($recipient, $subject, $template, $templateContext));
    }

    /**
     * @param string|array $recipient
     * @param string       $subject
     * @param string       $template        The name of twig template, e.g. AdminBaseBundle:Email:test.txt.twig
     * @param array        $templateContext
     *
     * @return \Swift_Message
     */
    public function createMessage($recipient, $subject, $template, array $templateContext = [])
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($recipient)
            ->setFrom($this->fromAddress)
            ->setBody($this->twig->render($template, $templateContext));

        $message->setTo(array_filter($message->getTo(), function($email) {
            foreach ($this->excludedDomains as $domain) {
                if (substr($email, -strlen($domain)) === $domain) {
                    return false;
                }
            }
            return true;
        }, ARRAY_FILTER_USE_KEY));

        return $message;
    }

    /**
     * @param \Swift_Message $message
     */
    public function sendMessage(\Swift_Message $message)
    {
        $result = $this->swift->send($message, $failedRecipients);

        if (!$result) {
            foreach ($failedRecipients as $recipient) {
                $this->logger->warn(sprintf('Sending email to %s failed.', $recipient));
            }
        }

        $msg = sprintf('Sent email with subject "%s" to ', $message->getSubject())
             .implode(', ', array_keys((array) $message->getTo()));
        $this->logger->info($msg);

        return $result;
    }
}
