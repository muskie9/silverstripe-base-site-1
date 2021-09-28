<?php

namespace Dynamic\Base\Extension;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;

/**
 * Class SEOMigrationExtension
 * @package Dynamic\Agency\Extension
 */
class SEOMigrationExtension extends DataExtension
{
    private static $db = [
        'OGTitleCustom' => 'Varchar(100)',
        'OGDescriptionCustom' => 'Varchar(150)',
    ];

    /**
     * @var string[]
     */
    private static $has_one = [
        'FacebookPageImage' => Image::class,
        'TwitterPageImage'  => Image::class,
        'OGImageCustom' => Image::class,
        'PinterestImageCustom' => Image::class,
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.Share',
            [
                HeaderField::create('LegacyOGHD', 'Legacy OpenGraph Image Fields'),
                LiteralField::create('LegacyOGTxt', '<p>For reference only to migrate images to the Share tab.</p>'),
                UploadField::create('FacebookPageImage', 'Image'),
                UploadField::create('TwitterPageImage', 'Image')
            ]
        );
    }
}
