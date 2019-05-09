<?php

namespace DNADesign\Rhino\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class RhinoSiteConfigExtension extends DataExtension
{
    private static $db = [
        'InstanceName' => 'Varchar(255)'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        // Instance Name
        $instanceName = TextField::create('InstanceName',
            'Instance Name')->setRightTitle('The way we refer to this instance of Rhino. Appears in email subjects.');
        $fields->addFieldToTab('Root.Rhino', $instanceName);
    }
}
