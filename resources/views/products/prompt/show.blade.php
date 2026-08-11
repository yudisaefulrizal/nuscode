@extends('layouts.app')

@section('title', $item->name.' - NUSCODE')
@section('description', \Illuminate\Support\Str::limit($item->description, 150))

@section('content')
<section>
  <h1>{{ $item->name }}</h1>

  @if($item->image)
    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->image) }}" alt="{{ $item->name }}" style="width:100%; max-width:600px; border-radius:15px; margin-bottom:1.5rem;">
  @endif

  <p>{{ $item->description }}</p>

  @if($item->category)
    <p><strong>Kategori:</strong> {{ $item->category }}</p>
  @endif

  @if($item->example_output)
    <h2>Contoh Hasil</h2>
    <blockquote>{{ $item->example_output }}</blockquote>
  @endif

  <p style="margin: 1.5rem 0;">
    @include('products._checkout-modal', ['type' => 'prompt', 'product' => $item])
  </p>
</section>
@endsection
