<?php

namespace Perform\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Admin\AdminRequest;
use Exporter\Source\DoctrineORMQuerySourceIterator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Exporter\Exporter;
use Exporter\Writer\CsvWriter;
use Exporter\Writer\JsonWriter;
use Exporter\Writer\XlsWriter;
use Perform\BaseBundle\Config\ExportConfig;

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

        $adminRequest = new AdminRequest($request, TypeConfig::CONTEXT_EXPORT);
        // entity name is only set on routes created by CrudLoader. Set it manually here
        if (!$request->query->has('entity')) {
            throw new \InvalidArgumentException(sprintf('%s requires the entity name.', __METHOD__));
        }
        $adminRequest->setEntity($request->query->get('entity'));

        $entity = $this->get('perform_base.doctrine.entity_resolver')->resolve($adminRequest->getEntity());
        $query = $this->get('perform_base.selector.entity')->getQueryBuilder($adminRequest, $entity)->getQuery();
        $config = $this->get('perform_base.config_store')->getExportConfig($entity);

        // writers should be configured by the export config
        $exporter = new Exporter([
            'csv' => new CsvWriter('php://output'),
            'json' => new JsonWriter('php://output'),
            'xls' => new XlsWriter('php://output'),
        ]);

        $format = $request->query->get('format');
        $filename = $config->getFilename($format);
        $source = new DoctrineORMQuerySourceIterator($query, $config->getFields());

        return $exporter->getResponse($format, $filename, $source);
    }
}
