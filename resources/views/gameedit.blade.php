@extends('main')

@section('h1')
    <h1>Edit match {{ $game->teamHome->name }} - {{ $game->teamVisitor->name }}</h1>
@endsection

@section('content')
    <div class="container" id="content_container">
        {!! Form::model($game, ['method' => 'PATCH', 'route' => ['game.update', $game->id], 'class' => 'edit form-horizontal']) !!}
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-sm-5 text-right">{{ $game->teamHome->name }}</div>
            <div class="col-sm-1">{!! Form::text('goals_home', null, ['class' => 'form-control']) !!}</div>
            <div class="col-sm-1">{!! Form::text('goals_visitor', null, ['class' => 'form-control']) !!}</div>
            <div class="col-sm-5">{{ $game->teamVisitor->name }}</div>
        </div>
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-sm-12 text-center">{!! Form::submit('Update Result', ['class' => 'btn btn-primary']) !!}</div>
        </div>
        @if($errors->has('goals_home'))
            <p class="alert alert-danger">
                {{ $errors->first('goals_home') }}
            </p>
        @endif
        @if($errors->has('goals_visitor'))
            <p class="alert alert-danger">
                {{ $errors->first('goals_visitor') }}
            </p>
        @endif
        {!! Form::close() !!}
    </div>
@endsection
