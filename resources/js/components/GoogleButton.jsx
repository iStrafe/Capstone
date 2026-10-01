import { usePage } from '@inertiajs/react';
import { buttonClasses } from './Button';
import { GoogleIcon } from './Icon';

/** Google sign-in leaves the site, so this is a plain link, not an Inertia one. */
export default function GoogleButton({ children }) {
    const { links } = usePage().props;

    return (
        <a href={links.google} className={buttonClasses({ variant: 'outline', className: 'w-full text-ink!' })}>
            <GoogleIcon /> {children}
        </a>
    );
}

export function OrDivider({ children = 'or with email' }) {
    return (
        <div className="flex items-center gap-3 text-sm text-muted">
            <span className="h-px flex-1 bg-mist" aria-hidden="true" />
            {children}
            <span className="h-px flex-1 bg-mist" aria-hidden="true" />
        </div>
    );
}
