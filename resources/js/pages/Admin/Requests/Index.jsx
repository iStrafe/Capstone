import { Link, router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import { buttonClasses } from '../../../components/Button';
import CatPhoto from '../../../components/CatPhoto';
import Icon from '../../../components/Icon';
import Pagination from '../../../components/Pagination';
import StatusBadge from '../../../components/StatusBadge';
import Tabs from '../../../components/Tabs';
import AdminLayout, { AdminHeader } from '../../../layouts/AdminLayout';

const tabs = [
    { key: 'pending', label: 'Pending' },
    { key: 'approved', label: 'Approved' },
    { key: 'rejected', label: 'Rejected' },
    { key: 'released', label: 'Released' },
    { key: 'all', label: 'All' },
];

// The admin list of adoption requests, one tab per status.
export default function Index({ requests, filters, counts }) {
    const [query, setQuery] = useState(filters.q);
    const first = useRef(true);

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

    const tabHref = (key) => `${window.location.pathname}?${new URLSearchParams(cleanQuery({ status: key, q: filters.q }))}`;

    return (
        <AdminLayout title="Adoption requests" active="requests">
            <AdminHeader title="Adoption requests">Approve one adopter per cat. Releasing a cat rejects its other pending requests.</AdminHeader>

            <Tabs
                label="Request status"
                items={tabs.map((tab) => ({ ...tab, count: counts[tab.key], active: filters.status === tab.key, href: tabHref(tab.key) }))}
            />

            <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div className="relative w-full sm:w-96">
                    <label htmlFor="request-search" className="sr-only">Search applicant or cat</label>
                    <Icon name="search" className="pointer-events-none absolute left-4 top-3.5 text-muted" />
                    <input
                        id="request-search"
                        type="search"
                        value={query}
                        onChange={(event) => setQuery(event.target.value)}
                        placeholder="Search applicant, email or cat"
                        className="h-12 w-full rounded-full border-[1.5px] border-mist-strong bg-white pl-12 pr-4 placeholder:text-muted focus:border-azure-500 focus:outline-3 focus:outline-azure-300"
                    />
                </div>
                <label htmlFor="request-sort" className="sr-only">Sort requests</label>
                <select
                    id="request-sort"
                    value={filters.sort}
                    onChange={(event) => visit({ sort: event.target.value })}
                    className="h-12 rounded-full border border-mist-strong bg-white pl-4 pr-9 text-sm font-medium focus:border-azure-500 focus:outline-3 focus:outline-azure-300 sm:w-48"
                >
                    <option value="oldest">Oldest first</option>
                    <option value="newest">Newest first</option>
                </select>
            </div>

            {requests.data.length > 0 ? (
                <div className="overflow-hidden rounded-[20px] border border-mist bg-white">
                    {/* Phones get stacked cards; wider screens get the table. */}
                    <ul className="divide-y divide-mist md:hidden">
                        {requests.data.map((request) => (
                            <li key={request.id}>
                                <Link href={request.url} className="flex items-start gap-3 p-4 hover:bg-cloud">
                                    <RequestCat request={request} />
                                    <div className="flex min-w-0 flex-1 flex-col gap-1">
                                        <div className="flex items-start justify-between gap-2">
                                            <span className="truncate font-bold">{request.applicant.name}</span>
                                            <StatusBadge status={request.statusKey} />
                                        </div>
                                        <span className="text-sm text-muted">
                                            {catName(request)} · sent {request.sentAt}
                                        </span>
                                        <CatNote request={request} />
                                    </div>
                                </Link>
                            </li>
                        ))}
                    </ul>

                    <table className="hidden w-full text-[15px] md:table">
                        <thead>
                            <tr className="bg-cloud text-left text-[13px] whitespace-nowrap text-muted">
                                <th scope="col" className="px-4 py-3 font-semibold">Applicant</th>
                                <th scope="col" className="px-4 py-3 font-semibold">Cat</th>
                                <th scope="col" className="px-4 py-3 font-semibold">Sent</th>
                                <th scope="col" className="px-4 py-3 font-semibold">Pickup</th>
                                <th scope="col" className="px-4 py-3 font-semibold">Status</th>
                                <th scope="col" className="px-4 py-3"><span className="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            {requests.data.map((request) => (
                                <tr key={request.id} className="border-t border-mist">
                                    <td className="max-w-xs px-4 py-3.5">
                                        <Link href={request.url} className="font-bold hover:text-azure-800 hover:underline">
                                            {request.applicant.name}
                                        </Link>
                                        <div className="text-[13px] text-muted">
                                            {request.validIdCount === 1 ? '1 ID attached' : `${request.validIdCount} IDs attached`}
                                        </div>
                                    </td>
                                    <td className="max-w-xs px-4 py-3.5">
                                        <div className="flex items-center gap-2.5">
                                            <RequestCat request={request} small />
                                            <div className="min-w-0">
                                                <div className="font-semibold">{catName(request)}</div>
                                                <CatNote request={request} />
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-4 py-3.5 whitespace-nowrap text-body">{request.sentAt}</td>
                                    <td className="px-4 py-3.5 whitespace-nowrap text-body">{request.pickupDate}</td>
                                    <td className="px-4 py-3.5 whitespace-nowrap">
                                        <StatusBadge status={request.statusKey} />
                                    </td>
                                    <td className="px-4 py-3.5 text-right whitespace-nowrap">
                                        <Link href={request.url} className={buttonClasses({ variant: request.statusKey === 'pending' ? 'dark' : 'outline', size: 'sm' })}>
                                            {request.statusKey === 'pending' ? 'Review' : 'Open'}
                                            <span className="sr-only"> {request.applicant.name}'s request</span>
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    <Pagination meta={requests.meta} links={requests.links} label="Request pages" className="border-t border-mist px-4 py-3" />
                </div>
            ) : (
                <div className="flex flex-col items-center gap-2 rounded-[20px] border border-dashed border-mist-strong px-6 py-14 text-center">
                    <p className="font-bold">{filters.q ? 'No requests match this search' : emptyText[filters.status]}</p>
                    {filters.q && <p className="text-body">Try another name, email or cat.</p>}
                </div>
            )}
        </AdminLayout>
    );
}

const emptyText = {
    pending: 'No requests are waiting for a decision',
    approved: 'No approved requests right now',
    rejected: 'No rejected requests',
    released: 'No cats have gone home yet',
    all: 'No adoption requests yet',
};

// Drop empty filters and the defaults, so URLs stay short.
function cleanQuery(filters) {
    return Object.fromEntries(Object.entries(filters).filter(([key, value]) => value && !(key === 'status' && value === 'pending')));
}

function catName(request) {
    return request.cat?.name ?? request.catName ?? 'Unknown cat';
}

function RequestCat({ request, small = false }) {
    const size = small ? 'size-9 rounded-[10px]' : 'size-12 rounded-xl';

    return request.cat ? (
        <CatPhoto cat={request.cat} className={`${size} shrink-0`} />
    ) : (
        <span className={`${size} flex shrink-0 items-center justify-center bg-neutral-bg text-muted`} aria-hidden="true">
            <Icon name="cat" size={18} />
        </span>
    );
}

// What the admin should know about the cat before deciding on a pending request.
function CatNote({ request }) {
    if (request.statusKey !== 'pending' || !request.cat) {
        return null;
    }

    const note = request.catArchived
        ? 'Cat is archived'
        : request.cat.reserved
          ? 'Already has an approved adopter'
          : request.catPendingCount > 1
            ? `${request.catPendingCount} of ${request.catPendingLimit} pending requests`
            : null;

    return note && <div className="text-xs font-semibold text-pending">{note}</div>;
}
