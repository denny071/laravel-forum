@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @forelse($threads as $thread)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="level">
                                <h4 class="flex">
                                    <a href="{{ $thread->path() }}">
                                       @if($thread->hasUpdatesFor(auth()->user()))
                                            <strong>{{ $thread->title }}</strong>
                                        @else
                                            {{ $thread->title }}
                                        @endif
                                    </a>
                                </h4>

                                <a href="{{ $thread->path() }}">
                                    {{ $thread->replies_count }} 回复
                                </a>
                            </div>
                        </div>

                        <div class="panel-body">
                            {{ $thread->body }}
                        </div>
                    </div>
                @empty
                        <p>暂时没有数据</p>

                @endforelse
            </div>
        </div>
    </div>
@endsection
