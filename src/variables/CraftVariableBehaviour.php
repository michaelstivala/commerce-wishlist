<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace michaelstivala\commercewishlist\variables;

use Craft;
use yii\base\Behavior;
use michaelstivala\commercewishlist\CommerceWishlist;

/**
 * Class CraftVariableBehavior
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class CraftVariableBehavior extends Behavior
{
    /**
     * @var Plugin
     */
    public $wishlist;

    public function init()
    {
        parent::init();

        // Point `craft.commerce` to the craft\commerce\Plugin instance
        $this->wishlist = CommerceWishlist::getInstance();
    }
    
    /**
     * Returns a new OrderQuery instance.
     *
     * @param mixed $criteria
     * @return OrderQuery
     */
    public function orders($criteria = null): OrderQuery
    {
        $query = Order::find();
        if ($criteria) {
            Craft::configure($query, $criteria);
        }
        return $query;
    }
}
