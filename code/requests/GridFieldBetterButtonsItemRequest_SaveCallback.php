<?php
//
//namespace DNADesign\Rhino\Extensions;
//
//
///**
// * Adds callback after save has completed and all relations have been created
// *
// * @package rhino
// */
//
// //TODO: Determine whether this is needed. BetterButtons is not part of the dependencies of this module.
//
//class GridFieldBetterButtonsItemRequest_SaveCallback extends GridFieldBetterButtonsItemRequest
//{
//    protected function saveAndRedirect($data, $form, $redirectLink)
//    {
//        $initial = false;
//        if (!$this->owner->record->isInDB()) {
//            $initial = true;
//        }
//        $return = parent::saveAndRedirect($data, $form, $redirectLink);
//        if ($initial) {
//            $this->owner->record->extend('afterInitialSave');
//        }
//        $this->owner->record->extend('afterSave');
//
//        return $return;
//    }
//}
