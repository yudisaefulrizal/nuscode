@extends('layouts.app')

@section('title', 'Artikel - NUSCODE')
@section('description', 'Kumpulan artikel teknis dari NUSCODE seputar jaringan, keamanan siber, dan pengembangan sistem.')

@section('content')
<section>
  <h1>Artikel</h1>

  @if($articles->isEmpty())
    <div class="empty-state">
      <p>Belum ada artikel tersedia.</p>
    </div>
  @else
    <div class="card-grid">
      @foreach($articles as $article)
        <a href="{{ route('articles.show', $article) }}" class="card">
          <h3>{{ $article->title }}</h3>
          @if($article->excerpt)
            <p>{{ $article->excerpt }}</p>
          @endif
          @if($article->author)
            <p><small>Oleh {{ $article->author }}</small></p>
          @endif
        </a>
      @endforeach
    </div>

    <div style="margin-top: 2rem;">
      {{ $articles->links() }}
    </div>
  @endif
</section>
@endsection
