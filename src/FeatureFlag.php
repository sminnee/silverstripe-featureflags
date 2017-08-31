<?php

namespace SilverStripe\FeatureFlags;

use Injector;
use Config;

/**
 * Class for interacting with the available feature flags
 */
class FeatureFlag
{

    public static function isEnabled($code, $context)
    {
        return Injector::inst()->get(FeatureFlagChecker::class)->isEnabled($code, $context);
    }

    /**
     * Enables the feature flag by writing it to durable storage. Persists across requests.
     *
     * @param $code
     * @param $context
     * @return mixed
     */
    public static function enable($code, $context)
    {
        return Injector::inst()->get(FeatureFlagSwitcher::class)->enable($code, $context);
    }

    /**
     * Disables the feature flag by writing it to durable storage. Persists across requests.
     *
     * @param $code
     * @param $context
     * @return mixed
     */
    public static function disable($code, $context)
    {
        return Injector::inst()->get(FeatureFlagSwitcher::class)->disable($code, $context);
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
