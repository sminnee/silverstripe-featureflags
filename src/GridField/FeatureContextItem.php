<?php

namespace SilverStripe\FeatureFlags\GridField;

use GridFieldDetailForm_ItemRequest;

/**
 * Subclass of GridFieldDetailForm_ItemRequest to override the save handler.
 * Stupid hack because the API doesn't allow anything better
 */
class FeatureContextItem extends GridFieldDetailForm_ItemRequest
{
    public function doSave($data, $form)
    {
        parent::doSave($data, $form);
        $this->record->saveContextFromForm($form);
    }
}
