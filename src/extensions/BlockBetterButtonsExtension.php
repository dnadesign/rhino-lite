<?php

namespace DNADesign\Rhino\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\Versioned;

class BlockBetterButtonsExtension extends DataExtension
{
    private static $better_buttons_enabled = false;

    public function onAfterWrite()
    {
        if (Versioned::get_stage() == 'Stage') {
            $this->owner->publish('Stage', 'Live');
        }
    }
}
