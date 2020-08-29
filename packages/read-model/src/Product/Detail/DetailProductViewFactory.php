<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Product\Detail;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Utils\Utils;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Shopsys\ReadModelBundle\Brand\BrandView;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;

class DetailProductViewFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    protected $seoSettingFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade $seoSettingFacade
     */
    public function __construct(
        Domain $domain,
        SeoSettingFacade $seoSettingFacade
    ) {
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice $sellingPrice
     * @param int $mainCategoryId
     * @param \Shopsys\ReadModelBundle\Image\ImageView[] $galleryImageViews
     * @param \Shopsys\ReadModelBundle\Brand\BrandView|null $brandView
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $productActionView
     * @param \Shopsys\ReadModelBundle\Parameter\ParameterView[] $parameterViews
     * @return \Shopsys\ReadModelBundle\Product\Detail\DetailProductView
     */
    public function createFromProduct(
        Product $product,
        ProductPrice $sellingPrice,
        int $mainCategoryId,
        array $galleryImageViews,
        ?BrandView $brandView,
        ProductActionView $productActionView,
        array $parameterViews
    ): DetailProductView {
        $domainId = $this->domain->getId();
        $locale = $this->domain->getLocale();

        return new DetailProductView(
            $product->getId(),
            $product->getSeoH1($domainId) ?: $product->getName($locale),
            $product->getDescription($domainId),
            $product->getCalculatedAvailability()->getName(),
            $sellingPrice,
            $product->getCatnum(),
            $product->getPartno(),
            $product->getEan(),
            $mainCategoryId,
            $product->getCalculatedSellingDenied(),
            $this->isProductInStock($product),
            $product->isMainVariant(),
            $product->isVariant() ? $product->getMainVariant()->getId() : null,
            $this->getFlagIdsForProduct($product),
            $product->getSeoTitle($domainId) ?: $product->getName($locale),
            $this->getSeoMetaDescription($product),
            $productActionView,
            $brandView,
            $this->getMainImageView($galleryImageViews),
            $galleryImageViews,
            $parameterViews
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return int[]
     */
    protected function getFlagIdsForProduct(Product $product): array
    {
        $flagIds = [];
        foreach ($product->getFlags() as $flag) {
            $flagIds[] = $flag->getId();
        }

        return $flagIds;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string
     */
    protected function getSeoMetaDescription(Product $product): string
    {
        $seoMetaDescription = $product->getSeoMetaDescription($this->domain->getId());

        if ($seoMetaDescription === null) {
            $seoMetaDescription = $this->seoSettingFacade->getDescriptionMainPage($this->domain->getId());
        }

        return $seoMetaDescription;
    }

    /**
     * @param array $imageViews
     * @return \Shopsys\ReadModelBundle\Image\ImageView|null
     */
    protected function getMainImageView(array $imageViews): ?ImageView
    {
        return Utils::getArrayValue($imageViews, 0, null);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return bool
     */
    protected function isProductInStock(Product $product): bool
    {
        return $product->getCalculatedAvailability()->getDispatchTime() === 0;
    }
}
