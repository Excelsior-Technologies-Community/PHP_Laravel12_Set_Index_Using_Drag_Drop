@if ($products->lastPage() > 1)
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm justify-content-center mb-0 gap-1">
            {{-- Previous Page Link --}}
            @if ($products->onFirstPage())
                <li class="page-item disabled"><span class="page-link rounded-2">&laquo; Prev</span></li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-2 pagination-ajax-link" href="{{ $products->previousPageUrl() }}" data-page="{{ $products->currentPage() - 1 }}">&laquo; Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @for ($i = 1; $i <= $products->lastPage(); $i++)
                @if ($i == $products->currentPage())
                    <li class="page-item active" aria-current="page"><span class="page-link rounded-2">{{ $i }}</span></li>
                @elseif ($i >= $products->currentPage() - 2 && $i <= $products->currentPage() + 2)
                    <li class="page-item">
                        <a class="page-link rounded-2 pagination-ajax-link" href="{{ $products->url($i) }}" data-page="{{ $i }}">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- Next Page Link --}}
            @if ($products->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-2 pagination-ajax-link" href="{{ $products->nextPageUrl() }}" data-page="{{ $products->currentPage() + 1 }}">Next &raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link rounded-2">Next &raquo;</span></li>
            @endif
        </ul>
    </nav>
    <div class="text-center text-muted small mt-2">
        Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} items
    </div>
@else
    <div class="text-center text-muted small">
        Showing {{ $products->total() }} items
    </div>
@endif
