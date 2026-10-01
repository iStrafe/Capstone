import { Link } from '@inertiajs/react';
import CatPhoto from './CatPhoto';
import Icon from './Icon';
import StatusBadge from './StatusBadge';

// What the card's footer says when the visitor can't simply start a request (the Adopt list sends requestState).
const requestNotes = {
    requested: { text: 'Request sent', icon: 'check', className: 'bg-pending-bg text-pending' },
    full: { text: 'Requests full for now', icon: 'info', className: 'bg-neutral-bg text-neutral' },
};

export default function CatCard({ cat, compact = false }) {
    const note = requestNotes[cat.requestState];

    return (
        <article className="group relative flex flex-col overflow-hidden rounded-[20px] border border-mist bg-white transition-shadow hover:shadow-[0_12px_32px_rgba(10,42,79,0.10)]">
            <div className="relative">
                <CatPhoto cat={cat} className={`w-full ${compact ? 'h-44' : 'h-56'}`} />
                <StatusBadge status="available" className="absolute left-3.5 top-3.5 bg-white" />
            </div>
            <div className="flex flex-1 flex-col gap-3.5 px-5 pb-5 pt-4">
                <div>
                    <h3 className="font-display text-2xl font-semibold">
                        {/* The whole card is clickable through this link's stretched hit area. */}
                        <Link href={cat.url} className="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline">
                            {cat.name}
                        </Link>
                    </h3>
                    <p className="mt-1 text-[15px] text-muted">{[cat.ageLabel, cat.sex, cat.breed].filter(Boolean).join(' · ')}</p>
                </div>
                {!compact &&
                    (note ? (
                        <span className={`mt-auto inline-flex h-10 items-center justify-center gap-2 rounded-full text-sm font-semibold ${note.className}`}>
                            <Icon name={note.icon} size={16} /> {note.text}
                        </span>
                    ) : (
                        <span className="mt-auto inline-flex h-10 items-center justify-center gap-2 rounded-full bg-azure-100 text-sm font-semibold text-ink group-hover:bg-azure-200">
                            View profile <Icon name="arrowRight" size={16} />
                        </span>
                    ))}
            </div>
        </article>
    );
}
