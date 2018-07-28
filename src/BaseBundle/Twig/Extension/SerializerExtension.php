<?php

namespace Perform\BaseBundle\Twig\Extension;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class SerializerExtension extends \Twig_Extension
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('perform_serialize', [$this, 'serialize']),
        ];
    }

    public function serialize($object, $format = 'json')
    {
        return $this->serializer->serialize($object, $format);
    }
}
