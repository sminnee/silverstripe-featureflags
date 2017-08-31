<?php

namespace SilverStripe\FeatureFlags;

use Exception;
use FeatureSelection;
use FeatureSelectionItem;

class FeatureFlagSwitcher implements FeatureFlagSwitchable
{
    public static function enable($code, $context)
    {
        $feature = FeatureSelection::get()->filter('Code', $code)->first();
        if (!$feature || !$feature->exists()) {
            throw new Exception(sprintf("Cannot enable: flag '%s' does not exist in the DB.", $code));
        }

        foreach ($context as $key => $obj) {
            $count = FeatureSelectionItem::get()->filter('ContextKey', $key)->filter('ContextID', $obj->ID)->count();
            if ($count) continue;

            $item = new FeatureSelectionItem();
            $item->ContextKey = $key;
            $item->ContextID = $obj->ID;
            $item->FeatureSelectionID = $feature->ID;
            $item->write();
        }
    }

    public static function disable($code, $context)
    {
        $feature = FeatureSelection::get()->filter('Code', $code)->first();
        if (!$feature || !$feature->exists()) {
            throw new Exception(sprintf("Cannot disable: flag '%s' does not exist in the DB.", $code));
        }

        foreach ($context as $key => $obj) {
            $items = FeatureSelectionItem::get()->filter('ContextKey', $key)->filter('ContextID', $obj->ID);
            foreach ($items as $item) {
                $item->delete();
            }
        }
    }
}
