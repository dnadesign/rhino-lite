<?php

class RhinoManyManyList extends ManyManyList {

	public function add($item, $extraFields = null) {
		parent::add($item, $extraFields);

		if($item && $item instanceof DataObject) {
			$item->extend('onAfterAdd', $this);
		}
	}
}