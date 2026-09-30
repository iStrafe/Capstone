import { useId } from 'react';

const inputClasses =
    'block w-full rounded-xl border-[1.5px] border-mist-strong bg-white px-3.5 text-base text-ink placeholder:text-muted focus:border-azure-500 focus:outline-3 focus:outline-azure-300 aria-invalid:border-rejected';

// A labelled input or textarea with its validation error underneath.
export default function Field({ label, error, help, multiline = false, className = '', ...props }) {
    const id = useId();
    const describedBy = [error && `${id}-error`, help && `${id}-help`].filter(Boolean).join(' ') || undefined;
    const Control = multiline ? 'textarea' : 'input';

    return (
        <div className={className}>
            <label htmlFor={id} className="mb-2 block text-sm font-semibold text-ink">
                {label}
            </label>
            <Control
                id={id}
                aria-invalid={error ? true : undefined}
                aria-describedby={describedBy}
                className={`${inputClasses} ${multiline ? 'min-h-24 py-3' : 'h-12'}`}
                {...props}
            />
            {help && !error && (
                <p id={`${id}-help`} className="mt-1.5 text-[13px] text-muted">
                    {help}
                </p>
            )}
            {error && (
                <p id={`${id}-error`} className="mt-1.5 text-[13px] font-medium text-rejected">
                    {error}
                </p>
            )}
        </div>
    );
}
