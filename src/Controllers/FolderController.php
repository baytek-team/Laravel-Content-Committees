<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers;

use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Document\Models\Folder;
use Baytek\Laravel\Content\Types\Document\Models\File;
use Baytek\Laravel\Content\Controllers\ContentController;
use Baytek\Laravel\Content\Events\ContentEvent;
use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Types\Document\Requests\FolderRequest;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Validator;
use View;

class FolderController extends ContentController
{
    /**
     * The model the Content Controller super class will use to access the committee
     *
     * @var App\ContentTypes\Events\Models\Event
     */
    protected $model = Folder::class;
    // protected $request = EventRequest::class;

    protected $viewPrefix = 'admin/committee';

    /**
     * List of views this content type uses
     * @var [type]
     */
    protected $views = [
        'index' => 'folder.index',
        'create' => 'folder.create',
        'edit' => 'folder.edit',
        'show' => 'folder.index'
    ];

    protected $redirectsKey = 'committee';

    /**
     * [__construct description]
     *
     * @return  null
     */
    public function __construct()
    {
        // $this->loadViewsFrom(resource_path().'/views', 'committee');

        parent::__construct();
    }

    /**
     * Show the index of the committee folder
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Committee $committee)
    {
        $folders = Content::childrenOfType($committee->id, 'folder')
            ->withRelationships()
            ->withRestricted()
            ->withStatus('r', Content::APPROVED)
            ->get();

        $files = Content::childrenOfType($committee->id, 'file')
            ->withRelationships()
            ->withRestricted()
            ->withStatus('r', Content::APPROVED)
            ->get(); 

        $this->viewData['index'] = [
            'current_category_id' => $committee->id,
            'folders' => $folders,
            'files' => $files,
            'committee' => $committee,
        ];

        return parent::contentIndex();
    }

    /**
     * Show the form for creating a new folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Committee $committee, $id = null)
    {
        $this->viewData['create'] = [
            'committee' => $committee,
            'folder' => new Folder,
            'parent' => is_null($id) ? $committee : Folder::find($id),
            'parents' => [],
        ];

        return parent::contentCreate();
    }

    /**
     * Store a new folder in the database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FolderRequest $request, Committee $committee)
    {
        $this->redirects = false;

        $request->merge(['key' => str_slug($request->title)]);

        $document = parent::contentStore($request);
        $document->saveRelation('parent-id', $request->parent_id);

        $document->onBit(Folder::APPROVED)->update();

        //ContentEvent required here, otherwise the parent id isn't properly accessible
        event(new ContentEvent($document));

        if ($request->parent_id == $committee->id) {
            return redirect(route('committees.folders.index', $committee));
        }
        else {
            return redirect(route('committees.folders.show', [$committee, $request->parent_id]));
        }
    }

    /**
     * Show the form for updating an existing folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Committee $committee, Folder $folder)
    {
        $this->viewData['edit'] = [
            'committee' => $committee,
            'folder' => $folder,
            'parent' => content($folder->parent()),
            'parents' => [],
        ];

        return parent::contentEdit($folder);
    }

    /**
     * Show the form for updating an existing folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function editParent(Committee $committee, Folder $folder)
    {
        $parents = Content::descendentsOfType($committee->id, 'folder');

        $this->viewData['edit'] = [
            'committee' => $committee,
            'folder' => $folder,
            'parent' => content($folder->parent()),
            'parents' => Content::hierarchy($parents, false),
            'disabledFlag' => false,
            'disabledDepth' => 0,
        ];

        return parent::contentEdit($folder);
    }

    /**
     * Update an existing folder
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(FolderRequest $request, Committee $committee, Folder $folder)
    {
        $this->redirects = false;

        $request->merge(['key' => str_slug($request->title)]);

        $document = parent::contentUpdate($request, $folder);

        //Update parent
        $document->removeRelationByType('parent-id');
        $document->saveRelation('parent-id', $request->parent_id ?: $committee->id);

        //ContentEvent required here, otherwise the parent id isn't properly accessible
        event(new ContentEvent($document));

        if ($request->parent_id == $committee->id) {
            return redirect(route('committees.folders.index', $committee));
        }
        else {
            return redirect(route('committees.folders.show', [$committee, $request->parent_id]));
        }
    }

    /**
     * Show the folder's files and folders?
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Committee $committee, Folder $folder)
    {
        $folders = Content::childrenOfType($folder->id, 'folder')
            ->withRelationships()
            ->withRestricted()
            ->withStatus('r', Content::APPROVED)
            ->get();

        $files = Content::childrenOfType($folder->id, 'file')
            ->withRelationships()
            ->withRestricted()
            ->withStatus('r', Content::APPROVED)
            ->get(); 

        $this->viewData['show'] = [
            'current_category_id' => $folder->id,
            'committee' => $committee,
            'folders' => $folders,
            'files' => $files,
            'folder' => $folder,
        ];

        return parent::contentShow($folder);
    }

    /**
     * Delete a committee folder and all its subfolders/files
     */
    public function destroy(Request $request, Committee $committee, Folder $folder)
    {
        $folder->load(['relations', 'relations.relation', 'relations.relationType']);

        $parent = content($folder->parent());
        $parent->load(['relations', 'relations.relation', 'relations.relationType']);

        getChildrenAndDelete($folder);

        //ContentEvent required here, otherwise the parent id isn't properly accessible
        event(new ContentEvent($folder));

        if ($parent && $parent->relationships()->get('content_type') == 'folder') {
            return redirect(route('committees.folders.show', [$committee, $parent]));
        }
        else {
            return redirect(route('committees.folders.index', $committee));
        }
    }

}