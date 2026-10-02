import { useEffect, useRef } from 'react';
import Button from './Button';

/**
 * A native <dialog> that asks before an action that's hard to undo. `children` is the explanation;
 * `confirmLabel` names the action ("Delete message"). `tone="danger"` makes the button red, and
 * `confirmDisabled` holds it until the admin has done what the dialog asks (like typing a name).
 */
export default function ConfirmDialog({ open, title, children, confirmLabel, cancelLabel = 'Cancel', tone = 'primary', processing = false, confirmDisabled = false, onConfirm, onClose }) {
    const dialog = useRef(null);

    useEffect(() => {
        const el = dialog.current;

        if (open && !el.open) {
            el.showModal();
        } else if (!open && el.open) {
            el.close();
        }
    }, [open]);

    return (
        <dialog
            ref={dialog}
            onClose={onClose}
            onClick={(event) => event.target === dialog.current && onClose()}
            aria-labelledby="confirm-title"
            className="m-auto w-[calc(100%-2rem)] max-w-md rounded-3xl bg-white p-0 text-ink shadow-2xl backdrop:bg-azure-950/50"
        >
            <div className="flex flex-col gap-4 p-6 sm:p-7">
                <h2 id="confirm-title" className="text-xl font-bold">
                    {title}
                </h2>
                <div className="leading-relaxed text-body">{children}</div>
                <div className="mt-2 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <Button variant="outline" size="sm" onClick={onClose}>
                        {cancelLabel}
                    </Button>
                    <Button variant={tone === 'danger' ? 'danger' : 'dark'} size="sm" onClick={onConfirm} disabled={processing || confirmDisabled} className="min-w-0">
                        <span className="truncate">{confirmLabel}</span>
                    </Button>
                </div>
            </div>
        </dialog>
    );
}
