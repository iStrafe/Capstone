import { useForm, usePage } from '@inertiajs/react';
import { useEffect, useId, useMemo, useRef, useState } from 'react';
import Button from '../../components/Button';
import CatPhoto from '../../components/CatPhoto';
import DatePicker, { formatLong } from '../../components/DatePicker';
import Field from '../../components/Field';
import Icon from '../../components/Icon';
import FocusLayout from '../../layouts/FocusLayout';
import { ADOPTION_TERMS } from '../../lib/adoptionTerms';
import { formatSize } from '../../lib/uploads';

const steps = ['Your details', 'ID and pickup day', 'Review and send'];

// Same limits as StoreAdoptionRequest, checked here first so nobody waits for an upload to fail.
const MAX_IDS = 2;
const MAX_ID_BYTES = 2 * 1024 * 1024;
const ID_TYPES = ['image/jpeg', 'image/png'];

// Which step each server error belongs to, so a failed send opens the step that needs fixing.
const stepOfField = (field) => {
    if (['name', 'email', 'phone', 'address'].includes(field)) return 0;
    if (field === 'date_of_adoption' || field.startsWith('valid_id')) return 1;

    return 2;
};

const looksLikeEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

// The adoption request for one cat: three steps on one page, sent together at the end.
export default function Request({ cat, applicant, today, latestPickup }) {
    const { links } = usePage().props;
    const [step, setStep] = useState(0);
    const headingRef = useRef(null);
    const form = useForm({
        cat_id: cat.id,
        name: applicant.name ?? '',
        email: applicant.email ?? '',
        phone: '',
        address: '',
        valid_id: [],
        date_of_adoption: '',
        terms: false,
    });

    // Move focus to the new step's heading, so keyboard and screen reader users start at the top.
    const firstRender = useRef(true);
    useEffect(() => {
        if (firstRender.current) {
            firstRender.current = false;

            return;
        }
        window.scrollTo({ top: 0 });
        headingRef.current?.focus();
    }, [step]);

    const goTo = (next) => {
        if (next > step) {
            const problems = checkStep(step, form.data);
            form.clearErrors();
            if (Object.keys(problems).length > 0) {
                form.setError(problems);

                return;
            }
        }
        setStep(next);
    };

    const submit = (event) => {
        event.preventDefault();
        if (step < steps.length - 1) {
            goTo(step + 1);

            return;
        }
        if (!form.data.terms) {
            form.setError('terms', 'Please read the adoption terms and tick the box to agree.');

            return;
        }
        form.post(links.adoptionSend, {
            forceFormData: true,
            onError: (errors) => setStep(Math.min(...Object.keys(errors).map(stepOfField))),
        });
    };

    const idError = form.errors.valid_id ?? form.errors['valid_id.0'] ?? form.errors['valid_id.1'];

    return (
        <FocusLayout title={`Adopt ${cat.name}`} exit={{ href: cat.url, label: `Back to ${cat.name}` }}>
            <div className="mx-auto max-w-7xl px-4 pb-20 pt-8 sm:px-8 sm:pt-10">
                <Stepper current={step} />

                <div className="mt-8 grid items-start gap-8 lg:grid-cols-[1fr_380px] lg:gap-10">
                    <form onSubmit={submit} noValidate className="flex flex-col gap-8 rounded-[24px] border border-mist bg-white p-6 sm:p-10" aria-labelledby="step-title">
                        <div>
                            <h1 id="step-title" ref={headingRef} tabIndex={-1} className="font-display text-3xl font-semibold focus:outline-none sm:text-4xl">
                                {['Tell us about yourself', 'Valid ID and pickup day', 'Review and send'][step]}
                            </h1>
                            <p className="mt-2 text-[17px] text-muted">
                                {
                                    [
                                        'Step 1 of 3. We use these details to reach you about your request.',
                                        'Step 2 of 3. Your details from step 1 are kept.',
                                        'Step 3 of 3. Check your answers, then read and accept the adoption terms.',
                                    ][step]
                                }
                            </p>
                        </div>

                        {step === 0 && <DetailsStep form={form} />}
                        {step === 1 && <IdAndDayStep form={form} cat={cat} today={today} latestPickup={latestPickup} idError={idError} />}
                        {step === 2 && <ReviewStep form={form} cat={cat} onEdit={setStep} />}

                        <div className="flex flex-col-reverse gap-3 border-t border-mist pt-7 sm:flex-row sm:items-center sm:justify-between">
                            {step > 0 ? (
                                <Button variant="outline" onClick={() => goTo(step - 1)}>
                                    <Icon name="chevronLeft" size={16} /> Back
                                </Button>
                            ) : (
                                <span />
                            )}
                            {step < steps.length - 1 ? (
                                <Button type="submit" variant="dark">
                                    {step === 0 ? 'Continue' : 'Continue to review'} <Icon name="arrowRight" size={18} />
                                </Button>
                            ) : (
                                <Button type="submit" disabled={form.processing}>
                                    <Icon name="send" size={18} /> {form.processing ? 'Sending…' : `Send request for ${cat.name}`}
                                </Button>
                            )}
                        </div>
                        {step === steps.length - 1 && (
                            <p className="-mt-4 text-sm text-muted">If something goes wrong, you stay on this page and nothing you typed or added is lost.</p>
                        )}
                    </form>

                    <CatSummary cat={cat} step={step} />
                </div>
            </div>
        </FocusLayout>
    );
}

// The same checks the server runs, for the fields on one step.
function checkStep(step, data) {
    const problems = {};

    if (step === 0) {
        if (!data.name.trim()) problems.name = 'Enter your full name.';
        if (!data.email.trim()) problems.email = 'Enter your email address.';
        else if (!looksLikeEmail(data.email.trim())) problems.email = 'Enter an email address like name@example.com.';
        if (!data.address.trim()) problems.address = 'Enter your home address.';
        // Optional, but when given it has to be digits (spaces, dashes, dots and brackets are dropped, as on the server).
        const phone = data.phone.replace(/[\s\-.()]/g, '');
        if (phone && !/^\+?[0-9]{7,15}$/.test(phone)) problems.phone = 'Enter a phone number using digits, like 0917 123 4567.';
    }

    if (step === 1) {
        if (data.valid_id.length === 0) problems.valid_id = 'Add a photo of at least one valid ID.';
        if (!data.date_of_adoption) problems.date_of_adoption = 'Choose the day you can pick the cat up.';
    }

    return problems;
}

function Stepper({ current }) {
    return (
        <>
            <p className="text-sm font-semibold text-azure-800 sm:hidden">
                Step {current + 1} of 3: {steps[current]}
            </p>
            <ol className="hidden items-center gap-4 sm:flex lg:gap-6" aria-label="Steps">
                {steps.map((label, index) => {
                    const done = index < current;
                    const active = index === current;

                    return (
                        <li key={label} className="flex items-center gap-4 lg:gap-6" aria-current={active ? 'step' : undefined}>
                            {index > 0 && <span className={`h-0.5 w-10 lg:w-16 ${index <= current ? 'bg-approved' : 'bg-mist-strong'}`} aria-hidden="true" />}
                            <span className="flex items-center gap-3">
                                <span
                                    className={`flex size-9 items-center justify-center rounded-full font-bold ${
                                        done ? 'bg-approved text-white' : active ? 'bg-azure-800 text-white' : 'border-[1.5px] border-mist-strong text-muted'
                                    }`}
                                >
                                    {done ? <Icon name="check" size={18} /> : index + 1}
                                </span>
                                <span className={`font-semibold ${done || active ? 'text-ink' : 'text-muted'}`}>
                                    {label}
                                    {done && <span className="sr-only"> (done)</span>}
                                </span>
                            </span>
                        </li>
                    );
                })}
            </ol>
        </>
    );
}

function DetailsStep({ form }) {
    const set = (field) => (event) => form.setData(field, event.target.value);

    return (
        <div className="flex flex-col gap-5">
            <div className="grid gap-5 sm:grid-cols-2">
                <Field label="Full name" name="name" autoComplete="name" required maxLength={255} value={form.data.name} onChange={set('name')} error={form.errors.name} />
                <Field label="Email" name="email" type="email" autoComplete="email" required maxLength={255} value={form.data.email} onChange={set('email')} error={form.errors.email} />
            </div>
            <Field
                label="Mobile number (optional)"
                name="phone"
                type="tel"
                autoComplete="tel"
                placeholder="09XX XXX XXXX"
                maxLength={20}
                help="Volunteers may text you to arrange the pickup."
                value={form.data.phone}
                onChange={set('phone')}
                error={form.errors.phone}
            />
            <Field
                label="Home address"
                name="address"
                autoComplete="street-address"
                required
                maxLength={255}
                placeholder="House number, street, barangay, city"
                help="Where the cat will live."
                value={form.data.address}
                onChange={set('address')}
                error={form.errors.address}
            />
        </div>
    );
}

function IdAndDayStep({ form, cat, today, latestPickup, idError }) {
    const ids = useId();
    const [dragging, setDragging] = useState(false);
    const inputRef = useRef(null);
    const files = form.data.valid_id;

    const addFiles = (list) => {
        const incoming = Array.from(list);
        const bad = incoming.find((file) => !ID_TYPES.includes(file.type));
        const big = incoming.find((file) => file.size > MAX_ID_BYTES);
        const empty = incoming.find((file) => file.size === 0);
        const room = MAX_IDS - files.length;

        form.clearErrors('valid_id', 'valid_id.0', 'valid_id.1');
        if (bad) return form.setError('valid_id', `${bad.name} isn't a JPG or PNG photo.`);
        if (big) return form.setError('valid_id', `${big.name} is larger than 2 MB. Try a smaller photo.`);
        if (empty) return form.setError('valid_id', `${empty.name} is empty. Choose the photo again.`);
        if (incoming.length > room) form.setError('valid_id', `You can add up to ${MAX_IDS} ID photos.`);

        form.setData('valid_id', [...files, ...incoming.slice(0, Math.max(room, 0))]);
    };

    const remove = (index) => {
        form.setData('valid_id', files.filter((_, position) => position !== index));
        // "Up to 2" and similar no longer apply once a photo is taken out.
        form.clearErrors('valid_id', 'valid_id.0', 'valid_id.1');
    };

    return (
        <div className="flex flex-col gap-9">
            <div className="flex flex-col gap-3.5">
                <h2 id={`${ids}-ids`} className="text-[17px] font-semibold">
                    Valid ID <span className="font-medium text-muted">(1 or 2 photos)</span>
                </h2>
                {files.length < MAX_IDS && (
                    <div
                        onDragOver={(event) => {
                            event.preventDefault();
                            setDragging(true);
                        }}
                        onDragLeave={() => setDragging(false)}
                        onDrop={(event) => {
                            event.preventDefault();
                            setDragging(false);
                            addFiles(event.dataTransfer.files);
                        }}
                        className={`flex flex-col items-start gap-4 rounded-2xl border-[1.5px] border-dashed p-5 sm:flex-row sm:items-center sm:p-7 ${
                            dragging ? 'border-azure-500 bg-azure-50' : idError ? 'border-rejected bg-cloud' : 'border-mist-strong bg-cloud'
                        }`}
                    >
                        <span className="flex size-14 shrink-0 items-center justify-center rounded-2xl border border-mist bg-white text-azure-800">
                            <Icon name="upload" size={24} />
                        </span>
                        <div className="flex flex-1 flex-col gap-1">
                            <span className="font-semibold">
                                Drag a photo of your ID here, or{' '}
                                <button type="button" onClick={() => inputRef.current?.click()} className="text-azure-700 underline underline-offset-2 hover:text-azure-900">
                                    choose a file
                                </button>
                            </span>
                            <span id={`${ids}-ids-help`} className="text-sm text-muted">
                                JPG or PNG, up to 2 MB each. Only AduCats admins can see it.
                            </span>
                        </div>
                        <input
                            ref={inputRef}
                            type="file"
                            accept="image/jpeg,image/png"
                            multiple
                            className="sr-only"
                            aria-labelledby={`${ids}-ids`}
                            aria-describedby={`${ids}-ids-help`}
                            tabIndex={-1}
                            onChange={(event) => {
                                addFiles(event.target.files);
                                event.target.value = '';
                            }}
                        />
                    </div>
                )}
                {files.length > 0 && (
                    <ul className="flex flex-col gap-2.5">
                        {files.map((file, index) => (
                            <FileRow key={`${file.name}-${index}`} file={file} onRemove={() => remove(index)} />
                        ))}
                    </ul>
                )}
                {idError && (
                    <p className="text-[13px] font-medium text-rejected" role="alert">
                        {idError}
                    </p>
                )}
            </div>

            <div className="flex flex-col gap-3.5">
                <h2 id={`${ids}-day`} className="text-[17px] font-semibold">
                    When can you pick {cat.name} up?
                </h2>
                <DatePicker
                    value={form.data.date_of_adoption}
                    onChange={(iso) => {
                        form.setData('date_of_adoption', iso);
                        form.clearErrors('date_of_adoption');
                    }}
                    min={today}
                    max={latestPickup}
                    labelledBy={`${ids}-day`}
                    describedBy={`${ids}-day-help`}
                    invalid={Boolean(form.errors.date_of_adoption)}
                />
                <p id={`${ids}-day-help`} className="text-sm text-muted" aria-live="polite">
                    {form.data.date_of_adoption ? `You picked ${formatLong(form.data.date_of_adoption)}.` : 'Past days are crossed out. Today is outlined. You can pick a day up to 3 months ahead.'}
                </p>
                {form.errors.date_of_adoption && (
                    <p className="text-[13px] font-medium text-rejected" role="alert">
                        {form.errors.date_of_adoption}
                    </p>
                )}
            </div>
        </div>
    );
}

function FileRow({ file, onRemove }) {
    const preview = useMemo(() => URL.createObjectURL(file), [file]);
    useEffect(() => () => URL.revokeObjectURL(preview), [preview]);

    return (
        <li className="flex items-center gap-3.5 rounded-2xl border border-mist px-4 py-3">
            <img src={preview} alt="" className="size-12 shrink-0 rounded-lg object-cover" />
            <span className="flex min-w-0 flex-1 flex-col">
                <span className="truncate font-semibold">{file.name}</span>
                <span className="text-[13px] text-muted">{formatSize(file.size)}</span>
            </span>
            <button
                type="button"
                onClick={onRemove}
                aria-label={`Remove ${file.name}`}
                className="flex size-11 shrink-0 items-center justify-center rounded-full text-muted hover:bg-azure-50 hover:text-ink"
            >
                <Icon name="close" size={18} />
            </button>
        </li>
    );
}

function ReviewStep({ form, cat, onEdit }) {
    const { data, errors } = form;
    const termsId = useId();

    return (
        <div className="flex flex-col gap-7">
            {errors.cat_id && (
                <p className="rounded-2xl bg-rejected-bg px-4 py-3 font-medium text-rejected" role="alert">
                    {errors.cat_id}
                </p>
            )}

            <div className="grid gap-5 md:grid-cols-2">
                <ReviewBox title="Your details" onEdit={() => onEdit(0)}>
                    <ReviewRow label="Name" value={data.name} />
                    <ReviewRow label="Email" value={data.email} />
                    <ReviewRow label="Mobile" value={data.phone || 'Not given'} />
                    <ReviewRow label="Address" value={data.address} />
                </ReviewBox>
                <ReviewBox title="ID and pickup" onEdit={() => onEdit(1)}>
                    <ReviewRow label="Valid ID" value={data.valid_id.map((file) => file.name).join(', ')} />
                    <ReviewRow label="Pickup" value={data.date_of_adoption ? formatLong(data.date_of_adoption) : ''} />
                </ReviewBox>
            </div>

            <div className="flex flex-col gap-3">
                <h2 id={termsId} className="text-[17px] font-bold">
                    Adoption terms
                </h2>
                <p className="text-body">If your request for {cat.name} is approved, you agree to:</p>
                <ol
                    tabIndex={0}
                    aria-labelledby={termsId}
                    className="flex max-h-72 list-decimal flex-col gap-2 overflow-y-auto rounded-2xl border border-mist bg-cloud py-4 pl-10 pr-5 text-[15px] leading-relaxed text-body focus:outline-3 focus:outline-azure-300"
                >
                    {ADOPTION_TERMS.map((term) => (
                        <li key={term}>{term}</li>
                    ))}
                </ol>
            </div>

            <div>
                <label className="flex items-start gap-3 text-base leading-normal">
                    <input
                        type="checkbox"
                        name="terms"
                        checked={data.terms}
                        onChange={(event) => {
                            form.setData('terms', event.target.checked);
                            form.clearErrors('terms');
                        }}
                        aria-invalid={errors.terms ? true : undefined}
                        aria-describedby={errors.terms ? `${termsId}-error` : undefined}
                        className="mt-0.5 size-5.5 shrink-0 accent-azure-800"
                    />
                    I've read the adoption terms and agree to follow them if my request is approved.
                </label>
                {errors.terms && (
                    <p id={`${termsId}-error`} className="mt-1.5 text-[13px] font-medium text-rejected" role="alert">
                        {errors.terms}
                    </p>
                )}
            </div>
        </div>
    );
}

function ReviewBox({ title, onEdit, children }) {
    return (
        <div className="flex flex-col gap-3 rounded-2xl border border-mist px-5 py-4">
            <div className="flex items-center justify-between">
                <h2 className="font-bold">{title}</h2>
                <button type="button" onClick={onEdit} className="text-sm font-semibold text-azure-700 hover:text-azure-900">
                    Edit<span className="sr-only"> {title.toLowerCase()}</span>
                </button>
            </div>
            <dl className="grid grid-cols-[80px_minmax(0,1fr)] gap-x-3 gap-y-2 text-[15px]">{children}</dl>
        </div>
    );
}

function ReviewRow({ label, value }) {
    return (
        <>
            <dt className="text-muted">{label}</dt>
            <dd className="break-words font-semibold">{value}</dd>
        </>
    );
}

function CatSummary({ cat, step }) {
    const facts = [
        ['Age', cat.ageLabel],
        ['Sex', cat.sex],
        ['Color', cat.color],
        ['Breed', cat.breed],
    ].filter(([, value]) => value);

    return (
        <aside className="overflow-hidden rounded-[24px] border border-mist bg-white" aria-label={`About ${cat.name}`}>
            <CatPhoto cat={cat} className="h-48 w-full sm:h-60" />
            <div className="flex flex-col gap-4 p-6">
                <div>
                    <p className="text-sm text-muted">You're asking to adopt</p>
                    <p className="font-display text-3xl font-semibold">{cat.name}</p>
                </div>
                <dl className="grid grid-cols-2 gap-x-3 gap-y-2 text-[15px]">
                    {facts.map(([label, value]) => (
                        <div key={label} className="contents">
                            <dt className="text-muted">{label}</dt>
                            <dd className="font-semibold">{value}</dd>
                        </div>
                    ))}
                </dl>
                <p className="rounded-2xl bg-sky px-4 py-3.5 text-sm leading-relaxed text-body">
                    {step < 2
                        ? `${cat.name}'s details are filled in for you, so your request always matches the profile.`
                        : 'After you send it, the request shows up in My requests as Pending while a volunteer reviews it.'}
                </p>
            </div>
        </aside>
    );
}
