<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   https://craftcms.github.io/license
 */

namespace barrelstrength\sproutseo\migrations;

use barrelstrength\sproutbasefields\migrations\m190313_000000_fix_non_abbreviation_administrative_codes;
use craft\db\Migration;

/**
 * m190313_000001_fix_non_abbreviation_administrative_codes_sproutseo migration.
 */
class m190313_000001_fix_non_abbreviation_administrative_codes_sproutseo extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $migration = new m190313_000000_fix_non_abbreviation_administrative_codes();

        ob_start();
        $migration->safeUp();
        ob_end_clean();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m190313_000001_fix_non_abbreviation_administrative_codes_sproutseo cannot be reverted.\n";

        return false;
    }
}
