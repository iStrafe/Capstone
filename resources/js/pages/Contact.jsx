import { useForm, usePage } from '@inertiajs/react';
import Button from '../components/Button';
import Field from '../components/Field';
import Icon from '../components/Icon';
import PageHeader from '../components/PageHeader';
import SiteLayout from '../layouts/SiteLayout';
import { FACEBOOK_URL, MAPS_URL } from '../lib/places';

// Contact page. The form posts to /contact, which redirects back here with a flash message.
export default function Contact() {
    const { links } = usePage().props;
    const form = useForm({ full_name: '', mobile_number: '', email: '', message: '' });

    const submit = (event) => {
        event.preventDefault();
        form.post(links.contactSend, { onSuccess: () => form.reset() });
    };

    return (
        <SiteLayout title="Contact" active="contact">
            <PageHeader eyebrow="Contact" title="Talk to AduCats">
                Questions about adopting, volunteering or a cat you saw on campus? Send us a message and a volunteer will get back to you.
            </PageHeader>

            <div className="mx-auto grid max-w-7xl items-start gap-8 px-4 pb-20 sm:px-8 lg:grid-cols-[1fr_400px]">
                <form onSubmit={submit} noValidate className="flex flex-col gap-5 rounded-[24px] border border-mist bg-white p-6 sm:p-9" aria-label="Contact form">
                    <div className="grid gap-5 sm:grid-cols-2">
                        <Field
                            label="Full name"
                            name="full_name"
                            autoComplete="name"
                            required
                            maxLength={255}
                            value={form.data.full_name}
                            onChange={(event) => form.setData('full_name', event.target.value)}
                            error={form.errors.full_name}
                        />
                        <Field
                            label="Mobile number"
                            name="mobile_number"
                            type="tel"
                            autoComplete="tel"
                            placeholder="09XX XXX XXXX"
                            required
                            maxLength={15}
                            value={form.data.mobile_number}
                            onChange={(event) => form.setData('mobile_number', event.target.value)}
                            error={form.errors.mobile_number}
                        />
                    </div>
                    <Field
                        label="Email (optional)"
                        name="email"
                        type="email"
                        autoComplete="email"
                        placeholder="you@example.com"
                        help="Add it if you’d like a reply by email."
                        value={form.data.email}
                        onChange={(event) => form.setData('email', event.target.value)}
                        error={form.errors.email}
                    />
                    <Field
                        label="Message"
                        name="message"
                        multiline
                        rows={6}
                        required
                        placeholder="How can we help?"
                        value={form.data.message}
                        onChange={(event) => form.setData('message', event.target.value)}
                        error={form.errors.message}
                    />
                    <div className="flex flex-col-reverse items-start justify-between gap-4 sm:flex-row sm:items-center">
                        <p className="text-sm text-muted">We usually answer within a few days.</p>
                        <Button type="submit" variant="dark" disabled={form.processing}>
                            <Icon name="send" size={18} /> {form.processing ? 'Sending…' : 'Send message'}
                        </Button>
                    </div>
                </form>

                <aside className="flex flex-col gap-4 rounded-[24px] border border-mist bg-white p-6 sm:p-7">
                    <h2 className="text-lg font-bold">Other ways to reach us</h2>
                    <p className="flex gap-2.5 leading-relaxed text-body">
                        <Icon name="pin" className="mt-0.5 shrink-0 text-azure-700" />
                        900 San Marcelino St., Ermita, Manila 1000
                    </p>
                    <a href={MAPS_URL} target="_blank" rel="noreferrer" className="flex items-center gap-2 font-semibold text-azure-700 hover:text-azure-900">
                        <Icon name="external" size={18} /> Open in Google Maps
                    </a>
                    <a href={FACEBOOK_URL} target="_blank" rel="noreferrer" className="flex items-center gap-2 font-semibold text-azure-700 hover:text-azure-900">
                        <Icon name="external" size={18} /> AduCats on Facebook
                    </a>
                </aside>
            </div>
        </SiteLayout>
    );
}
