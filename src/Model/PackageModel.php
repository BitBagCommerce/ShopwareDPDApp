<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class PackageModel implements PackageModelInterface
{
    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.package.height")
     * @Assert\GreaterThan(value="0", message="bitbag.shopware_dpd_app.validator.order_model.package.height")
     */
    private int $height;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.package.width")
     * @Assert\GreaterThan(value="0", message="bitbag.shopware_dpd_app.validator.order_model.package.height")
     */
    private int $width;

    /**
     * @Assert\NotBlank(message="bitbag.shopware_dpd_app.validator.order_model.package.depth")
     * @Assert\GreaterThan(value="0", message="bitbag.shopware_dpd_app.validator.order_model.package.height")
     */
    private int $depth;

    public function __construct(?int $height, ?int $width, ?int $depth)
    {
        $this->height = $height;
        $this->width = $width;
        $this->depth = $depth;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }
}
