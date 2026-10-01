import { useForm, usePage } from '@inertiajs/react';
import Button from '../../components/Button';
import Field from '../../components/Field';
import FocusLayout from '../../layouts/FocusLayout';

// Opened from the link in the password reset email.
export default function ResetPassword({ token, email }) {
    const { links } = usePage().props;
    const form = useForm({ token, email, password: '', password_confirmation: '' });

    const submit = (event) => {
        event.preventDefault();
        form.post(links.passwordStore, { onFinish: () => form.reset('password', 'password_confirmation') });
    };

    return (
        <FocusLayout title="Choose a new password" exit={{ href: links.login, label: 'Back to log in', icon: 'chevronLeft' }}>
            <div className="mx-auto max-w-md px-4 py-10 sm:py-16">
                <form onSubmit={submit} className="flex flex-col gap-5 rounded-[24px] border border-mist bg-white p-6 sm:p-8">
                    <div>
                        <h1 className="font-display text-[28px] font-semibold leading-tight">Choose a new password</h1>
                        <p className="mt-2 leading-relaxed text-body">Use a long password you don’t use anywhere else.</p>
                    </div>

                    <Field
                        label="Email"
                        type="email"
                        autoComplete="email"
                        required
                        value={form.data.email}
                        onChange={(event) => form.setData('email', event.target.value)}
                        error={form.errors.email}
                    />
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

                    <Button type="submit" disabled={form.processing} className="w-full">
                        {form.processing ? 'Saving…' : 'Save password'}
                    </Button>
                </form>
            </div>
        </FocusLayout>
    );
}
