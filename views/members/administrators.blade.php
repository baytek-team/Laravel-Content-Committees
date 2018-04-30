@extends('committees::members.template')

@section('page.head.menu')
 {{--    <div class="ui secondary menu">
        <div class="right item">
            <a class="ui primary button" href="{{ route('member.create') }}">
                <i class="user add icon"></i>{{ ___('Add Member') }}
            </a>
        </div>
    </div> --}}
@endsection

@section('content')
@if (Auth::user()->hasRole([\Baytek\Laravel\Users\Roles\Root::ROLE]))
    <div>
        <i class="spy icon"></i>
        {{ ___('indicates root user') }}
    </div>
@endif
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
            @if ($member->hasRole([\Baytek\Laravel\Users\Roles\Root::ROLE])
                && Auth::user()->hasRole([\Baytek\Laravel\Users\Roles\Root::ROLE]) == false
            )
                @continue
            @endif
            <tr data-member-id="{{ $member->id }}">
                <td>
                    @if ($member->hasRole([\Baytek\Laravel\Users\Roles\Root::ROLE]))
                        <i class="spy icon"></i>
                    @endif
                    {{ $member->name }}
                </td>
                <td>{{ $member->email }}</td>
                <td class="collapsing right aligned">
                    <div class="ui text compact menu">
                        <a href="{{ route('committees.members.edit', ['member' => $member]) }}" class="item">
                            <i class="pencil icon"></i> {{ ___('Edit') }}
                        </a>

                        @if (Auth::user()->hasRole([\Baytek\Laravel\Users\Roles\Root::ROLE]))
                            <a class="item" href="{{ route('user.roles', ['user' => $member]) }}">
                                <i class="user icon"></i> {{ ___('Roles') }}
                            </a>
                        @endif
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