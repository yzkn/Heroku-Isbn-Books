{{-- Copyright (c) 2019 YA-androidapp(https://github.com/YA-androidapp) All rights reserved. --}}

@extends('layouts.app')

@php
    $title = __('Books');
@endphp
@section('content')
<div class="container">
    <h1><a href="{{ url('books/') }}" class="text-dark">{{ $title }}</a></h1>
    <div class="card">
        <div class="card-header">
            {{ $book->summary__title }}
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-md-2">{{ __('ISBN') }}</dt>
                <dd class="col-md-10">{{ $book->summary__isbn }}</dd>
                <dt class="col-md-2">{{ __('Cover') }}</dt>
                <dd class="col-md-10">
                @if (!empty($book->summary__cover))
                    <a href="{{ $book->summary__cover }}">
                        <img src="{{ $book->summary__cover }}" class="img-thumbnail summary__cover" />
                    </a>
                @endif
                </dd>
                <!--
                <dt class="col-md-2">{{ __('Title') }}</dt>
                <dd class="col-md-10">{{ $book->summary__title }}</dd>
                -->
                <dt class="col-md-2">{{ __('Author') }}</dt>
                <dd class="col-md-10">{{ $book->summary__author }}</dd>
                <dt class="col-md-2">{{ __('Publisher') }}</dt>
                <dd class="col-md-10">{{ $book->summary__publisher }}</dd>
                <dt class="col-md-2">{{ __('Pubdate') }}</dt>
                <dd class="col-md-10">{{ $book->summary__pubdate }}</dd>
                <dt class="col-md-2">{{ __('Series') }}</dt>
                <dd class="col-md-10">{{ $book->summary__series }}</dd>
                <dt class="col-md-2">{{ __('Volume') }}</dt>
                <dd class="col-md-10">{{ $book->summary__volume }}</dd>
                <dt class="col-md-2">{{ __('Reader') }}</dt>
                <dd class="col-md-10">{{ isset($book->reader->name)?$book->reader->name:'' }}</dd>
            </dl>
            <dl class="row">
                <dt class="col-md-2">{{ __('Created') }}:</dt>
                <dd class="col-md-10">
                    <time itemprop="dateCreated" datetime="{{ $book->created_at }}">
                        {{ $book->created_at }}
                    </time>
                </dd>
                <dt class="col-md-2">{{ __('Updated') }}:</dt>
                <dd class="col-md-10">
                    <time itemprop="dateModified" datetime="{{ $book->updated_at }}">
                        {{ $book->updated_at }}
                    </time>
                </dd>
            </dl>
        </div>
        <div class="card-footer text-muted">
            <div class="edit">
                @can('allow_admin')
                    <a href="{{ url('books/create') }}" class="btn btn-info">{{ __('Create') }}</a>
                    <a href="{{ url('books/create?id='.$book->id) }}" class="btn btn-info">{{ __('Duplicate') }}</a>
                @endcan
                <a href="{{ url('books/'.$book->id.'/edit') }}" class="btn btn-primary">
                    {{ __('Edit') }}
                </a>
                @component('components.btn-del')
                    @slot('table', 'books')
                    @slot('id', $book->id)
                    @slot('title', $book->summary__title)
                @endcomponent
            </div>
        </div>
    </div>
</div>
@endsection
