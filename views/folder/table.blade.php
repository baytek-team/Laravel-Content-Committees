<style>
        .resource-details {
            display: block;
            position: relative;
        }
        .resource-details .ui.progress {
            position:absolute;
            top: 0;
            left: 0;
            height: 3px;
            overflow: hidden;
            width: 100%;
            padding:0;
            margin:0;
        }
        .resource-details .ui.progress .bar {
            height: 3px;
            padding:0;
            margin:0;
        }
    </style>
    <table class="ui selectable very basic table">
        <thead>
            <tr>
                <th>{{ ___('Folder Name') }}</th>
            </tr>
        </thead>
        <tbody class="dropzone-preview">
            @foreach($folders as $item)
                <tr data-category-id="{{ $item->id }}">
                    <td class="resource-details">
                        <i class="folder icon"></i>
                        <a class="item" href="{{ route('committees.folders.show', [$committee, $item]) }}">
                            {{ $item->title }}
                        </a>
                        <div style="float: right">
                            <div class="ui compact text menu">
                                <a class="item" href="{{ route('committees.folders.edit', [$committee, $item]) }}">
                                    <i class="pencil icon"></i>
                                    {{ ___('Edit') }}
                                </a>
                                @button(___('Delete'), [
                                    'method' => 'delete',
                                    'location' => 'committees.folders.destroy',
                                    'type' => 'route',
                                    'confirm' => 'Are you sure you want to delete this folder?<br/>All subfolders and files will be deleted.</br>This cannot be undone.',
                                    'class' => 'item action',
                                    'prepend' => '<i class="delete icon"></i>',
                                    'model' => [$committee, $item],
                                ])
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            @foreach($files as $item)
                <tr data-category-id="{{ $item->id }}">
                    <td class="resource-details">
                        <i class="{{ \App\ContentTypes\Resources\Models\File::getIconCssClass($item->getMeta('original')) }}"></i>
                        <a class="item" href="{{ route('committees.file.download', [$committee, $item]) }}">
                            {{ $item->title }}
                        </a>
                        <div style="float: right">
                            <div class="ui compact text menu">
                                <a class="item" href="{{ route('committees.file.edit', [$committee, $item]) }}">
                                    <i class="pencil icon"></i>
                                    {{ ___('Edit') }}
                                </a>
                                @button(___('Delete'), [
                                    'method' => 'post',
                                    'location' => 'committees.file.delete',
                                    'type' => 'route',
                                    'confirm' => 'Are you sure you want to delete this file?',
                                    'class' => 'item action',
                                    'prepend' => '<i class="delete icon"></i>',
                                    'model' => [$committee, $item],
                                ])
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            @if(!$folders->count() && !$files->count())
                <tr>
                    <td colspan="3">
                        <div class="ui centered">{{ ___('There are no results') }}</div>
                    </td>
                </tr>
            @endif
        </tbody>
        <tfoot class="dropzone-template" style="display:none">
            <tr>
                <td class="resource-details">
                    <i class="file outline icon"></i>
                    <span class="uploading"><strong>Uploading: </strong></span>
                    <span class="dz-error-message" data-dz-errormessage></span>
                    <a class="file-name" data-dz-name data-href="{{ route('committees.file.download', [$committee, 1]) }}"></a>
                    {{-- <div class="item dz-size" data-dz-size></div> --}}

                    <div style="float: right">
                        <div class="ui compact text menu">
                            <a style="display: none" class="edit-button item" data-href="{{ route('committees.file.edit', [$committee, 1]) }}">
                                <i class="pencil icon"></i>
                                {{ ___('Edit') }}
                            </a>
                            <a class="item delete-button" data-dz-remove data-href="{{ route('committees.file.delete', [$committee, 1]) }}" >
                                <i class="delete icon"></i>
                                <span class="delete-text">{{ ___('Remove') }}</span>
                            </a>
                        </div>
                    </div>

                    <div class="ui active green progress">
                        <div class="bar" data-dz-uploadprogress>
                            <div class="progress"></div>
                        </div>
                        {{-- <div class="label">Uploading <span data-dz-name></span></div> --}}
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>