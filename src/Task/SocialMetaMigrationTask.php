<?php

namespace Dynamic\Base\Task;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;
use SilverStripe\Versioned\Versioned;

/**
 *
 */
class OGImageMigrationTask extends BuildTask
{
    const SILVERSTRIPE_SEO = [
        'FacebookPageImageID' => 'MetaImageID',
    ];

    const SHARE_CARE = [
        'OGImageCustom' => 'MetaImageID',
    ];

    /**
     * Set a custom url segment (to follow dev/tasks/)
     *
     * @config
     * @var string
     */
    private static $segment = 'og-image-migration-task';

    /**
     * @var bool
     */
    private static $primary_share_care = true;

    /**
     * @var bool $enabled If set to FALSE, keep it from showing in the list
     * and from being executable through URL or CLI.
     */
    protected $enabled = true;

    /**
     * @var string $title Shown in the overview on the {@link TaskRunner}
     * HTML or CLI interface. Should be short and concise, no HTML allowed.
     */
    protected $title = 'Base Site - OG Image Migration task';

    /**
     * @var string $description Describe the implications the task has,
     * and the changes it makes. Accepts HTML formatting.
     */
    protected $description = 'Migrates legacy OG image relations to the new fields';

    /**
     * @param HTTPRequest $request
     */
    public function run($request)
    {
        if ($mapping = $this->getFieldMapping()) {
            foreach ($this->getPages() as $page) {
                $publish = $page->isPublished();

                foreach ($mapping as $legacyField => $newField) {
                    $page->{$newField} = $page->{$legacyField};
                }

                $page->writeToStage(Versioned::DRAFT);

                if ($publish) {
                    $page->publishSingle();
                }
            }
        }
    }

    /**
     * @return mixed
     */
    protected function getFieldMapping()
    {
        $mapping = $this->config()->get('primary_share_care')
            ? static::SHARE_CARE
            : static::SILVERSTRIPE_SEO;

        $this->extend('updateSocialFieldMapping', $mapping);

        return $mapping;
    }

    /**
     * @return \Generator
     */
    protected function getPages()
    {
        $filterZero = $this->config()->get('primary_share_care')
            ? [
                'OGImageCustomID:GreaterThan' => 0,
                'MetaImageID' => 0,
            ]
            : [
                'FacebookPageImageID:GreaterThan' => 0,
                'MetaImageID' => 0,
            ];

        $this->extend('updateFilterZero', $filterZero);

        $pages1 = \Page::get()
            ->filter($filterZero)->column();

        $filterNull = $this->config()->get('primary_share_care')
            ? [
                'OGTitleCustom:not' => [null, ''],
                'MetaTitle' => [null, ''],
            ]
            : [
                'FacebookPageTitle:not' => [null, ''],
                'MetaTitle' => [null, ''],
            ];

        $this->extend('updateFilterNull', $filterNull);

        $pages2 = \Page::get()->filter($filterNull)->column();

        if (!count($pages1) && count($pages2)) {
            $pagesID = $pages2;
        } elseif (!count($pages2) && count($pages1)) {
            $pagesID = $pages1;
        } elseif (!count($pages1) && !count($pages2)) {
            $pagesID = [];
        } else {
            $pagesID = array_intersect($pages1, $pages2);
        }

        if (!count($pagesID)) {
            return [];
        }

        $pages = \Page::get()->byIDs($pagesID);

        foreach ($pages as $page) {
            yield $page;
        }
    }
}
