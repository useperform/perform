<?php

namespace Admin\NotificationBundle;

/**
 * Immutable notification object, sent with Notifier.
 */
class Notification
{
    protected $recipients;
    protected $type;
    protected $context;

    /**
     * @param RecipientInterface[] $recipients An array of recipients
     * @param string               $type       Notification type
     * @param array                $context    Any variables required for the notification type
     */
    public function __construct($recipients, $type, array $context = [])
    {
        $this->recipients = is_array($recipients) ? $recipients : [$recipients];
        foreach ($this->recipients as $recipient) {
            if (!$recipient instanceof RecipientInterface) {
                throw new \InvalidArgumentException(sprintf('%s must be supplied an array of objects implementing Admin\NotificationBundle\RecipientInterface, %s given.', __CLASS__, is_object($recipient) ? get_class($recipient) : var_export($recipient, true)));
            }
        }

        $this->type = $type;
        $this->context = $context;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getContext()
    {
        return $this->context;
    }
}
