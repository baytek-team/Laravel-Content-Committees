@extends('committees::folder.template')

@section('content')
<div id="registration" class="ui container">
    <div class="ui hidden divider"></div>
    <form action="{{ route('committees.folders.update', [$committee, $folder]) }}" method="POST" class="ui form">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        @include('committees::folder.form')
        <div class="ui hidden divider"></div>
        <div class="ui hidden divider"></div>

        <div class="ui error message"></div>
        <div class="field actions">
            <a class="ui button" href="{{ $folder->parent() != $committee->id ? route('committees.folders.show', [$committee, $folder->parent()]) : route('committees.folders.index', $committee) }}">{{ ___('Cancel') }}</a>

            <button type="submit" class="ui right floated primary button">
                {{ ___('Update Folder') }}
            </button>
        </div>
    </form>
</div>

@endsection