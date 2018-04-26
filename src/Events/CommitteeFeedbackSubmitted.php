<?php

namespace Baytek\Laravel\Content\Types\Committee\Events;

use Auth;

use App\ContentTypes\Committees\Models\Committee;
use App\ContentTypes\Members\Models\Member;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

class CommitteeFeedbackSubmitted
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Request $request, $committee)
    {
        // Get the committee the feedback form was submitted on and members to be notified for that committee
        $committee = content('committee/' . $committee, true, Committee::class);
        $notificationMembers = $committee->members()->where('notifications', '1')->get();

        // Add Joyel to get feedback, until they set up a group inbox
        $notificationMembers->push(new Member(['name' => 'Feedback', 'email' => 'feedback@rogc.com']));

        // Get the member who submitted the form
        $member = Member::find(Auth::user()->id)->load('restrictedMeta');

        $this->type = 'CommitteeFeedbackSubmitted';
        $this->title = $committee->title . ' Feedback Submission';
        $this->user = $notificationMembers;

        // Submit member info if the member requested that the submission is not anonymous
        $this->parameters = [
            'form_contents'  => $request,
            'committee_name' => $committee->title,
            'sender_name'  => ($request->is_anonymous) ? 'Anonymous' : $member->name,
            'sender_email' => ($request->is_anonymous) ? null : $member->metadata('display_email'),
            'sender_member_id'  => ($request->is_anonymous) ? null : $member->metadata('member_id'),
            'sender_home_phone' => ($request->is_anonymous) ? null : $member->metadata('home_phone'),
            'sender_work_phone' => ($request->is_anonymous) ? null : $member->metadata('work_phone'),
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
