@extends('layouts.app')

@section('title', 'Prompt - NUSCODE')

@section('content')
<section>
  <h1>Prompt</h1>

  @if($items->isEmpty())
    <div class="empty-state">
      <p>Belum ada produk tersedia.</p>
    </div>
  @else
    <div class="card-grid">
      @foreach($items as $item)
        <a href="{{ route('products.prompt.show', $item) }}" class="card">
          @if($item->image)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->image) }}" alt="{{ $item->name }}">
          @endif
          <h3>{{ $item->name }}</h3>
          <p>{{ \Illuminate\Support\Str::limit($item->description, 100) }}</p>
          <p class="price">Rp{{ number_format($item->price, 0, ',', '.') }}</p>
        </a>
      @endforeach
    </div>

    <div style="margin-top: 2rem;">
      {{ $items->links() }}
    </div>
  @endif
</section>
@endsection
