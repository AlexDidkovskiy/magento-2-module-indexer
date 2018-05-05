<?php
namespace EchoEcho\Indexer\Plugin\Magento\Catalog\Model\Product;

use Magento\Catalog\Model\Product\Action as Subject;
use Closure;
use EchoEcho\Indexer\Model\Indexer\Product\IsFragile\Processor\MassAction as MassActionProcessor;

class Action
{
    /**
     * @var MassActionProcessor
     */
    protected $massActionProcessor;

    /**
     * Action constructor.
     * @param MassActionProcessor $massActionProcessor
     */
    public function __construct(MassActionProcessor $massActionProcessor)
    {
        $this->massActionProcessor = $massActionProcessor;
    }

    /**
     * Plugin for updating index after admin mass action to update attributes
     * This mass action does not call save on product itself, but saves attributes
     * @param Subject $subject
     * @param Closure $closure
     * @param array $productIds
     * @param array $attrData
     * @param $storeId
     * @return mixed
     */
    public function aroundUpdateAttributes(
        Subject $subject,
        Closure $closure,
        array $productIds,
        array $attrData,
        $storeId
    ) {
        $result = $closure($productIds, $attrData, $storeId);
        $this->massActionProcessor->process(array_unique($productIds), $attrData);

        return $result;
    }
}