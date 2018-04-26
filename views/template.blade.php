@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        <i class="announcement icon"></i>
        <div class="content">
        	@if(isset($committee->title) && $committee->title)
				 {{ ___('Committee: ') . $committee->title }}
        	@else
				{{ ___('Committees') }}
        	@endif
            <div class="sub header">{{ ___('Manage the committees of the system.') }}</div>
        </div>
    </h1>
@endsection
