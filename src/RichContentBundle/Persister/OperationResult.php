<?php

namespace Perform\RichContentBundle\Persister;

use Perform\RichContentBundle\Entity\Content;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class OperationResult implements \JsonSerializable
{
    protected $content;
    protected $newIds = [];

    public function __construct(Content $content, array $newIds = [])
    {
        $this->content = $content;
        $this->newIds = $newIds;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getNewIds()
    {
        return $this->newIds;
    }

    public function jsonSerialize()
    {
        return [
            'contentId' => $this->content->getId(),
            'newBlockIds' => $this->newIds,
        ];
    }
}
