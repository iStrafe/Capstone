import { Link } from '@inertiajs/react';

/** Link tabs for switching a list's filter, each with an optional count. */
export default function Tabs({ label, items, className = '' }) {
    return (
        <nav aria-label={label} className={`-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0 ${className}`}>
            <ul className="flex min-w-max gap-1 border-b border-mist">
                {items.map((item) => (
                    <li key={item.key}>
                        <Link
                            href={item.href}
                            preserveScroll
                            aria-current={item.active ? 'page' : undefined}
                            className={`-mb-px flex h-12 items-center gap-2 border-b-2 px-4 font-semibold ${item.active ? 'border-azure-600 text-ink' : 'border-transparent text-muted hover:text-ink'}`}
                        >
                            {item.label}
                            {item.count !== undefined && (
                                <span className={`rounded-full px-2 py-0.5 text-xs font-bold ${item.active ? 'bg-azure-100 text-azure-800' : 'bg-neutral-bg text-neutral'}`}>{item.count}</span>
                            )}
                        </Link>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
