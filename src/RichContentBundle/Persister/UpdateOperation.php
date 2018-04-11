<?php

namespace Perform\RichContentBundle\Persister;

use Perform\RichContentBundle\Entity\Content;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class UpdateOperation implements OperationInterface
{
    protected $content;
    protected $blockDefinitions = [];
    protected $newBlockDefinitions = [];
    protected $blockOrder = [];

    public function __construct(Content $content, array $blockDefinitions, array $newBlockDefinitions, array $blockOrder)
    {
        $this->content = $content;
        $this->blockDefinitions = $blockDefinitions;
        $this->newBlockDefinitions = $newBlockDefinitions;
        $this->blockOrder = $blockOrder;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getBlockDefinitions()
    {
        return $this->blockDefinitions;
    }

    public function getNewBlockDefinitions()
    {
        return $this->newBlockDefinitions;
    }

    public function getBlockOrder()
    {
        return $this->blockOrder;
    }
}
