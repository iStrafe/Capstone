import { Head, Link, usePage } from '@inertiajs/react';
import FlashMessage from '../components/FlashMessage';
import Icon from '../components/Icon';
import Logo from '../components/Logo';

// A quiet frame for step-by-step tasks like the adoption request: logo, one way out, no main nav.
export default function FocusLayout({ title, exit, children }) {
    const { links } = usePage().props;

    return (
        <>
            <Head title={title} />
            <a href="#main" className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-white focus:px-4 focus:py-2">
                Skip to content
            </a>

            <header className="border-b border-mist bg-cloud">
                <div className="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:h-20 sm:px-8">
                    <Logo href={links.home} />
                    {exit && (
                        <Link href={exit.href} className="flex items-center gap-2 font-semibold text-azure-700 hover:text-azure-900">
                            <Icon name="close" size={18} /> {exit.label}
                        </Link>
                    )}
                </div>
            </header>

            <FlashMessage />

            <main id="main" className="min-h-[70vh]">{children}</main>
        </>
    );
}
