<?php
namespace Craft;

class SproutSeo_SchemaModel extends BaseModel
{
	public $schemaId = null;
	public $type = null;

	protected function defineAttributes()
	{
		return array(
			'identity' => AttributeType::Mixed,
			'contacts' => AttributeType::Mixed,
			'social'   => AttributeType::Mixed,

			// @todo - move to a meta tag model
			'ownership' => AttributeType::Mixed,
		);
	}

	/**
	 * Factory to return schema of any type
	 *
	 * @param        $target
	 * @param string $format
	 *
	 * @return string
	 */
	public function getSchema($target, $format = 'array')
	{
		if ($target)
		{
			$this->schemaId = $target;
		}

		$targetMethod = 'get' . ucfirst($target);

		$schema = $this->{$targetMethod}();

		if ($format == 'json')
		{
			return JsonHelper::encode($schema);
		}

		return $schema;
	}


	// Supported Schema Types
	// =========================================================================

	public function getType()
	{
		$this->getSchema('identity');

		return $this->type;
	}

	protected function getIdentity()
	{
		$structuredData = $this->prepareSchemaObject();

		$schema = $this->{$this->schemaId};

		$structuredData['name']                 = isset($schema['name']) ? $schema['name'] : null;
		$structuredData['description']          = isset($schema['description']) ? $schema['description'] : null;
		$structuredData['url']                  = isset($schema['url']) ? $schema['url'] : null;
		$structuredData['logo']                 = isset($schema['logo']) ? $schema['logo'] : null;
		$structuredData['keywords']             = isset($schema['keywords']) ? $schema['keywords'] : null;
		$structuredData['alternateEntityName']  = isset($schema['alternateEntityName']) ? $schema['alternateEntityName'] : null;

		$structuredData['address']       = isset($schema['address']) ? $schema['address'] : null;
		$structuredData['streetAddress'] = isset($schema['streetAddress']) ? $schema['streetAddress'] : null;
		$structuredData['locality']      = isset($schema['locality']) ? $schema['locality'] : null;
		$structuredData['region']        = isset($schema['region']) ? $schema['region'] : null;
		$structuredData['postalCode']    = isset($schema['postalCode']) ? $schema['postalCode'] : null;
		$structuredData['country']       = isset($schema['country']) ? $schema['country'] : null;

		$structuredData['latitude']            = isset($schema['latitude']) ? $schema['latitude'] : null;
		$structuredData['longitude']           = isset($schema['longitude']) ? $schema['longitude'] : null;
		$structuredData['dunsNumber']          = isset($schema['dunsNumber']) ? $schema['dunsNumber'] : null;
		$structuredData['organizationFounder'] = isset($schema['organizationFounder']) ? $schema['organizationFounder'] : null;
		$structuredData['foundingDate']        = isset($schema['foundingDate']) ? $schema['foundingDate'] : null;
		$structuredData['foundingLocation']    = isset($schema['foundingLocation']) ? $schema['foundingLocation'] : null;

		$structuredData['localBusinessInfo']   = isset($schema['localBusinessInfo']) ? $schema['localBusinessInfo'] : null;
		$structuredData['openClose']           = isset($schema['openClose']) ? $schema['openClose'] : null;

		return $structuredData;
	}

	protected function getOrganization()
	{
	}

	protected function getPerson()
	{
	}

	protected function getWebsite()
	{
	}

	protected function getPlace()
	{
	}


	protected function getContacts()
	{
		$contacts = $this->{$this->schemaId};

		$contactPoints = array();

		if (count($contacts))
		{
			foreach ($contacts as $contact)
			{
				$contactPoints[] = array(
					'@type'       => 'ContactPoint',
					'contactType' => isset($contact['contactType']) ? $contact['contactType'] : $contact[0],
					'telephone'   => isset($contact['telephone']) ? $contact['telephone'] : $contact[1]
				);
			}
		}

		return $contactPoints;
	}

	protected function getSocial()
	{
		$profiles = $this->{$this->schemaId};

		$profileLinks = array();

		if (count($profiles))
		{
			foreach ($profiles as $profile)
			{
				$profileLinks[] = array(
					'profileName' => isset($profile['profileName']) ? $profile['profileName'] : $profile[0],
					'url' => isset($profile['url']) ? $profile['url'] : $profile[1]
				);
			}
		}

		return $profileLinks;
	}

	protected function getOwnership()
	{
		$ownership = $this->{$this->schemaId};

		return $ownership;
	}

	// Custom Schema Types
	// =========================================================================

	public function getSchemaMap($object, $mapId)
	{
	}


	// Protected Methods
	// =========================================================================

	protected function prepareSchemaObject()
	{
		$this->type = $this->{$this->schemaId}['@type'];

		return array(
			"@context" => "http://schema.org",
			"@type"    => $this->type
		);
	}
}
