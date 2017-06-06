<?php

namespace Perform\MailingListBundle;

/**
 * Common names to use when managing subscriber attributes across connectors.
 *
 * Each connector should use these names on Subscriber queue entities
 * to ensure operability.
 *
 * For example, the attribute 'first_name' may map to 'forename' on
 * one provider and 'FIRSTNAME' on another.
 *
 * Both connectors can cooperate if they refer to 'first_name' on the
 * Subscriber queue.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
final class SubscriberFields
{
    const FIRST_NAME = 'first_name';
}
