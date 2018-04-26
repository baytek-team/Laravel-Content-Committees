@extends('committees::members.template')

@section('content')
<div id="registration" class="et_pb_column ui container">
    <form class="ui form" action="{{route('committees.members.store', [$committee])}}" method="POST">
        {{ csrf_field() }}

        @include('committees::members.form')
        <div class="ui hidden divider"></div>
        <div class="ui hidden divider"></div>

        <div class="ui error message"></div>
        <div class="field actions">
            <a class="ui button" href="{{ route('committees.members.index', [$committee]) }}">{{ ___('Cancel') }}</a>
            <button type="submit" class="ui right floated primary button">
                {{ ___('Add Member to Committee') }}
            </button>
        </div>

    </form>
</div>

@endsection