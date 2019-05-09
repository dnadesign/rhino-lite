<?php

namespace DNADesign\Rhino\ORM;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ManyManyList;

class RhinoManyManyList extends ManyManyList
{
    public function add($item, $extraFields = null)
    {
        parent::add($item, $extraFields);

        if ($item && $item instanceof DataObject) {
            $item->extend('onAfterAdd', $this);
        }
    }
}
