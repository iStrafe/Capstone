import { useEffect, useMemo } from 'react';

// A temporary URL to preview a file the admin picked, released when the file changes.
export default function useObjectUrl(file) {
    const url = useMemo(() => (file ? URL.createObjectURL(file) : null), [file]);

    useEffect(() => () => url && URL.revokeObjectURL(url), [url]);

    return url;
}
