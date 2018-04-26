<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers\Api;

use Baytek\Laravel\Content\Controllers\ApiController;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Committee\Events\CommitteeFeedbackSubmitted;
use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Committee\Scopes\ApprovedCommitteeScope;
use Baytek\Laravel\Content\Types\Committee\Scopes\CommitteeScope;
use Baytek\Laravel\Content\Types\Document\Models\File;
use Baytek\Laravel\Content\Types\Document\Models\Folder;
use Baytek\Laravel\Content\Types\Webpage\Webpage;
use Baytek\Laravel\Users\Members\Models\Member;
use Illuminate\Http\Request;

use App;
use Auth;
use Carbon\Carbon;
use Response;

class CommitteeController extends ApiController
{
	public function all()
    {
    	return Committee::withMeta()->get();
    }

    public function view($committee)
    {
    	$committee = content('committee/' . $committee)->load('meta');
		$committee->webpages = Content::childrenOfType($committee->id, 'webpage')->get();

    	return $committee;
    }

    public function members($committee)
    {
    	$committee = content('committee/' . $committee, true, Committee::class);

        return $committee->members()->with('committees')->get();

    }

    public function documents($committee)
    {
        $path = '/committees/'.$committee.'/documents';
    	$committee = content('committee/' . $committee);

        //See if user has permission to view the private files
        $member = Member::find(\Auth::user()->id); //->load('meta')->load('committees')

        $permission = false;

        if ($member->hasRole('Root') || $member->hasRole('Administrator') || $member->hasRole('Manager')) {
            $permission = true;
        }
        else {
            foreach ($member->committees as $memberCommittee) {
                if ($memberCommittee->key == $committee->key || $memberCommittee->key == 'board-of-directors') {
                    $permission = true;
                    break;
                }
            }
        }

        $files = ($permission) ? Content::childrenOfType($committee->id, 'file')->withMeta(true)->withRestricted()->withStatus('r', File::APPROVED)->get() : collect([]);
        $folders = ($permission) ? Content::childrenOfType($committee, 'resource-category')->withStatus('r', Folder::APPROVED)->get() : collect([]);

    	return [
    		'folders' => $folders->each(function(&$self) use ($path) {
                $self->path = $path.'/'.$self->key;
    		}),
    		'files' => $files,
            'path' => $path,
            'parent' => $committee,
            'title' => 'Documents',
            'id' => $committee->id,
            'permission' => $permission,
    	];
    }

    // This needs to be done because the other solution is to have the route renamed
    public function figureout(Request $request, $committee, $folder)
    {
        if(stripos($folder, 'folder/create') !== false) {
            return (new FolderController)->create($request, $committee, $folder);
        }
        else if(stripos($folder, 'file/upload') !== false) {
            return (new FileController)->create($request, $committee, $folder);
        }
        else if(stripos($folder, 'folder/delete') !== false) {
            return (new FolderController)->destroy($request, $committee, $folder);
        }
        else if(stripos($folder, 'file/delete') !== false) {
            return (new FileController)->destroy($request, $committee, $folder);
        }
        else if(stripos($folder, 'file/approve') !== false) {
            return (new FileController)->approve($request, $committee, $folder);
        }
        else if(stripos($folder, 'file/update') !== false) {
            return (new FileController)->update($request, $committee, $folder);
        }
    }

    /**
     * Handle feedback form submissions
     */
    public function feedback(Request $request, $committee)
    {
        // Notify the appropriate committee members of their feedback
        event(new CommitteeFeedbackSubmitted($request, $committee));

        return [
            'status' => 'success',
            'message' => 'Thank you for your feedback!',
        ];
    }

    public function blacklist()
    {
        // fifteen minutes
        ini_set('max_execution_time', 900);

        Content::descendentsOfType(content('content-type/committee', false), 'webpage')
            ->each(function(&$self) {
                if (!$self->hasStatus(Webpage::EXCLUDED))
                    $self->onBit(Webpage::EXCLUDED)->update();
            });

        Content::descendentsOfType(content('content-type/committee', false), 'file')
            ->each(function(&$self) {
                if (!$self->hasStatus(File::EXCLUDED))
                    $self->onBit(File::EXCLUDED)->update();
            });

        return 'Committee files and webpages pages blacklisted from search';
    }
}
