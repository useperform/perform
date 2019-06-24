<?php

namespace Perform\UserBundle\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Perform\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Update the password hash when saving a user.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 */
class UserListener implements EventSubscriber
{
    protected $encoder;
    protected $locator;

    /**
     * Inject a service locator so the encoder factory is lazily
     * loaded when required.
     *
     * @param ServiceLocator $locator
     */
    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->handleEvent($args);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->handleEvent($args);
    }

    protected function handleEvent(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();
        if (!$user instanceof User) {
            return;
        }

        // set password expiry to right now if not explicitly set
        if (!$user->getPasswordExpiresAt()) {
            $user->setPasswordExpiresAt(new \DateTime());
        }

        $this->updatePassword($user);
        if ($args instanceof PreUpdateEventArgs) {
            //update the change set when doing an update
            $entityManager = $args->getEntityManager();
            $metadata = $entityManager->getClassMetadata(get_class($user));
            $unitOfWork = $entityManager->getUnitOfWork();
            $unitOfWork->recomputeSingleEntityChangeSet($metadata, $user);
        }
    }

    protected function updatePassword(User $user)
    {
        $password = $user->getPlainPassword();
        if (strlen($password) === 0) {
            return;
        }
        if (!$this->encoder) {
            $this->encoder = $this->locator->get('encoder_factory')->getEncoder($user);
        }
        $user->setPassword($this->encoder->encodePassword($password, $user->getSalt()));
        $user->eraseCredentials();
    }
}
