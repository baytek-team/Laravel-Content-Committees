<?php

namespace Baytek\Laravel\Content\Types\Committee\Models;

use Baytek\Laravel\Users\Members\Models\Member;
use Baytek\Laravel\Users\Members\Scopes\ApprovedMemberScope;
use Baytek\Laravel\Users\Members\Scopes\MetadataScope;
use Baytek\Laravel\Users\User;

use Illuminate\Support\Facades\Request;

use Baytek\Laravel\StatusBit\Statusable;
use Baytek\Laravel\StatusBit\Interfaces\StatusInterface;

class CommitteeMember extends Member implements StatusInterface
{
    use Statusable;

    /**
     * A member can belong to many committees
     */
    public function committees()
    {
        return $this->belongsToMany(Committee::class, 'content_user', 'user_id', 'content_id')
            ->withPivot('title', 'admin', 'notifications', 'sorting');
    }
}
