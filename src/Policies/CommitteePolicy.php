<?php

namespace Baytek\Laravel\Content\Types\Committee\Policies;

use Baytek\Laravel\Content\Policies\GeneralPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommitteePolicy extends GeneralPolicy
{
    public $contentType = 'Committee';
}
