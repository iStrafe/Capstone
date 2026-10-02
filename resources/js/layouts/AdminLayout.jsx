import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import FlashMessage from '../components/FlashMessage';
import Icon from '../components/Icon';
import Logo from '../components/Logo';

/**
 * The admin area: a sidebar with counts on the left, the page on the right. On phones the
 * sidebar opens from a menu button. `active` is the sidebar key of the current section.
 */
export default function AdminLayout({ title, active, children }) {
    const { auth, admin, links } = usePage().props;
    const [menuOpen, setMenuOpen] = useState(false);

    useEffect(() => router.on('navigate', () => setMenuOpen(false)), []);

    const items = [
        { key: 'cats', label: 'Cats', icon: 'cat', href: admin.links.cats },
        { key: 'requests', label: 'Adoption requests', icon: 'list', href: admin.links.requests, count: admin.pendingRequests, countLabel: 'pending' },
        { key: 'messages', label: 'Messages', icon: 'inbox', href: admin.links.messages, count: admin.unreadMessages, countLabel: 'not handled' },
        { key: 'news', label: 'News & events', icon: 'news', href: admin.links.news },
        { key: 'breed', label: 'Breed helper', icon: 'spark', href: admin.links.breedHelper },
    ];

    const sidebar = (
        <div className="flex h-full flex-col gap-7 px-4 py-6">
            <div className="flex items-center gap-2.5 px-2">
                <Logo href={links.home} />
                <span className="rounded-full bg-neutral-bg px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-neutral">Admin</span>
            </div>

            <nav aria-label="Admin" className="flex flex-col gap-1">
                {items.map((item) => {
                    const current = item.key === active;

                    return (
                        <Link
                            key={item.key}
                            href={item.href}
                            aria-current={current ? 'page' : undefined}
                            className={`flex h-11 items-center gap-3 whitespace-nowrap rounded-xl px-3.5 text-[15px] ${current ? 'bg-white font-bold text-ink shadow-[0_1px_0_#D2DCEA]' : 'font-medium text-body hover:bg-white/70 hover:text-ink'}`}
                        >
                            <Icon name={item.icon} />
                            <span className="flex-1">{item.label}</span>
                            {item.count > 0 && (
                                <span className="flex h-6 min-w-6 items-center justify-center rounded-full bg-azure-600 px-2 text-xs font-bold text-white">
                                    {item.count}
                                    <span className="sr-only"> {item.countLabel}</span>
                                </span>
                            )}
                        </Link>
                    );
                })}
            </nav>

            <div className="mt-auto flex flex-col gap-2">
                <Link href={links.home} className="flex h-10 items-center gap-2.5 px-3.5 font-medium text-body hover:text-ink">
                    <Icon name="external" size={18} /> View public site
                </Link>
                <div className="flex items-center gap-3 rounded-2xl bg-white p-3">
                    <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-azure-800 font-bold text-white" aria-hidden="true">
                        {auth.user.name.charAt(0).toUpperCase()}
                    </span>
                    <div className="flex min-w-0 flex-col">
                        <span className="truncate font-semibold">{auth.user.name}</span>
                        <button type="button" onClick={() => router.post(links.logout)} className="self-start text-[13px] font-semibold text-azure-700 hover:underline">
                            Log out
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );

    return (
        <>
            <Head title={`${title} · Admin`} />
            <a href="#main" className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-white focus:px-4 focus:py-2">
                Skip to content
            </a>

            <div className="flex min-h-screen">
                <aside className="hidden w-72 shrink-0 border-r border-mist bg-sky lg:block">
                    <div className="sticky top-0 h-screen overflow-y-auto">{sidebar}</div>
                </aside>

                <div className="flex min-w-0 flex-1 flex-col">
                    <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-mist bg-cloud/95 px-4 backdrop-blur lg:hidden">
                        <Logo href={links.home} />
                        <button
                            type="button"
                            onClick={() => setMenuOpen((open) => !open)}
                            aria-expanded={menuOpen}
                            aria-controls="admin-menu"
                            aria-label={menuOpen ? 'Close menu' : 'Open menu'}
                            className="flex size-11 items-center justify-center rounded-xl border border-mist-strong bg-white"
                        >
                            <Icon name={menuOpen ? 'close' : 'menu'} />
                        </button>
                    </header>
                    {menuOpen && (
                        <div id="admin-menu" className="border-b border-mist bg-sky lg:hidden">
                            {sidebar}
                        </div>
                    )}

                    <FlashMessage />

                    <main id="main" className="flex w-full max-w-[1280px] flex-1 flex-col gap-6 px-4 py-6 sm:px-8 lg:px-12 lg:py-9">
                        {children}
                    </main>
                </div>
            </div>
        </>
    );
}

/** Page title row: heading and a line of help on the left, actions on the right. */
export function AdminHeader({ title, children, actions }) {
    return (
        <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 className="font-display text-[34px] font-semibold leading-tight sm:text-[40px]">{title}</h1>
                {children && <p className="mt-1.5 text-[15px] text-muted">{children}</p>}
            </div>
            {actions && <div className="flex flex-wrap gap-3">{actions}</div>}
        </div>
    );
}

/** A white card with a heading, used for the sections of admin detail pages. */
export function AdminCard({ title, aside, children, className = '' }) {
    return (
        <section className={`flex flex-col gap-4 rounded-[20px] border border-mist bg-white p-5 sm:p-6 ${className}`}>
            {(title || aside) && (
                <div className="flex items-center justify-between gap-3">
                    {title && <h2 className="text-lg font-bold">{title}</h2>}
                    {aside}
                </div>
            )}
            {children}
        </section>
    );
}
