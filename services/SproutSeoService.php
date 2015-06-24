<?php
namespace Craft;

/**
 * Class SproutSeoService
 *
 * @package Craft
 */
class SproutSeoService extends BaseApplicationComponent
{
	/**
	 * @var SproutSeo_MetaService
	 */
	public $meta;

	/**
	 * @var SproutSeo_SettingsService
	 */
	public $settings;

	/**
	 * @var SproutSeo_SitemapService
	 */
	public $sitemap;

	public function init()
	{
		parent::init();

		$this->meta     = Craft::app()->getComponent('sproutSeo_meta');
		$this->sitemap  = Craft::app()->getComponent('sproutSeo_sitemap');
		$this->settings = Craft::app()->getComponent('sproutSeo_settings');
	}
}