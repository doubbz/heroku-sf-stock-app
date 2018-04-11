<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class ModifyProductStockEvent extends Event
{
    /** @var  string */
    protected $productId;

    /** @var  int */
    protected $quantity;

    /**
     * @param string $productId
     * @param int $quantity
     */
    public function __construct(string $productId, int $quantity)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }
}