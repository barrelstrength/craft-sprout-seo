<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   https://craftcms.github.io/license
 */

namespace barrelstrength\sproutseo\models;

use barrelstrength\sproutbasefields\models\Address;
use barrelstrength\sproutseo\SproutSeo;
use craft\base\Model;
use craft\helpers\Json;
use DateTime;

/**
 *
 * @property null|string $websiteIdentityType
 */
class Globals extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $siteId;

    /**
     * @var array
     */
    public $identity;

    /**
     * @var array
     */
    public $ownership;

    /**
     * @var array
     */
    public $contacts;

    /**
     * @var array
     */
    public $social;

    /**
     * @var array
     */
    public $robots;

    /**
     * @var array
     */
    public $settings;

    /**
     * @var DateTime
     */
    public $dateCreated;

    /**
     * @var DateTime
     */
    public $dateUpdated;

    /**
     * @var int
     */
    public $uid;

    /**
     * @var Address|null
     */
    public $addressModel;

    public function __construct($config = [])
    {
        // Unset any deprecated properties
        // @todo - deprecate variables in 5.x
        // Craft's m190913_152146_update_preview_targets migration triggered error looking for this
        unset($config['meta']);

        parent::__construct($config);
    }

    public function init()
    {
        if (isset($this->identity['address']) && $this->addressModel === null) {
            $addressModel = new Address();
            $addressModel->setAttributes($this->identity['address'], false);
            $this->addressModel = $addressModel;
        }
    }

    /**
     * Factory to return schema of any type
     *
     * @param string $target
     * @param string $format
     *
     * @return array|string
     */
    public function getGlobalByKey($target, $format = 'array')
    {
        if (!$target) {
            return null;
        }

        $targetMethod = 'get'.ucfirst($target);

        $schema = $this->{$targetMethod}();

        if ($schema && $format === 'json') {
            return Json::encode($schema);
        }

        return $schema;
    }

    /**
     * @return null|string
     */
    public function getWebsiteIdentityType()
    {
        $this->getGlobalByKey('identity');
        $identityType = 'Organization';

        if (isset($this->identity['@type']) && $this->identity['@type'] != '') {
            $identityType = $this->identity['@type'];
        }

        return $identityType;
    }

    /**
     * Determine if the selected Website Identity Schema Type is a Local Business
     *
     * @return null|string
     */
    public function isLocalBusiness()
    {
        $this->getGlobalByKey('identity');

        return isset($this->identity['organizationSubTypes'][0]) && $this->identity['organizationSubTypes'][0] === 'LocalBusiness';
    }

    /**
     * Get the values associated with the Identity column in the database
     *
     * @return array|null
     */
    protected function getIdentity()
    {
        if (isset($this->identity['image']) && is_array($this->identity['image'])) {
            $this->identity['image'] = $this->identity['image'][0] ?? null;
        }

        return $this->identity;
    }

    /**
     * Get the values associated with the Contacts column in the database
     *
     * @return array|null
     */
    protected function getContacts()
    {
        $contacts = $this->contacts;
        $contactPoints = null;

        if (is_array($contacts)) {
            foreach ($contacts as $contact) {
                $contactPoints[] = [
                    '@type' => 'ContactPoint',
                    'contactType' => $contact['contactType'] ?? $contact[0],
                    'telephone' => $contact['telephone'] ?? $contact[1]
                ];
            }
        }

        return $contactPoints;
    }

    /**
     * Get the values associated with the Social column in the database
     *
     * @return array|null
     */
    protected function getSocial()
    {
        $profiles = $this->social;

        $profileLinks = null;

        if (is_array($profiles)) {
            foreach ($profiles as $profile) {
                $profileLinks[] = [
                    'profileName' => $profile['profileName'] ?? $profile[0],
                    'url' => $profile['url'] ?? $profile[1]
                ];
            }
        }

        return $profileLinks;
    }

    /**
     * Get the values associated with the Ownership column in the database
     *
     * @return array|null
     */
    protected function getOwnership()
    {
        return $this->ownership;
    }

    /**
     * @return string|null
     */
    protected function getRobots()
    {
        $robots = SproutSeo::$app->optimize->prepareRobotsMetadataValue($this->robots);

        if ($robots === null) {
            return null;
        }

        return $robots;
    }

    /**
     * Get the values associated with the Settings column in the database
     *
     * @return array|null
     */
    protected function getSettings()
    {
        return $this->settings;
    }
}
