<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers\Api;

use Baytek\Laravel\Content\Controllers\ApiController;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Committee\Events\CommitteeDocumentAdded;
use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Committee\Scopes\ApprovedCommitteeScope;
use Baytek\Laravel\Content\Types\Committee\Scopes\CommitteeScope;
use Baytek\Laravel\Content\Types\Document\Models\File;
use Baytek\Laravel\Content\Types\Document\Models\Folder;
use Baytek\Laravel\Content\Types\Committee\Models\CommitteeMember;
use Illuminate\Http\Request;

use App;
use Auth;
use Carbon\Carbon;
use Response;
use Storage;
use File as FS;

class FileController extends ApiController
{
    public function view($committee, $folder, $file)
    {
    	$folder = preg_replace('/\/*file$/', '', $folder);
        
        $path = ($folder) ? "committee/{$committee}/{$folder}/{$file}" : "committee/{$committee}/{$file}";
    	$file = (new File)->getWithPath($path)->first()->load('meta');

        $allowDownload = true;

        //If the file is restricted, make sure you have permission
        if ($file->hasStatus(File::RESTRICTED)) {

            $allowDownload = false;

            //See if user has permission to view the private files
            $member = CommitteeMember::find(\Auth::user()->id); //->load('meta')->load('committees')

            if ($member->hasRole('Root') || $member->hasRole('Administrator') || $member->hasRole('Manager')) {
                $allowDownload = true;
            }
            else {
                foreach ($member->committees as $memberCommittee) {
                    if ($memberCommittee->key == $committee || $memberCommittee->key == 'board-of-directors') {
                        $allowDownload = true;
                        break;
                    }
                }
            }
        }

        if ($allowDownload) {
            return Response::download(storage_path('app/' . $file->metadata('file')), $file->metadata('original'));
        }
        else {
            return abort(403);
        }
    }

    public function create(Request $request, $committee, $folder)
    {
        //Get the folder, or else set the folder to the committee, for root level folder creation
        $key = preg_replace('/\/*file\/upload$/', '', $folder);
        if ($key) {
           $folder = content("committee/$committee/" . $key, true, Folder::class);
        } else {
            $folder = content($committee, true, Committee::class);
        }

        $uploaded = $request->file('file');
        $originalName = $uploaded->getClientOriginalName();

        $path = $uploaded->store('resources');

        $file = new File([
            'key' => str_slug($originalName) . '_' . date('Y-m-d_H-i-s'),
            'language' => 'en',
            'title' => $originalName,
            'content' => ''
        ]);

        $file->save();

        $file->saveRelation('content-type', $file->getContentIdByKey('file'));
        $file->saveMetadata('file', $path);
        $file->saveMetadata('original', $originalName);
        $file->saveMetadata('size', FS::size($uploaded));
        $file->saveMetadata('mime', FS::mimeType($uploaded));

        if(!is_null($folder)) { // Check to see if the folder is empty if it is, we need to use the category id
            $file->saveRelation('parent-id', $folder->id);
        }

        return $file->load('meta');
    }

    public function approve(Request $request, $committee, $folder)
    {
        $file = content("committee/$committee/" . preg_replace('/\/*file\/approve$/', '', $folder), true, File::class);

        if ($request->title) {
            $file->title = $request->title;
        }

        //Add restricted status if only viewable by committee members
        if (!$request->viewableByAllMembers) {
            $file->onBit(File::RESTRICTED)->update();
        }

        /**
         * Handle notifications - only send a notification if the option is checked
         */
        if ($request->emailNotification) {
            // Notify the appropriate committee members that a document was added
            $folderPath = preg_replace('/\/*file\/approve$/', '', $folder);
            $folderPath = str_replace($file->key, '', $folderPath);
            event(new CommitteeDocumentAdded($request, $committee, $file, $folderPath));
        }

        $file->onBit(File::APPROVED)->onBit(File::EXCLUDED)->update();

        return response()->json([
            'status' => 'success',
            'file' => $file->load('meta'),
        ]);
    }

    public function update(Request $request, $committee, $folder)
    {
        //Step 1: Update the original file record with the new file metadata
        $file = File::withRestricted()->find($request->originalFile);

        if ($request->file && $request->file != $request->originalFile) {
            $replacement = File::withRestricted()->find($request->file);

            $file->saveMetadata('file', $replacement->getMeta('file'));
            $file->saveMetadata('original', $replacement->getMeta('original'));
            $file->saveMetadata('size', $replacement->getMeta('size'));
            $file->saveMetadata('mime', $replacement->getMeta('mime'));
        }

        if ($request->title) {
            $file->title = $request->title;
            $file->update();
        }

        //Step 2: Send notification optionally
        if ($request->emailNotification) {
            // Notify the appropriate committee members that a document was added
            $folderPath = preg_replace('/\/*file\/approve$/', '', $folder);
            $folderPath = str_replace($file->key, '', $folderPath);
            event(new CommitteeDocumentAdded($request, $committee, $file, $folderPath));
        }

        //Step 3: Send the success response
        return response()->json([
            'status' => 'success',
            'file' => $file->load('meta'),
        ]);
    }

    public function destroy(Request $request, $committee, $folder)
    {
        $file = content("committee/$committee/" . preg_replace('/\/file\/delete$/', '', $folder), true, File::class);

        $file->offBit(File::APPROVED)->onBit(File::DELETED)->update();
        Storage::delete($file->getMeta('file'));
        $file->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

}
