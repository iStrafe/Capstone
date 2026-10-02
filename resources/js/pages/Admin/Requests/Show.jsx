import { Link, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import Button, { ButtonLink } from '../../../components/Button';
import CatPhoto from '../../../components/CatPhoto';
import ConfirmDialog from '../../../components/ConfirmDialog';
import Icon from '../../../components/Icon';
import StatusBadge from '../../../components/StatusBadge';
import AdminLayout, { AdminCard } from '../../../layouts/AdminLayout';

// One adoption request: who applied, their valid IDs, the other requests for the cat, and the decision.
export default function Show({ request, validIds, account, otherRequests, actions, otherPendingCount, statusUrl, pdfUrl, listUrl }) {
    const catName = request.cat?.name ?? request.catName ?? 'this cat';

    return (
        <AdminLayout title={`Request for ${catName}`} active="requests">
            <nav aria-label="Breadcrumb" className="flex items-center gap-1.5 text-sm">
                <Link href={listUrl} className="font-semibold text-azure-700 hover:underline">
                    Adoption requests
                </Link>
                <Icon name="chevronRight" size={14} className="text-muted" />
                <span className="text-muted">{request.applicant.name}</span>
            </nav>

            <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div className="flex items-center gap-4">
                    {request.cat && <CatPhoto cat={request.cat} className="size-16 shrink-0 rounded-[18px] sm:size-18" />}
                    <div>
                        <div className="flex flex-wrap items-center gap-3">
                            <h1 className="font-display text-[30px] font-semibold leading-tight sm:text-[40px]">Request for {catName}</h1>
                            <StatusBadge status={request.statusKey} />
                        </div>
                        <p className="mt-1 text-muted">
                            Sent {request.sentAt} · Wants to pick up {request.pickupDate}
                        </p>
                    </div>
                </div>
                {request.cat && (
                    <ButtonLink href={request.cat.url} inertia variant="outline" size="sm" className="self-start sm:self-auto">
                        View {catName}’s profile
                    </ButtonLink>
                )}
            </div>

            <div className="flex flex-col gap-6 lg:flex-row lg:items-start">
                <div className="flex min-w-0 flex-1 flex-col gap-5">
                    <AdminCard title="Applicant">
                        <dl className="grid gap-x-6 gap-y-3 text-[15px] sm:grid-cols-[150px_1fr]">
                            <Row label="Name"><span className="font-semibold">{request.applicant.name}</span></Row>
                            <Row label="Email">
                                {request.applicant.email ? <a href={`mailto:${request.applicant.email}`} className="text-azure-700 hover:underline">{request.applicant.email}</a> : 'Not given'}
                            </Row>
                            <Row label="Mobile">
                                {request.applicant.phone ? <a href={`tel:${request.applicant.phone}`} className="text-azure-700 hover:underline">{request.applicant.phone}</a> : 'Not given'}
                            </Row>
                            <Row label="Address">{request.applicant.address || 'Not given'}</Row>
                            <Row label="Account">
                                {account
                                    ? `Member since ${account.memberSince} · ${account.otherRequests === 0 ? 'no other requests' : account.otherRequests === 1 ? '1 other request' : `${account.otherRequests} other requests`}`
                                    : 'Sent before accounts were required'}
                            </Row>
                        </dl>
                    </AdminCard>

                    <AdminCard title="Valid IDs" aside={<span className="text-sm text-muted">Only admins can open these</span>}>
                        {validIds.length > 0 ? (
                            <div className="grid gap-4 sm:grid-cols-2">
                                {validIds.map((id) => (
                                    <figure key={id.url} className="flex flex-col gap-2">
                                        <a href={id.url} target="_blank" rel="noreferrer" className="block overflow-hidden rounded-2xl border border-mist bg-neutral-bg">
                                            <img src={id.url} alt={`${id.label} sent by ${request.applicant.name}`} className="h-48 w-full object-contain" />
                                        </a>
                                        <figcaption className="flex justify-between text-sm">
                                            <span className="text-muted">{id.label}</span>
                                            <a href={id.url} target="_blank" rel="noreferrer" className="font-semibold text-azure-700 hover:underline">
                                                Open full size
                                            </a>
                                        </figcaption>
                                    </figure>
                                ))}
                            </div>
                        ) : (
                            <p className="text-body">No ID was attached to this request.</p>
                        )}
                    </AdminCard>

                    {request.cat && (
                        <AdminCard title={`Other requests for ${catName}`}>
                            {otherRequests.length > 0 ? (
                                <ul className="flex flex-col divide-y divide-mist">
                                    {otherRequests.map((other) => (
                                        <li key={other.id} className="flex flex-wrap items-center justify-between gap-2 py-2.5 first:pt-0 last:pb-0">
                                            <span>
                                                <Link href={other.url} className="font-semibold hover:text-azure-800 hover:underline">{other.name}</Link>{' '}
                                                <span className="text-sm text-muted">· sent {other.sentAt} · pickup {other.pickupDate}</span>
                                            </span>
                                            <StatusBadge status={other.statusKey} />
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                <p className="text-body">Nobody else has asked for {catName}.</p>
                            )}
                        </AdminCard>
                    )}
                </div>

                <aside className="flex w-full flex-col gap-5 lg:w-88 lg:shrink-0">
                    <Decision request={request} catName={catName} actions={actions} otherPendingCount={otherPendingCount} statusUrl={statusUrl} />

                    <AdminCard title="History">
                        <History request={request} />
                    </AdminCard>

                    <ButtonLink href={pdfUrl} variant="outline" size="sm" className="self-start">
                        <Icon name="file" size={16} /> Adoption contract (PDF)
                    </ButtonLink>
                </aside>
            </div>
        </AdminLayout>
    );
}

function Row({ label, children }) {
    return (
        <>
            <dt className="text-muted">{label}</dt>
            <dd className="break-words">{children}</dd>
        </>
    );
}

// Buttons for each move the status rules allow. Blocked moves are shown disabled with the reason.
const buttons = {
    Approved: { label: (name) => `Approve ${name}`, icon: 'check', variant: 'dark' },
    Released: { label: () => 'Mark as released', icon: 'heart', variant: 'dark' },
    Rejected: { label: () => 'Reject', variant: 'danger' },
    Pending: { label: () => 'Reopen as pending', icon: 'restore', variant: 'outline' },
};

function Decision({ request, catName, actions, otherPendingCount, statusUrl }) {
    const { errors } = usePage().props;
    const form = useForm({ status: '' });
    const [confirming, setConfirming] = useState(null);
    const first = request.applicant.name.split(' ')[0];

    const send = (status) => {
        form.transform(() => ({ status }));
        form.post(statusUrl, { preserveScroll: true, onFinish: () => setConfirming(null) });
    };

    const explain = {
        Approved: `${catName} leaves the adoption list while this request is approved.${otherPendingCount > 0 ? ' The other pending requests wait until the cat is released.' : ''}`,
        Released: `${catName} went home with ${first}. This is final${otherPendingCount > 0 ? `, and the ${otherPendingCount === 1 ? 'other pending request is' : `other ${otherPendingCount} pending requests are`} rejected automatically` : ''}.`,
        Rejected:
            request.statusKey === 'approved'
                ? `${first} won’t adopt ${catName} after all. ${catName} goes back on the adoption list.`
                : `${first} will see that this request wasn’t approved.`,
        Pending: `The request goes back to Pending so you can decide again.`,
    };

    const confirmTitles = {
        Approved: `Approve ${first} to adopt ${catName}?`,
        Released: `Mark ${catName} as released to ${first}?`,
        Rejected: `Reject ${first}’s request?`,
        Pending: 'Reopen this request?',
    };

    return (
        <AdminCard title="Decision">
            {actions.length === 0 ? (
                <p className="text-body">
                    {catName} went home with {first}
                    {request.releasedAt ? ` on ${request.releasedAt}` : ''}. Released requests are final.
                </p>
            ) : (
                <>
                    {actions.map((action) => {
                        const button = buttons[action.status];

                        return (
                            <div key={action.status} className="flex flex-col gap-1.5">
                                <Button
                                    variant={action.refusal ? 'disabled' : button.variant}
                                    disabled={Boolean(action.refusal) || form.processing}
                                    onClick={() => setConfirming(action.status)}
                                    className="w-full"
                                >
                                    {button.icon && <Icon name={button.icon} size={18} />} {button.label(first)}
                                </Button>
                                {action.refusal && <p className="text-[13px] text-muted">{action.refusal}</p>}
                            </div>
                        );
                    })}
                    {errors.status && (
                        <p role="alert" className="rounded-xl bg-rejected-bg px-3.5 py-2.5 text-sm font-medium text-rejected">
                            {errors.status}
                        </p>
                    )}
                    <p className="text-[13px] leading-normal text-muted">
                        {request.statusKey === 'pending' && 'Approving reserves the cat. Release it once the cat has gone home.'}
                        {request.statusKey === 'approved' && 'Release the cat when it goes home, or reject if the adoption falls through.'}
                        {request.statusKey === 'rejected' && 'Reopen only if this request was rejected by mistake.'}
                    </p>
                </>
            )}

            <ConfirmDialog
                open={confirming !== null}
                title={confirming ? confirmTitles[confirming] : ''}
                confirmLabel={confirming ? buttons[confirming].label(first) : ''}
                tone={confirming === 'Rejected' ? 'danger' : 'primary'}
                processing={form.processing}
                onConfirm={() => send(confirming)}
                onClose={() => setConfirming(null)}
            >
                {confirming && explain[confirming]}
            </ConfirmDialog>
        </AdminCard>
    );
}

function History({ request }) {
    const rejected = request.statusKey === 'rejected';
    const steps = [
        { label: 'Request sent', date: request.sentAt, done: true },
        rejected
            ? { label: 'Not approved', date: request.approvedAt ? `Was approved ${request.approvedAt}` : null, done: true, tone: 'rejected' }
            : { label: 'Approved', date: request.approvedAt, done: Boolean(request.approvedAt) && request.statusKey !== 'pending' },
        ...(rejected ? [] : [{ label: 'Released', date: request.releasedAt, done: request.statusKey === 'released' }]),
    ];

    return (
        <ol className="flex flex-col">
            {steps.map((step, index) => (
                <li key={step.label} className="relative pb-4 pl-7 last:pb-0">
                    {index < steps.length - 1 && <span className="absolute bottom-0 left-[6px] top-5 w-0.5 bg-mist" aria-hidden="true" />}
                    <span
                        className={`absolute left-0 top-1 size-3.5 rounded-full ${step.tone === 'rejected' ? 'bg-rejected' : step.done ? 'bg-azure-500' : 'bg-mist-strong'}`}
                        aria-hidden="true"
                    />
                    <div className="font-semibold">{step.label}</div>
                    <div className="text-[13px] text-muted">{step.done ? step.date ?? 'Done' : 'Waiting'}</div>
                </li>
            ))}
        </ol>
    );
}
