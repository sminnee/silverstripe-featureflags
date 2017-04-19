<?php

class FeatureSelectionItem extends DataObject
{
    private static $db = [
        'ContextKey' => 'Varchar(50)',
        'ContextID' => 'Int',
    ];

    private static $has_one = [
        'FeatureSelection' => FeatureSelection::class,
    ];
}
