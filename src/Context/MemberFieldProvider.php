<?php

namespace SilverStripe\FeatureFlags\Context;

use DataObject;
use DB;
use ReadonlyField;
use CheckboxSetField;
use FieldList;
use Member;

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
            new CheckboxSetField($this->key, $this->key, Member::get())
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
