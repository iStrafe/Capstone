import { Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import ArchiveCatDialog from '../../../components/ArchiveCatDialog';
import Button, { buttonClasses } from '../../../components/Button';
import Field from '../../../components/Field';
import Icon from '../../../components/Icon';
import StatusBadge from '../../../components/StatusBadge';
import AdminLayout, { AdminCard, AdminHeader } from '../../../layouts/AdminLayout';
import useObjectUrl from '../../../lib/useObjectUrl';

// One page to add a cat or edit one, with a preview of the public card.
export default function Edit({ cat, defaults, submitUrl, indexUrl, placeholder }) {
    const editing = cat !== null;
    const archived = cat?.state === 'archived';
    const [archiving, setArchiving] = useState(null);
    const [restoring, setRestoring] = useState(false);

    const form = useForm({
        cat_name: cat?.name ?? '',
        age: cat?.age ?? '',
        color: cat?.color ?? defaults?.color ?? '',
        breed: cat?.breed ?? defaults?.breed ?? '',
        sex: cat?.sex ?? '',
        Medical_Record: cat?.medicalRecord ?? '',
        status: archived ? undefined : (cat?.status ?? 'Active'),
        cat_image: null,
        cat_clip: null,
        // Files only travel in a POST, so updates are sent as POST with Laravel's method override.
        ...(editing ? { _method: 'put' } : {}),
    });
    const { data, errors } = form;
    // Editing a field clears its old error, so fixed fields don't stay red until the next save.
    const set = (field, value) => {
        form.setData(field, value);
        form.clearErrors(field);
    };

    const photo = useObjectUrl(data.cat_image) ?? cat?.image ?? null;
    const clip = useObjectUrl(data.cat_clip) ?? cat?.clip ?? null;
    const name = data.cat_name.trim() || cat?.name || 'New cat';
    const title = editing ? `Edit ${cat.name}` : 'Add a cat';

    const submit = (event) => {
        event.preventDefault();
        form.post(submitUrl, { forceFormData: true, preserveScroll: true });
    };

    const restore = () => router.patch(cat.restoreUrl, {}, { onStart: () => setRestoring(true), onFinish: () => setRestoring(false) });

    const ageLabel = data.age === '' ? 'Age unknown' : Number(data.age) < 1 ? 'Under 1 year' : Number(data.age) === 1 ? '1 year' : `${data.age} years`;
    const previewState = editing ? (data.status === 'Inactive' && !archived ? 'inactive' : cat.state === 'inactive' && data.status === 'Active' ? 'available' : cat.state) : 'available';

    return (
        <AdminLayout title={title} active="cats">
            <nav aria-label="Breadcrumb" className="flex items-center gap-1.5 text-sm">
                <Link href={indexUrl} className="font-semibold text-azure-700 hover:underline">Cats</Link>
                <Icon name="chevronRight" size={14} className="text-muted" />
                <span className="text-muted">{title}</span>
            </nav>

            <AdminHeader
                title={title}
                actions={
                    <>
                        <Link href={indexUrl} className={buttonClasses({ variant: 'outline', size: 'sm' })}>Cancel</Link>
                        <Button type="submit" form="cat-form" variant="dark" size="sm" disabled={form.processing}>
                            {form.processing ? 'Saving…' : editing ? 'Save changes' : 'Add cat'}
                        </Button>
                    </>
                }
            >
                {editing ? (archived ? `${cat.name} is archived, so these changes only show on the archived page.` : 'Changes show on the public profile as soon as you save.') : 'New cats go straight onto the adoption list.'}
            </AdminHeader>

            {form.progress && (
                <div className="h-1.5 overflow-hidden rounded-full bg-mist" role="progressbar" aria-label="Uploading" aria-valuenow={form.progress.percentage} aria-valuemin={0} aria-valuemax={100}>
                    <div className="h-full bg-azure-500" style={{ width: `${form.progress.percentage}%` }} />
                </div>
            )}

            <form id="cat-form" onSubmit={submit} className="flex flex-col gap-6 lg:flex-row lg:items-start" noValidate>
                <div className="flex min-w-0 flex-1 flex-col gap-5">
                    <AdminCard title="Photo and clip">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="flex flex-col gap-2">
                                <div className="relative overflow-hidden rounded-2xl border border-mist bg-azure-50">
                                    {photo ? (
                                        <img src={photo} alt={`Photo of ${name}`} className="h-48 w-full object-cover" />
                                    ) : (
                                        <div className="flex h-48 flex-col items-center justify-center gap-2 text-muted">
                                            <Icon name="image" size={28} /> No photo yet
                                        </div>
                                    )}
                                </div>
                                <FileButton
                                    label={photo ? 'Replace photo' : 'Choose a photo'}
                                    accept="image/jpeg,image/png,image/gif"
                                    onChange={(file) => set('cat_image', file)}
                                    fileName={data.cat_image?.name}
                                    error={errors.cat_image}
                                />
                            </div>
                            <div className="flex flex-col gap-2">
                                <div className="overflow-hidden rounded-2xl border border-dashed border-mist-strong bg-cloud">
                                    {clip ? (
                                        <video src={clip} controls className="h-48 w-full bg-ink object-contain">
                                            <track kind="captions" />
                                        </video>
                                    ) : (
                                        <div className="flex h-48 flex-col items-center justify-center gap-1.5 text-center font-semibold text-azure-800">
                                            <Icon name="play" size={24} /> No clip
                                            <span className="text-[13px] font-medium text-muted">Optional</span>
                                        </div>
                                    )}
                                </div>
                                <FileButton
                                    label={clip ? 'Replace clip' : 'Add a short clip'}
                                    accept="video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-flv"
                                    onChange={(file) => set('cat_clip', file)}
                                    fileName={data.cat_clip?.name}
                                    error={errors.cat_clip}
                                />
                            </div>
                        </div>
                        <p className="text-[13px] text-muted">Photos: JPG, PNG or GIF. SVG isn’t accepted. Clips: MP4, MOV, AVI, WMV or FLV, up to 25 MB. Without a photo the site shows the neutral placeholder.</p>
                    </AdminCard>

                    <AdminCard title="Details">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <Field label="Name" value={data.cat_name} onChange={(e) => set('cat_name', e.target.value)} error={errors.cat_name} required maxLength={255} />
                            <Field
                                label="Age in years"
                                type="number"
                                min={0}
                                max={30}
                                step={1}
                                inputMode="numeric"
                                value={data.age}
                                onChange={(e) => set('age', e.target.value)}
                                error={errors.age}
                                help="Leave empty if unknown. 0 shows as “Under 1 year”."
                            />
                            <Field label="Color" value={data.color} onChange={(e) => set('color', e.target.value)} error={errors.color} maxLength={50} />
                            <Field label="Breed" value={data.breed} onChange={(e) => set('breed', e.target.value)} error={errors.breed} maxLength={100} />
                        </div>

                        <fieldset>
                            <legend className="mb-2 text-sm font-semibold text-ink">Sex</legend>
                            <div className="flex gap-2.5">
                                {['Female', 'Male'].map((sex) => (
                                    <label
                                        key={sex}
                                        className={`flex h-11 cursor-pointer items-center gap-2 rounded-full border-[1.5px] px-4 font-medium has-focus-visible:outline-3 has-focus-visible:outline-azure-300 ${data.sex === sex ? 'border-ink bg-ink text-white' : 'border-mist-strong bg-white hover:border-azure-500'}`}
                                    >
                                        <input type="radio" name="sex" value={sex} checked={data.sex === sex} onChange={() => set('sex', sex)} className="sr-only" />
                                        {data.sex === sex && <Icon name="check" size={16} />} {sex}
                                    </label>
                                ))}
                            </div>
                            {errors.sex && <p className="mt-1.5 text-[13px] font-medium text-rejected">{errors.sex}</p>}
                        </fieldset>

                        <Field
                            label="Health notes"
                            multiline
                            rows={3}
                            value={data.Medical_Record}
                            onChange={(e) => set('Medical_Record', e.target.value)}
                            error={errors.Medical_Record}
                            maxLength={255}
                            help="For example: Spayed, vaccinated (anti-rabies Sep 2026)."
                        />
                    </AdminCard>
                </div>

                <aside className="flex w-full flex-col gap-5 lg:w-88 lg:shrink-0">
                    {editing && (
                        <AdminCard title="Visibility">
                            {archived ? (
                                <>
                                    <p className="text-body">
                                        Archived {cat.archivedAt}. {cat.archiveReason ? `Visitors see: “${cat.archiveReason}”` : 'No reason was given.'}
                                    </p>
                                    <Button variant="quiet" size="sm" onClick={restore} disabled={restoring} className="self-start">
                                        <Icon name="restore" size={16} /> Restore {cat.name}
                                    </Button>
                                </>
                            ) : (
                                <div>
                                    <label htmlFor="cat-status" className="mb-2 block text-sm font-semibold text-ink">Status</label>
                                    <select
                                        id="cat-status"
                                        value={data.status}
                                        onChange={(e) => set('status', e.target.value)}
                                        className="h-12 w-full rounded-xl border-[1.5px] border-mist-strong bg-white px-3.5 focus:border-azure-500 focus:outline-3 focus:outline-azure-300"
                                    >
                                        <option value="Active">Active, shown on the site</option>
                                        <option value="Inactive">Inactive, hidden from the site</option>
                                    </select>
                                    <p className="mt-1.5 text-[13px] text-muted">Inactive hides {name} from the public list without archiving.</p>
                                    {errors.status && <p className="mt-1.5 text-[13px] font-medium text-rejected">{errors.status}</p>}
                                </div>
                            )}
                        </AdminCard>
                    )}

                    <section className="flex flex-col gap-2.5" aria-label="Preview of the public card">
                        <span className="text-[13px] font-bold uppercase tracking-[0.08em] text-muted">Preview</span>
                        <div className="overflow-hidden rounded-[20px] border border-mist bg-white">
                            <div className="relative">
                                <img src={photo ?? placeholder} alt="" className="h-48 w-full bg-azure-50 object-cover" />
                                <StatusBadge status={previewState} className="absolute left-3.5 top-3.5 bg-white" />
                            </div>
                            <div className="px-5 pb-5 pt-4">
                                <p className="font-display text-2xl font-semibold">{name}</p>
                                <p className="mt-1 text-[15px] text-muted">{[ageLabel, data.sex, data.breed].filter(Boolean).join(' · ')}</p>
                            </div>
                        </div>
                    </section>

                    {editing && !archived && (
                        <Button variant="danger" size="sm" onClick={() => setArchiving(cat)} className="self-start">
                            <Icon name="archive" size={16} /> Archive {cat.name}
                        </Button>
                    )}
                </aside>
            </form>

            <ArchiveCatDialog cat={archiving} onClose={() => setArchiving(null)} />
        </AdminLayout>
    );
}

// A file input that looks like a button, with the chosen file's name next to it.
function FileButton({ label, accept, onChange, fileName, error }) {
    return (
        <div>
            <label className={`${buttonClasses({ variant: 'quiet', size: 'sm' })} cursor-pointer has-focus-visible:outline-3 has-focus-visible:outline-azure-300`}>
                <Icon name="upload" size={16} /> {label}
                <input type="file" accept={accept} className="sr-only" onChange={(event) => onChange(event.target.files[0] ?? null)} />
            </label>
            {fileName && <p className="mt-1.5 truncate text-[13px] text-muted">{fileName}</p>}
            {error && <p className="mt-1.5 text-[13px] font-medium text-rejected">{error}</p>}
        </div>
    );
}
