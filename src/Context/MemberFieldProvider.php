<?php

namespace SilverStripe\FeatureFlags\Context;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member as MemberData;

class MemberFieldProvider implements FieldProvider
{

    private $key;

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getCMSFields()
    {
        return new FieldList(
            new CheckboxSetField($this->key, $this->key, MemberData::get())
        );
    }

    public function convertItemsToFormData($items)
    {
        return [
            $this->key => $items
        ];
    }

    public function convertFormDataToItems($formData)
    {
        return (array)$formData[$this->key];
    }
}
