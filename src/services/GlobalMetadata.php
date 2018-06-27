<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com/
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   http://sprout.barrelstrengthdesign.com/license
 */

namespace barrelstrength\sproutseo\services;

use barrelstrength\sproutseo\helpers\SproutSeoOptimizeHelper;
use barrelstrength\sproutseo\models\Globals;
use barrelstrength\sproutseo\migrations\InsertDefaultGlobalsBySite;
use craft\base\Component;
use craft\db\Query;
use craft\helpers\Json;
use Craft;

/**
 * Class SproutSeo_GlobalMetadataService
 *
 * @package Craft
 */
class GlobalMetadata extends Component
{
    /**
     * Get Global Metadata values
     *
     * @param null $siteId
     *
     * @return Globals
     * @throws \craft\errors\SiteNotFoundException
     * @throws \yii\base\Exception
     */
    public function getGlobalMetadata($siteId = null)
    {
        $query = (new Query())
            ->select('*')
            ->from(['{{%sproutseo_metadata_globals}}']);

        if ($siteId) {
            $query->where(['siteId' => $siteId]);
        } else {
            $site = Craft::$app->getSites()->getPrimarySite();
            $query->where(['siteId' => $site->id]);
        }

        $results = $query->one();

        $results['meta'] = isset($results['meta']) ? Json::decode($results['meta']) : null;
        $results['identity'] = isset($results['identity']) ? Json::decode($results['identity']) : null;
        $results['contacts'] = isset($results['contacts']) ? Json::decode($results['contacts']) : null;
        $results['ownership'] = isset($results['ownership']) ? Json::decode($results['ownership']) : null;
        $results['social'] = isset($results['social']) ? Json::decode($results['social']) : null;
        $results['robots'] = isset($results['robots']) ? Json::decode($results['robots']) : null;
        $results['settings'] = isset($results['settings']) ? Json::decode($results['settings']) : null;

        if (isset($results['identity']['url'])) {
            $results['identity']['url'] = SproutSeoOptimizeHelper::getGlobalMetadataSiteUrl($results['identity']['url']);
        }

        if (isset($results['settings']['ogTransform'])) {
            $results['meta']['ogTransform'] = $results['settings']['ogTransform'];
        }

        if (isset($results['settings']['twitterTransform'])) {
            $results['meta']['twitterTransform'] = $results['settings']['twitterTransform'];
        }

        $schema = new Globals($results);
        //$schema->attributes = $results;

        return $schema;
    }

    /**
     * Save Global Metadata to database
     *
     * @param $globalKeys
     * @param $globals
     *
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function saveGlobalMetadata($globalKeys, $globals)
    {
        if (!is_array($globalKeys)) {
            [$globalKeys];
        }

        foreach ($globalKeys as $globalKey) {
            $values[$globalKey] = $globals->getGlobalByKey($globalKey, 'json');
        }
        $values['siteId'] = $globals->siteId;
        // new site?
        $results = (new Query())
            ->select('*')
            ->from(['{{%sproutseo_metadata_globals}}'])
            ->where(['siteId' => $globals->siteId])
            ->one();

        if (!$results) {
            //save default settings
            $migration = new InsertDefaultGlobalsBySite([
                'siteId' => $globals->siteId,
            ]);

            ob_start();
            $migration->up();
            ob_end_clean();
        }

        $result = Craft::$app->db->createCommand()->update('{{%sproutseo_metadata_globals}}',
            $values,
            'siteId=:siteId',
            [':siteId' => $globals->siteId]
        )->execute();

        return true;
    }

    /**
     * @return \craft\db\Command
     */
    public function installDefaultGlobalMetadata()
    {
        $locale = Craft::$app->i18n->getLocaleById(Craft::$app->language);

        $defaultSettings = '{
            "seoDivider":"-",
            "defaultOgType":"",
            "ogTransform":"sproutSeo-socialSquare",
            "twitterTransform":"sproutSeo-socialSquare",
            "defaultTwitterCard":"summary",
            "appendTitleValueOnHomepage":"",
            "appendTitleValue": ""}
        ';

        $result = Craft::$app->db->createCommand()->insert('{{%sproutseo_metadata_globals}}', [
            'locale' => $locale,
            'identity' => null,
            'ownership' => null,
            'contacts' => null,
            'social' => null,
            'robots' => null,
            'settings' => $defaultSettings
        ]);

        return $result;
    }
}