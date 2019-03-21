<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers\Api;

use Baytek\Laravel\Content\Types\Document\Requests\FolderRequest;
use Baytek\Laravel\Content\Controllers\ApiController;
use Baytek\Laravel\Content\Events\ContentEvent;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Committee\Scopes\ApprovedCommitteeScope;
use Baytek\Laravel\Content\Types\Committee\Scopes\CommitteeScope;
use Baytek\Laravel\Content\Types\Document\Models\File;
use Baytek\Laravel\Content\Types\Document\Models\Folder;
use Baytek\Laravel\Content\Types\Document\Scopes\ApprovedFolderScope;
use Baytek\Laravel\Content\Types\Committee\Models\CommitteeMember;
use Illuminate\Http\Request;

use App;
use Auth;
use Carbon\Carbon;
use Response;
use Validator;

class FolderController extends ApiController
{
    public function view($committee, $folderpath)
    {
        $path = config('committee.routes.key', 'committee').'/'.$committee.'/documents/'.$folderpath;

        // $folder = content("committee/$committee/$folder", true, Folder::class);

        $folder = null;
        $items = Content::withPath("committee/$committee/$folderpath");

        if (count($items) > 1) {
            foreach ($items as $item) {
                $parents = $item->getParents();

                if (isset($parents[3]) && $parents[3]->key == $committee) {
                    $folder = $item;
                    break;
                }
            }
        } else {
            $folder = $items->first();
        }

        if (!$folder) {
            return abort(404);
        }

        //See if user has permission to view the private files
        $member = CommitteeMember::find(\Auth::user()->id); //->load('meta')->load('committees')

        $permission = false;

        if ($member->hasRole('Root') || $member->hasRole('Administrator') || $member->hasRole('Manager')) {
            $permission = true;
        } else {
            foreach ($member->committees as $memberCommittee) {
                if ($memberCommittee->key == $committee || $memberCommittee->key == 'board-of-directors') {
                    $permission = true;
                    break;
                }
            }
        }


        $files = ($permission) ? Content::childrenOfType($folder->id, 'file')->withMeta(true)->withRestricted()->withStatus(File::APPROVED)->get() : collect([]);
        $folders = ($permission) ? Content::childrenOfType($folder->id, 'folder')->withStatus(Folder::APPROVED)->get() : collect([]);

        return [
            'folders' => $folders->each(function (&$self) use ($path) {
                $self->path = $path.'/'.$self->key;
            }),
            'files' => $files,
            'path' => $path,
            'parent' => content($folder->parent()),
            'title' => $folder->title,
            'id' => $folder->id,
            'permission' => $permission,
        ];
    }

    public function create(Request $request, $committee, $folder)
    {
        Validator::make(
            $request->all(),
            (new FolderRequest)->rules(),
            (new FolderRequest)->messages()
        )->validate();

        //Get the folder, or else set the folder to the committee, for root level folder creation
        $key = preg_replace('/\/*folder\/create$/', '', $folder);
        if ($key) {
            $folder = content("committee/$committee/" . $key, true, Folder::class);
        } else {
            $folder = content($committee, true, Committee::class);
        }

        $request->merge(['key' => str_slug($request->title)]);
        $request->merge(['language' => App::getLocale()]);

        //Save folder and relationships
        $newFolder = new Folder($request->all());
        $newFolder->save();
        $newFolder->saveMetadata('author_id', Auth::user()->id);
        $newFolder->saveRelation('parent-id', $folder->id);
        $newFolder->saveRelation('content-type', content('folder')->id);

        //Approve the folder
        $newFolder->onBit(Folder::APPROVED)->update();

        //ContentEvent required here, otherwise the parent id isn't properly accessible
        event(new ContentEvent($newFolder));

        //Add the path before returning the response
        $newFolder->path = '/committees/'.$committee.'/documents/';
        if ($key) {
            $newFolder->path .= $key.'/';
        }
        $newFolder->path .= $newFolder->key;
        $newFolder->parent = $newFolder->parent();

        return response()->json([
            'status' => 'success',
            'folder' => $newFolder,
        ]);
    }

    public function destroy(Request $request, $committee, $folder)
    {
        $folder = content("committee/$committee/". preg_replace('/\/*folder\/delete$/', '', $folder), true, Folder::class)->load(['relations', 'relations.relation', 'relations.relationType']);

        getChildrenAndDelete($folder);

        return response()->json([
            'status' => 'success',
            'message' => 'Document deleted!'
        ]);
    }
}
