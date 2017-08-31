<?php

namespace SilverStripe\FeatureFlags;

/**
 * Provides methods for persistently changing the feature flags.
 */
interface FeatureFlagSwitchable
{
    public static function enable($code, $context);
    public static function disable($code, $context);
}
