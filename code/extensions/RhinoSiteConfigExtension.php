<?php

class RhinoSiteConfigExtension extends DataExtension {

	private static $db = array(
		'InstanceName' => 'Varchar(255)'
	);

	public function updateCMSFields(FieldList $fields) {

		// Instance Name
		$instanceName = TextField::create('InstanceName', 'Instance Name')->setRightTitle('The way we refer to this instance of Rhino. Appears in email subjects.');
		$fields->addFieldToTab('Root.Rhino', $instanceName);
	}

}