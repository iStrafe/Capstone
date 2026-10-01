import { Link, usePage } from '@inertiajs/react';
import { ButtonLink } from '../../components/Button';
import CatPhoto from '../../components/CatPhoto';
import Icon from '../../components/Icon';
import PageHeader from '../../components/PageHeader';
import StatusBadge from '../../components/StatusBadge';
import SiteLayout from '../../layouts/SiteLayout';

const stages = ['Sent', 'Under review', 'Approved', 'Released'];

// How far along the tracker each status is (index into `stages`).
const progress = { pending: 1, approved: 2, released: 3, rejected: 1 };

// The signed-in applicant's adoption requests, newest first.
export default function Mine({ requests }) {
    const { links } = usePage().props;

    return (
        <SiteLayout title="My requests" active={null}>
            <PageHeader
                eyebrow="Your account"
                title="My adoption requests"
                actions={
                    requests.length > 0 && (
                        <ButtonLink href={links.adopt} inertia variant="outline" className="self-start md:self-auto">
                            Browse more cats
                        </ButtonLink>
                    )
                }
            >
                Follow each request from the moment you send it until your cat goes home.
            </PageHeader>

            <div className="mx-auto flex max-w-7xl flex-col gap-5 px-4 pb-20 sm:px-8">
                {requests.length > 0 ? (
                    requests.map((request) => <RequestCard key={request.id} request={request} links={links} />)
                ) : (
                    <div className="flex flex-col items-center gap-3 rounded-[20px] border border-dashed border-mist-strong px-6 py-14 text-center">
                        <span className="flex size-13 items-center justify-center rounded-2xl bg-azure-50 text-azure-700">
                            <Icon name="heart" size={24} />
                        </span>
                        <p className="font-bold">No requests yet</p>
                        <p className="max-w-md text-body">When you ask to adopt a cat, the request shows up here so you can follow it.</p>
                        <ButtonLink href={links.adopt} inertia size="sm" className="mt-2">
                            Meet the cats <Icon name="arrowRight" size={16} />
                        </ButtonLink>
                    </div>
                )}
            </div>
        </SiteLayout>
    );
}

function RequestCard({ request, links }) {
    const name = request.cat?.name ?? request.catName;
    const photo = request.cat ?? { name, image: null, placeholder: null };

    return (
        <article className="flex flex-col gap-5 rounded-[24px] border border-mist bg-white p-5 sm:flex-row sm:items-center sm:gap-7 sm:p-6" aria-labelledby={`request-${request.id}`}>
            {photo.image || photo.placeholder ? (
                <CatPhoto cat={photo} className="h-44 w-full shrink-0 rounded-[18px] sm:size-36" />
            ) : (
                <span className="flex h-44 w-full shrink-0 items-center justify-center rounded-[18px] bg-azure-50 text-azure-600 sm:size-36" aria-hidden="true">
                    <Icon name="heart" size={40} />
                </span>
            )}

            <div className="flex min-w-0 flex-1 flex-col gap-4">
                <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div className="flex flex-wrap items-center gap-3">
                        <h2 id={`request-${request.id}`} className="font-display text-[28px] font-semibold leading-tight">
                            {request.cat ? (
                                <Link href={request.cat.url} className="hover:text-azure-800 hover:underline">
                                    {name}
                                </Link>
                            ) : (
                                name
                            )}
                        </h2>
                        <StatusBadge status={request.statusKey} />
                    </div>
                    <p className="text-sm text-muted">
                        Sent {request.sentAt} · Pickup {request.pickupDate}
                    </p>
                </div>

                <Tracker status={request.statusKey} />

                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                    <p className="text-[15px] leading-relaxed text-body">{note(request, name)}</p>
                    {request.statusKey === 'rejected' && (
                        <ButtonLink href={links.adopt} inertia variant="quiet" size="sm" className="self-start sm:self-auto">
                            See other cats
                        </ButtonLink>
                    )}
                </div>
            </div>
        </article>
    );
}

// What the applicant should know or do next, in plain words.
function note(request, name) {
    switch (request.statusKey) {
        case 'approved':
            return `Approved${request.approvedAt ? ` on ${request.approvedAt}` : ''}! A volunteer will contact you to arrange picking up ${name}. Bring your valid ID.`;
        case 'released':
            return `${name} went home with you${request.releasedAt ? ` on ${request.releasedAt}` : ''}. Thank you for adopting!`;
        case 'rejected':
            return 'This request wasn’t approved. You’re welcome to ask for another cat.';
        default:
            return request.cat?.reserved
                ? `Another applicant has been approved for ${name}. If that adoption falls through, your request is still in line.`
                : 'A volunteer is reviewing your request. The answer will show up here.';
    }
}

function Tracker({ status }) {
    const reached = progress[status] ?? 0;
    const rejected = status === 'rejected';

    return (
        <ol className="grid grid-cols-4 gap-2" aria-label="Progress">
            {stages.map((label, index) => {
                const isRejectedStep = rejected && index === 2;
                // Filled bars run up to the current stage; a released request has finished every stage.
                const filled = index <= reached && !(rejected && index > 2);
                const finished = status === 'released' || index < reached;
                const current = index === reached && !rejected && status !== 'released';

                return (
                    <li key={label} className="flex flex-col gap-2" aria-current={current ? 'step' : undefined}>
                        <span className={`h-1.5 rounded-full ${isRejectedStep ? 'bg-rejected' : filled ? 'bg-azure-500' : 'bg-mist'}`} aria-hidden="true" />
                        <span className={`text-[13px] font-semibold ${isRejectedStep ? 'text-rejected' : filled ? 'text-ink' : 'text-muted'}`}>
                            {isRejectedStep ? 'Not approved' : label}
                            {finished && !isRejectedStep && <span className="sr-only"> (done)</span>}
                        </span>
                    </li>
                );
            })}
        </ol>
    );
}
