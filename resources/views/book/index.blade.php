{{-- Copyright (c) 2019 YA-androidapp(https://github.com/YA-androidapp) All rights reserved. --}}

@extends('layouts.app')

@php
    $title = __('Books');
@endphp
@section('content')
<div class="container">
    <h1>{{ $title }}</h1>
    <div class="table-responsive">
        <div id="books">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th colspan="3">
                            <input type="text" class="form-control search" placeholder="Search" />
                        </th>
                        <th>
                        </th>
                        <th>
                            @can('allow_admin')
                                <a href="{{ url('books/create') }}" class="btn btn-info">{{ __('Create') }}</a>
                            @endcan
                        </th>
                    </tr>
                    <tr>
                        <th>{{ __('Cover') }}</th>
                        <th class="sort" data-sort="title">{{ __('Title') }}</th>
                        <th class="sort" data-sort="pubdate">{{ __('Pubdate') }}</th>
                        <th class="sort" data-sort="author">{{ __('Author') }}</th>
                        <th class="sort" data-sort="publisher">{{ __('Publisher') }}</th>
                        <th>{{ __('Reader') }}</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @foreach ($books as $book)
                        <tr>
                            <td>
                                <a href="{{ url('books/'.$book->id) }}">
                                    @if (!empty($book->summary__cover))
                                        <img src="{{ $book->summary__cover }}" class="img-thumbnail summary__cover" />
                                    @else
                                        {{ $book->id }}
                                    @endif
                                </a>
                            </td>
                            <td class="title">{{ $book->summary__title }}</td>
                            <td class="pubdate">{{ $book->summary__pubdate }}</td>
                            <td class="author">{{ $book->summary__author }}</td>
                            <td class="publisher">{{ $book->summary__publisher }}</td>
                            <td>{{ isset($book->reader->name)?$book->reader->name:'' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
