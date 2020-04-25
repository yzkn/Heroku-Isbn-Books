{{-- Copyright (c) 2019 YA-androidapp(https://github.com/YA-androidapp) All rights reserved. --}}

@extends('layouts.app')

@php
    $title = __('Books');
@endphp
@section('content')
<div class="container">
    <h1>{{ $title }}</h1>
    <div class="table-responsive">
        <div id="series">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th colspan="4">
                            <input type="text" class="form-control search" placeholder="Search" />
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="sort" data-sort="series">{{ __('Series') }}</th>
                        <th>{{ __('Publisher') }} <br /> {{ __('Author') }}</th>
                        <th colspan="{{ $colspan }}">{{ __('Volume') }} <br /> {{ __('Pubdate') }}</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @foreach ($books_groupby_series as $ser => $books_in_series)
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($books_in_series as $vol => $book)
                            @if ($i===0)
                                <tr id="{{ $book->summary__isbn }}">
                            @endif
                            @if ($i===0)
                                <td>
                                    <input class="check-for-hide-row" type="checkbox" title="Hide" onclick="hide('{{ $book->summary__isbn }}')">
                                </td>
                                <td class="series">{{ $ser }}</td>
                                <td>{{ $book->summary__publisher }} <br /> {{ $book->summary__author }}</td>
                            @endif
                            <td>
                                <a href="{{ url('books/'.$book->id) }}">
                                    <b>
                                        {{ $vol }}
                                    </b>
                                    @if (!empty($book->summary__cover))
                                        <img src="{{ $book->summary__cover }}" class="img-thumbnail summary__cover" />
                                    @endif
                                    <br />
                                    {{ $book->summary__pubdate }}
                                </a>
                            </td>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
