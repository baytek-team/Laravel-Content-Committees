<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers;

use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Committee\Requests\CommitteeRequest;
use Baytek\Laravel\Content\Types\Document\Models\File;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Controllers\ContentController;
use Baytek\Laravel\Content\Events\ContentEvent;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Validator;
use View;

class CommitteeController extends ContentController
{
    /**
     * The model the Content Controller super class will use to access the committee
     *
     * @var App\ContentTypes\Events\Models\Event
     */
    protected $model = Committee::class;
    protected $request = CommitteeRequest::class;

    protected $viewPrefix = 'admin';

    /**
     * List of views this content type uses
     * @var [type]
     */
    protected $views = [
        'index' => 'index',
        'create' => 'create',
        'edit' => 'edit',
        'show' => 'show'
    ];

    protected $redirectsKey = 'committee';

    /**
     * Show the index of all content with content type 'committee'
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->viewData['index'] = [
            'committees' => Committee::withMeta()->paginate(),
        ];

        return parent::contentIndex();
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return parent::contentCreate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make(
            $request->all(),
            (new $this->request)->rules(),
            (new $this->request)->messages()
        )->validate();

        $this->redirects = false;

        $request->merge(['key' => str_slug($request->title)]);

        $committee = parent::contentStore($request);
        $committee->saveRelation('parent-id', content('committee')->id);
        $committee->saveMetadata('type', $request->type);
        $committee->onBit(Committee::APPROVED)->update();

        //ContentEvent required here, otherwise the parent id isn't properly accessible
        event(new ContentEvent($committee));

        return redirect(route('committees.show', $committee->id));
    }

    /**
     * Update a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::make(
            $request->all(),
            (new $this->request)->rules(),
            (new $this->request)->messages()
        )->validate();

        $this->redirects = false;

        $request->merge(['key' => str_slug($request->title)]);

        $committee = parent::contentUpdate($request, $id);
        $committee->saveMetadata('type', $request->type);

        event(new ContentEvent($committee));

        return redirect(route('committees.show', $committee->id));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Committee $committee)
    {
        $folders = Content::childrenOfType($committee->id, 'folder')
            ->withRelationships()
            ->withRestricted()
            ->withStatus(Content::APPROVED)
            ->get();

        $files = Content::childrenOfType($committee->id, 'file')
            ->withRelationships()
            ->withRestricted()
            ->withStatus(Content::APPROVED)
            ->get();

        $this->viewData['show'] = [
            'folders' => $folders,
            'files' => $files,
            'current_category_id' => $committee->id,
            'members' => $committee->members(),
            'webpages' => Content::childrenOfType($committee->id, 'webpage')->paginate(),
        ];

        return parent::contentShow($committee);
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $committee = $this->bound($id)->load(['meta']);

        $this->viewData['edit'] = [
            'type' => $committee->getMeta('type'),
        ];

        return parent::contentEdit($id);
    }

    /**
     * Edit the translation of a event
     * @param  $id  Content Id for the event translation being edited
     * @return \Illuminate\Http\Response
     */
    public function editTranslation($id)
    {
        $committee = $this->bound($id);

        return view('admin.events.committees.translate', [
            'event' => $committee
        ]);
    }

    public function destroy(Committee $committee)
    {
        $committee->members()->sync([]);

        getChildrenAndDelete($committee->load(['relations', 'relations.relation', 'relations.relationType']));

        event(new ContentEvent($committee));

        return redirect(route('committees.index'));
    }
}
