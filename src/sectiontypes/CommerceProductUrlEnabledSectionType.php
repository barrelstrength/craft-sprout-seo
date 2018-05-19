<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com/
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   http://sprout.barrelstrengthdesign.com/license
 */

namespace barrelstrength\sproutseo\sectiontypes;

use barrelstrength\sproutseo\base\UrlEnabledSectionType;

use Craft;

/**
 * Class CommerceProductUrlEnabledSectionType
 */
class CommerceProductUrlEnabledSectionType extends UrlEnabledSectionType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Commerce Products';
    }

    /**
     * @return string
     */
    public function getIdVariableName()
    {
        return 'productId';
    }

    /**
     * @return string
     */
    public function getIdColumnName()
    {
        if ($this->typeIdContext == 'matchedElementCheck') {
            return 'typeId';
        }

        return 'productTypeId';
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return Craft::$app->commerce_productTypes->getProductTypeById($id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getFieldLayoutSettingsObject($id)
    {
        $productType = $this->getById($id);

        return $productType;
    }

    /**
     * @return string
     */
    public function getElementTableName()
    {
        return 'commerce_products';
    }

    /**
     * @return string
     */
    public function getElementType()
    {
        return 'Commerce_Product';
    }

    /**
     * @return string
     */
    public function getMatchedElementVariable()
    {
        return 'product';
    }

    /**
     * @return mixed
     */
    public function getAllUrlEnabledSections()
    {
        $urlEnabledSections = [];

        $sections = Craft::$app->commerce_productTypes->getAllProductTypes();

        foreach ($sections as $section) {
            if ($section->hasUrls) {
                $urlEnabledSections[] = $section;
            }
        }

        return $urlEnabledSections;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return 'commerce_producttypes_i18n';
    }

    /**
     * Don't have Sprout SEO trigger ResaveElements task after saving a field layout.
     * This is already supported by Craft Commerce.
     *
     * @return bool
     */
    public function resaveElementsAfterFieldLayoutSaved()
    {
        return false;
    }

    /**
     * @param null $elementGroupId
     *
     * @return mixed|void
     */
    public function resaveElements($elementGroupId = null)
    {
        if (!$elementGroupId) {
            // @todo - Craft Feature Request
            // This data should be available from the SaveFieldLayout event, not relied on in the URL
            $elementGroupId = Craft::$app->request->getSegment(4);
        }

        $criteria = Craft::$app->elements->getCriteria('Commerce_Product');
        // @todo - call commerce service
        $productType = Craft::$app->categories->getGroupById($elementGroupId);
        $locales = array_values($productType->getLocales());

        if ($locales) {
            $primaryLocale = $locales[0];

            $criteria->locale = $primaryLocale->locale;
            $criteria->productTypeId = $elementGroupId;
            $criteria->status = null;
            $criteria->localeEnabled = null;
            $criteria->limit = null;

            Craft::$app->tasks->createTask('ResaveElements', Craft::t('sprout-seo', 'Re-saving Commerce Products and metadata.'), [
                'elementType' => 'Commerce_Product',
                'criteria' => $criteria->getAttributes()
            ]);
        }
    }
}