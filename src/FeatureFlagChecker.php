<?php

namespace SilverStripe\FeatureFlags;

/**
 * Default implementation fo FeatureFlagCheckable.
 * Uses the FeatureSelection / FeatureSelectionItem data objects
 */
class FeatureFlagChecker implements FeatureFlagCheckable
{

    public static function isEnabled($code, $context)
    {
        $feature = FeatureSelection::get()->filter([ 'Code' => $code ])->first();

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
                'ContextID' => $obj->ID,
            ]);

            // Any context match will result in the feature being enabled
            if ($contextTest->count() > 0) {
                return true;
            }
        }

        return false;
    }
}
