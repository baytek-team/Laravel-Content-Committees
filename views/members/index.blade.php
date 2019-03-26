@extends('committees::members.template')

@section('page.head.menu')
    <div class="ui secondary contextual menu">
        @link(__('Add Member'), [
            'location' => 'committees.members.create',
            'model' => [
                'committee' => $committee
            ],
            'prepend' => '<i class="user add icon"></i>',
            'type' => 'route',
        ])
    </div>
@endsection

@section('content')
<table class="ui selectable very basic table">
    <thead>
        <tr>
            <th>{{ ___('Name') }}</th>
            <th>{{ ___('Email') }}</th>
            <th class="center aligned collapsing">{{ ___('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($members as $member)
            <tr data-member-id="{{ $member->id }}">
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
                <td class="collapsing right aligned">
                    <div class="ui text compact menu">
                        @can('update', $member)
                        <a href="{{ route('committees.members.edit', ['committee' => $committee, 'member' => $member]) }}" class="item">
                            <i class="edit icon"></i> {{ ___('Edit') }}
                        </a>
                        @endcan

                        @link(___('Delete'), [
                            'method' => 'delete',
                            'location' => 'committees.members.destroy',
                            'type' => 'route',
                            'confirm' => '<h1 class=\'ui inverted header\'>'.___('Delete this member from committee?').'<div class=\'sub header\'>'.$member->name.'</div></h1>',
                            'class' => 'item action',
                            'prepend' => '<i class="delete icon"></i>',
                            'model' => ['committee' => $committee, 'member' => $member],
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

{{ $members->links('pagination.default') }}

@endsection