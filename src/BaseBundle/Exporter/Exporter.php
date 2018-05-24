<?php

namespace Perform\BaseBundle\Exporter;

use Perform\BaseBundle\Config\ConfigStoreInterface;
use Perform\BaseBundle\Selector\EntitySelector;
use Perform\BaseBundle\Type\TypeRegistry;
use Exporter\Exporter as BaseExporter;
use Perform\BaseBundle\Crud\CrudRequest;
use Perform\BaseBundle\Config\TypeConfig;
use Perform\BaseBundle\Config\ExportConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class Exporter
{
    protected $configStore;
    protected $selector;
    protected $typeRegistry;
    protected $writerFactory;

    public function __construct(ConfigStoreInterface $configStore, EntitySelector $selector, TypeRegistry $typeRegistry, WriterFactoryInterface $writerFactory)
    {
        $this->configStore = $configStore;
        $this->selector = $selector;
        $this->typeRegistry = $typeRegistry;
        $this->writerFactory = $writerFactory;
    }

    /**
     * @return StreamedResponse
     */
    public function getResponse(CrudRequest $crudRequest, $format)
    {
        $crudName = $crudRequest->getCrudName();

        $crudRequest->setDefaultFilter($this->configStore->getFilterConfig($crudName)->getDefault());
        $defaultSort = $this->configStore->getTypeConfig($crudName)->getDefaultSort();
        $crudRequest->setDefaultSortField($defaultSort[0]);
        $crudRequest->setDefaultSortDirection($defaultSort[1]);

        $entityClass = $this->configStore->getEntityClass($crudName);
        $exportConfig = $this->configStore->getExportConfig($crudName);
        $exporter = new BaseExporter($this->getWritersFromConfig($exportConfig));

        $query = $this->selector->getQueryBuilder($crudRequest, $entityClass)->getQuery();
        $exportFields = $this->configStore->getTypeConfig($crudName)->getTypes(CrudRequest::CONTEXT_EXPORT);
        $source = new TypedDoctrineORMQuerySourceIterator($this->typeRegistry, $query, $exportFields);

        return $exporter->getResponse($format, $exportConfig->getFilename($format), $source);
    }

    protected function getWritersFromConfig(ExportConfig $exportConfig)
    {
        $writers = [];
        foreach ($exportConfig->getFormatOptions() as $format => $options) {
            $writers[$format] = $this->writerFactory->create($format, $options);
        }

        return $writers;
    }
}
