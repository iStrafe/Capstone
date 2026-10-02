import { Link, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import Button, { buttonClasses } from '../../components/Button';
import ConfirmDialog from '../../components/ConfirmDialog';
import Field from '../../components/Field';
import Icon from '../../components/Icon';
import Pagination from '../../components/Pagination';
import StatusBadge from '../../components/StatusBadge';
import AdminLayout, { AdminHeader } from '../../layouts/AdminLayout';
import { PHOTO_MAX, formatSize, maxSize, sizeProblem, useUploadLimit } from '../../lib/uploads';
import useObjectUrl from '../../lib/useObjectUrl';

// News & events posts: the list, with the editor panel beside it for a new or an existing post.
export default function News({ posts, editing, indexUrl, createUrl, storeUrl, publicUrl }) {
    const [deleting, setDeleting] = useState(null);
    const [processing, setProcessing] = useState(false);
    const editingId = editing && editing !== 'new' ? editing.id : null;

    const remove = () =>
        router.delete(deleting.deleteUrl, {
            preserveScroll: true,
            onStart: () => setProcessing(true),
            onFinish: () => setProcessing(false),
            onSuccess: () => setDeleting(null),
        });

    return (
        <AdminLayout title="News & events" active="news">
            <AdminHeader
                title="News & events"
                actions={
                    <>
                        <a href={publicUrl} target="_blank" rel="noreferrer" className={buttonClasses({ variant: 'outline', size: 'sm' })}>
                            <Icon name="external" size={16} /> Public page
                        </a>
                        <Link href={createUrl} preserveScroll className={buttonClasses({ variant: 'primary', size: 'sm' })}>
                            <Icon name="plus" size={16} /> New post
                        </Link>
                    </>
                }
            >
                Posts appear on the public News & events page, newest first.
            </AdminHeader>

            <div className="flex flex-col gap-6 xl:flex-row xl:items-start">
                {editing && <Editor key={editingId ?? 'new'} post={editing === 'new' ? null : editing} storeUrl={storeUrl} indexUrl={indexUrl} />}

                <div className="min-w-0 flex-1 overflow-hidden rounded-[20px] border border-mist bg-white xl:order-first">
                    {posts.data.length > 0 ? (
                        <>
                            <ul className="divide-y divide-mist">
                                {posts.data.map((post) => (
                                    <li key={post.id} className={`flex items-center gap-3 px-4 py-3 ${post.id === editingId ? 'bg-azure-50' : ''}`}>
                                        {post.image ? (
                                            <img src={post.image} alt="" className="h-11 w-14 shrink-0 rounded-[10px] object-cover" />
                                        ) : (
                                            <span className="flex h-11 w-14 shrink-0 items-center justify-center rounded-[10px] bg-neutral-bg text-muted" aria-hidden="true">
                                                <Icon name="image" size={18} />
                                            </span>
                                        )}
                                        <div className="flex min-w-0 flex-1 flex-col gap-0.5 sm:flex-row sm:items-center sm:gap-4">
                                            <Link href={post.editUrl} preserveScroll className="min-w-0 flex-1 truncate font-semibold hover:text-azure-800 hover:underline">
                                                {post.title}
                                            </Link>
                                            <span className="flex items-center gap-3 text-sm text-body">
                                                <time dateTime={post.isoDate}>{post.date}</time>
                                                <StatusBadge status={post.isUpcoming ? 'approved' : 'inactive'}>{post.isUpcoming ? 'Upcoming' : 'Past'}</StatusBadge>
                                            </span>
                                        </div>
                                        <div className="flex shrink-0 gap-2">
                                            <Link
                                                href={post.editUrl}
                                                preserveScroll
                                                aria-label={`Edit ${post.title}`}
                                                title="Edit post"
                                                className="flex size-10 items-center justify-center rounded-xl border border-mist bg-white hover:border-azure-500"
                                            >
                                                <Icon name="edit" size={18} />
                                            </Link>
                                            <button
                                                type="button"
                                                onClick={() => setDeleting(post)}
                                                aria-label={`Delete ${post.title}`}
                                                title="Delete post"
                                                className="flex size-10 items-center justify-center rounded-xl border border-mist bg-white text-rejected hover:border-rejected"
                                            >
                                                <Icon name="trash" size={18} />
                                            </button>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                            <Pagination meta={posts.meta} links={posts.links} label="Post pages" className="border-t border-mist px-4 py-3" />
                        </>
                    ) : (
                        <div className="flex flex-col items-center gap-2 px-6 py-14 text-center">
                            <p className="font-bold">No posts yet</p>
                            <p className="text-body">Announce the next adoption drive or share news from the org.</p>
                        </div>
                    )}
                </div>
            </div>

            <ConfirmDialog
                open={deleting !== null}
                title="Delete this post?"
                confirmLabel="Delete post"
                tone="danger"
                processing={processing}
                onConfirm={remove}
                onClose={() => setDeleting(null)}
            >
                {deleting && <>“{deleting.title}” is removed from the public page. This can’t be undone.</>}
            </ConfirmDialog>
        </AdminLayout>
    );
}

function Editor({ post, storeUrl, indexUrl }) {
    const form = useForm({
        title: post?.title ?? '',
        event_date: post?.isoDate ?? '',
        description: post?.description ?? '',
        eventimage: null,
        remove_image: false,
        ...(post ? { _method: 'put' } : {}),
    });
    const { data, errors } = form;
    // Editing a field clears its old error, so fixed fields don't stay red until the next save.
    const set = (field, value) => {
        form.setData(field, value);
        form.clearErrors(field);
    };
    const limit = useUploadLimit();
    const chosen = useObjectUrl(data.eventimage);
    const image = chosen ?? (data.remove_image ? null : post?.image);

    const submit = (event) => {
        event.preventDefault();
        form.post(post ? post.updateUrl : storeUrl, { forceFormData: true, preserveScroll: true });
    };

    return (
        <section aria-labelledby="editor-title" className="flex w-full flex-col gap-4 rounded-[20px] border border-mist bg-white p-5 sm:p-6 xl:w-[440px] xl:shrink-0">
            <div className="flex items-center justify-between">
                <h2 id="editor-title" className="text-lg font-bold">{post ? 'Edit post' : 'New post'}</h2>
                <Link href={indexUrl} preserveScroll aria-label="Close editor" className="flex size-10 items-center justify-center rounded-xl border border-mist hover:border-azure-500">
                    <Icon name="close" size={18} />
                </Link>
            </div>

            <form onSubmit={submit} className="flex flex-col gap-4" noValidate>
                <Field label="Title" value={data.title} onChange={(e) => set('title', e.target.value)} error={errors.title} maxLength={255} required />
                <Field label="Event date" type="date" value={data.event_date} onChange={(e) => set('event_date', e.target.value)} error={errors.event_date} required />
                <Field
                    label="Description"
                    multiline
                    rows={6}
                    value={data.description}
                    onChange={(e) => set('description', e.target.value)}
                    error={errors.description}
                    maxLength={10000}
                    required
                />

                <div className="flex flex-col gap-2">
                    <span className="text-sm font-semibold text-ink">Image</span>
                    <div className="flex flex-wrap items-center gap-3">
                        {image ? (
                            <img src={image} alt="Post image" className="h-20 w-30 rounded-xl object-cover" />
                        ) : (
                            <span className="flex h-20 w-30 items-center justify-center rounded-xl bg-neutral-bg text-muted" aria-hidden="true">
                                <Icon name="image" size={22} />
                            </span>
                        )}
                        <label className={`${buttonClasses({ variant: 'quiet', size: 'sm' })} cursor-pointer has-focus-visible:outline-3 has-focus-visible:outline-azure-300`}>
                            {image ? 'Replace' : 'Choose an image'}
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                                className="sr-only"
                                onChange={(event) => {
                                    const file = event.target.files[0] ?? null;
                                    const problem = sizeProblem(file, PHOTO_MAX, limit, 'image');
                                    // Lets the same file be chosen again after it was refused.
                                    event.target.value = '';

                                    if (problem) {
                                        form.setError('eventimage', problem);

                                        return;
                                    }
                                    form.clearErrors('eventimage');
                                    form.setData((current) => ({ ...current, eventimage: file, remove_image: false }));
                                }}
                            />
                        </label>
                        {image && (
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() => form.setData((current) => ({ ...current, eventimage: null, remove_image: Boolean(post?.image) }))}
                            >
                                Remove
                            </Button>
                        )}
                    </div>
                    <p className="text-[13px] text-muted">JPG, PNG, GIF or WebP, up to {formatSize(maxSize(PHOTO_MAX, limit))}. Optional.</p>
                    {errors.eventimage && <p className="text-[13px] font-medium text-rejected">{errors.eventimage}</p>}
                </div>

                <div className="flex justify-end gap-2.5">
                    <Link href={indexUrl} preserveScroll className={buttonClasses({ variant: 'outline', size: 'sm' })}>Cancel</Link>
                    <Button type="submit" variant="dark" size="sm" disabled={form.processing}>
                        {form.processing ? 'Saving…' : post ? 'Save post' : 'Publish post'}
                    </Button>
                </div>
            </form>
        </section>
    );
}
