import { Link } from '@inertiajs/react';
import Icon from './Icon';

/**
 * Page links for a list sent through App\Support\Paginated. Hidden when everything fits on one page.
 */
export default function Pagination({ meta, links, label = 'Pages', className = '' }) {
    if (!meta || meta.lastPage <= 1) {
        return null;
    }

    const step = 'flex size-10 items-center justify-center rounded-xl border border-mist-strong bg-white text-ink hover:border-azure-500';

    return (
        <nav aria-label={label} className={`flex flex-col items-center justify-between gap-3 sm:flex-row ${className}`}>
            <p className="text-sm text-muted">
                Showing {meta.from}–{meta.to} of {meta.total}
            </p>
            <div className="flex items-center gap-1.5">
                {links.prev ? (
                    <Link href={links.prev} preserveScroll className={step} aria-label="Previous page">
                        <Icon name="chevronLeft" size={16} />
                    </Link>
                ) : (
                    <span className={`${step} opacity-40`} aria-hidden="true">
                        <Icon name="chevronLeft" size={16} />
                    </span>
                )}
                {links.pages.map((page, index) =>
                    page.url === null ? (
                        <span key={`gap-${index}`} className="px-1 text-muted" aria-hidden="true">…</span>
                    ) : (
                        <Link
                            key={page.label}
                            href={page.url}
                            preserveScroll
                            aria-current={page.active ? 'page' : undefined}
                            aria-label={`Page ${page.label}`}
                            className={`hidden size-10 items-center justify-center rounded-xl text-sm font-semibold sm:flex ${page.active ? 'bg-azure-800 text-white' : 'border border-mist-strong bg-white hover:border-azure-500'}`}
                        >
                            {page.label}
                        </Link>
                    ),
                )}
                <span className="px-2 text-sm font-semibold sm:hidden">
                    {meta.currentPage} / {meta.lastPage}
                </span>
                {links.next ? (
                    <Link href={links.next} preserveScroll className={step} aria-label="Next page">
                        <Icon name="chevronRight" size={16} />
                    </Link>
                ) : (
                    <span className={`${step} opacity-40`} aria-hidden="true">
                        <Icon name="chevronRight" size={16} />
                    </span>
                )}
            </div>
        </nav>
    );
}
