<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Config\FieldConfig;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Exporter\TypedDoctrineORMQuerySourceIterator;
use Symfony\Component\Routing\Annotation\Route;
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

        // entity name is only set on routes created by CrudLoader. Set it manually here
        $request->attributes->set('_crud', $request->query->get('crud'));
        try {
            $crudRequest = CrudRequest::fromRequest($request, CrudRequest::CONTEXT_EXPORT);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(sprintf('%s requires the entity name.', __METHOD__), 1, $e);
        }

        $format = $request->query->get('format');

        return $this->get('perform_base.exporter')->getResponse($crudRequest, $format);
    }
}
