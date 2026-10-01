import { Link, useForm, usePage } from '@inertiajs/react';
import Button from '../../components/Button';
import Field from '../../components/Field';
import GoogleButton, { OrDivider } from '../../components/GoogleButton';
import AuthLayout from '../../layouts/AuthLayout';

export default function Register({ panelCat }) {
    const { links } = usePage().props;
    const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });

    const submit = (event) => {
        event.preventDefault();
        form.post(links.register, { onFinish: () => form.reset('password', 'password_confirmation') });
    };

    const adopting = panelCat?.adopting;
    const quote = adopting
        ? { title: `${panelCat.name} is waiting.`, text: `Make an account and you’ll go straight back to ${panelCat.name}’s adoption request.` }
        : { title: 'One account, every request.', text: 'Follow each adoption request from sent to released.' };

    return (
        <AuthLayout title="Create an account" cat={panelCat} quote={quote}>
            <div>
                <h1 className="font-display text-4xl font-semibold sm:text-[44px]">Create your account</h1>
                <p className="mt-2 text-[17px] text-body">
                    {adopting ? `You need one to adopt ${panelCat.name}. It takes a minute.` : 'You need one to adopt. It takes a minute.'}
                </p>
            </div>

            <GoogleButton>Sign up with Google</GoogleButton>
            <OrDivider />

            <form onSubmit={submit} className="flex flex-col gap-[18px]">
                <Field
                    label="Full name"
                    autoComplete="name"
                    placeholder="Juan Dela Cruz"
                    required
                    value={form.data.name}
                    onChange={(event) => form.setData('name', event.target.value)}
                    error={form.errors.name}
                />
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
                <div className="grid gap-4 sm:grid-cols-2">
                    <Field
                        label="Password"
                        type="password"
                        autoComplete="new-password"
                        help="At least 8 characters."
                        required
                        value={form.data.password}
                        onChange={(event) => form.setData('password', event.target.value)}
                        error={form.errors.password}
                    />
                    <Field
                        label="Confirm password"
                        type="password"
                        autoComplete="new-password"
                        required
                        value={form.data.password_confirmation}
                        onChange={(event) => form.setData('password_confirmation', event.target.value)}
                        error={form.errors.password_confirmation}
                    />
                </div>

                <Button type="submit" variant="primary" disabled={form.processing} className="w-full">
                    {form.processing ? 'Creating your account…' : 'Create account'}
                </Button>
            </form>

            <p className="text-center text-body">
                Already have one?{' '}
                <Link href={links.login} className="font-semibold text-azure-700 hover:text-azure-900 hover:underline">
                    Log in
                </Link>
            </p>
        </AuthLayout>
    );
}
