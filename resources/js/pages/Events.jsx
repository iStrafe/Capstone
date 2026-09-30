import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import { ButtonLink } from '../components/Button';
import EventCard, { EventBadge } from '../components/EventCard';
import Icon from '../components/Icon';
import PageHeader from '../components/PageHeader';
import SiteLayout from '../layouts/SiteLayout';

const filters = [
    { key: 'all', label: 'All' },
    { key: 'upcoming', label: 'Upcoming' },
    { key: 'past', label: 'Past' },
];

// Public News & events page. `events` arrive newest first.
export default function Events({ events }) {
    const { links } = usePage().props;
    const [filter, setFilter] = useState('all');

    // The next thing coming up gets the big card: the soonest upcoming event.
    const featured = events.filter((event) => event.isUpcoming).at(-1);
    const shown = events.filter((event) => {
        if (filter === 'upcoming') return event.isUpcoming;
        if (filter === 'past') return !event.isUpcoming;

        return true;
    });
    const rest = filter === 'past' ? shown : shown.filter((event) => event.id !== featured?.id);

    return (
        <SiteLayout title="News & events" active="events">
            <PageHeader
                eyebrow="News & events"
                title="What's happening at AduCats"
                actions={
                    events.length > 0 && (
                        <div role="group" aria-label="Show" className="flex gap-2">
                            {filters.map((item) => (
                                <button
                                    key={item.key}
                                    type="button"
                                    onClick={() => setFilter(item.key)}
                                    aria-pressed={filter === item.key}
                                    className="h-10 rounded-full border border-mist-strong bg-white px-4 text-sm font-medium hover:border-azure-500 aria-pressed:border-ink aria-pressed:bg-ink aria-pressed:text-white"
                                >
                                    {item.label}
                                </button>
                            ))}
                        </div>
                    )
                }
            >
                Adoption drives, feeding schedules and stories from campus.
            </PageHeader>

            <div className="mx-auto flex max-w-7xl flex-col gap-8 px-4 pb-20 sm:px-8">
                {featured && filter !== 'past' && (
                    <article className={`grid overflow-hidden rounded-[28px] border border-mist ${featured.image ? 'bg-white lg:grid-cols-[1.35fr_1fr]' : 'bg-azure-50'}`}>
                        {featured.image && <img src={featured.image} alt="" className="h-64 w-full object-cover sm:h-80 lg:h-full lg:min-h-96" />}
                        <div className={`flex flex-col justify-center gap-4 p-7 sm:p-10 ${featured.image ? '' : 'max-w-3xl'}`}>
                            <div className="flex flex-wrap items-center gap-3">
                                <EventBadge upcoming />
                                <time dateTime={featured.isoDate} className="flex items-center gap-2 font-semibold text-azure-700">
                                    <Icon name="calendar" size={18} /> {featured.date}
                                </time>
                            </div>
                            <h2 className="font-display text-3xl font-semibold leading-tight sm:text-4xl">{featured.title}</h2>
                            <p className="whitespace-pre-line text-[17px] leading-relaxed text-body">{featured.description}</p>
                            <ButtonLink href={links.adopt} className="self-start">
                                Meet the cats first <Icon name="arrowRight" size={18} />
                            </ButtonLink>
                        </div>
                    </article>
                )}

                {rest.length > 0 ? (
                    <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {rest.map((event) => (
                            <EventCard key={event.id} event={event} headingLevel="h2" />
                        ))}
                    </div>
                ) : (
                    !(featured && filter === 'upcoming') && (
                        <div className="flex flex-col items-center gap-3 rounded-[20px] border border-dashed border-mist-strong px-6 py-14 text-center" role="status">
                            <span className="flex size-13 items-center justify-center rounded-2xl bg-azure-50 text-azure-700">
                                <Icon name="calendar" size={24} />
                            </span>
                            <p className="font-bold">{events.length === 0 ? 'No news yet' : `No ${filter} events`}</p>
                            <p className="text-body">Follow AduCats on Facebook for the latest from campus.</p>
                        </div>
                    )
                )}
            </div>
        </SiteLayout>
    );
}
