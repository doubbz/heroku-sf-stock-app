<?php

namespace AppBundle;

/**
 * Define the event for this application.
 */
final class Events
{
    /**
     * Sent whenever a Add stock command is given.
     * @var string
     */
    const ADD_PRODUCT_STOCK = 'app.product_stock.add';

    /**
     * Sent whenever a Remove stock command is given.
     * @var string
     */
    const REMOVE_PRODUCT_STOCK = 'app.product_stock.remove';
}
