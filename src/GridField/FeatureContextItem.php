<?php

namespace SilverStripe\FeatureFlags\GridField;

use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;

/**
 * Subclass of GridFieldDetailForm_ItemRequest to override the save handler.
 * Stupid hack because the API doesn't allow anything better
 */
class FeatureContextItem extends GridFieldDetailForm_ItemRequest
{
    public function saveFormIntoRecord($data, $form)
    {
        $record = parent::saveFormIntoRecord($data, $form);

        $record->saveContextFromForm($form);
    }
}
