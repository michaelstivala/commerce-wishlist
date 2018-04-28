<?php
/**
 * Commerce Wishlist plugin for Craft CMS 3.x
 *
 * Add wish list functionality to Craft Commerce 2
 *
 * @link      https://michaelstivala.com
 * @copyright Copyright (c) 2018 Michael Stivala
 */

namespace michaelstivala\commercewishlist\elements;

use Craft;
use craft\base\Model;
use craft\base\Element;
use craft\helpers\Json;
use InvalidArgumentException;
use craft\commerce\records\Purchasable;
use michaelstivala\commercewishlist\CommerceWishlist;

/**
 * WishlistItem Element
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Michael Stivala
 * @package   CommerceWishlist
 * @since     1.0.0
 */
class WishlistItem extends Element
{
    public $userId;
    
    public function getPurchasable()
    {
        return $this->hasOne(Purchasable::class, ['id' => 'purchasableId']);
    }
}
