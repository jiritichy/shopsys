<?php

namespace Tests\ReadModelBundle\Unit\Product\Detail;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Shopsys\ReadModelBundle\Brand\BrandView;
use Shopsys\ReadModelBundle\Image\ImageView;
use Shopsys\ReadModelBundle\Product\Action\ProductActionView;
use Shopsys\ReadModelBundle\Product\Detail\DetailProductView;
use Shopsys\ReadModelBundle\Product\Detail\DetailProductViewFactory;

class DetailProductViewFactoryTest extends TestCase
{
    private const MAIN_PAGE_DESCRIPTION = 'MainPageDescription';

    /**
     * @dataProvider getTestGetNameData
     * @param string|null $seoH1
     * @param string|null $name
     * @param string|null $expected
     */
    public function testGetName(
        ?string $seoH1,
        ?string $name,
        ?string $expected
    ): void {
        $detailProductView = $this->createDetailProductView(
            [
                'getSeoH1' => $seoH1,
                'getName' => $name,
            ],
            0,
            1,
            [],
            null,
            new ProductActionView(0, false, false, ''),
            []
        );

        self::assertSame($expected, $detailProductView->getName());
    }

    /**
     * @return array
     */
    public function getTestGetNameData(): array
    {
        return [
            [
                'seoH1' => 'seo title',
                'name' => 'title',
                'expected' => 'seo title',
            ], [
                'seoH1' => '',
                'name' => 'title',
                'expected' => 'title',
            ], [
                'seoH1' => null,
                'name' => 'title',
                'expected' => 'title',
            ], [
                'seoH1' => 'seo title',
                'name' => '',
                'expected' => 'seo title',
            ], [
                'seoH1' => 'seo title',
                'name' => null,
                'expected' => 'seo title',
            ], [
                'seoH1' => null,
                'name' => null,
                'expected' => null,
            ], [
                'seoH1' => '',
                'name' => '',
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider getTestNullableArguments
     * @param string|null $input
     * @param string|null $expected
     */
    public function testNullableArguments(
        ?string $input,
        ?string $expected
    ): void {
        $detailProductView = $this->createDetailProductView(
            [
                'getDescription' => $input,
                'getCatnum' => $input,
                'getEan' => $input,
                'getPartno' => $input,
            ],
            0,
            1,
            [],
            null,
            new ProductActionView(0, false, false, ''),
            []
        );

        self::assertSame($expected, $detailProductView->getDescription());
        self::assertSame($expected, $detailProductView->getCatnum());
        self::assertSame($expected, $detailProductView->getEan());
        self::assertSame($expected, $detailProductView->getPartno());
    }

    /**
     * @return array
     */
    public function getTestNullableArguments(): array
    {
        return [
            [
                'input' => 'some text',
                'expected' => 'some text',
            ], [
                'input' => null,
                'expected' => null,
            ],
        ];
    }

    /**
     * @dataProvider getTestGetAvailabilityAndInStock
     * @param string $availabilityString
     * @param int|null $dispatchTime
     * @param string $expectedAvailabilityString
     * @param bool $expectedInStockStatus
     */
    public function testGetAvailabilityAndInStock(
        string $availabilityString,
        ?int $dispatchTime,
        string $expectedAvailabilityString,
        bool $expectedInStockStatus
    ): void {
        $productAvailabilityMock = $this->createMock(Availability::class);
        $productAvailabilityMock->method('getName')->willReturn($availabilityString);
        $productAvailabilityMock->method('getDispatchTime')->willReturn($dispatchTime);

        $detailProductView = $this->createDetailProductView(
            [
                'getCalculatedAvailability' => $productAvailabilityMock,
            ],
            0,
            1,
            [],
            null,
            new ProductActionView(0, false, false, ''),
            []
        );

        self::assertSame($expectedAvailabilityString, $detailProductView->getAvailability());
        self::assertSame($expectedInStockStatus, $detailProductView->isInStock());
    }

    /**
     * @return array
     */
    public function getTestGetAvailabilityAndInStock(): array
    {
        return [
            [
                'availabilityString' => 'available',
                'dispatchTime' => 0,
                'expectedAvailabilityString' => 'available',
                'expectedInStockStatus' => true,
            ], [
                'availabilityString' => 'available',
                'dispatchTime' => 10,
                'expectedAvailabilityString' => 'available',
                'expectedInStockStatus' => false,
            ], [
                'availabilityString' => 'available',
                'dispatchTime' => null,
                'expectedAvailabilityString' => 'available',
                'expectedInStockStatus' => false,
            ],
        ];
    }

    /**
     * @dataProvider getTestVariants
     * @param bool $isMainVariant
     * @param bool $isVariant
     * @param \Shopsys\FrameworkBundle\Model\Product\Product|null $mainVariantMock
     * @param int|null $expectedMainVariantId
     */
    public function testVariants(
        bool $isMainVariant,
        bool $isVariant,
        ?Product $mainVariantMock,
        ?int $expectedMainVariantId
    ): void {
        $detailProductView = $this->createDetailProductView(
            [
                'isMainVariant' => $isMainVariant,
                'isVariant' => $isVariant,
                'getMainVariant' => $mainVariantMock,
            ],
            0,
            1,
            [],
            null,
            new ProductActionView(0, false, false, ''),
            []
        );

        self::assertSame($isMainVariant, $detailProductView->isMainVariant());
        self::assertSame($isVariant, $detailProductView->isVariant());
        self::assertSame($expectedMainVariantId, $detailProductView->getMainVariantId());
    }

    /**
     * @return array
     */
    public function getTestVariants(): array
    {
        $mainVariantId = 5;

        $mainVariantMock = $this->createMock(Product::class);
        $mainVariantMock->method('getId')->willReturn($mainVariantId);

        return [
            [
                'isMainVariant' => true,
                'isVariant' => false,
                'mainVariantMock' => null,
                'expectedMainVariantId' => null,
            ], [
                'isMainVariant' => false,
                'isVariant' => false,
                'mainVariantMock' => null,
                'expectedMainVariantId' => null,
            ], [
                'isMainVariant' => false,
                'isVariant' => true,
                'mainVariantMock' => $mainVariantMock,
                'expectedMainVariantId' => $mainVariantId,
            ],
        ];
    }

    /**
     * @dataProvider getTestGetSeoMetaDescription
     * @param string|null $input
     * @param string|null $expected
     */
    public function testGetSeoMetaDescription(
        ?string $input,
        ?string $expected
    ): void {
        $detailProductView = $this->createDetailProductView(
            [
                'getSeoMetaDescription' => $input,
            ],
            0,
            1,
            [],
            null,
            new ProductActionView(0, false, false, ''),
            []
        );

        self::assertSame($expected, $detailProductView->getSeoMetaDescription());
    }

    /**
     * @return array
     */
    public function getTestGetSeoMetaDescription(): array
    {
        return [
            [
                'input' => 'some text',
                'expected' => 'some text',
            ], [
                'input' => null,
                'expected' => self::MAIN_PAGE_DESCRIPTION,
            ], [
                'input' => '',
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider getTestGetMainImageView
     * @param array $imageViews
     * @param \Shopsys\ReadModelBundle\Image\ImageView|null $expectedMainImageView
     */
    public function testGetMainImageView(
        array $imageViews,
        ?ImageView $expectedMainImageView
    ): void {
        $detailProductView = $this->createDetailProductView(
            [],
            0,
            1,
            $imageViews,
            null,
            new ProductActionView(0, false, false, ''),
            []
        );

        self::assertSame($expectedMainImageView, $detailProductView->getMainImageView());
    }

    /**
     * @return array
     */
    public function getTestGetMainImageView(): array
    {
        $mainImageView = new ImageView(2, 'jpg', 'product', null);

        $imageViews = [
            $mainImageView,
            new ImageView(3, 'jpg', 'product', null),
            new ImageView(4, 'jpg', 'product', null),
            new ImageView(5, 'jpg', 'product', null),
        ];

        return [
            [
                'imageViews' => [],
                'expectedMainImageView' => null,
            ], [
                'imageViews' => $imageViews,
                'expectedMainImageView' => $mainImageView,
            ], [
                'imageViews' => [$mainImageView],
                'expectedMainImageView' => $mainImageView,
            ],
        ];
    }

    /**
     * @param array $productData
     * @param int $sellingPriceAmount
     * @param int|null $mainCategoryId
     * @param \Shopsys\ReadModelBundle\Image\ImageView[] $imageViews
     * @param \Shopsys\ReadModelBundle\Brand\BrandView|null $brandView
     * @param \Shopsys\ReadModelBundle\Product\Action\ProductActionView $productActionView
     * @param \Shopsys\ReadModelBundle\Parameter\ParameterView[] $parameterViews
     * @return \Shopsys\ReadModelBundle\Product\Detail\DetailProductView
     */
    private function createDetailProductView(
        array $productData,
        int $sellingPriceAmount,
        ?int $mainCategoryId,
        array $imageViews,
        ?BrandView $brandView,
        ProductActionView $productActionView,
        array $parameterViews
    ): DetailProductView {
        $domainMock = $this->createDomainMock();
        $seoSettingFacadeMock = $this->createSeoSettingFacadeMock();

        $detailProductViewFactory = new DetailProductViewFactory($domainMock, $seoSettingFacadeMock);

        return $detailProductViewFactory->createFromProduct(
            $this->createProductMock($productData),
            $this->createProductPrice($sellingPriceAmount),
            $mainCategoryId,
            $imageViews,
            $brandView,
            $productActionView,
            $parameterViews
        );
    }

    /**
     * @param array $productData
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    private function createProductMock(array $productData): Product
    {
        $productMock = $this->createMock(Product::class);

        $productData = array_merge($this->getDefaultProductData(), $productData);

        foreach ($productData as $methodName => $value) {
            $productMock->method($methodName)->willReturn($value);
        }

        return $productMock;
    }

    /**
     * @return array
     */
    private function getDefaultProductData(): array
    {
        $productAvailabilityMock = $this->createMock(Availability::class);
        $productAvailabilityMock->method('getName')->willReturn('available');
        $productAvailabilityMock->method('getDispatchTime')->willReturn(0);

        return [
            'getCalculatedAvailability' => $productAvailabilityMock,
            'getCalculatedSellingDenied' => false,
            'getCatnum' => '',
            'getDescription' => '',
            'getEan' => '',
            'getFlags' => [],
            'getId' => 1,
            'getMainVariant' => null,
            'getName' => '',
            'getPartno' => '',
            'getSeoH1' => '',
            'getSeoMetaDescription' => '',
            'getSeoTitle' => '',
            'isMainVariant' => false,
            'isVariant' => false,
        ];
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private function createDomainMock(): Domain
    {
        $domainMock = $this->createMock(Domain::class);
        $domainMock->method('getId')->willReturn(1);
        return $domainMock;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    private function createSeoSettingFacadeMock(): SeoSettingFacade
    {
        $seoSettingFacadeMock = $this->getMockBuilder(SeoSettingFacade::class)
            ->disableOriginalConstructor()
            ->getMock();

        $seoSettingFacadeMock->method('getDescriptionMainPage')->willReturn(self::MAIN_PAGE_DESCRIPTION);

        return $seoSettingFacadeMock;
    }

    /**
     * @param int $amount
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice
     */
    private function createProductPrice(int $amount): ProductPrice
    {
        return new ProductPrice(new Price(Money::create($amount), Money::create($amount)), false);
    }
}
