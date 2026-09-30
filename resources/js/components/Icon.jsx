// Stroke icons used across the React pages. Decorative by default (aria-hidden).
const paths = {
    arrowRight: <path d="M5 12h14M13 6l6 6-6 6" />,
    chevronRight: <path d="M9 6l6 6-6 6" />,
    chevronDown: <path d="M6 9l6 6 6-6" />,
    check: <path d="M5 12l5 5L20 7" />,
    close: <path d="M6 6l12 12M18 6L6 18" />,
    menu: <path d="M4 7h16M4 12h16M4 17h16" />,
    search: (
        <>
            <circle cx="11" cy="11" r="7" />
            <path d="M20 20l-3.5-3.5" />
        </>
    ),
    heart: <path d="M12 20s-7-4.4-7-10a4 4 0 017-2.6A4 4 0 0119 10c0 5.6-7 10-7 10z" />,
    gift: (
        <>
            <rect x="4" y="9" width="16" height="11" rx="1" />
            <path d="M3 9h18M12 9v11M12 9c-2-4-6-4-6-1.5S10 9 12 9zM12 9c2-4 6-4 6-1.5S14 9 12 9z" />
        </>
    ),
    shield: <path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6z" />,
    play: <path d="M8 5l11 7-11 7z" />,
    file: (
        <>
            <path d="M6 3h8l4 4v14H6z" />
            <path d="M14 3v4h4" />
        </>
    ),
    pin: (
        <>
            <path d="M12 21s-6.5-6-6.5-11a6.5 6.5 0 0113 0c0 5-6.5 11-6.5 11z" />
            <circle cx="12" cy="10" r="2.3" />
        </>
    ),
    calendar: (
        <>
            <rect x="3.5" y="5" width="17" height="15" rx="2" />
            <path d="M3.5 10h17M8 3v4M16 3v4" />
        </>
    ),
    info: (
        <>
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 11v5M12 8h.01" />
        </>
    ),
    users: (
        <>
            <circle cx="9" cy="8" r="3.5" />
            <path d="M2.5 19c1-3.5 3.6-5 6.5-5s5.5 1.5 6.5 5" />
            <circle cx="17" cy="9" r="2.5" />
            <path d="M17 14c2.3 0 4 1.3 4.5 4" />
        </>
    ),
    send: <path d="M4 12l16-8-6 16-2.5-6.5z" />,
    mail: (
        <>
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="M3 7l9 6 9-6" />
        </>
    ),
    phone: <path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a1 1 0 01-1 1A16 16 0 014 5a1 1 0 011-1z" />,
    image: (
        <>
            <rect x="3.5" y="4.5" width="17" height="15" rx="2" />
            <circle cx="9" cy="10" r="1.8" />
            <path d="M4 18l5-5 4 4 3-3 4 4" />
        </>
    ),
    external: <path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h5" />,
};

export default function Icon({ name, size = 20, className = '', ...props }) {
    return (
        <svg
            width={size}
            height={size}
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
            aria-hidden="true"
            focusable="false"
            className={className}
            {...props}
        >
            {paths[name]}
        </svg>
    );
}

export function PawIcon({ size = 22, className = '' }) {
    return (
        <svg width={size} height={size} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false" className={className}>
            <circle cx="5" cy="10" r="2.2" />
            <circle cx="9" cy="5.5" r="2.2" />
            <circle cx="15" cy="5.5" r="2.2" />
            <circle cx="19" cy="10" r="2.2" />
            <path d="M12 11c-3.2 0-6 3.6-6 6.2 0 2 1.6 2.8 3 2.8 1.2 0 2-.6 3-.6s1.8.6 3 .6c1.4 0 3-.8 3-2.8C18 14.6 15.2 11 12 11z" />
        </svg>
    );
}
