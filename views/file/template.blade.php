@extends('contents::admin')

@section('page.head.header')
    <h1 class="ui header">
        <i class="sitemap icon"></i>
        <div class="content">
            {{ ___('Committee File Management') }}
            <div class="sub header">{{ ___('Manage the committee files.') }}</div>
        </div>
    </h1>
@endsection
