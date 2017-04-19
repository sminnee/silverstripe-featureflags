<?php

use SilverStripe\FeatureFlags\GridField\FeatureContextItem;

class FeatureFlagAdmin extends ModelAdmin
{
    private static $managed_models = [
        FeatureSelection::class
    ];

    private static $url_segment = 'featureflags';

    private static $menu_title = 'Feature Flags';

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        if ($gridField = $form->Fields()->dataFieldByName('FeatureSelection')) {
            $gridField->getConfig()
                ->getComponentByType(GridFieldDetailForm::class)
                ->setItemRequestClass(FeatureContextItem::class);
        }

        return $form;
    }
}
