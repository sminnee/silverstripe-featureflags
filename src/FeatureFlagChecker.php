<?php

namespace SilverStripe\FeatureFlags;

use FeatureSelection;

/**
 * Default implementation fo FeatureFlagCheckable.
 * Uses the FeatureSelection / FeatureSelectionItem data objects
 */
class FeatureFlagChecker implements FeatureFlagCheckable
{
    public static function isEnabled($code, $context)
    {
        $feature = FeatureSelection::get()->filter('Code', $code)->first();
        if (!$feature || !$feature->exists()) {
            return false;
        }

        // Simple modes
        if ($feature->EnableMode === 'On') {
            return true;
        }
        if ($feature->EnableMode === 'Off') {
            return false;
        }

        // TODO: validate context

        // Check each context value against the selections
        foreach ($context as $key => $obj) {
            $contextTest = $feature->Items()->filter([
                'ContextKey' => $key,
                'ContextID' => $obj ? $obj->ID : 0,
            ]);

            // Any context match will result in the feature being enabled
            if ($contextTest->count() > 0) {
                return true;
            }
        }

        return false;
    }
}
