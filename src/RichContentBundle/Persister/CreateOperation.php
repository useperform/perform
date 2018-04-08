<?php

namespace Perform\RichContentBundle\Persister;

use Perform\RichContentBundle\Entity\Content;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class CreateOperation extends UpdateOperation
{
    public function __construct(array $newBlockDefinitions, array $blockOrder, $title = 'Untitled')
    {
        $content = new Content();
        $content->setTitle($title);
        parent::__construct($content, [], $newBlockDefinitions, $blockOrder);
    }
}
