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
}
