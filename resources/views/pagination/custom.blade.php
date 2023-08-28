<ul class="pagination">
    <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
        <a href="{{ $paginator->previousPageUrl() }}" class="page-link">{{ $prev_text }}</a>
    </li>

    @php
        $start = max(1, $paginator->currentPage() - 2);
        $end = min($paginator->lastPage(), $paginator->currentPage() + 2 + (2 - ($paginator->currentPage() - $start)));
    @endphp

    @for ($i = $start; $i <= $end; $i++)
        <li class="page-item {{ $paginator->currentPage() === $i ? 'active' : '' }}">
            <a href="{{ $paginator->url($i) }}" class="page-link">{{ $i }}</a>
        </li>
    @endfor

    <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
        <a href="{{ $paginator->nextPageUrl() }}" class="page-link">{{ $next_text }}</a>
    </li>
    
    <li class="page-item disabled">
        <span class="page-link">Showing items from {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} out of {{ $paginator->total() }}</span>
    </li>
</ul>
