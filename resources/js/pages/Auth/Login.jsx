import { Link, useForm, usePage } from '@inertiajs/react';
import Button from '../../components/Button';
import Field from '../../components/Field';
import GoogleButton, { OrDivider } from '../../components/GoogleButton';
import AuthLayout from '../../layouts/AuthLayout';

export default function Login({ panelCat }) {
    // errors: messages the server sends with a redirect here, such as a failed Google sign-in.
    const { links, errors } = usePage().props;
    const form = useForm({ email: '', password: '', remember: false });

    const submit = (event) => {
        event.preventDefault();
        form.post(links.login, { onFinish: () => form.reset('password') });
    };

    const adopting = panelCat?.adopting;

    return (
        <AuthLayout title="Log in" cat={panelCat} quote={quoteFor(panelCat)}>
            <div>
                <h1 className="font-display text-4xl font-semibold sm:text-[44px]">Welcome back</h1>
                <p className="mt-2 text-[17px] text-body">
                    {adopting ? `Log in to send your request to adopt ${panelCat.name}.` : 'Log in to send and track adoption requests.'}
                </p>
            </div>

            <GoogleButton>Continue with Google</GoogleButton>
            <OrDivider />

            <form onSubmit={submit} className="flex flex-col gap-5">
                <Field
                    label="Email"
                    type="email"
                    autoComplete="email"
                    required
                    value={form.data.email}
                    onChange={(event) => form.setData('email', event.target.value)}
                    error={form.errors.email ?? errors.email}
                />
                <Field
                    label="Password"
                    type="password"
                    autoComplete="current-password"
                    required
                    value={form.data.password}
                    onChange={(event) => form.setData('password', event.target.value)}
                    error={form.errors.password}
                />

                <div className="flex flex-wrap items-center justify-between gap-3">
                    <label className="flex items-center gap-2.5 text-[15px]">
                        <input
                            type="checkbox"
                            checked={form.data.remember}
                            onChange={(event) => form.setData('remember', event.target.checked)}
                            className="size-5 accent-azure-800"
                        />
                        Keep me logged in
                    </label>
                    <Link href={links.passwordRequest} className="text-[15px] font-semibold text-azure-700 hover:text-azure-900 hover:underline">
                        Forgot password?
                    </Link>
                </div>

                <Button type="submit" variant="primary" disabled={form.processing} className="w-full">
                    {form.processing ? 'Logging in…' : 'Log in'}
                </Button>
            </form>

            <p className="text-center text-body">
                New to AduCats?{' '}
                <Link href={links.register} className="font-semibold text-azure-700 hover:text-azure-900 hover:underline">
                    Create an account
                </Link>
            </p>
        </AuthLayout>
    );
}

function quoteFor(cat) {
    if (cat?.adopting) {
        return { title: `${cat.name} is waiting.`, text: `Log in and you’ll go straight back to ${cat.name}’s adoption request.` };
    }

    if (cat) {
        return { title: `${cat.name} is looking for a home.`, text: 'Log in to send and follow your adoption requests.' };
    }

    return { title: 'Every campus cat deserves a home.', text: 'Log in to send and follow your adoption requests.' };
}
