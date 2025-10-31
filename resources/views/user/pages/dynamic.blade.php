@extends('user.master_page')

@section('title', ($page->title ?? 'Page') . ' | Forward Edge Consulting')

@section('main')

    {{-- Optional global header/footer from config --}}
    @php $headerType = config('pagebuilder.globals.header_block_type'); @endphp
    @if($headerType) @includeIf('user.pages.block.' . $headerType, ['block' => (object)['data'=>[]]]) @endif

    @foreach($page->blocks as $block)
      @includeIf('user.pages.block.' . $block->type, ['block' => $block])
    @endforeach

    @php $footerType = config('pagebuilder.globals.footer_block_type'); @endphp
    @if($footerType) @includeIf('user.pages.block.' . $footerType, ['block' => (object)['data'=>[]]]) @endif

@endsection