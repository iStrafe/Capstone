import { useState } from 'react';
import Icon from './Icon';

// A news or event post: optional photo, date, upcoming/past badge, title and text.
export default function EventCard({ event, showImage = true, headingLevel: Heading = 'h3' }) {
    // A missing file shows the same empty frame as a post without a photo.
    const [failed, setFailed] = useState(null);

    return (
        <article className="flex flex-col overflow-hidden rounded-[20px] border border-mist bg-white">
            {showImage &&
                (event.image && failed !== event.image ? (
                    <img src={event.image} alt="" loading="lazy" onError={() => setFailed(event.image)} className="h-48 w-full object-cover" />
                ) : (
                    <div className="flex h-48 items-center justify-center bg-neutral-bg text-muted" aria-hidden="true">
                        <Icon name="image" size={32} />
                    </div>
                ))}
            <div className="flex flex-1 flex-col gap-2.5 p-6">
                <div className="flex items-center justify-between gap-3">
                    {event.date && (
                        <time dateTime={event.isoDate} className="flex items-center gap-2 text-sm font-semibold text-azure-700">
                            <Icon name="calendar" size={16} /> {event.date}
                        </time>
                    )}
                    <EventBadge upcoming={event.isUpcoming} />
                </div>
                <Heading className="font-display text-2xl font-semibold">{event.title}</Heading>
                <p className="line-clamp-4 whitespace-pre-line leading-relaxed text-body">{event.description}</p>
            </div>
        </article>
    );
}

export function EventBadge({ upcoming }) {
    return (
        <span
            className={`inline-flex h-6 shrink-0 items-center gap-1.5 rounded-full px-2.5 text-[13px] font-semibold ${upcoming ? 'bg-approved-bg text-approved' : 'bg-neutral-bg text-body'}`}
        >
            <span className="size-[7px] rounded-full bg-current" aria-hidden="true" />
            {upcoming ? 'Upcoming' : 'Past'}
        </span>
    );
}
