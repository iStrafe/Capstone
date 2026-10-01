import { Head, Link, router, usePage } from '@inertiajs/react';
import { createContext, useContext, useEffect, useState } from 'react';
import { buttonClasses } from '../components/Button';
import DonateDialog from '../components/DonateDialog';
import FlashMessage from '../components/FlashMessage';
import Icon from '../components/Icon';
import Logo from '../components/Logo';

const DonateContext = createContext(() => {});

/** Opens the donate dialog from anywhere inside the layout. */
export const useDonate = () => useContext(DonateContext);

// Public site chrome for the React pages: header, flash message, donate dialog and footer.
export default function SiteLayout({ title, active, children }) {
    const { auth, links, errors } = usePage().props;
    const user = auth.user;
    const [menuOpen, setMenuOpen] = useState(false);
    // Reopen the donate form when the server sent it back with validation errors.
    const [donateOpen, setDonateOpen] = useState(Boolean(errors?.amount || errors?.description));

    useEffect(() => router.on('navigate', () => setMenuOpen(false)), []);

    const nav = [
        // `inertia` marks pages that are already React; the rest are Blade and need a full page load.
        { key: 'adopt', label: 'Adopt', href: links.adopt, inertia: true },
        { key: 'events', label: 'News & events', href: links.events, inertia: true },
        { key: 'about', label: 'About', href: links.about, inertia: true },
        { key: 'contact', label: 'Contact', href: links.contact, inertia: true },
    ];

    const logout = () => router.post(links.logout);

    return (
        <>
            <Head title={title} />
            <a href="#main" className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-white focus:px-4 focus:py-2">
                Skip to content
            </a>

            <header className="sticky top-0 z-30 border-b border-mist bg-cloud/95 backdrop-blur">
                <div className="mx-auto flex h-16 max-w-7xl items-center justify-between gap-6 px-4 sm:h-20 sm:px-8">
                    <div className="flex items-center gap-12">
                        <Logo href={links.home} />
                        <nav aria-label="Main" className="hidden gap-8 lg:flex">
                            {nav.map((item) => (
                                <NavLink
                                    key={item.key}
                                    item={item}
                                    aria-current={active === item.key ? 'page' : undefined}
                                    className="border-b-2 border-transparent py-2 font-medium text-body hover:text-ink aria-[current=page]:border-azure-500 aria-[current=page]:font-semibold aria-[current=page]:text-ink"
                                >
                                    {item.label}
                                </NavLink>
                            ))}
                        </nav>
                    </div>

                    <div className="hidden items-center gap-4 lg:flex">
                        <button type="button" onClick={() => setDonateOpen(true)} className={buttonClasses({ variant: 'outline', size: 'sm' })}>
                            <Icon name="heart" size={16} /> Donate
                        </button>
                        {user ? (
                            <UserMenu user={user} links={links} onLogout={logout} />
                        ) : (
                            <>
                                <Link href={links.register} className="font-semibold text-azure-700 hover:text-azure-900">
                                    Register
                                </Link>
                                <Link href={links.login} className={buttonClasses({ variant: 'dark', size: 'sm' })}>
                                    Log in
                                </Link>
                            </>
                        )}
                    </div>

                    <button
                        type="button"
                        onClick={() => setMenuOpen((open) => !open)}
                        aria-expanded={menuOpen}
                        aria-controls="mobile-menu"
                        aria-label={menuOpen ? 'Close menu' : 'Open menu'}
                        className="flex size-11 items-center justify-center rounded-xl border border-mist-strong bg-white lg:hidden"
                    >
                        <Icon name={menuOpen ? 'close' : 'menu'} />
                    </button>
                </div>

                {menuOpen && (
                    <div id="mobile-menu" className="border-t border-mist bg-white px-4 pb-6 pt-2 lg:hidden">
                        <nav aria-label="Main" className="flex flex-col">
                            {nav.map((item) => (
                                <NavLink key={item.key} item={item} aria-current={active === item.key ? 'page' : undefined} className="border-b border-mist py-3.5 text-lg font-medium aria-[current=page]:text-azure-700">
                                    {item.label}
                                </NavLink>
                            ))}
                            {user && (
                                <>
                                    <Link href={links.myRequests} className="border-b border-mist py-3.5 text-lg font-medium">My requests</Link>
                                    <Link href={links.profile} className="border-b border-mist py-3.5 text-lg font-medium">Profile</Link>
                                    {user.isAdmin && <a href={links.admin} className="border-b border-mist py-3.5 text-lg font-medium">Admin dashboard</a>}
                                </>
                            )}
                        </nav>
                        <div className="mt-5 flex flex-col gap-3">
                            <button type="button" onClick={() => { setMenuOpen(false); setDonateOpen(true); }} className={buttonClasses({ variant: 'outline' })}>
                                <Icon name="heart" size={16} /> Donate
                            </button>
                            {user ? (
                                <button type="button" onClick={logout} className={buttonClasses({ variant: 'quiet' })}>
                                    Log out
                                </button>
                            ) : (
                                <>
                                    <Link href={links.login} className={buttonClasses({ variant: 'dark' })}>Log in</Link>
                                    <Link href={links.register} className={buttonClasses({ variant: 'quiet' })}>Create an account</Link>
                                </>
                            )}
                        </div>
                    </div>
                )}
            </header>

            <FlashMessage />

            <DonateContext.Provider value={() => setDonateOpen(true)}>
                <main id="main">{children}</main>
            </DonateContext.Provider>

            <Footer links={links} />

            <DonateDialog open={donateOpen} onClose={() => setDonateOpen(false)} />
        </>
    );
}

function NavLink({ item, ...props }) {
    return item.inertia ? <Link href={item.href} {...props} /> : <a href={item.href} {...props} />;
}

function UserMenu({ user, links, onLogout }) {
    const [open, setOpen] = useState(false);

    useEffect(() => {
        if (!open) return;
        const close = (event) => event.key === 'Escape' && setOpen(false);
        window.addEventListener('keydown', close);

        return () => window.removeEventListener('keydown', close);
    }, [open]);

    return (
        <div className="relative" onBlur={(event) => !event.currentTarget.contains(event.relatedTarget) && setOpen(false)}>
            <button
                type="button"
                onClick={() => setOpen((value) => !value)}
                aria-expanded={open}
                aria-haspopup="true"
                className="flex items-center gap-2 rounded-full py-1 pl-1 pr-2 hover:bg-azure-50"
            >
                <span className="flex size-10 items-center justify-center rounded-full bg-azure-800 font-bold text-white" aria-hidden="true">
                    {user.name.charAt(0).toUpperCase()}
                </span>
                <span className="max-w-40 truncate font-medium">{user.name}</span>
                <Icon name="chevronDown" size={16} />
            </button>
            {open && (
                <div className="absolute right-0 top-full mt-2 w-56 overflow-hidden rounded-2xl border border-mist bg-white py-2 shadow-xl">
                    <Link href={links.myRequests} className="block px-4 py-2.5 hover:bg-azure-50">My requests</Link>
                    <Link href={links.profile} className="block px-4 py-2.5 hover:bg-azure-50">Profile</Link>
                    {user.isAdmin && <a href={links.admin} className="block px-4 py-2.5 hover:bg-azure-50">Admin dashboard</a>}
                    <button type="button" onClick={onLogout} className="block w-full border-t border-mist px-4 py-2.5 text-left hover:bg-azure-50">
                        Log out
                    </button>
                </div>
            )}
        </div>
    );
}

function Footer({ links }) {
    const column = 'flex flex-col gap-2.5';
    const link = 'text-azure-200 hover:text-white';

    return (
        <footer className="bg-azure-950 text-azure-100">
            <div className="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-14 sm:px-8">
                <div className="flex flex-col justify-between gap-10 md:flex-row">
                    <div className="flex max-w-sm flex-col gap-3.5">
                        <Logo href={links.home} dark />
                        <p className="leading-relaxed text-azure-200">
                            A volunteer group at Adamson University caring for the campus cats and matching them with loving homes.
                        </p>
                    </div>
                    <div className="grid grid-cols-2 gap-10 sm:grid-cols-3 sm:gap-16">
                        <div className={column}>
                            <span className="font-bold text-white">Adopt</span>
                            <Link href={links.adopt} className={link}>Available cats</Link>
                            <Link href={links.myRequests} className={link}>My requests</Link>
                        </div>
                        <div className={column}>
                            <span className="font-bold text-white">AduCats</span>
                            <Link href={links.about} className={link}>About us</Link>
                            <Link href={links.events} className={link}>News & events</Link>
                            <Link href={links.contact} className={link}>Contact</Link>
                        </div>
                        <div className={`${column} max-w-56`}>
                            <span className="font-bold text-white">Visit</span>
                            <span className="flex gap-2 leading-snug text-azure-200">
                                <Icon name="pin" size={18} className="mt-0.5 shrink-0" />
                                900 San Marcelino St., Ermita, Manila 1000
                            </span>
                        </div>
                    </div>
                </div>
                <p className="border-t border-azure-900 pt-5 text-[13px] text-azure-300">
                    AduCats is not a shelter. Abandoning animals is unlawful under RA 10631, Section 7.
                </p>
            </div>
        </footer>
    );
}
