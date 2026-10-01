import { Head, Link, usePage } from '@inertiajs/react';
import Icon, { PawIcon } from '../components/Icon';
import Logo from '../components/Logo';

/**
 * Log in and register: a cat photo with a short note on the left, the form on the right.
 * On phones the photo becomes a band under the header. `cat` is null when no cat has a photo.
 */
export default function AuthLayout({ title, cat, quote, children }) {
    const { links } = usePage().props;

    return (
        <>
            <Head title={title} />
            <a href="#main" className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-white focus:px-4 focus:py-2">
                Skip to content
            </a>

            <div className="flex min-h-screen flex-col lg:flex-row">
                <header className="flex h-16 items-center justify-between border-b border-mist bg-cloud px-4 lg:hidden">
                    <Logo href={links.home} />
                    <Link href={links.home} className="flex items-center gap-1.5 font-semibold text-azure-700 hover:text-azure-900">
                        <Icon name="chevronLeft" size={18} /> Home
                    </Link>
                </header>

                <aside className="relative h-44 shrink-0 overflow-hidden bg-azure-950 sm:h-56 lg:sticky lg:top-0 lg:h-screen lg:w-[43%]">
                    <PanelPhoto cat={cat} />
                    <div className="absolute left-10 top-10 hidden lg:block">
                        <Logo href={links.home} dark />
                    </div>
                    <div className="absolute inset-x-10 bottom-10 hidden flex-col gap-1.5 rounded-[20px] bg-azure-950/95 px-6 py-5 text-white lg:flex">
                        <p className="font-display text-[22px] font-semibold">{quote.title}</p>
                        <p className="text-[15px] leading-normal text-azure-100">{quote.text}</p>
                    </div>
                </aside>

                <main id="main" className="flex flex-1 justify-center px-4 py-8 sm:px-8 lg:items-center lg:py-12">
                    <div className="flex w-full max-w-[460px] flex-col gap-5">
                        <Notice />
                        {children}
                    </div>
                </main>
            </div>
        </>
    );
}

function PanelPhoto({ cat }) {
    if (cat?.image) {
        return <img src={cat.image} alt={`${cat.name}, one of the AduCats`} className="size-full object-cover" />;
    }

    // No cat photo to show: a quiet brand panel instead.
    return (
        <div className="flex size-full items-center justify-center text-azure-800" aria-hidden="true">
            <PawIcon size={160} />
        </div>
    );
}

// The session's message: a password reset notice, an expired page, too many tries.
export function Notice() {
    const { flash } = usePage().props;
    const error = flash?.error;
    const message = error ?? flash?.success ?? flash?.status;

    if (!message) {
        return null;
    }

    return (
        <div
            role={error ? 'alert' : 'status'}
            className={`flex items-start gap-3 rounded-2xl px-4 py-3 text-[15px] font-medium ${error ? 'bg-rejected-bg text-rejected' : 'bg-approved-bg text-approved'}`}
        >
            <Icon name={error ? 'info' : 'check'} className="mt-0.5 shrink-0" />
            <p>{message}</p>
        </div>
    );
}
