<?php

namespace Perform\RichContentBundle\Persister;

/**
 * Represents a request to save rich content, usually coming from an
 * editor api request.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
interface OperationInterface
{
    /**
     * @return \Perform\RichContentBundle\Entity\Content The content entity to be saved
     */
    public function getContent();

    /**
     * @return array An array of all existing block definitions on the
     *               content entity, with possibly updated values. Any existing
     *               blocks not included in these definitions will be removed.
     */
    public function getBlockDefinitions();

    /**
     * @return array an array of block definitions that don't exist in
     *               the database yet, to be added to this content
     */
    public function getNewBlockDefinitions();

    /**
     * @return array A list of ids detailing the order of blocks for
     *               this content. A block may be used more than once.
     *
     * New blocks that have not been saved to the database yet should
     * use 'stub' ids beginning with an underscore,
     * e.g. _983983498234.
     */
    public function getBlockOrder();
}
