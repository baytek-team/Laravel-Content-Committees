<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers;

use Baytek\Laravel\Content\Controllers\ApiController;
use Baytek\Laravel\Content\Controllers\ContentController;
use Baytek\Laravel\Content\Events\ContentEvent;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Committee\Models\CommitteeMember;
use Baytek\Laravel\Users\Roles\Member;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Validator;
use View;

class MemberController extends ApiController
{
    /**
     * The model the Content Controller super class will use to access the committee
     *
     * @var App\ContentTypes\Events\Models\Event
     */
    protected $model = CommitteeMember::class;

    /**
     * Show the index of all content with content type 'committee'
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Committee $committee)
    {
        return response()
            ->view('committees::members.index', [
                'committee' => $committee,
                'members' => $committee->members()->paginate(),
            ], 200);
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Committee $committee)
    {
        return response()
            ->view('committees::members.create', [
                'committee' => $committee,
                'members' => Member::all()->whereNotIn('id', $committee->members->pluck('id')),
                'member' => new CommitteeMember,
                'pivot' => (object) ['admin' => '', 'title' => '', 'notifications' => '']
            ], 200);
        // return parent::contentCreate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Committee $committee, Request $request)
    {
        $member = CommitteeMember::find($request->member);

        $committee->members()->save($member, [
            'title' => $request->title ?: '',
            'admin' => isset($request->admin) ? 1: 0,
            'notifications' => isset($request->notify) ? 1: 0,
            'sorting' => isset($request->sorting) ? $request->sorting : 0,
        ]);

        flash('Member successfully added.');

        return redirect(route('committees.members.edit', [$committee, $member]));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Committee $committee, CommitteeMember $member)
    {
        return response()
            ->view('committees::members.edit', [
                'committee' => $committee,
                'members' => Member::all()->whereNotIn('id', $committee->members->pluck('id')),
                'member' => $member,
                'pivot' => $committee->members->find($member)->pivot
            ], 200);
    }

    /**
     * Update a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Committee $committee, CommitteeMember $member, Request $request)
    {
        $committee->members()->syncWithoutDetaching([$member->id => [
            'title' => $request->title ?: '',
            'admin' => isset($request->admin) ? 1: 0,
            'notifications' => isset($request->notify) ? 1: 0,
            'sorting' => isset($request->sorting) ? $request->sorting : 0,
        ]]);

        flash('Member successfully updated.');

        return redirect(route('committees.members.edit', [$committee, $member]));
    }

    /**
     * Update a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Committee $committee, CommitteeMember $member, Request $request)
    {
        $committee->members()->detach($member->id);

        flash('Member successfully removed.');

        return redirect(route('committees.members.index', $committee));
    }
}
