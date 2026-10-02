import { useForm, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import Button from './Button';
import Field from './Field';
import Icon from './Icon';

const presets = [100, 250, 500];

/**
 * Donation form in a native <dialog>. Submitting creates a PayMongo payment link on the
 * server and sends the browser to PayMongo's checkout page.
 */
export default function DonateDialog({ open, onClose }) {
    const dialog = useRef(null);
    const { links } = usePage().props;
    const form = useForm({ amount: '250', description: 'Donation for the AduCats campus cats' });
    // When PayMongo can't make the link, the server sends the visitor back with a message. The page
    // shows it too, but under this dialog's backdrop, so the dialog repeats it.
    const [failure, setFailure] = useState(null);

    useEffect(() => {
        const el = dialog.current;

        if (open && !el.open) {
            setFailure(null);
            el.showModal();
        } else if (!open && el.open) {
            el.close();
        }
    }, [open]);

    const submit = (event) => {
        event.preventDefault();
        setFailure(null);
        form.post(links.donate, { preserveScroll: true, onSuccess: (page) => setFailure(page.props.flash?.error ?? null) });
    };

    return (
        <dialog
            ref={dialog}
            onClose={onClose}
            onClick={(event) => event.target === dialog.current && onClose()}
            aria-labelledby="donate-title"
            className="m-auto w-[calc(100%-2rem)] max-w-md rounded-3xl bg-white p-0 text-ink shadow-2xl backdrop:bg-azure-950/50"
        >
            <form onSubmit={submit} className="flex flex-col gap-5 p-6 sm:p-8">
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <p className="flex items-center gap-2 text-sm font-bold text-azure-700">
                            <Icon name="gift" size={18} /> Support the cats
                        </p>
                        <h2 id="donate-title" className="mt-1 font-display text-3xl font-semibold">
                            Make a donation
                        </h2>
                    </div>
                    <button type="button" onClick={onClose} aria-label="Close" className="rounded-full p-2 text-muted hover:bg-azure-50">
                        <Icon name="close" />
                    </button>
                </div>

                <fieldset>
                    <legend className="mb-2 text-sm font-semibold">Amount in pesos</legend>
                    <div className="flex flex-wrap gap-2">
                        {presets.map((amount) => {
                            const selected = String(amount) === form.data.amount;

                            return (
                                <button
                                    key={amount}
                                    type="button"
                                    aria-pressed={selected}
                                    onClick={() => form.setData('amount', String(amount))}
                                    className={`h-11 rounded-full border px-5 text-[15px] font-semibold ${selected ? 'border-azure-800 bg-azure-800 text-white' : 'border-mist-strong bg-white hover:border-azure-500'}`}
                                >
                                    ₱{amount}
                                </button>
                            );
                        })}
                    </div>
                </fieldset>

                <Field
                    label="Or enter an amount"
                    type="number"
                    min="1"
                    max="100000"
                    step="0.01"
                    inputMode="decimal"
                    required
                    value={form.data.amount}
                    onChange={(event) => form.setData('amount', event.target.value)}
                    error={form.errors.amount}
                />
                <Field
                    label="What is it for?"
                    multiline
                    required
                    maxLength={255}
                    value={form.data.description}
                    onChange={(event) => form.setData('description', event.target.value)}
                    error={form.errors.description}
                />

                {failure && (
                    <p role="alert" className="rounded-2xl bg-rejected-bg px-4 py-3 text-[15px] font-medium text-rejected">
                        {failure}
                    </p>
                )}
                <Button type="submit" size="lg" disabled={form.processing}>
                    {form.processing ? 'Opening PayMongo…' : 'Continue to payment'}
                </Button>
                <p className="-mt-2 text-center text-[13px] text-muted">You'll pay securely on PayMongo with GCash, Maya or card.</p>
            </form>
        </dialog>
    );
}
