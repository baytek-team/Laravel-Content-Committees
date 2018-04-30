<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers;

use Baytek\Laravel\Content\Controllers\ContentController;
use Baytek\Laravel\Content\Events\ContentEvent;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Committee\Requests\WebpageRequest;
use Baytek\Laravel\Content\Types\Webpage\Webpage;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;
use View;

class WebpageController extends ContentController
{
    /**
     * The model the Content Controller super class will use to access the committee
     *
     * @var App\ContentTypes\Webpages\Models\Webpage
     */
    protected $model = Webpage::class;
    protected $request = WebpageRequest::class;

    protected $viewPrefix = 'admin/committee';

    /**
     * List of views this content type uses
     * @var [type]
     */
    protected $views = [
        'index' => 'webpage.index',
        'create' => 'webpage.create',
        'edit' => 'webpage.edit',
        'show' => 'webpage.show',
    ];

    protected $redirectsKey = 'committee';

    /**
     * Show the index of all content with content type 'committee'
     *
     * @param  \App\ContentTypes\Committees\Models\Committee $committee
     * @return \Illuminate\Http\Response
     */
    public function index(Committee $committee)
    {
        $this->viewData['index'] = [
            'committee' => $committee,
            // 'webpages' => $committee->webpages()->paginate(),
            'webpages' => Content::childrenOfType($committee->id, 'webpage')->withStatus(Webpage::APPROVED)->paginate(),
        ];

        return parent::contentIndex();
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @param  \App\ContentTypes\Committees\Models\Committee $committee
     * @return \Illuminate\Http\Response
     */
    public function create(Committee $committee)
    {
        $this->viewData['create'] = [
            'committee' => $committee,
        ];

        return parent::contentCreate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\ContentTypes\Committees\Requests\WebpageRequest  $request
     * @param  \App\ContentTypes\Committees\Models\Committee $committee
     * @return \Illuminate\Http\Response
     */
    public function store(WebpageRequest $request, Committee $committee)
    {
        Validator::make(
            $request->all(),
            (new $this->request)->rules()
        )->validate();

        $this->redirects = false;

        $request->merge([
            'key' => str_slug($request->title),
        ]);

        $webpage = parent::contentStore($request);
        $webpage->saveRelation('parent-id', $committee->id);
        $webpage->onBit(Webpage::APPROVED)->onBit(WebPage::EXCLUDED)->update();

        event(new ContentEvent($webpage));

        return redirect(route('committee.webpages.edit', [$committee, $webpage]));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @param  \App\ContentTypes\Committees\Models\Committee $committee
     * @return \Illuminate\Http\Response
     */
    public function edit(Committee $committee, Webpage $webpage)
    {
        $this->viewData['edit'] = [
            'committee' => $committee,
        ];

        return parent::contentEdit($webpage);
    }

    /**
     * Update a newly created event in storage.
     *
     * @param  \App\ContentTypes\Committees\Requests\WebpageRequest  $request
     * @param  \App\ContentTypes\Committees\Models\Committee $committee
     * @return \Illuminate\Http\Response
     */
    public function update(WebpageRequest $request, Committee $committee, Webpage $webpage)
    {
        $result = Validator::make(
            $request->all(),
            (new $this->request)->rules()
        )->validate();

        $this->redirects = false;

        $request->merge(['key' => str_slug($request->title)]);

        $webpage = parent::contentUpdate($request, $webpage->id);

        return redirect(route('committee.webpages.edit', [$committee, $webpage]));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @param  \App\ContentTypes\Committees\Models\Committee $committee
     * @return \Illuminate\Http\Response
     */
    public function show(Committee $committee, Webpage $webpage)
    {
        $categories = Content::childrenOf($webpage->id)
            ->with(['relations', 'relations.relation', 'relations.relationType'])
            ->get()
            ->sortBy(function ($category) {
                return $category->relationships()->get('content_type') != 'folder';
            });

        $this->viewData['show'] = [
            'categories' => $categories,
        ];

        return parent::contentShow($webpage->id);
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @param  \App\ContentTypes\Committees\Models\Committee $committee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Committee $committee, Webpage $webpage)
    {
        getChildrenAndDelete($webpage->load(['relations', 'relations.relation', 'relations.relationType']));

        event(new ContentEvent($webpage));

        return redirect(route('committee.webpages.index', $committee));
    }
}
