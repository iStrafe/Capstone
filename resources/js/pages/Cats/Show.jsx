import { Link, usePage } from '@inertiajs/react';
import { ButtonLink } from '../../components/Button';
import CatCard from '../../components/CatCard';
import CatPhoto from '../../components/CatPhoto';
import Icon from '../../components/Icon';
import StatusBadge from '../../components/StatusBadge';
import SiteLayout from '../../layouts/SiteLayout';

export default function Show({ cat, medicalRecord, status, adoption, otherCats }) {
    const { links } = usePage().props;

    return (
        <SiteLayout title={cat.name} active="adopt">
            <div className="mx-auto max-w-7xl px-4 sm:px-8">
                <nav aria-label="Breadcrumb" className="flex items-center gap-2 pt-7 text-sm">
                    <a href={links.adopt} className="font-medium text-azure-700 hover:text-azure-900">Adopt</a>
                    <Icon name="chevronRight" size={14} className="text-muted" />
                    <span aria-current="page" className="text-muted">{cat.name}</span>
                </nav>

                {status ? <Unavailable cat={cat} status={status} links={links} /> : <Profile cat={cat} medicalRecord={medicalRecord} adoption={adoption} links={links} />}

                {otherCats.length > 0 && (
                    <section aria-labelledby="others-title" className="flex flex-col gap-5 pb-20">
                        <h2 id="others-title" className="font-display text-3xl font-semibold">
                            {status ? 'Cats looking for a home' : 'Other cats you might like'}
                        </h2>
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                            {otherCats.map((other) => (
                                <CatCard key={other.id} cat={other} compact />
                            ))}
                        </div>
                    </section>
                )}
            </div>
        </SiteLayout>
    );
}

function Profile({ cat, medicalRecord, adoption, links }) {
    const facts = [
        ['Age', cat.ageLabel],
        ['Sex', cat.sex],
        ['Color', cat.color],
        ['Breed', cat.breed],
    ].filter(([, value]) => value);

    return (
        <section className="grid gap-10 pb-14 pt-6 lg:grid-cols-[1.1fr_1fr] lg:gap-14">
            <div className="flex flex-col gap-4">
                <CatPhoto cat={cat} className="aspect-[5/4] w-full rounded-[28px]" />
                {cat.clip && (
                    <figure className="overflow-hidden rounded-[20px] bg-azure-950">
                        <video src={cat.clip} controls preload="metadata" className="aspect-video w-full" aria-label={`Video of ${cat.name}`} />
                    </figure>
                )}
            </div>

            <div className="flex flex-col gap-6">
                <div className="flex flex-col gap-3">
                    <StatusBadge status="available" className="self-start" />
                    <h1 className="font-display text-5xl font-semibold leading-none sm:text-6xl">{cat.name}</h1>
                </div>

                <dl className="grid grid-cols-2 gap-3">
                    {facts.map(([label, value]) => (
                        <div key={label} className="flex flex-col gap-1 rounded-2xl border border-mist bg-white px-4 py-3.5">
                            <dt className="text-[13px] font-semibold text-muted">{label}</dt>
                            <dd className="text-[17px] font-semibold">{value}</dd>
                        </div>
                    ))}
                </dl>

                {medicalRecord && (
                    <div className="rounded-2xl border border-mist bg-white px-5 py-4">
                        <h2 className="flex items-center gap-2 text-sm font-semibold text-muted">
                            <Icon name="file" size={16} /> Health notes
                        </h2>
                        <p className="mt-1.5 whitespace-pre-line leading-relaxed">{medicalRecord}</p>
                    </div>
                )}

                <AdoptPanel cat={cat} adoption={adoption} links={links} />
            </div>
        </section>
    );
}

// What the visitor can do next: log in, send a request, follow their request, or why they can't.
function AdoptPanel({ cat, adoption, links }) {
    const { auth } = usePage().props;

    if (!auth.user) {
        return (
            <Panel title={`Want to adopt ${cat.name}?`} text="Adopting needs an account, so we can reach you about your request.">
                <ButtonLink href={links.login} variant="onDark" size="lg">Log in to adopt</ButtonLink>
                <a href={links.register} className="text-center font-semibold text-white underline underline-offset-4">
                    New here? Create an account
                </a>
            </Panel>
        );
    }

    if (adoption.alreadyRequested) {
        return (
            <Panel title="You already asked to adopt this cat" text={adoption.refusal}>
                <ButtonLink href={links.myRequests} variant="onDark" size="lg">See my request</ButtonLink>
            </Panel>
        );
    }

    if (adoption.refusal) {
        return (
            <Panel title="Not taking requests right now" text={adoption.refusal}>
                <ButtonLink href={links.adopt} variant="onDark" size="lg">See other cats</ButtonLink>
            </Panel>
        );
    }

    return (
        <Panel title={`Ready to adopt ${cat.name}?`} text="Tell us about yourself, add a valid ID and pick a pickup day. A volunteer reviews every request.">
            {/* The adoption form still lives on the Blade adoption page; #adopt-{id} opens it for this cat. */}
            <ButtonLink href={`${links.adopt}#adopt-${cat.id}`} variant="onDark" size="lg">
                Start adoption request <Icon name="arrowRight" size={18} />
            </ButtonLink>
        </Panel>
    );
}

function Panel({ title, text, children }) {
    return (
        <div className="flex flex-col gap-3.5 rounded-3xl bg-azure-900 p-7 text-white">
            <h2 className="font-display text-2xl font-semibold">{title}</h2>
            {text && <p className="leading-relaxed text-azure-100">{text}</p>}
            {children}
        </div>
    );
}

function Unavailable({ cat, status, links }) {
    return (
        <section className="flex justify-center py-12" data-cat-status={status.reason}>
            <div className="flex w-full max-w-xl flex-col items-center gap-5 rounded-[28px] border border-mist bg-white p-8 text-center sm:p-10">
                <CatPhoto cat={cat} className="h-64 w-full rounded-[20px] grayscale-[35%]" />
                <StatusBadge status={status.reason} />
                <h1 className="font-display text-4xl font-semibold">{status.title}</h1>
                <p className="text-lg leading-relaxed text-body">{status.text}</p>

                {status.reason === 'archived' && (
                    <div className="w-full rounded-2xl bg-azure-50 px-5 py-4 text-left">
                        <p>
                            <strong>Why:</strong> {status.archiveReason}
                        </p>
                        {status.archivedOn && <p className="mt-1 text-sm text-muted">Archived on {status.archivedOn}</p>}
                    </div>
                )}

                <ButtonLink href={links.adopt} size="lg">See cats available for adoption</ButtonLink>
                <Link href={links.home} className="font-semibold text-azure-700 hover:text-azure-900">Back to home</Link>
            </div>
        </section>
    );
}
