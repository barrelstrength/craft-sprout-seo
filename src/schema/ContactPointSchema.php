<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com/
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   http://sprout.barrelstrengthdesign.com/license
 */

namespace barrelstrength\sproutseo\schema;

use barrelstrength\sproutseo\base\Schema;

class ContactPointSchema extends Schema
{
    public $contact;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Contact Point';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'ContactPoint';
    }

    /**
     * @return bool
     */
    public function isUnlistedSchemaType()
    {
        return true;
    }

    /**
     * @return array|null
     */
    public function addProperties()
    {
        $contact = $this->contact;

        if (!$contact) {
            return null;
        }

        $this->addText('contactType', $contact['contactType']);
        $this->addTelephone('telephone', $contact['telephone']);
    }
}