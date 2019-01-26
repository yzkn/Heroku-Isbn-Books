@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>

                <div class="card-body">
                    <a class="dropdown-item" href="{{ url('/books') }}">Books</a>
                    <a class="dropdown-item" href="{{ url('/series') }}">Series</a>
                    @can('allow_system')
                        <a class="dropdown-item" href="{{ url('/csv/import') }}">CSV Import</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
