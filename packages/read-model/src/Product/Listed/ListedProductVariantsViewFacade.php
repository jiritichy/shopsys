<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Product\Listed;

use Shopsys\FrameworkBundle\Model\Product\ProductFacade;

class ListedProductVariantsViewFacade
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade
     */
    protected $productFacade;

    /**
     * @var \Shopsys\ReadModelBundle\Product\Listed\CreateProductViewsFromEntityHelper
     */
    protected $createProductViewsFromEntityHelper;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     * @param \Shopsys\ReadModelBundle\Product\Listed\CreateProductViewsFromEntityHelper $createProductViewsFromEntityHelper
     */
    public function __construct(
        ProductFacade $productFacade,
        CreateProductViewsFromEntityHelper $createProductViewsFromEntityHelper
    ) {
        $this->productFacade = $productFacade;
        $this->createProductViewsFromEntityHelper = $createProductViewsFromEntityHelper;
    }

    /**
     * @param int $productId
     * @return \Shopsys\ReadModelBundle\Product\Listed\ListedProductView[]
     */
    public function getAllVariants(int $productId): array
    {
        $product = $this->productFacade->getById($productId);

        if (!$product->isMainVariant()) {
            return [];
        }

        return $this->createProductViewsFromEntityHelper->createFromProducts($product->getVariants());
    }
}
