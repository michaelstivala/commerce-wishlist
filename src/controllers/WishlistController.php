<?php
/**
 * Commerce Wishlist plugin for Craft CMS 3.x
 *
 * Add wish list functionality to Craft Commerce 2
 *
 * @link      https://michaelstivala.com
 * @copyright Copyright (c) 2018 Michael Stivala
 */

namespace michaelstivala\commercewishlist\controllers;

use Craft;
use craft\web\Controller;
use michaelstivala\commercewishlist\CommerceWishlist;

/**
 * Wishlist Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Michael Stivala
 * @package   CommerceWishlist
 * @since     1.0.0
 */
class WishlistController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/commerce-wishlist/wishlist-controller
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the WishlistControllerController actionIndex() method';

        return $result;
    }

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/commerce-wishlist/wishlist-controller/do-something
     *
     * @return mixed
     */
    public function actionAddToWishlist()
    {
        // Services we will be using.
        $request = Craft::$app->getRequest();
        $user = Craft::$app->getUser();

        // Backwards compatible way of adding to the cart
        if ($purchasableId = $request->getParam('purchasableId')) {
            $options = $request->getParam('options') ?: [];

            $wishlist = CommerceWishlist::getInstance()->wishlists->addToWishlist($user->id, $purchasableId, $options);
        }

        // Add multiple items to the cart
        if ($purchasables = $request->getParam('purchasables')) {
            foreach ($purchasables as $key => $purchasable) {
                $purchasableId = $request->getRequiredParam("purchasables.{$key}.id");
                $options = $request->getParam("purchasables.{$key}.options") ?: [];

                $wishlist = CommerceWishlist::getInstance()->wishlists->addToWishlist($user->id, $purchasableId, $options);
            }
        };

        return $this->redirectToPostedUrl();
    }

    public function actionRemoveFromWishlist()
    {
        $request = Craft::$app->getRequest();
        $user = Craft::$app->getUser();

        if ($wishlistItemId = $request->getParam('wishlistItemId')) {
            CommerceWishlist::getInstance()->wishlists->removeFromWishlist($user->id, $wishlistItemId);
        }
    }
}
