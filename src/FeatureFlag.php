<?php

namespace SilverStripe\FeatureFlags;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Config\Config;

/**
 * Class for interacting with the available feature flags
 */
class FeatureFlag
{

    public static function isEnabled($code, $context)
    {
        return Injector::inst()->get(FeatureFlagChecker::class)->isEnabled($code, $context);
    }

    public static function allFeatures()
    {
        return (array)Config::inst()->get(self::class, 'features');
    }

    public static function getFeature($code)
    {
        foreach(self::allFeatures() as $feature) {
            if($feature['code'] == $code) {
                return $feature;
            }
        }
    }
}
