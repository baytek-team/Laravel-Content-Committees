@extends('committees::template')

@section('page.head.menu')
    <div class="ui secondary contextual menu">
        @if(Auth::user()->can('Create Committee'))
            <a class="item" href="{{ route('committees.create') }}">
                <i class="add icon"></i>{{ ___('Add Committee') }}
            </a>
        @endif
    </div>
@endsection

@section('content')
<table class="ui selectable very basic table">
    <thead>
        <tr>
            <th>{{ ___('Committee Title') }}</th>
            <th class="center aligned collapsing">{{ ___('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($committees as $committee)
            <tr class="nine wide" data-committee-id="{{ $committee->id }}">
                <td>
                    <a class="item" href="{{ route('committees.show', $committee->id) }}">
                        {{ str_limit($committee->title, 100) }}
                    </a>
                </td>
                <td class="right aligned collapsing">
                    <div class="ui compact text menu">
                        <a class="item" href="{{ route('committees.edit', $committee->id) }}">
                            <i class="edit icon"></i>
                            {{ ___('Edit') }}
                        </a>
                        @button(___('Delete'), [
                            'method' => 'delete',
                            'location' => 'committees.destroy',
                            'type' => 'route',
                            'confirm' => 'Are you sure you want to delete this committee?<br/>All committee webpages, folders and files will be deleted.</br>This cannot be undone.',
                            'class' => 'item action',
                            'prepend' => '<i class="delete icon"></i>',
                            'model' => $committee,
                        ])
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3">
                    <div class="ui centered">{{ ___('There are no results') }}</div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $committees->links('pagination.default') }}

@endsection