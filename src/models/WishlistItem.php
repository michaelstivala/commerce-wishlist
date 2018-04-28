<?php
/**
 * Commerce Wishlist plugin for Craft CMS 3.x
 *
 * Add wish list functionality to Craft Commerce 2
 *
 * @link      https://michaelstivala.com
 * @copyright Copyright (c) 2018 Michael Stivala
 */

namespace michaelstivala\commercewishlist\models;

use Craft;
use craft\base\Model;
use craft\base\Element;
use craft\helpers\Json;
use InvalidArgumentException;
use craft\validators\UniqueValidator;
use michaelstivala\commercewishlist\CommerceWishlist;
use michaelstivala\commercewishlist\records\WishlistItem as WishlistItemRecord;

/**
 * WishlistItem Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @property-read string $optionsSignature the unique hash of the options
 * @author    Michael Stivala
 * @package   CommerceWishlist
 * @since     1.0.0
 */
class WishlistItem extends Model
{
    // Public Properties
    // =========================================================================

    public $id;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    /**
     * User ID
     * @var int
     */
    public $userId;

    /**
     * Purchasable Id
     * @var int
     */
    public $purchasableId;

    /**
     * Options
     * @var array
     */
    private $_options = [];

    /**
     * Purchasable
     */
    private $_purchasable;

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['userId', 'required'],
            ['purchasableId', 'required'],
            // ['options', 'required'],
            ['optionsSignature', 'required'],

            [['optionsSignature'], UniqueValidator::class, 'targetClass' => WishlistItemRecord::class, 'targetAttribute' => ['userId', 'purchasableId', 'optionsSignature'], 'message' => 'Not Unique'],
        ];
    }

    public function attributes()
    {
        $attributes = parent::attributes();

        $attributes[] = 'optionsSignature';

        return $attributes;
    }

    /**
     * Gets the options for the line item.
     */
    public function getOptions(): array
    {
        return $this->_options;
    }

    /**
     * Set the options array on the line item.
     *
     * @param array|string $options
     */
    public function setOptions($options)
    {
        if (is_string($options)) {
            $options = Json::decode($options);
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException('Options must be an array.');
        }

        ksort($options);

        $this->_options = $options;
    }

    /**
     * Returns a unique hash of the line item options
     */
    public function getOptionsSignature()
    {
        return md5(Json::encode($this->_options));
    }

    /**
     * @return PurchasableInterface|null
     */
    public function getPurchasable()
    {
        if (null === $this->_purchasable && null !== $this->purchasableId) {
            $this->_purchasable = Craft::$app->getElements()->getElementById($this->purchasableId);
        }

        return $this->_purchasable;
    }

    /**
     * @param \craft\commerce\base\Element $purchasable
     */
    public function setPurchasable(Element $purchasable)
    {
        $this->purchasableId = $purchasable->id;
        $this->_purchasable = $purchasable;
    }
}
