<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers\Api;

use Baytek\Laravel\Content\Types\Webpage\Webpage;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Webpage\Controllers\Api\WebpageController as ParentController;

use Auth;

class WebpageController extends ParentController
{
    public function categories($category)
    {
        $page = content($category);

        $page->path = $page->getMeta('path');

        $page->children = Webpage::withoutGlobalScopes()->with([
                    'relations',
                    'relations.relation',
                    'relations.relationType',
                    'meta'
                ])
            ->childrenOfType($page->id, 'webpage')
            ->withStatus(Webpage::APPROVED)
            ->get();

        $page->resources = Content::childrenOfType($page->id, 'file')
            ->withStatus(Content::APPROVED)
            ->withMeta()
            ->get();

        //Set the path for the children
        if (count($page->children)) {
            foreach ($page->children as $key => $child) {
                $page->children[$key]->path = $page->children[$key]->getMeta('path');
            }
        }

        //Set the path for the page
        if (!isset($page->path)) {
            $page->path = $page->getMeta('path');
        }

        $page->parent = content($page->parent());

        return $page;
    }
}