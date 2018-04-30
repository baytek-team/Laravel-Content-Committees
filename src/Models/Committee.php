<?php

namespace Baytek\Laravel\Content\Types\Committee\Models;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Committee\Models\CommitteeMember;
use Baytek\Laravel\Content\Types\Committee\Scopes\ApprovedCommitteeScope;
use Baytek\Laravel\Content\Types\Committee\Scopes\CommitteeScope;
use Baytek\Laravel\Content\Types\Webpage\Webpage;

class Committee extends Content
{
    /**
     * Content keys that will be saved to the relation tables
     * @var Array
     */
    public $relationships = [
        'content-type' => 'committee'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        static::addGlobalScope(new CommitteeScope);
        static::addGlobalScope(new ApprovedCommitteeScope);
        parent::boot();
    }

    public function webpages()
    {
        return $this->kin(Webpage::class, 'webpage');
    }

    public function contentType()
    {
        return $this->kin(Content::class, 'content-type');
    }

    public function folders()
    {
        $this->kin(Folder::class, 'resource-category');
    }

    public function files()
    {
        $this->kin(File::class, 'file');
    }

    public function members()
    {
        return $this->belongsToMany(CommitteeMember::class, 'content_user', 'content_id', 'user_id')
            ->withPivot('title', 'admin', 'notifications', 'sorting')
            ->orderBy('sorting', 'asc');
    }
}
