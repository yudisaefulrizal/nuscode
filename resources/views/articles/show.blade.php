@extends('layouts.app')

@section('title', $article->title.' - NUSCODE')
@section('description', $article->excerpt ?? '')

@section('content')
<section>
  {!! $article->content !!}
</section>
@endsection
