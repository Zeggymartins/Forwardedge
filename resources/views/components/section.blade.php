@props(['title' => null, 'subtitle' => null, 'id' => null, 'class' => ''])

<section @if($id) id="{{ $id }}" @endif class="section-gap {{ $class }}">
  <div class="container">
    @if($title)
      <h2 class="sec-title title-anim mb-2">{{ $title }}</h2>
    @endif
    @if($subtitle)
      <p class="desc text-muted mb-4">{{ $subtitle }}</p>
    @endif
    <div class="content">
      {{ $slot }}
    </div>
  </div>
</section>
