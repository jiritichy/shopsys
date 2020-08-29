<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Parameter;

use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCachedAttributesFacade;

class ParameterViewFacade
{
    /**
     * @var \Shopsys\ReadModelBundle\Parameter\ParameterViewFactory
     */
    protected $parameterViewFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductCachedAttributesFacade
     */
    protected $productCachedAttributesFacade;

    /**
     * @param \Shopsys\ReadModelBundle\Parameter\ParameterViewFactory $parameterViewFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCachedAttributesFacade $productCachedAttributesFacade
     */
    public function __construct(
        ParameterViewFactory $parameterViewFactory,
        ProductCachedAttributesFacade $productCachedAttributesFacade
    ) {
        $this->parameterViewFactory = $parameterViewFactory;
        $this->productCachedAttributesFacade = $productCachedAttributesFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\ReadModelBundle\Parameter\ParameterView[]
     */
    public function getAllForProduct(Product $product): array
    {
        $productParameterValues = $this->productCachedAttributesFacade->getProductParameterValues($product);

        $parameterViews = [];

        foreach ($productParameterValues as $productParameterValue) {
            $parameterViews[] = $this->parameterViewFactory->createFromProductParameterValue($productParameterValue);
        }

        return $parameterViews;
    }
}
