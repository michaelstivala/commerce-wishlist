<?php
/**
 * Commerce Wishlist plugin for Craft CMS 3.x
 *
 * Add wish list functionality to Craft Commerce 2
 *
 * @link      https://michaelstivala.com
 * @copyright Copyright (c) 2018 Michael Stivala
 */

namespace michaelstivala\commercewishlist\services;

use Craft;
use craft\elements\User;
use craft\base\Component;
use craft\commerce\base\Purchasable;
use craft\commerce\base\PurchasableInterface;
use michaelstivala\commercewishlist\CommerceWishlist;
use michaelstivala\commercewishlist\models\WishlistItem;
use michaelstivala\commercewishlist\records\WishlistItem as WishlistItemRecord;
use michaelstivala\commercewishlist\elements\WishlistItem as WishlistItemElement;

/**
 * Wishlists Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Michael Stivala
 * @package   CommerceWishlist
 * @since     1.0.0
 */
class Wishlists extends Component
{
    public function getWishlistItems()
    {
        $query = WishlistItemRecord::find()->where(['userId' => Craft::$app->getUser()->id]);

        return array_map(function ($record) {
            $record['options'] = $record['options'] ?: [];
            unset($record['optionsSignature']);
            return new WishlistItem($record->toArray());
        }, $query->all());
    }

    public function addToWishlist(int $userId, int $purchasableId, array $options = [])
    {
        $wishlistItem = new WishlistItem;
        $wishlistItem->userId = $userId;
        $wishlistItem->setOptions($options);

        /** @var PurchasableInterface $purchasable */
        $purchasable = Craft::$app->getElements()->getElementById($purchasableId);
        $wishlistItem->setPurchasable($purchasable);
        
        if (! $wishlistItem->validate()) {
            return false;
        }

        // Save it to the database
        ($record = new WishlistItemRecord($wishlistItem));
        $record->options = $wishlistItem->getOptions();
        $record->optionsSignature = $wishlistItem->getOptionsSignature();

        $record->save();
        return $wishlistItem;
    }

    public function removeFromWishlist(int $userId, int $wishlistItemId)
    {
        $wishlistItem = WishlistItemRecord::find()->where([
            'userId' => $userId,
            'id' => $wishlistItemId,
        ])->one();

        if ($wishlistItem) {
            $wishlistItem->delete();
        }
    }
}
