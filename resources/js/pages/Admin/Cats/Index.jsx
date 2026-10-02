import { Link, router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import Button, { buttonClasses } from '../../../components/Button';
import CatPhoto from '../../../components/CatPhoto';
import ArchiveCatDialog from '../../../components/ArchiveCatDialog';
import ConfirmDialog from '../../../components/ConfirmDialog';
import Icon from '../../../components/Icon';
import Pagination from '../../../components/Pagination';
import StatusBadge from '../../../components/StatusBadge';
import Tabs from '../../../components/Tabs';
import AdminLayout, { AdminHeader } from '../../../layouts/AdminLayout';

const tabs = [
    { key: 'active', label: 'Active' },
    { key: 'inactive', label: 'Inactive' },
    { key: 'archived', label: 'Archived' },
];

const help = {
    active: 'Active cats show on the adoption list until someone is approved to adopt them.',
    inactive: 'Inactive cats are hidden from the public list without being archived.',
    archived: 'Archived cats keep their adoption history. Their public page explains why they’re gone.',
};

// The admin cat inventory, one tab per status.
export default function Index({ cats, filters, counts, createUrl }) {
    const [query, setQuery] = useState(filters.q);
    const [archiving, setArchiving] = useState(null);
    const [deleting, setDeleting] = useState(null);
    const first = useRef(true);
    const archived = filters.status === 'archived';

    const visit = (changes) =>
        router.get(window.location.pathname, cleanQuery({ ...filters, ...changes }), { preserveState: true, preserveScroll: true, replace: true });

    // Search as the admin types, after a short pause.
    useEffect(() => {
        if (first.current) {
            first.current = false;

            return undefined;
        }
        const timer = setTimeout(() => visit({ q: query }), 300);

        return () => clearTimeout(timer);
    }, [query]);

    const tabHref = (key) => `${window.location.pathname}?${new URLSearchParams(cleanQuery({ status: key }))}`;

    return (
        <AdminLayout title="Cats" active="cats">
            <AdminHeader
                title="Cats"
                actions={
                    <Link href={createUrl} className={buttonClasses({ variant: 'primary', size: 'sm' })}>
                        <Icon name="plus" size={16} /> Add a cat
                    </Link>
                }
            >
                {help[filters.status]}
            </AdminHeader>

            <Tabs label="Cat status" items={tabs.map((tab) => ({ ...tab, count: counts[tab.key], active: filters.status === tab.key, href: tabHref(tab.key) }))} />

            <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div className="relative w-full sm:w-96">
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
                <label htmlFor="cat-sex" className="sr-only">Sex</label>
                <select
                    id="cat-sex"
                    value={filters.sex}
                    onChange={(event) => visit({ sex: event.target.value })}
                    className="h-12 rounded-full border border-mist-strong bg-white pl-4 pr-9 text-sm font-medium focus:border-azure-500 focus:outline-3 focus:outline-azure-300 sm:w-40"
                >
                    <option value="">Any sex</option>
                    <option value="Female">Female</option>
                    <option value="Male">Male</option>
                </select>
            </div>

            {cats.data.length > 0 ? (
                <div className="overflow-hidden rounded-[20px] border border-mist bg-white">
                    <ul className="divide-y divide-mist md:hidden">
                        {cats.data.map((cat) => (
                            <li key={cat.id} className="flex flex-col gap-3 p-4">
                                <div className="flex items-start gap-3">
                                    <CatPhoto cat={cat} className={`size-14 shrink-0 rounded-xl ${archived ? 'grayscale-[0.4]' : ''}`} />
                                    <div className="flex min-w-0 flex-1 flex-col gap-1">
                                        <div className="flex items-start justify-between gap-2">
                                            <span className="truncate font-bold">{cat.name}</span>
                                            <StatusBadge status={cat.state} />
                                        </div>
                                        <span className="text-sm text-muted">{archived ? `Archived ${cat.archivedAt}` : catMeta(cat)}</span>
                                        {archived ? <Reason cat={cat} /> : <CatNotes cat={cat} />}
                                    </div>
                                </div>
                                <RowActions cat={cat} archived={archived} onArchive={setArchiving} onDelete={setDeleting} />
                            </li>
                        ))}
                    </ul>

                    <table className="hidden w-full text-[15px] md:table">
                        <thead>
                            <tr className="bg-cloud text-left text-[13px] text-muted">
                                <th scope="col" className="px-4 py-3 font-semibold">Cat</th>
                                {archived ? (
                                    <>
                                        <th scope="col" className="px-4 py-3 font-semibold">Reason shown to visitors</th>
                                        <th scope="col" className="px-4 py-3 font-semibold">Archived</th>
                                    </>
                                ) : (
                                    <>
                                        <th scope="col" className="px-4 py-3 font-semibold">On the site</th>
                                        <th scope="col" className="px-4 py-3 font-semibold">Pending requests</th>
                                        <th scope="col" className="px-4 py-3 font-semibold">Updated</th>
                                    </>
                                )}
                                <th scope="col" className="px-4 py-3"><span className="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            {cats.data.map((cat) => (
                                <tr key={cat.id} className="border-t border-mist">
                                    <td className="px-4 py-3">
                                        <div className="flex items-center gap-3.5">
                                            <CatPhoto cat={cat} className={`size-12 shrink-0 rounded-xl ${archived ? 'grayscale-[0.4]' : ''}`} />
                                            <div>
                                                <Link href={cat.editUrl} className="font-bold hover:text-azure-800 hover:underline">{cat.name}</Link>
                                                <div className="text-[13px] text-muted">{catMeta(cat)}</div>
                                                {!archived && <CatNotes cat={cat} />}
                                            </div>
                                        </div>
                                    </td>
                                    {archived ? (
                                        <>
                                            <td className="max-w-sm px-4 py-3 text-body"><Reason cat={cat} /></td>
                                            <td className="px-4 py-3 text-body">{cat.archivedAt}</td>
                                        </>
                                    ) : (
                                        <>
                                            <td className="px-4 py-3"><StatusBadge status={cat.state} /></td>
                                            <td className="px-4 py-3 text-body">{cat.pendingCount === 0 ? 'None' : cat.pendingCount}</td>
                                            <td className="px-4 py-3 text-body">{cat.updatedAt}</td>
                                        </>
                                    )}
                                    <td className="px-4 py-3">
                                        <RowActions cat={cat} archived={archived} onArchive={setArchiving} onDelete={setDeleting} />
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    <Pagination meta={cats.meta} links={cats.links} label="Cat pages" className="border-t border-mist px-4 py-3" />
                </div>
            ) : (
                <div className="flex flex-col items-center gap-2 rounded-[20px] border border-dashed border-mist-strong px-6 py-14 text-center">
                    <p className="font-bold">{filters.q || filters.sex ? 'No cats match this search' : emptyText[filters.status]}</p>
                    {(filters.q || filters.sex) && <p className="text-body">Try another name, color or breed.</p>}
                </div>
            )}

            <ArchiveCatDialog cat={archiving} onClose={() => setArchiving(null)} />
            <DeleteDialog cat={deleting} onClose={() => setDeleting(null)} />
        </AdminLayout>
    );
}

const emptyText = {
    active: 'No active cats yet. Add the first one.',
    inactive: 'No inactive cats',
    archived: 'No archived cats',
};

function cleanQuery(filters) {
    return Object.fromEntries(Object.entries(filters).filter(([key, value]) => value && !(key === 'status' && value === 'active')));
}

function catMeta(cat) {
    return [cat.ageLabel, cat.sex, cat.breed].filter(Boolean).join(' · ');
}

function CatNotes({ cat }) {
    return !cat.image && <div className="text-xs font-semibold text-pending">No photo yet</div>;
}

function Reason({ cat }) {
    return cat.archiveReason ? <span className="text-body">{cat.archiveReason}</span> : <span className="italic text-muted">No reason given</span>;
}

const iconButton = 'flex size-10 shrink-0 items-center justify-center rounded-xl border border-mist bg-white hover:border-azure-500';

function RowActions({ cat, archived, onArchive, onDelete }) {
    const [restoring, setRestoring] = useState(false);

    if (archived) {
        const restore = () => router.patch(cat.restoreUrl, {}, { preserveScroll: true, onStart: () => setRestoring(true), onFinish: () => setRestoring(false) });

        return (
            <div className="flex justify-end gap-2">
                <Button variant="quiet" size="sm" onClick={restore} disabled={restoring}>
                    <Icon name="restore" size={16} /> Restore<span className="sr-only"> {cat.name}</span>
                </Button>
                <Button variant="danger" size="sm" onClick={() => onDelete(cat)}>
                    <Icon name="trash" size={16} /> Delete<span className="sr-only"> {cat.name}</span>
                </Button>
            </div>
        );
    }

    return (
        <div className="flex justify-end gap-2">
            <a href={cat.publicUrl} target="_blank" rel="noreferrer" className={iconButton} aria-label={`View ${cat.name}’s public page`} title="View public page">
                <Icon name="eye" size={18} />
            </a>
            <Link href={cat.editUrl} className={buttonClasses({ variant: 'quiet', size: 'sm' })}>
                <Icon name="edit" size={16} /> Edit<span className="sr-only"> {cat.name}</span>
            </Link>
            <button type="button" onClick={() => onArchive(cat)} className={`${iconButton} text-rejected hover:border-rejected`} aria-label={`Archive ${cat.name}`} title="Archive">
                <Icon name="archive" size={18} />
            </button>
        </div>
    );
}

/** Deleting can't be undone, so the admin types the cat's name first. */
function DeleteDialog({ cat, onClose }) {
    const [typed, setTyped] = useState('');
    const [processing, setProcessing] = useState(false);
    const word = cat?.name.toUpperCase() ?? '';

    useEffect(() => setTyped(''), [cat?.id]);

    const remove = () =>
        router.delete(cat.deleteUrl, { preserveScroll: true, onStart: () => setProcessing(true), onFinish: () => setProcessing(false), onSuccess: onClose });

    return (
        <ConfirmDialog
            open={cat !== null}
            title={cat ? `Delete ${cat.name} for good?` : ''}
            confirmLabel="Delete for good"
            cancelLabel={cat ? `Keep ${cat.name}` : 'Cancel'}
            tone="danger"
            processing={processing}
            confirmDisabled={typed.trim().toUpperCase() !== word}
            onConfirm={remove}
            onClose={onClose}
        >
            {cat && (
                <div className="flex flex-col gap-3">
                    <p>This removes {cat.name}’s profile. Past adoption requests stay in the records. You can’t undo this.</p>
                    <label htmlFor="delete-confirm" className="text-sm font-semibold text-ink">
                        Type {word} to confirm
                    </label>
                    <input
                        id="delete-confirm"
                        value={typed}
                        onChange={(event) => setTyped(event.target.value)}
                        autoComplete="off"
                        className="h-12 w-full rounded-xl border-[1.5px] border-mist-strong px-3.5 text-ink focus:border-azure-500 focus:outline-3 focus:outline-azure-300"
                    />
                </div>
            )}
        </ConfirmDialog>
    );
}
