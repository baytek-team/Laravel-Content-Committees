@extends('committees::template')

@section('content')
    <div id="registration" class="ui container">
        <div class="ui hidden divider"></div>
        <form action="{{ route('committees.update', $committee->id) }}" method="POST" class="ui form">
            {{ csrf_field() }}
            {{ method_field('PUT') }}

            @include('committees::form')

            <div class="ui hidden divider"></div>

            <div class="ui hidden error message"></div>
            <div class="field actions">
                <a class="ui button" href="{{ route('committees.index') }}">{{ ___('Cancel') }}</a>

                <button type="submit" class="ui right floated primary button">
                    {{ ___('Update') }}
                </button>
            </div>
        </form>
    </div>
@endsection