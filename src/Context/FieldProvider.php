<?php

namespace SilverStripe\FeatureFlags\Context;

/**
 * Interface for new context UIs
 */
interface FieldProvider
{

    public function getKey();

    public function setKey($key);

    public function getCMSFields();

    public function convertItemsToFormData($items);

    public function convertFormDataToItems($formData);
}
