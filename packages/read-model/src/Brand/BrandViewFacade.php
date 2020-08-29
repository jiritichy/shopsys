<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Brand;

use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;

class BrandViewFacade
{
    /**
     * @var \Shopsys\ReadModelBundle\Brand\BrandViewFactory
     */
    protected $brandViewFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\ReadModelBundle\Brand\BrandViewFactory $brandViewFactory
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade $friendlyUrlFacade
     */
    public function __construct(
        BrandViewFactory $brandViewFactory,
        FriendlyUrlFacade $friendlyUrlFacade
    ) {
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->brandViewFactory = $brandViewFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\ReadModelBundle\Brand\BrandView|null
     */
    public function getForProduct(Product $product): ?BrandView
    {
        $brand = $product->getBrand();

        if ($brand === null) {
            return null;
        }

        return $this->brandViewFactory->createFromBrand(
            $brand,
            $this->friendlyUrlFacade->getAbsoluteUrlByRouteNameAndEntityIdOnCurrentDomain('front_brand_detail', $brand->getId())
        );
    }
}
