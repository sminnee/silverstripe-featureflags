<?php

use SilverStripe\FeatureFlags\Context\FieldProvider;
use SilverStripe\FeatureFlags\FeatureFlag;

class FeatureSelection extends DataObject
{
    private static $db = [
        'Code' => 'Varchar(50)',
        'EnableMode' => 'Enum("Off, On, Partial", "Off")',
    ];

    private static $indexes = [
        'Code' => true,
    ];

    private static $has_many = [
        'Items' => FeatureSelectionItem::class,
    ];

    private static $summary_fields = [
        'Code',
        'Title',
        'EnableMode',
    ];

    public function getTitle()
    {
        $features = FeatureFlag::allFeatures();
        foreach ($features as $feature) {
            if ($feature['code'] === $this->Code) {
                return $feature['title'];
            }
        }
    }

    public function canCreate($member = null, $context = [])
    {
        return false;
    }

    public function canDelete($member = null)
    {
        return false;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', new ReadonlyField('Code'), 'EnableMode');
        $fields->removeFieldFromTab('Root', 'Items');

        $fieldProviders = $this->getFieldProviders();
        foreach($fieldProviders as $fieldProvider) {
            $this->addContextFields($fields, $fieldProvider);
        }

        // If there are no context field providers, Partial mode is not allowed
        if(!$fieldProviders) {
            $fields->dataFieldByName('EnableMode')->setSource([
                'On' => 'On',
                'Off' => 'Off',
            ]);
        }

        return $fields;
    }

    public function saveContextFromForm(Form $form)
    {
        foreach($this->getFieldProviders() as $fieldProvider) {
            $this->saveContext($form, $fieldProvider);
        }

    }

    /**
     * Add fields for the given context key, using the field provider for the given class
     */
    protected function addContextFields(FieldList $fields, FieldProvider $fieldProvider)
    {

        foreach ($fieldProvider->getCMSFields() as $field) {
            $fields->addFieldToTab('Root.Main', $field);
        }

        $ids = $this->Items()->filter([ 'ContextKey' => $fieldProvider->getKey() ])->column('ContextID');
        $formData = $fieldProvider->convertItemsToFormData($ids);
        $fields->setValues($formData);
    }

    /**
     * Add fields for the given field provider
     */
    protected function saveContext(Form $form, FieldProvider $fieldProvider)
    {
        $items = $formData = $fieldProvider->convertFormDataToItems($form->getData());
        $key = $fieldProvider->getKey();

        // Remove bad items
        $badItems = $this->Items()->filter([ 'ContextKey' => $key ]);
        if ($items) {
            $badItems = $badItems->filter([ 'ContextID:not' => $items ]);
        }

        foreach ($badItems as $item) {
            $item->delete();
        }

        if ($items) {
            // Itentify existing items to neither add nor delete
            $existingItems = $this->Items()
                ->filter([ 'ContextKey' => $key, 'ContextID' => $items ])
                ->column('ContextID');

            // Add new items
            foreach (array_diff($items, $existingItems) as $itemID) {
                $item = new FeatureSelectionItem;
                $item->ContextKey = $key;
                $item->ContextID = $itemID;
                $this->Items()->add($item);
            }
        }
    }

    /**
     * Return the FieldProvider instances for selecting the context of this feature flag
     */
    protected function getFieldProviders()
    {
        $feature = FeatureFlag::getFeature($this->Code);
        if(empty($feature['context'])) return [];

        $fieldProviderMap = Config::inst()->get(FeatureFlagAdmin::class, 'context_field_providers');
        $fieldProviders = [];

        foreach($feature['context'] as $key => $className) {
            if (empty($fieldProviderMap[$className])) {
                throw new \LogicException('Can\'t find context field provider for ' . $className);
            }
            $fieldProviderClass = $fieldProviderMap[$className];
            $fieldProvider = new $fieldProviderClass();
            $fieldProvider->setKey($key);
            $fieldProviders[] = $fieldProvider;
        }

        return $fieldProviders;
    }

    public function requireDefaultRecords()
    {
        $features = FeatureFlag::allFeatures();
        foreach ($features as $feature) {
            $code = $feature['code'];
            if (self::get()->filter([ 'Code' => $code ])->count() === 0) {
                $selection = new FeatureSelection;
                $selection->Code = $code;
                $selection->write();
                DB::alteration_message('Adding feature "' . $code . '"', 'changed');
            }
        }
    }
}
