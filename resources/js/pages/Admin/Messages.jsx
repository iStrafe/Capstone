import { Link, router } from '@inertiajs/react';
import { useState } from 'react';
import Button, { buttonClasses } from '../../components/Button';
import ConfirmDialog from '../../components/ConfirmDialog';
import Icon from '../../components/Icon';
import AdminLayout, { AdminHeader } from '../../layouts/AdminLayout';

const filters = [
    { key: 'open', label: 'To handle' },
    { key: 'handled', label: 'Handled' },
    { key: 'all', label: 'All' },
];

// Messages from the public contact form: the list on the left, the opened message on the right.
export default function Messages({ messages, selected, filter, counts }) {
    const filterHref = (key) => `${window.location.pathname}${key === 'open' ? '' : `?filter=${key}`}`;

    return (
        <AdminLayout title="Messages" active="messages">
            <AdminHeader title="Messages">
                From the contact form on the public site.{' '}
                {counts.open === 0 ? 'Nothing is waiting for a reply.' : counts.open === 1 ? '1 message is not handled yet.' : `${counts.open} messages are not handled yet.`}
            </AdminHeader>

            <div className="flex flex-col overflow-hidden rounded-[20px] border border-mist bg-white lg:min-h-[560px] lg:flex-row">
                <div className={`flex flex-col gap-3 border-mist bg-cloud p-3 sm:p-4 lg:w-96 lg:shrink-0 lg:border-r ${selected ? 'max-lg:border-b' : ''}`}>
                    <nav aria-label="Message filter" className="flex flex-wrap gap-2">
                        {filters.map((item) => (
                            <Link
                                key={item.key}
                                href={filterHref(item.key)}
                                aria-current={filter === item.key ? 'page' : undefined}
                                className="flex h-9 items-center rounded-full border border-mist-strong bg-white px-4 text-sm font-medium hover:border-azure-500 aria-[current=page]:border-ink aria-[current=page]:bg-ink aria-[current=page]:text-white"
                            >
                                {item.label}
                                {item.key !== 'all' && <span className="ml-1.5 opacity-80">· {counts[item.key]}</span>}
                            </Link>
                        ))}
                    </nav>

                    {messages.data.length > 0 ? (
                        <ul className="flex flex-col gap-1" aria-label="Messages">
                            {messages.data.map((message) => {
                                const current = selected?.id === message.id;

                                return (
                                    <li key={message.id}>
                                        <Link
                                            href={message.url}
                                            preserveScroll
                                            aria-current={current ? 'true' : undefined}
                                            className={`flex gap-3 rounded-2xl px-3.5 py-3 ${current ? 'bg-white shadow-[0_1px_0_#D2DCEA]' : 'hover:bg-white/70'}`}
                                        >
                                            <span className={`mt-2 size-2 shrink-0 rounded-full ${message.handled ? '' : 'bg-azure-500'}`} aria-hidden="true" />
                                            <div className="min-w-0 flex-1">
                                                <div className="flex justify-between gap-2">
                                                    <span className={`truncate ${message.handled ? 'font-medium' : 'font-bold'}`}>
                                                        {message.name}
                                                        {!message.handled && <span className="sr-only"> (not handled)</span>}
                                                    </span>
                                                    <span className="shrink-0 text-[13px] text-muted">{message.receivedAt}</span>
                                                </div>
                                                <p className="truncate text-sm text-muted">{message.preview}</p>
                                            </div>
                                        </Link>
                                    </li>
                                );
                            })}
                        </ul>
                    ) : (
                        <p className="px-2 py-8 text-center text-muted">{filter === 'open' ? 'Nothing is waiting for a reply.' : 'No messages here.'}</p>
                    )}

                    {messages.meta.lastPage > 1 && (
                        <div className="mt-auto flex items-center justify-between px-1 text-sm">
                            {messages.links.prev ? <Link href={messages.links.prev} className="font-semibold text-azure-700">Newer</Link> : <span />}
                            <span className="text-muted">Page {messages.meta.currentPage} of {messages.meta.lastPage}</span>
                            {messages.links.next ? <Link href={messages.links.next} className="font-semibold text-azure-700">Older</Link> : <span />}
                        </div>
                    )}
                </div>

                {selected ? <MessageView message={selected} /> : <p className="m-auto hidden p-10 text-muted lg:block">Pick a message to read it.</p>}
            </div>
        </AdminLayout>
    );
}

function MessageView({ message }) {
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [busy, setBusy] = useState(false);
    const options = { preserveScroll: true, onStart: () => setBusy(true), onFinish: () => setBusy(false) };

    const toggleHandled = () => router.patch(message.handledUrl, { handled: message.handled ? 0 : 1 }, options);
    const remove = () => router.delete(message.deleteUrl, { ...options, onFinish: () => { setBusy(false); setConfirmDelete(false); } });

    return (
        <article className="flex min-w-0 flex-1 flex-col gap-5 p-5 sm:p-8" aria-labelledby="message-from">
            <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                <div>
                    <h2 id="message-from" className="text-2xl font-bold">{message.name}</h2>
                    <p className="mt-1 text-muted">
                        {message.receivedAtFull}
                        {message.handled && ` · Handled ${message.handledAt}`}
                    </p>
                </div>
                <div className="flex gap-2">
                    <Button variant={message.handled ? 'outline' : 'dark'} size="sm" onClick={toggleHandled} disabled={busy}>
                        <Icon name={message.handled ? 'restore' : 'check'} size={16} /> {message.handled ? 'Mark as unhandled' : 'Mark handled'}
                    </Button>
                    <button
                        type="button"
                        onClick={() => setConfirmDelete(true)}
                        aria-label="Delete message"
                        title="Delete message"
                        className="flex size-10 items-center justify-center rounded-xl border border-mist bg-white text-rejected hover:border-rejected"
                    >
                        <Icon name="trash" size={18} />
                    </button>
                </div>
            </div>

            <div className="flex flex-wrap gap-3">
                {message.email ? (
                    <a href={`mailto:${message.email}`} className={buttonClasses({ variant: 'quiet', size: 'sm' })}>
                        <Icon name="mail" size={16} /> {message.email}
                    </a>
                ) : (
                    <span className="flex h-10 items-center gap-2 text-sm text-muted">
                        <Icon name="mail" size={16} /> Email not given
                    </span>
                )}
                {message.phone && (
                    <a href={`tel:${message.phone}`} className={buttonClasses({ variant: 'quiet', size: 'sm' })}>
                        <Icon name="phone" size={16} /> {message.phone}
                    </a>
                )}
            </div>

            <p className="max-w-2xl whitespace-pre-line break-words text-[17px] leading-relaxed">{message.message}</p>

            <ConfirmDialog
                open={confirmDelete}
                title="Delete this message?"
                confirmLabel="Delete message"
                tone="danger"
                processing={busy}
                onConfirm={remove}
                onClose={() => setConfirmDelete(false)}
            >
                The message from {message.name} is removed for good. This can’t be undone.
            </ConfirmDialog>
        </article>
    );
}
