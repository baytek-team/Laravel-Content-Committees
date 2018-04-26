@extends('committees::file.template')

@section('content')
    <div id="registration" class="ui container">
        <div class="ui hidden divider"></div>
        <form action="{{ route('committees.file.update', [$committee->id, $file->id]) }}" method="POST" class="ui form">
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            @include('committees::file.form')
            <div class="ui hidden divider"></div>

            <div class="ui hidden error message"></div>
            <div class="field actions">
                <a class="ui button" href="{{ $file->parent() != $committee->id ? route('committees.folders.show', [$committee, $file->parent()]) : route('committees.folders.index', $committee) }}">{{ ___('Cancel') }}</a>

                <button type="submit" class="ui right floated primary button">
                    {{ ___('Update') }}
                </button>
            </div>
        </form>
    </div>
@endsection