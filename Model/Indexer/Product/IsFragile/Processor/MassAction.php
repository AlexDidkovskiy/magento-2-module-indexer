<?php
namespace EchoEcho\Indexer\Model\Indexer\Product\IsFragile\Processor;

use EchoEcho\Indexer\Model\Indexer\Product\IsFragile\AbstractProcessor;

class MassAction extends AbstractProcessor
{
    /**
     * @param array $productIds
     * @param $attrData
     */
    public function process(array $productIds, $attrData)
    {
        $this->reindexList($productIds);
    }
}