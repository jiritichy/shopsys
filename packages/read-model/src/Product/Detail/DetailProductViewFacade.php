<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Product\Detail;

use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCachedAttributesFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface;
use Shopsys\ReadModelBundle\Brand\BrandViewFacade;
use Shopsys\ReadModelBundle\Image\ImageViewFacade;
use Shopsys\ReadModelBundle\Parameter\ParameterViewFacade;
use Shopsys\ReadModelBundle\Product\Action\ProductActionViewFacade;

class DetailProductViewFacade
{
    /**
     * @var \Shopsys\ReadModelBundle\Image\ImageViewFacade
     */
    protected $imageViewFacade;

    /**
     * @var \Shopsys\ReadModelBundle\Product\Detail\DetailProductViewFactory
     */
    protected $detailProductViewFactory;

    /**
     * @var \Shopsys\ReadModelBundle\Product\Action\ProductActionViewFacade
     */
    protected $productActionViewFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface
     */
    protected $productOnCurrentDomainFacade;

    /**
     * @var \Shopsys\ReadModelBundle\Brand\BrandViewFacade
     */
    protected $brandViewFacade;

    /**
     * @var \Shopsys\ReadModelBundle\Parameter\ParameterViewFacade
     */
    protected $parameterViewFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductCachedAttributesFacade
     */
    protected $productCachedAttributesFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade
     */
    protected $categoryFacade;

    /**
     * @param \Shopsys\ReadModelBundle\Product\Detail\DetailProductViewFactory $detailProductViewFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface $productOnCurrentDomainFacade
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCachedAttributesFacade $productCachedAttributesFacade
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade
     * @param \Shopsys\ReadModelBundle\Image\ImageViewFacade $imageViewFacade
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionViewFacade $productActionViewFacade
     * @param \Shopsys\ReadModelBundle\Brand\BrandViewFacade $brandViewFacade
     * @param \Shopsys\ReadModelBundle\Parameter\ParameterViewFacade $parameterViewFacade
     */
    public function __construct(
        DetailProductViewFactory $detailProductViewFactory,
        ProductOnCurrentDomainFacadeInterface $productOnCurrentDomainFacade,
        ProductCachedAttributesFacade $productCachedAttributesFacade,
        CategoryFacade $categoryFacade,
        ImageViewFacade $imageViewFacade,
        ProductActionViewFacade $productActionViewFacade,
        BrandViewFacade $brandViewFacade,
        ParameterViewFacade $parameterViewFacade
    ) {
        $this->detailProductViewFactory = $detailProductViewFactory;
        $this->productOnCurrentDomainFacade = $productOnCurrentDomainFacade;
        $this->productCachedAttributesFacade = $productCachedAttributesFacade;
        $this->categoryFacade = $categoryFacade;
        $this->imageViewFacade = $imageViewFacade;
        $this->productActionViewFacade = $productActionViewFacade;
        $this->brandViewFacade = $brandViewFacade;
        $this->parameterViewFacade = $parameterViewFacade;
    }

    /**
     * @param int $productId
     * @return \Shopsys\ReadModelBundle\Product\Detail\DetailProductView
     */
    public function getVisibleProductDetail(int $productId): DetailProductView
    {
        $product = $this->productOnCurrentDomainFacade->getVisibleProductById($productId);

        return $this->createFromProduct($product);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\ReadModelBundle\Product\Detail\DetailProductView
     */
    protected function createFromProduct(Product $product): DetailProductView
    {
        $imageViews = $this->imageViewFacade->getAllImagesByEntityId(Product::class, $product->getId());
        $productActionView = $this->productActionViewFacade->getForProduct($product);
        $brandView = $this->brandViewFacade->getForProduct($product);
        $parameterViews = $this->parameterViewFacade->getAllForProduct($product);

        return $this->detailProductViewFactory->createFromProduct(
            $product,
            $this->productCachedAttributesFacade->getProductSellingPrice($product),
            $this->categoryFacade->getProductMainCategoryOnCurrentDomain($product)->getId(),
            $imageViews,
            $brandView,
            $productActionView,
            $parameterViews
        );
    }
}
