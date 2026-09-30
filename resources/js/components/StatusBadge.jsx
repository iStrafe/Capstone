const tones = {
    available: 'bg-approved-bg text-approved',
    pending: 'bg-pending-bg text-pending',
    approved: 'bg-approved-bg text-approved',
    rejected: 'bg-rejected-bg text-rejected',
    released: 'bg-released-bg text-released',
    reserved: 'bg-pending-bg text-pending',
    adopted: 'bg-released-bg text-released',
    inactive: 'bg-neutral-bg text-neutral',
    archived: 'bg-neutral-bg text-neutral',
};

const labels = {
    available: 'Available',
    pending: 'Pending',
    approved: 'Approved',
    rejected: 'Rejected',
    released: 'Released',
    reserved: 'Adoption in progress',
    adopted: 'Adopted',
    inactive: 'Not available',
    archived: 'Archived',
};

// Status pill: the word always comes with a dot, so it never depends on color alone.
export default function StatusBadge({ status, children, className = '' }) {
    return (
        <span className={`inline-flex h-7 items-center gap-1.5 whitespace-nowrap rounded-full px-3 text-[13px] font-semibold ${tones[status] ?? tones.inactive} ${className}`}>
            <span className="size-[7px] rounded-full bg-current" aria-hidden="true" />
            {children ?? labels[status] ?? status}
        </span>
    );
}
