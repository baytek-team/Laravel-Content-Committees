<?php

namespace Baytek\Laravel\Content\Types\Committee\Events;

use Auth;

use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Committee\Models\CommitteeMember;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class CommitteeDocumentAdded
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, $committee, $file, $folderPath = '')
    {
        // Get the committee the feedback form was submitted on and members to be notified for that committee
        $committee = content('committee/' . $committee, true, Committee::class);

        // Get all committee members because if we are sending a notification, it will be sent to the whole committee
        $notificationMembers = $committee->members()->get();

        // If chosen to also notify the board of directors get those members
        if($request->notifyBoard) {
            $boardCommittee = content('committee/board-of-directors', true, Committee::class);
            $boardNotificationMembers = $boardCommittee->members()->get();
            // If there are some members add them into the collection of members to notify
            if(!empty($boardNotificationMembers)) {
                $notificationMembers = $notificationMembers->merge($boardNotificationMembers);
            }
        }

        // Get the member who submitted the form
        $member = CommitteeMember::find(Auth::user()->id);

        // The member who uploaded the document must also be notified?
        if (!$notificationMembers->contains($member)) {
            $notificationMembers->push($member);
        }
        
        // Build the committee, folder and file URL based off the known route - see API Routes for details
        // app/committees/{committee}
        $committee_url = sprintf('%1$s/committees/%2$s', route('app'), $committee->key);
        // app/committees/{committee}/documents/{folder?}
        $folder_url = sprintf('%1$s/documents/%2$s', $committee_url, $folderPath);
        // api/committees/{committee}/documents/{folder?}/{file}
        $file_url   = sprintf('%1$s/api/committees/%2$s/documents/%3$s/file/%4$s', config('app.url'), $committee->key, $folderPath, $file->key);

        $this->type = 'CommitteeDocumentAdded';
        $this->title = ($request->emailSubject) ?: $committee->title . ' Document Added';
        $this->user = $notificationMembers;

        // Submit member info if the member requested that the submission is not anonymous
        $this->parameters = [
            'form_contents'  => ($request->emailBody) ?: '',
            'committee_name' => $committee->title,
            'committee_url' => $committee_url,
            'folder_path'  => ($folderPath) ?: '/',
            'folder_url'   => $folder_url,
            'file_name'    => ($file->title) ?: 'Unknown Filename',
            'file_url'     => $file_url,
            'sender_name'  => $member->name,
            'sender_email' => $member->metadata('display_email'),
            'sender_member_id'  => $member->metadata('member_id'),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('rogc');
    }
}
