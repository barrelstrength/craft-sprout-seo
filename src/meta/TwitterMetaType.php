<?php
/**
 * @link      https://sprout.barrelstrengthdesign.com
 * @copyright Copyright (c) Barrel Strength Design LLC
 * @license   https://craftcms.github.io/license
 */

namespace barrelstrength\sproutseo\meta;

use barrelstrength\sproutseo\base\MetaImageTrait;
use barrelstrength\sproutseo\base\MetaType;
use barrelstrength\sproutseo\SproutSeo;
use Craft;
use craft\base\Field;
use craft\errors\SiteNotFoundException;
use Throwable;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;

/**
 * Implements all attributes used in search metadata
 */
class TwitterMetaType extends MetaType
{
    use MetaImageTrait;

    /**
     * @var string|null
     */
    protected $twitterCard;

    /**
     * @var string|null
     */
    protected $twitterSite;

    /**
     * @var string|null
     */
    protected $twitterCreator;

    /**
     * @var string|null
     */
    protected $twitterUrl;

    /**
     * @var string|null
     */
    protected $twitterTitle;

    /**
     * @var string|null
     */
    protected $twitterDescription;

    /**
     * @var string|null
     */
    protected $twitterImage;

    /**
     * @var string|null
     */
    protected $twitterTransform;

    /**
     * @var string|null
     */
    private $twitterProfileName;

    public static function displayName(): string
    {
        return Craft::t('sprout-seo', 'Twitter Card');
    }

    public function attributes(): array
    {
        $attributes = parent::attributes();
        $attributes[] = 'twitterCard';
        $attributes[] = 'twitterSite';
        $attributes[] = 'twitterCreator';
        $attributes[] = 'twitterTitle';
        $attributes[] = 'twitterDescription';
        $attributes[] = 'twitterUrl';
        $attributes[] = 'twitterImage';
        $attributes[] = 'twitterTransform';

        return $attributes;
    }

    /**
     * @return array
     */
    public function getAttributesMapping(): array
    {
        return [
            'twitterCard' => 'twitter:card',
            'twitterSite' => 'twitter:site',
            'twitterCreator' => 'twitter:creator',
            'twitterTitle' => 'twitter:title',
            'twitterDescription' => 'twitter:description',
            'twitterUrl' => 'twitter:url',
            'twitterImage' => 'twitter:image'
        ];
    }

    /**
     * @return string|null
     */
    public function getTwitterCard()
    {
        if ($this->twitterCard || $this->metadata->getRawDataOnly()) {
            return $this->twitterCard;
        }

        return SproutSeo::$app->optimize->globals->settings['defaultTwitterCard'] ?? 'summary';
    }

    /**
     * @param $value
     */
    public function setTwitterCard($value)
    {
        $this->twitterCard = $value;
    }

    /**
     * @return string|null
     */
    public function getTwitterSite()
    {
        if ($this->twitterCreator || $this->metadata->getRawDataOnly()) {
            return $this->twitterSite;
        }

        return $this->getTwitterProfileName();
    }

    /**
     * @param $value
     */
    public function setTwitterSite($value)
    {
        $this->twitterSite = $value;
    }

    /**
     * @return string|null
     */
    public function getTwitterCreator()
    {
        if ($this->twitterCreator || $this->metadata->getRawDataOnly()) {
            return $this->twitterCreator;
        }

        return $this->getTwitterProfileName();
    }

    /**
     * @param $value
     */
    public function setTwitterCreator($value)
    {
        $this->twitterCreator = $value;
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function getTwitterUrl()
    {
        if ($this->twitterUrl || $this->metadata->getRawDataOnly()) {
            return $this->twitterUrl;
        }

        return $this->metadata->getCanonical();
    }

    /**
     * @param $value
     */
    public function setTwitterUrl($value)
    {
        $this->twitterUrl = $value;
    }

    /**
     * @return string|null
     * @throws Exception
     * @throws Throwable
     */
    public function getTwitterTitle()
    {
        if ($this->twitterTitle || $this->metadata->getRawDataOnly()) {
            return $this->twitterTitle;
        }

        if ($optimizedTitle = $this->metadata->getOptimizedTitle()) {
            return trim($optimizedTitle) ?: null;
        }

        return trim(SproutSeo::$app->optimize->globals->identity['name']);
    }

    /**
     * @param $value
     */
    public function setTwitterTitle($value)
    {
        $this->twitterTitle = $value;
    }

    /**
     * @return string|null
     * @throws Exception
     * @throws Throwable
     */
    public function getTwitterDescription()
    {
        $descriptionLength = SproutSeo::$app->settings->getDescriptionLength();

        if ($this->twitterDescription || $this->metadata->getRawDataOnly()) {
            return mb_substr($this->twitterDescription, 0, $descriptionLength) ?: null;
        }

        if ($optimizedDescription = $this->metadata->getOptimizedDescription()) {
            return mb_substr($optimizedDescription, 0, $descriptionLength) ?: null;
        }

        $globalDescription = SproutSeo::$app->optimize->globals->identity['description'] ?? null;

        return mb_substr($globalDescription, 0, $descriptionLength) ?: null;
    }

    /**
     * @param $value
     */
    public function setTwitterDescription($value)
    {
        $this->twitterDescription = $value;
    }

    /**
     * @return mixed|string|null
     * @throws Exception
     * @throws Throwable
     */
    public function getTwitterImage()
    {
        if ($this->twitterImage || $this->metadata->getRawDataOnly()) {
            return $this->twitterImage;
        }

        if ($optimizedImage = $this->metadata->getOptimizedImage()) {
            return $this->normalizeImageValue($optimizedImage);
        }

        return SproutSeo::$app->optimize->globals->identity['image'] ?? null;
    }

    /**
     * @param $value
     *
     * @throws Throwable
     */
    public function setTwitterImage($value)
    {
        if (is_array($value)) {
            $this->twitterImage = $value[0] ?? null;
        } else {
            $this->twitterImage = $value;
        }
    }

    /**
     * @return string|null
     */
    public function getTwitterTransform()
    {
        if ($this->twitterTransform || $this->metadata->getRawDataOnly()) {
            return $this->twitterTransform;
        }

        return SproutSeo::$app->optimize->globals->settings['twitterTransform'] ?? null;
    }

    /**
     * @param $value
     */
    public function setTwitterTransform($value)
    {
        $this->twitterTransform = $value;
    }

    public function getHandle(): string
    {
        return 'twitterCard';
    }

    public function getIconPath(): string
    {
        return '@sproutbaseicons/twitter.svg';
    }

    /**
     * @param Field $field
     *
     * @return string
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getSettingsHtml(Field $field): string
    {
        return Craft::$app->getView()->renderTemplate('sprout-seo/_components/fields/elementmetadata/blocks/twitter-card', [
            'meta' => $this,
            'field' => $field
        ]);
    }

    public function showMetaDetailsTab(): bool
    {
        return SproutSeo::$app->optimize->elementMetadataField->showTwitter;
    }

    /**
     * @return array
     * @throws Exception
     * @throws SiteNotFoundException
     */
    public function getMetaTagData(): array
    {
        $tagData = parent::getMetaTagData();

        if (isset($tagData['twitter:image'])) {
            $tagData['twitter:image'] = $this->prepareAssetMetaData($tagData['twitter:image'], $this->getTwitterTransform());
        }

        return $tagData;
    }

    /**
     * Check our Social Profile settings for a Twitter profile.
     * Return the first Twitter profile
     *
     * @return string|null
     */
    public function getTwitterProfileName()
    {
        // Have we already done this?
        if ($this->twitterProfileName) {
            return $this->twitterProfileName;
        }

        $socialProfiles = SproutSeo::$app->optimize->globals->social ?? null;

        if ($socialProfiles === null) {
            return null;
        }

        $twitterProfileName = null;

        foreach ($socialProfiles as $profile) {
            $socialProfileNameFromPost = $profile[0] ?? null;
            $socialProfileNameFromSettings = $profile['profileName'] ?? null;

            // Support syntax for both POST data being saved and previous saved social settings
            if ($socialProfileNameFromPost === 'Twitter' or $socialProfileNameFromSettings === 'Twitter') {
                $twitterUrlFromPost = isset($socialProfileNameFromPost) ? $profile[1] : null;
                $twitterUrl = $socialProfileNameFromSettings !== null ? $profile['url'] : $twitterUrlFromPost;

                $twitterProfileName = '@'.mb_substr($twitterUrl, strrpos($twitterUrl, '/') + 1);

                break;
            }
        }

        // memoize it if we need it again
        $this->twitterProfileName = $twitterProfileName;

        return $twitterProfileName;
    }

}
