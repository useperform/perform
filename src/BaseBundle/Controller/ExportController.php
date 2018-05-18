<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Exporter\TypedDoctrineORMQuerySourceIterator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Exporter\Exporter;

/**
 * Export entities to a variety of formats.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class ExportController extends Controller
{
    /**
     * @Route("", name="perform_base_export_stream")
     */
    public function streamAction(Request $request)
    {
        // $this->denyAccessUnlessGranted('EXPORT')

        $crudRequest = CrudRequest::fromRequest($request, TypeConfig::CONTEXT_EXPORT);
        // entity name is only set on routes created by CrudLoader. Set it manually here
        if (!$request->query->has('entity')) {
            throw new \InvalidArgumentException(sprintf('%s requires the entity name.', __METHOD__));
        }
        $entity = $this->get('perform_base.doctrine.entity_resolver')->resolve($request->query->get('entity'));
        $crudRequest->setEntityClass($entity);
        $format = $request->query->get('format');

        return $this->get('perform_base.exporter')->getResponse($crudRequest, $format);
    }
}
