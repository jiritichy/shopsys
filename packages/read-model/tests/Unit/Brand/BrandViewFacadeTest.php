<?php

declare(strict_types=1);

namespace Tests\ReadModelBundle\Unit\Brand;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ReadModelBundle\Brand\BrandView;
use Shopsys\ReadModelBundle\Brand\BrandViewFacade;
use Shopsys\ReadModelBundle\Brand\BrandViewFactory;

class BrandViewFacadeTest extends TestCase
{
    public function testCreateFromProductWithBrand(): void
    {
        $id = 1;
        $name = 'Brand';
        $mainUrl = 'https://webserver:8080/brand';

        $brandMock = $this->createMock(Brand::class);
        $brandMock->method('getId')->willReturn($id);
        $brandMock->method('getName')->willReturn($name);

        $productMock = $this->createMock(Product::class);
        $productMock->method('getBrand')->willReturn($brandMock);

        $friendlyUrlFacadeMock = $this->createMock(FriendlyUrlFacade::class);
        $friendlyUrlFacadeMock->method('getAbsoluteUrlByRouteNameAndEntityIdOnCurrentDomain')->willReturn($mainUrl);

        $brandViewFacade = new BrandViewFacade(new BrandViewFactory(), $friendlyUrlFacadeMock);

        self::assertEquals(
            new BrandView($id, $name, $mainUrl),
            $brandViewFacade->getForProduct($productMock)
        );
    }

    public function testCreateFromProductWithoutBrand(): void
    {
        $productMock = $this->createMock(Product::class);
        $productMock->method('getBrand')->willReturn(null);

        $friendlyUrlFacadeMock = $this->createMock(FriendlyUrlFacade::class);

        $brandViewFacade = new BrandViewFacade(new BrandViewFactory(), $friendlyUrlFacadeMock);

        self::assertEquals(
            null,
            $brandViewFacade->getForProduct($productMock)
        );
    }
}
