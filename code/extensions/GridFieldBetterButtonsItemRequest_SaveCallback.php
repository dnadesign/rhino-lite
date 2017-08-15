<?php

/**
 * Adds callback after save has completed and all relations have been created
 *
 * @package rhino
 */
class GridFieldBetterButtonsItemRequest_SaveCallback extends GridFieldBetterButtonsItemRequest {


	protected function saveAndRedirect($data, $form, $redirectLink) {
		$initial = false;
		if (!$this->owner->record->isInDB()) {
			$initial = true;
		}
		$return = parent::saveAndRedirect($data, $form, $redirectLink);
		if ($initial) {
			$this->owner->record->extend('afterInitialSave');
		}
		$this->owner->record->extend('afterSave');
		return $return;
	}
}