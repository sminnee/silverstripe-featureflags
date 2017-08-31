<?php

namespace SilverStripe\FeatureFlags;

interface FeatureFlagSwitchable
{
    public static function enable($code, $context);
    public static function disable($code, $context);
}
