@if ($paginator->hasPages())
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <li class="disabled page-item">
            <a href="#" class="" aria-label="Previous">
                <i class="fa-regular fa-angle-left"></i>
            </a>
        </li>
    @else
        <li class="">
            <a href="{{ $paginator->previousPageUrl() }}" class="" rel="prev"><i class="fa-regular fa-angle-left"></i></a>
        </li>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <li class="">
                <a href="#" class="">{{ $element }}</a>
            </li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="page-item ">
                        <a href="#" class="active">{{ $page }}<span class="sr-only">(current)</span></a>
                    </li>
                @else
                    <li class="page-item">
                        <a href="{{ $url}}" class="">{{ $page }}</a>
                    </li>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <li class="page-item">
            <a href="{{ $paginator->nextPageUrl() }}" class="" rel="next"><i class="fa-regular fa-angle-right"></i></a>
        </li>
    @else
        <li class="disabled page-item">
            <a href="#" class="disabled " aria-label="Next">
                <span aria-hidden="true"><i class="fa-regular fa-angle-right"></i></span>
                <span class="sr-only">@lang('Next')</span>
            </a>
        </li>
    @endif
@endif
