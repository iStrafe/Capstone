import { Link } from '@inertiajs/react';
import { PawIcon } from './Icon';

export default function Logo({ href, dark = false }) {
    return (
        <Link href={href} className={`flex items-center gap-2.5 ${dark ? 'text-white' : 'text-ink'}`}>
            <span className="flex size-10 items-center justify-center rounded-xl bg-azure-500 text-white">
                <PawIcon />
            </span>
            <span className="font-display text-2xl font-bold">AduCats</span>
        </Link>
    );
}
