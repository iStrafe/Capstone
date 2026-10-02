import { usePage } from '@inertiajs/react';

const MB = 1024 * 1024;

// What the site itself allows, matching the server's validation rules.
export const PHOTO_MAX = 10 * MB;
export const CLIP_MAX = 25 * MB;

// Room left in a save for the text fields and the form's own overhead.
const FORM_ROOM = 64 * 1024;

// "2 MB", "1.5 MB" or "800 KB".
export function formatSize(bytes) {
    return bytes >= MB ? `${Number((bytes / MB).toFixed(1))} MB` : `${Math.max(1, Math.round(bytes / 1024))} KB`;
}

// PHP's upload limits from php.ini, shared with admin pages. A file over them never reaches the site.
export function useUploadLimit() {
    const limit = usePage().props.admin?.uploadLimit;

    return { file: limit?.file ?? Infinity, total: limit?.total ?? Infinity };
}

// The largest file of one kind that can be sent: the site's own limit, or PHP's if that's lower.
export function maxSize(siteMax, limit) {
    return Math.min(siteMax, limit.file);
}

// Why a chosen file can't be sent, or null when it's fine.
export function sizeProblem(file, siteMax, limit, noun) {
    if (!file || file.size <= maxSize(siteMax, limit)) {
        return null;
    }
    const why = limit.file < siteMax ? `This server takes files up to ${formatSize(limit.file)}, set by upload_max_filesize in php.ini.` : `The ${noun} can be up to ${formatSize(siteMax)}.`;

    return `This ${noun} is ${formatSize(file.size)}. ${why}`;
}

// Why the chosen files can't go in one save, or null when they fit.
export function totalProblem(files, limit) {
    const size = files.reduce((sum, file) => sum + (file?.size ?? 0), 0);

    if (size + FORM_ROOM <= limit.total) {
        return null;
    }

    return `Together these files are ${formatSize(size)}, and this server takes up to ${formatSize(limit.total)} per save, set by post_max_size in php.ini. Save them one at a time.`;
}
