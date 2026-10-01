import { useForm, usePage } from '@inertiajs/react';
import Button from '../../components/Button';
import Field from '../../components/Field';
import Icon from '../../components/Icon';
import FocusLayout from '../../layouts/FocusLayout';

export default function ForgotPassword() {
    const { links, flash } = usePage().props;
    const form = useForm({ email: '' });

    const submit = (event) => {
        event.preventDefault();
        form.post(links.passwordEmail);
    };

    return (
        <FocusLayout title="Forgot your password?" exit={{ href: links.login, label: 'Back to log in', icon: 'chevronLeft' }}>
            <div className="mx-auto max-w-md px-4 py-10 sm:py-16">
                <form onSubmit={submit} className="flex flex-col gap-5 rounded-[24px] border border-mist bg-white p-6 sm:p-8">
                    <div>
                        <h1 className="font-display text-[28px] font-semibold leading-tight">Forgot your password?</h1>
                        <p className="mt-2 leading-relaxed text-body">Enter your email and we’ll send a link to choose a new one.</p>
                    </div>

                    {flash?.status && (
                        <p role="status" className="flex gap-2.5 rounded-xl bg-approved-bg px-3.5 py-3 text-sm font-semibold text-approved">
                            <Icon name="mail" size={18} className="mt-px shrink-0" /> {flash.status}
                        </p>
                    )}

                    <Field
                        label="Email"
                        type="email"
                        autoComplete="email"
                        placeholder="you@example.com"
                        required
                        value={form.data.email}
                        onChange={(event) => form.setData('email', event.target.value)}
                        error={form.errors.email}
                    />

                    <Button type="submit" disabled={form.processing} className="w-full">
                        {form.processing ? 'Sending…' : 'Email me a link'}
                    </Button>

                    <p className="text-sm text-muted">Signed up with Google? This also works if you want a password for your account.</p>
                </form>
            </div>
        </FocusLayout>
    );
}
