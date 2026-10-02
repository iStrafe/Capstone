import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import ConfirmDialog from './ConfirmDialog';

/** Asks for the reason visitors will see on the cat's page before archiving it. */
export default function ArchiveCatDialog({ cat, onClose }) {
    const [reason, setReason] = useState('');
    const [error, setError] = useState(null);
    const [processing, setProcessing] = useState(false);

    useEffect(() => {
        setReason('');
        setError(null);
    }, [cat?.id]);

    const archive = () =>
        router.patch(
            cat.archiveUrl,
            { archive_reason: reason },
            {
                preserveScroll: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onSuccess: onClose,
                onError: (errors) => setError(errors.archive_reason ?? 'The cat could not be archived.'),
            },
        );

    return (
        <ConfirmDialog
            open={cat !== null}
            title={cat ? `Archive ${cat.name}?` : ''}
            confirmLabel="Archive"
            tone="danger"
            processing={processing}
            onConfirm={archive}
            onClose={onClose}
        >
            {cat && (
                <div className="flex flex-col gap-3">
                    <p>{cat.name} leaves the adoption list. The adoption history is kept, and you can restore the cat later.</p>
                    <label htmlFor="archive-reason" className="text-sm font-semibold text-ink">
                        Reason shown to visitors <span className="font-normal text-muted">(optional)</span>
                    </label>
                    <textarea
                        id="archive-reason"
                        value={reason}
                        maxLength={255}
                        onChange={(event) => setReason(event.target.value)}
                        placeholder="For example: Moved to a partner shelter."
                        aria-invalid={error ? true : undefined}
                        className="min-h-20 w-full rounded-xl border-[1.5px] border-mist-strong px-3.5 py-3 text-ink placeholder:text-muted focus:border-azure-500 focus:outline-3 focus:outline-azure-300"
                    />
                    {error && <p className="text-[13px] font-medium text-rejected">{error}</p>}
                </div>
            )}
        </ConfirmDialog>
    );
}
