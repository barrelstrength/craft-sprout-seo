<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com/
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   http://sprout.barrelstrengthdesign.com/license
 */

namespace barrelstrength\sproutseo\elements\actions;

use barrelstrength\sproutseo\SproutSeo;
use craft\base\ElementAction;
use Craft;

class ChangePermanentMethod extends ElementAction
{
    /**
     * @inheritDoc IComponentType::getName()
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('sprout-seo', 'Update Method to 301');
    }

    /**
     * @inheritDoc IElementAction::isDestructive()
     *
     * @return bool
     */
    public function isDestructive()
    {
        return false;
    }

    /**
     * @todo - ElementCriteriaModel doesn't exist any longer. Update for Craft 3: ElementQueryInterface
     *
     * @param ElementCriteriaModel $query
     *
     * @return bool|int
     * @throws \yii\base\Exception
     */
    public function performAction(ElementCriteriaModel $query)
    {
        $elementIds = $query->ids();

        $response = false;

        // Call updateMethods service
        $response = SproutSeo::$app->redirects->updateRedirectMethod($elementIds, SproutSeo_RedirectMethods::Permanent);

        $message = SproutSeo::$app->redirects->getMethodUpdateResponse($response);

        $this->setMessage($message);

        return $response;
    }

    /**
     * @inheritDoc BaseElementAction::defineParams()
     *
     * @return array
     */
    protected function defineParams()
    {
        return [];
    }
}