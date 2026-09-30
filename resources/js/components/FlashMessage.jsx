import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import Icon from './Icon';

// Shows the session's success/error message (for example a failed donation) at the top of the page.
export default function FlashMessage() {
    const { flash } = usePage().props;
    const [hidden, setHidden] = useState(false);

    useEffect(() => setHidden(false), [flash?.success, flash?.error]);

    const message = flash?.error ?? flash?.success;

    if (!message || hidden) {
        return null;
    }

    const isError = Boolean(flash?.error);

    return (
        <div className="mx-auto mt-4 w-full max-w-7xl px-4 sm:px-8">
            <div
                role={isError ? 'alert' : 'status'}
                className={`flex items-start gap-3 rounded-2xl px-4 py-3 text-[15px] ${isError ? 'bg-rejected-bg text-rejected' : 'bg-approved-bg text-approved'}`}
            >
                <Icon name="info" className="mt-0.5 shrink-0" />
                <p className="flex-1 font-medium">{message}</p>
                <button type="button" onClick={() => setHidden(true)} aria-label="Dismiss message" className="-m-1 rounded-full p-1 hover:bg-white/60">
                    <Icon name="close" size={18} />
                </button>
            </div>
        </div>
    );
}
