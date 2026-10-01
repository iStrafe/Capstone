import { Link, router, useForm, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import Button from '../../components/Button';
import Field from '../../components/Field';
import Icon, { GoogleIcon } from '../../components/Icon';
import SiteLayout from '../../layouts/SiteLayout';

// What each saved form says next to its button. The server sends these codes as flash.status.
const saved = {
    'profile-updated': 'details',
    'password-updated': 'password',
    'password-set': 'password',
};

export default function Edit({ account }) {
    const { links, flash } = usePage().props;
    const savedSection = saved[flash?.status];

    return (
        <SiteLayout title="Profile and security" active={null}>
            <div className="mx-auto flex max-w-7xl flex-col gap-8 px-4 pb-20 pt-8 sm:px-8 sm:pt-12 lg:flex-row lg:gap-10">
                <AccountNav links={links} />

                <div className="flex min-w-0 flex-1 flex-col gap-6">
                    <h1 className="font-display text-4xl font-semibold sm:text-[44px]">Profile and security</h1>
                    <DetailsSection account={account} saved={savedSection === 'details'} />
                    <PasswordSection account={account} saved={savedSection === 'password'} justSet={flash?.status === 'password-set'} />
                    <DeleteSection account={account} />
                </div>
            </div>
        </SiteLayout>
    );
}

function AccountNav({ links }) {
    const item = 'flex h-11 shrink-0 items-center gap-2.5 rounded-xl px-3.5 font-medium text-body hover:bg-white hover:text-ink';

    return (
        <nav aria-label="Your account" className="flex shrink-0 flex-col gap-1 lg:w-60">
            <span className="px-3.5 pb-3 text-[13px] font-bold uppercase tracking-[0.14em] text-azure-700">Your account</span>
            <div className="flex flex-wrap gap-1 lg:flex-col">
                <Link href={links.myRequests} className={item}>
                    <Icon name="list" size={18} /> My requests
                </Link>
                <Link href={links.profile} aria-current="page" className={`${item} bg-white font-bold text-ink shadow-[0_1px_0_#D2DCEA]`}>
                    <Icon name="user" size={18} /> Profile and security
                </Link>
                <button type="button" onClick={() => router.post(links.logout)} className={item}>
                    <Icon name="logOut" size={18} /> Log out
                </button>
            </div>
        </nav>
    );
}

function Section({ id, title, text, danger = false, children }) {
    return (
        <section
            aria-labelledby={id}
            className={`flex flex-col gap-6 rounded-[24px] border bg-white p-6 sm:p-8 xl:flex-row xl:gap-12 ${danger ? 'border-[#e7b7b0]' : 'border-mist'}`}
        >
            <div className="flex flex-col gap-2 xl:w-70 xl:shrink-0">
                <h2 id={id} className={`text-xl font-bold ${danger ? 'text-rejected' : 'text-ink'}`}>
                    {title}
                </h2>
                <div className="text-[15px] leading-relaxed text-muted">{text}</div>
            </div>
            <div className="flex max-w-xl flex-1 flex-col gap-[18px]">{children}</div>
        </section>
    );
}

function Saved({ show, children = 'Saved' }) {
    return (
        <span role="status" className="flex items-center gap-1.5 text-sm font-semibold text-approved">
            {show && (
                <>
                    <Icon name="check" size={16} /> {children}
                </>
            )}
        </span>
    );
}

function DetailsSection({ account, saved }) {
    const { links } = usePage().props;
    const form = useForm({ name: account.name, email: account.email });

    const submit = (event) => {
        event.preventDefault();
        form.patch(links.profile, { preserveScroll: true, onSuccess: () => form.setDefaults() });
    };

    return (
        <Section id="details-title" title="Your details" text="Used to fill in your adoption requests.">
            <form onSubmit={submit} className="flex flex-col gap-[18px]">
                <Field
                    label="Full name"
                    autoComplete="name"
                    required
                    value={form.data.name}
                    onChange={(event) => form.setData('name', event.target.value)}
                    error={form.errors.name}
                />
                <Field
                    label="Email"
                    type="email"
                    autoComplete="email"
                    required
                    help="You log in with this email."
                    value={form.data.email}
                    onChange={(event) => form.setData('email', event.target.value)}
                    error={form.errors.email}
                />
                <div className="flex items-center gap-3">
                    <Button type="submit" size="sm" disabled={form.processing || !form.isDirty}>
                        Save details
                    </Button>
                    <Saved show={saved && !form.isDirty} />
                </div>
            </form>
        </Section>
    );
}

function PasswordSection({ account, saved, justSet }) {
    const { links } = usePage().props;
    const form = useForm({ current_password: '', password: '', password_confirmation: '' });

    const submit = (event) => {
        event.preventDefault();
        form.put(links.passwordUpdate, {
            errorBag: 'updatePassword',
            preserveScroll: true,
            onSuccess: () => form.reset(),
            onError: () => form.reset('password', 'password_confirmation'),
        });
    };

    const newPasswordFields = (
        <div className="grid gap-4 sm:grid-cols-2">
            <Field
                label="New password"
                type="password"
                autoComplete="new-password"
                help="At least 8 characters."
                required
                value={form.data.password}
                onChange={(event) => form.setData('password', event.target.value)}
                error={form.errors.password}
            />
            <Field
                label="Confirm new password"
                type="password"
                autoComplete="new-password"
                required
                value={form.data.password_confirmation}
                onChange={(event) => form.setData('password_confirmation', event.target.value)}
                error={form.errors.password_confirmation}
            />
        </div>
    );

    if (!account.hasPassword) {
        return (
            <Section id="password-title" title="Set a password" text="Then you can also log in with your email and password.">
                <form onSubmit={submit} className="flex flex-col gap-[18px]">
                    <p className="flex gap-2.5 rounded-[14px] bg-sky px-4 py-3.5 text-sm leading-normal text-body">
                        <GoogleIcon className="mt-px shrink-0" />
                        <span>You signed up with Google, so this account has no password yet. You can keep using Google either way.</span>
                    </p>
                    {newPasswordFields}
                    <Button type="submit" size="sm" disabled={form.processing} className="self-start">
                        Set password
                    </Button>
                </form>
            </Section>
        );
    }

    return (
        <Section id="password-title" title="Password" text="Use a long password you don’t use anywhere else.">
            <form onSubmit={submit} className="flex flex-col gap-[18px]">
                <Field
                    label="Current password"
                    type="password"
                    autoComplete="current-password"
                    required
                    value={form.data.current_password}
                    onChange={(event) => form.setData('current_password', event.target.value)}
                    error={form.errors.current_password}
                />
                {newPasswordFields}
                <div className="flex items-center gap-3">
                    <Button type="submit" size="sm" disabled={form.processing}>
                        Update password
                    </Button>
                    <Saved show={saved}>{justSet ? 'Password set. You can now log in with it too.' : 'Saved'}</Saved>
                </div>
            </form>
        </Section>
    );
}

function DeleteSection({ account }) {
    const { links, errors } = usePage().props;
    // Reopen the dialog when the server sent it back with an error.
    const [open, setOpen] = useState(Boolean(errors?.userDeletion));

    return (
        <Section
            id="delete-title"
            title="Delete account"
            danger
            text="Removes your account and logs you out. This can’t be undone."
        >
            <p className="leading-relaxed text-body">
                Requests you already sent stay with the volunteers, so let them know if you no longer want to adopt.{' '}
                {account.hasPassword ? 'You’ll be asked for your password to confirm.' : 'You’ll be asked to type your email to confirm.'}
            </p>
            <Button variant="danger" size="sm" onClick={() => setOpen(true)} className="self-start">
                <Icon name="trash" size={16} /> Delete my account
            </Button>
            <DeleteDialog account={account} open={open} onClose={() => setOpen(false)} action={links.profile} />
        </Section>
    );
}

function DeleteDialog({ account, open, onClose, action }) {
    const dialog = useRef(null);
    const form = useForm(account.hasPassword ? { password: '' } : { confirm_email: '' });

    useEffect(() => {
        const el = dialog.current;

        if (open && !el.open) {
            el.showModal();
        } else if (!open && el.open) {
            el.close();
        }
    }, [open]);

    const close = () => {
        form.reset();
        form.clearErrors();
        onClose();
    };

    const submit = (event) => {
        event.preventDefault();
        form.delete(action, { errorBag: 'userDeletion', preserveScroll: true, onError: () => form.reset() });
    };

    return (
        <dialog
            ref={dialog}
            onClose={close}
            onClick={(event) => event.target === dialog.current && close()}
            aria-labelledby="delete-dialog-title"
            className="m-auto w-[calc(100%-2rem)] max-w-md rounded-3xl bg-white p-0 text-ink shadow-2xl backdrop:bg-azure-950/50"
        >
            <form onSubmit={submit} className="flex flex-col gap-5 p-6 sm:p-8">
                <div>
                    <h2 id="delete-dialog-title" className="font-display text-3xl font-semibold">
                        Delete your account?
                    </h2>
                    <p className="mt-2 leading-relaxed text-body">You’ll be logged out, and you won’t be able to log back in to this account.</p>
                </div>

                {account.hasPassword ? (
                    <Field
                        label="Your password"
                        type="password"
                        autoComplete="current-password"
                        required
                        autoFocus
                        value={form.data.password}
                        onChange={(event) => form.setData('password', event.target.value)}
                        error={form.errors.password}
                    />
                ) : (
                    <Field
                        label={`Type ${account.email} to confirm`}
                        type="email"
                        autoComplete="off"
                        required
                        autoFocus
                        value={form.data.confirm_email}
                        onChange={(event) => form.setData('confirm_email', event.target.value)}
                        error={form.errors.confirm_email}
                    />
                )}

                <div className="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <Button variant="outline" onClick={close}>
                        Cancel
                    </Button>
                    <Button type="submit" variant="danger" disabled={form.processing}>
                        {form.processing ? 'Deleting…' : 'Delete account'}
                    </Button>
                </div>
            </form>
        </dialog>
    );
}
