@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        <i class="user group icon"></i>
        <div class="content">
            {{ ___('Committee Member Management') }}
            <div class="sub header">{{ ___('Add, remove and edit committee members.') }}</div>
        </div>
    </h1>
@endsection
