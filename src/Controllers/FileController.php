<?php

namespace Baytek\Laravel\Content\Types\Committee\Controllers;

use Baytek\Laravel\Content\Types\Committee\Models\Committee;
use Baytek\Laravel\Content\Types\Document\Models\Folder;
use Baytek\Laravel\Content\Types\Document\Models\File;

use Baytek\Laravel\Content\Models\Content;
use Baytek\Laravel\Content\Controllers\ContentController;
use Baytek\Laravel\Content\Events\ContentEvent;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Validator;
use View;
use File as FS;
use Response;

class FileController extends ContentController
{
    /**
     * The model the Content Controller super class will use to access the committee
     *
     * @var App\ContentTypes\Events\Models\Event
     */
    protected $model = File::class;

    protected $viewNamespace = 'committees';

    /**
     * List of views this content type uses
     * @var [type]
     */
    protected $views = [
        'edit' => 'file.edit',
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
     * Download the file from the backend
     */
    public function download(Committee $committee, $file)
    {
        $file = $this->bound($file);
        $file->load('meta');

        return Response::download(storage_path('app/' . $file->metadata('file')), $file->metadata('original'));
    }

    /**
     * Show the form for creating a new webpage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id = null)
    {
        $this->redirects = false;

        $uploaded = $request->file('file');
        $originalName = $uploaded->getClientOriginalName();

        $path = $uploaded->store('resources');

        $file = new File([
            'key' => str_slug($originalName) . '_' . date('Y-m-d_H-i-s'),
            'language' => $request->language,
            'title' => $originalName,
            'content' => '',
        ]);

        $file->save();

        $file->saveRelation('content-type', $file->getContentIdByKey('file'));
        $file->saveMetadata('file', $path);
        $file->saveMetadata('original', $originalName);
        $file->saveMetadata('size', FS::size($uploaded));
        $file->saveMetadata('mime', FS::mimeType($uploaded));

        if(!is_null($id)) {
            $file->saveRelation('parent-id', $id);
        }

        $file->onBit(File::APPROVED)->onBit(File::RESTRICTED)->onBit(File::EXCLUDED)->update();

        //ContentEvent required here, otherwise the parent id isn't properly accessible
        event(new ContentEvent($file));

        return $file;
    }

    /**
     * Show the form for editing a file.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Committee $committee, $file)
    {
        $file = $this->bound($file);

        $this->viewData['edit'] = [
            'committee' => $committee,
            'file' => $file,
            'isNotRestricted' => ($file->hasStatus(File::RESTRICTED)) ? false : true,
        ];

        return parent::contentEdit($file);
    }

    /**
     * Update a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Committee $committee, $file)
    {
        $file = $this->bound($file);

        $file->update($request->all());

        if ($file->hasStatus(File::RESTRICTED) && $request->isNotRestricted) {
            $file->offBit(File::RESTRICTED)->update();
        }
        else if (!$file->hasStatus(File::RESTRICTED) && !$request->isNotRestricted) {
            $file->onBit(File::RESTRICTED)->update();
        }

        //ContentEvent required here, otherwise the parent id isn't properly accessible
        event(new ContentEvent($file));

        if ($file->parent() != $committee->id) {
            return redirect(route('committees.folders.show', [$committee, $file->parent()]));
        }
        else {
            return redirect(route('committees.folders.index', $committee));
        }
    }

    /**
     * Remove a file from storage and set its status to deleted
     */
    public function delete(Request $request, Committee $committee, $file)
    {
        $file = $this->bound($file);

        $file->offBit(File::APPROVED)->onBit(File::DELETED)->update();
        \Storage::delete($file->getMeta('file'));
        $file->delete();

        //ContentEvent required here, otherwise the parent id isn't properly accessible
        event(new ContentEvent($file));

        return back();
    }

}