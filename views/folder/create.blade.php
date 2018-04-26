@extends('committees::folder.template')

@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <form action="{{route('committees.folders.store', $committee)}}" method="POST" class="ui form">
                {{ csrf_field() }}

                @include('committees::folder.form')

                <div class="field actions">
    	            <a class="ui button" href="{{ $parent->id != $committee->id ? route('committees.folders.show', [$committee, $parent->id]) : route('committees.folders.index', $committee) }}">{{ ___('Cancel') }}</a>
    	            <button type="submit" class="ui right floated primary button">
    	            	{{ ___('Create Folder') }}
                	</button>
                </div>
            </form>
        </div>
    </div>
@endsection