@extends('committees::template')
@include('committees::folder.dropzone', ['resource_id' => $committee->id])

@section('outer-content')
    <div class="ui divider"></div>
    <div class="ui hidden divider"></div>

    <div class="ui two column grid">
        <div class="column">
            <h2 class="ui header">
                <i class="user icon"></i>
                <div class="content">
                    @link(___('Members'), [
                        'location' => 'committees.members.index',
                        'type' => 'route',
                        'class' => 'item',
                        'model' => [
                            'committee' => $committee
                        ]
                    ])
                    <div class="sub header">{{ ___('Manage the users in the committee') }}</div>
                </div>
            </h2>
            <div class="ui hidden divider"></div>
            @if($webpages->isNotEmpty())
                <div class="ui cards">
                    {{-- @include('admin.resources.folder.table') --}}
                </div>
            @else
                {{ ___('There are no new submitted resources to translate and moderate.') }}
            @endif
        </div>
        <div class="column">
            <h2 class="ui header">
                <i class="globe icon"></i>
                <div class="content">
                    @link(___('Webpages'), [
                        'location' => 'committees.webpages.index',
                        'type' => 'route',
                        'class' => 'item',
                        'model' => [
                            'committee' => $committee
                        ]
                    ])
                    <div class="sub header">{{ ___('Manage the webpages of the committee') }}</div>
                </div>
            </h2>
            <div class="ui hidden divider"></div>
            @if($webpages->isNotEmpty())
                <table class="ui very basic compact selectable table">
                    <tbody>
                        {{-- @include('admin.resources.folder.table') --}}
                    </tbody>
                </table>
            @else
                {{ ___('There are no new suggested keywords to moderate.') }}
            @endif

        </div>
    </div>

    <div class="ui hidden divider"></div>
    <div class="ui divider"></div>
    <div class="ui hidden divider"></div>

    <div class="ui one column grid">
        <div class="column">
            <h2 class="ui header">
                <i class="file text icon"></i>
                <div class="content">
                    @link(___('Documents'), [
                        'location' => 'committees.folders.index',
                        'type' => 'route',
                        'class' => 'item',
                        'model' => [
                            'committee' => $committee
                        ]
                    ])
                    <div class="sub header">{{ ___('Manage the documents of the committee') }}</div>
                </div>
            </h2>
            <div class="ui hidden divider"></div>
            <div class="ui small feed">

            </div>
        </div>
    </div>
@endsection

@section('content')
@include('committees::folder.table')
@endsection