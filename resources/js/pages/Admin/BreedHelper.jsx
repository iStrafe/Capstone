import { Link, useForm } from '@inertiajs/react';
import Button, { buttonClasses } from '../../components/Button';
import Icon from '../../components/Icon';
import AdminLayout, { AdminCard, AdminHeader } from '../../layouts/AdminLayout';
import useObjectUrl from '../../lib/useObjectUrl';

// Upload a photo and get a breed guess from OpenAI, with the reasons. Useful when adding a cat.
export default function BreedHelper({ result, error, analyzeUrl, addCatUrl }) {
    const form = useForm({ image: null });
    const photo = useObjectUrl(form.data.image);

    const submit = (event) => {
        event.preventDefault();
        form.post(analyzeUrl, { forceFormData: true, preserveState: true, preserveScroll: true });
    };

    const addCatHref = result?.breed
        ? `${addCatUrl}?${new URLSearchParams(Object.fromEntries(Object.entries({ breed: result.breed, color: result.color }).filter(([, value]) => value)))}`
        : addCatUrl;

    return (
        <AdminLayout title="Breed helper" active="breed">
            <AdminHeader title="Breed helper">Upload a photo and get a breed guess with the reasons. Useful when adding a new cat.</AdminHeader>

            <div className="flex flex-col gap-6 lg:flex-row lg:items-start">
                <AdminCard title="Photo" className="w-full lg:w-[420px] lg:shrink-0">
                    <form onSubmit={submit} className="flex flex-col gap-4">
                        {photo ? (
                            <img src={photo} alt="The photo to analyze" className="h-72 w-full rounded-2xl bg-cloud object-contain" />
                        ) : (
                            <label className="flex h-72 cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-[1.5px] border-dashed border-mist-strong bg-cloud text-center font-semibold text-azure-800 has-focus-visible:outline-3 has-focus-visible:outline-azure-300">
                                <Icon name="upload" size={26} /> Choose a photo of the cat
                                <span className="text-[13px] font-medium text-muted">JPG or PNG, up to 2 MB</span>
                                <input type="file" accept="image/jpeg,image/png" className="sr-only" onChange={(event) => form.setData('image', event.target.files[0] ?? null)} />
                            </label>
                        )}
                        <div className="flex flex-wrap gap-2.5">
                            {photo && (
                                <label className={`${buttonClasses({ variant: 'quiet', size: 'sm' })} cursor-pointer has-focus-visible:outline-3 has-focus-visible:outline-azure-300`}>
                                    Choose another photo
                                    <input type="file" accept="image/jpeg,image/png" className="sr-only" onChange={(event) => form.setData('image', event.target.files[0] ?? null)} />
                                </label>
                            )}
                            <Button type="submit" variant="dark" size="sm" disabled={!form.data.image || form.processing}>
                                <Icon name="spark" size={16} /> {form.processing ? 'Analyzing…' : 'Analyze'}
                            </Button>
                        </div>
                        {form.errors.image && <p className="text-[13px] font-medium text-rejected">{form.errors.image}</p>}
                        <p className="text-[13px] text-muted">The photo is sent to OpenAI for the guess and isn’t kept on the site.</p>
                    </form>
                </AdminCard>

                <section aria-live="polite" className="flex min-w-0 flex-1 flex-col gap-4 rounded-[20px] border border-mist bg-white p-6 sm:p-7">
                    {error ? (
                        <>
                            <span className="text-[13px] font-bold uppercase tracking-[0.08em] text-muted">No result</span>
                            <p role="alert" className="rounded-2xl bg-rejected-bg px-4 py-3.5 font-medium text-rejected">{error}</p>
                            <p className="text-body">You can still add the cat and fill in the breed yourself.</p>
                        </>
                    ) : result ? (
                        <>
                            <span className="text-[13px] font-bold uppercase tracking-[0.08em] text-azure-700">Best guess</span>
                            {result.breed ? (
                                <>
                                    <h2 className="font-display text-[34px] font-semibold leading-tight sm:text-[40px]">{result.breed}</h2>
                                    {result.color && <p className="text-body">Color: {result.color}</p>}
                                    {result.traits.length > 0 && (
                                        <ol className="flex list-decimal flex-col gap-2.5 pl-5 text-[16px] leading-relaxed text-body marker:font-semibold marker:text-azure-700">
                                            {result.traits.map((trait) => (
                                                <li key={trait}>{trait}</li>
                                            ))}
                                        </ol>
                                    )}
                                </>
                            ) : (
                                <p className="whitespace-pre-line text-[16px] leading-relaxed text-body">{result.text}</p>
                            )}
                            <p className="rounded-2xl bg-pending-bg px-4 py-3.5 text-sm font-medium text-pending">
                                This is a guess from a photo, not a vet’s assessment.
                            </p>
                            {result.breed && (
                                <Link href={addCatHref} className={`${buttonClasses({ variant: 'outline', size: 'sm' })} self-start`}>
                                    <Icon name="plus" size={16} /> Add a cat with this breed
                                </Link>
                            )}
                        </>
                    ) : (
                        <div className="flex flex-col items-center gap-2 py-16 text-center">
                            <Icon name="spark" size={28} className="text-azure-600" />
                            <p className="font-bold">No guess yet</p>
                            <p className="max-w-sm text-body">Choose a photo and press Analyze. The guess and the reasons for it show here.</p>
                        </div>
                    )}
                </section>
            </div>
        </AdminLayout>
    );
}
