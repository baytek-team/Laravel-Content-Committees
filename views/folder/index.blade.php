@extends('committees::folder.template')
@include('committees::folder.dropzone', ['resource_id' => $current_category_id])

@section('page.head.menu')
    <div class="ui secondary menu">
        {{--  href="{{ route('resource.folder.resource.create', $current_category_id) }}" --}}
        <a class="item dz-clickable">
            <i class="file text icon"></i>
            {{ ___('Add File') }}
        </a>
        @if(!isset($folder))
            @link(___('Add Folder'), [
                'location' => 'committees.folders.create',
                'type' => 'route',
                'class' => 'item',
                'prepend' => '<i class="folder icon"></i>',
                'model' => [
                    'committee' => $committee,

                ]
            ])
        @else
            @link(___('Add Folder'), [
                'location' => 'committees.folders.create.child',
                'type' => 'route',
                'class' => 'item',
                'prepend' => '<i class="folder icon"></i>',
                'model' => [
                    'committee' => $committee,
                    'folder' => $folder
                ]
            ])
        @endif
    </div>
@endsection

@section('content')
    @include('committees::folder.table')

    <div id="upload-dimmer" class="ui page dimmer">
        <div class="content">
            <div class="center">
                <h2 class="ui inverted icon header">
                    <i class="cloud upload icon"></i>
                    Drop to upload your file
                    <div class="sub header">The file will start uploading automatically.</div>
                </h2>
            </div>
        </div>
    </div>

    {{-- {{ $categories->links('pagination.default') }} --}}
@endsection