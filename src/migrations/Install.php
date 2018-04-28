<?php
/**
 * Commerce Wishlist plugin for Craft CMS 3.x
 *
 * Add wish list functionality to Craft Commerce 2
 *
 * @link      https://michaelstivala.com
 * @copyright Copyright (c) 2018 Michael Stivala
 */

namespace michaelstivala\commercewishlist\migrations;

use michaelstivala\commercewishlist\CommerceWishlist;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Commerce Wishlist Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Michael Stivala
 * @package   CommerceWishlist
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

    // commercewishlist_wishlistitem table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%commercewishlist_wishlistitem}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%commercewishlist_wishlistitem}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'userId' => $this->integer()->notNull(),
                    'purchasableId' => $this->integer()->notNull(),
                    'options' => $this->text(),
                    'optionsSignature' => $this->string()->notNull(),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    // commercewishlist_wishlistitem table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%commercewishlist_wishlistitem}}',
                'userId',
                true
            ),
            '{{%commercewishlist_wishlistitem}}',
            'userId',
            false
        );
        $this->createIndex(
            $this->db->getIndexName(
                '{{%commercewishlist_wishlistitem}}',
                'purchasableId',
                true
            ),
            '{{%commercewishlist_wishlistitem}}',
            'purchasableId',
            false
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%commercewishlist_wishlistitem}}', 'userId'),
            '{{%commercewishlist_wishlistitem}}',
            'userId',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%commercewishlist_wishlistitem}}', 'purchasableId'),
            '{{%commercewishlist_wishlistitem}}',
            'purchasableId',
            '{{%elements}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // commercewishlist_wishlistitem table
        $this->dropTableIfExists('{{%commercewishlist_wishlistitem}}');
    }
}
