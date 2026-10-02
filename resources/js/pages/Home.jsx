import { Link, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import Button, { ButtonLink } from '../components/Button';
import CatCard from '../components/CatCard';
import CatPhoto from '../components/CatPhoto';
import EventCard from '../components/EventCard';
import Icon from '../components/Icon';
import SiteLayout, { useDonate } from '../layouts/SiteLayout';

const steps = [
    { title: 'Create an account', text: 'You need an account to adopt, so we can reach you and keep your request safe.' },
    { title: 'Pick your cat', text: 'Browse the profiles, then send a request for the one you connect with.' },
    { title: 'Send your request', text: 'Add your details, a valid ID, and the day you can pick them up.' },
    { title: 'Meet and take home', text: 'Volunteers review it. Once approved, we hand your cat over on campus.' },
];

export default function Home({ cats, events }) {
    const { links } = usePage().props;
    const [query, setQuery] = useState('');

    const shown = useMemo(() => {
        const terms = query.toLowerCase().split(/\s+/).filter(Boolean);

        return cats.filter((cat) => {
            const text = [cat.name, cat.color, cat.breed, cat.sex].join(' ').toLowerCase();

            return terms.every((term) => text.includes(term));
        });
    }, [cats, query]);

    // The hero shows cats that have a photo first; the placeholder is a last resort.
    const heroCats = [...cats.filter((cat) => cat.image), ...cats.filter((cat) => !cat.image)];
    const [featured, ...rest] = heroCats;
    const side = rest.slice(0, 2);

    return (
        <SiteLayout>
            {/* Hero */}
            <section className="mx-auto grid max-w-7xl items-center gap-12 px-4 pb-16 pt-10 sm:px-8 lg:grid-cols-[1fr_1.05fr] lg:gap-16 lg:pb-20 lg:pt-16">
                <div className="flex flex-col gap-6">
                    <p className="text-xs font-bold uppercase tracking-[0.14em] text-azure-700">Adamson University campus cats</p>
                    <h1 className="font-display text-5xl font-semibold leading-[1.02] sm:text-6xl lg:text-7xl">Every campus cat deserves a home.</h1>
                    <p className="max-w-xl text-lg leading-relaxed text-body sm:text-xl">
                        AduCats is a student volunteer group. We look after the cats living around campus and help each one find a person ready to take them home.
                    </p>
                    <div className="flex flex-col gap-3 sm:flex-row">
                        <ButtonLink href={links.adopt} inertia size="lg">
                            Meet the cats <Icon name="arrowRight" size={18} />
                        </ButtonLink>
                        <ButtonLink href="#how-it-works" variant="outline" size="lg">
                            How adoption works
                        </ButtonLink>
                    </div>
                </div>

                {featured ? (
                    <div className="relative grid grid-cols-[1.6fr_1fr] gap-4">
                        <CatPhoto cat={featured} className="row-span-2 h-80 w-full rounded-[28px] sm:h-[520px]" />
                        {side.map((cat) => (
                            <CatPhoto key={cat.id} cat={cat} className="h-full min-h-36 w-full rounded-[24px]" />
                        ))}
                        {side.length < 2 && <div className="rounded-[24px] bg-azure-100" aria-hidden="true" />}
                        {side.length < 1 && <div className="rounded-[24px] bg-azure-200" aria-hidden="true" />}
                        <Link
                            href={featured.url}
                            className="absolute -bottom-5 left-4 flex items-center gap-3.5 rounded-2xl border border-mist bg-white px-5 py-3.5 shadow-[0_12px_32px_rgba(10,42,79,0.14)] hover:border-azure-500"
                        >
                            <span className="flex flex-col">
                                <span className="font-display text-xl font-semibold">{featured.name}</span>
                                <span className="text-sm text-muted">{[featured.ageLabel, featured.sex].filter(Boolean).join(' · ')}</span>
                            </span>
                            <span className="inline-flex h-7 items-center gap-1.5 rounded-full bg-approved-bg px-3 text-[13px] font-semibold text-approved">
                                <span className="size-[7px] rounded-full bg-current" aria-hidden="true" />
                                Available
                            </span>
                        </Link>
                    </div>
                ) : (
                    <div className="flex h-80 items-center justify-center rounded-[28px] bg-azure-100 p-8 text-center text-lg text-body">
                        No cats are up for adoption right now. Check back soon!
                    </div>
                )}
            </section>

            {/* Available cats */}
            <section aria-labelledby="available-title" className="bg-sky py-16 lg:py-20">
                <div className="mx-auto flex max-w-7xl flex-col gap-8 px-4 sm:px-8">
                    <div className="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                        <div className="flex flex-col gap-2.5">
                            <p className="text-xs font-bold uppercase tracking-[0.14em] text-azure-700">Available now</p>
                            <h2 id="available-title" className="font-display text-4xl font-semibold sm:text-[44px]">Looking for a home</h2>
                        </div>
                        <div className="relative w-full md:w-80">
                            <label htmlFor="cat-search" className="sr-only">Search cats</label>
                            <Icon name="search" className="pointer-events-none absolute left-4 top-3.5 text-muted" />
                            <input
                                id="cat-search"
                                type="search"
                                value={query}
                                onChange={(event) => setQuery(event.target.value)}
                                placeholder="Search by name, color or breed"
                                className="h-12 w-full rounded-full border-[1.5px] border-mist-strong bg-white pl-12 pr-4 placeholder:text-muted focus:border-azure-500 focus:outline-3 focus:outline-azure-300"
                            />
                        </div>
                    </div>

                    {shown.length > 0 ? (
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            {shown.map((cat) => (
                                <CatCard key={cat.id} cat={cat} />
                            ))}
                        </div>
                    ) : (
                        <p className="rounded-2xl bg-white p-8 text-center text-body" role="status">
                            {cats.length === 0 ? 'No cats are up for adoption right now.' : `No cats match “${query}”.`}
                        </p>
                    )}

                    <Link href={links.adopt} className="flex items-center gap-2 self-start font-semibold text-azure-700 hover:text-azure-900">
                        Go to the adoption page <Icon name="arrowRight" size={18} />
                    </Link>
                </div>
            </section>

            {/* How it works */}
            <section id="how-it-works" aria-labelledby="how-title" className="mx-auto flex max-w-7xl scroll-mt-24 flex-col gap-10 px-4 py-16 sm:px-8 lg:py-20">
                <div className="flex max-w-2xl flex-col gap-2.5">
                    <p className="text-xs font-bold uppercase tracking-[0.14em] text-azure-700">How adoption works</p>
                    <h2 id="how-title" className="font-display text-4xl font-semibold sm:text-[44px]">Four steps to bringing one home</h2>
                </div>
                <ol className="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    {steps.map((step, index) => (
                        <li key={step.title} className="flex flex-col gap-3.5">
                            <span className="flex size-12 items-center justify-center rounded-full border-[1.5px] border-mist-strong bg-white font-display text-[22px] font-semibold text-azure-600" aria-hidden="true">
                                {index + 1}
                            </span>
                            <span className="text-lg font-bold">{step.title}</span>
                            <span className="leading-relaxed text-body">{step.text}</span>
                        </li>
                    ))}
                </ol>
            </section>

            {/* Disclaimer and donation */}
            <section className="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 pb-16 sm:px-8 lg:grid-cols-2 lg:pb-20">
                <div className="flex flex-col gap-4 rounded-[28px] bg-azure-900 p-8 text-white sm:p-10">
                    <p className="flex items-center gap-2.5 font-bold text-azure-200">
                        <Icon name="shield" /> Please read
                    </p>
                    <h2 className="font-display text-3xl font-semibold leading-tight sm:text-[34px]">The university is not a cat shelter.</h2>
                    <p className="leading-relaxed text-azure-100">
                        We care for cats already on campus and help them get adopted. Please don't leave pets here: abandoning an animal is unlawful under RA 10631, Section 7.
                    </p>
                    <Link href={links.about} className="self-start font-semibold text-white underline underline-offset-4 hover:text-azure-200">
                        Read our full statement
                    </Link>
                </div>
                <div className="flex flex-col gap-4 rounded-[28px] bg-azure-100 p-8 sm:p-10">
                    <p className="flex items-center gap-2.5 font-bold text-azure-800">
                        <Icon name="gift" /> Support the cats
                    </p>
                    <h2 className="font-display text-3xl font-semibold leading-tight sm:text-[34px]">Food, vet visits and vaccines, all funded by you.</h2>
                    <p className="leading-relaxed text-body">Every peso goes to caring for the campus cats until they find a home.</p>
                    <DonateButton />
                </div>
            </section>

            {/* News and events */}
            {events.length > 0 && (
                <section aria-labelledby="news-title" className="mx-auto flex max-w-7xl flex-col gap-7 px-4 pb-20 sm:px-8">
                    <div className="flex items-end justify-between gap-4">
                        <h2 id="news-title" className="font-display text-3xl font-semibold sm:text-4xl">News &amp; events</h2>
                        <Link href={links.events} className="flex items-center gap-2 font-semibold text-azure-700 hover:text-azure-900">
                            All updates <Icon name="arrowRight" size={18} />
                        </Link>
                    </div>
                    <div className="grid gap-6 md:grid-cols-3">
                        {events.map((event) => (
                            <EventCard key={event.id} event={event} />
                        ))}
                    </div>
                </section>
            )}
        </SiteLayout>
    );
}

// Lives inside SiteLayout, so it can open the layout's donate dialog.
function DonateButton() {
    const openDonate = useDonate();

    return (
        <Button onClick={openDonate} size="lg" className="self-start max-sm:h-auto max-sm:min-h-14 max-sm:whitespace-normal max-sm:py-3 max-sm:text-left">
            <Icon name="heart" size={18} /> Donate with GCash, Maya or card
        </Button>
    );
}
