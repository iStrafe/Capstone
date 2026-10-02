// Stroke icons used across the React pages. Decorative by default (aria-hidden).
const paths = {
    arrowRight: <path d="M5 12h14M13 6l6 6-6 6" />,
    chevronRight: <path d="M9 6l6 6-6 6" />,
    chevronLeft: <path d="M15 6l-6 6 6 6" />,
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
    upload: <path d="M12 16V4M7 9l5-5 5 5M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3" />,
    user: (
        <>
            <circle cx="12" cy="8" r="4" />
            <path d="M4 20c1.5-4 4.5-6 8-6s6.5 2 8 6" />
        </>
    ),
    clock: (
        <>
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 7.5V12l3 2" />
        </>
    ),
    external: <path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h5" />,
    list: <path d="M9 6h11M9 12h11M9 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01" />,
    logOut: <path d="M15 4h3a2 2 0 012 2v12a2 2 0 01-2 2h-3M10 16l4-4-4-4M14 12H4" />,
    trash: <path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a1 1 0 001 1h8a1 1 0 001-1l1-12M9 7V4h6v3" />,
    inbox: <path d="M4 13l2.5-8h11L20 13M4 13v6h16v-6M4 13h4.5l1 2.5h5l1-2.5H20" />,
    cat: <path d="M5 20v-9l-1-6 4.5 3h7L20 5l-1 6v9zM9.5 13.5h.01M14.5 13.5h.01M10.5 16.5h3" />,
    news: <path d="M5 5h11v14H6a1 1 0 01-1-1zM16 9h3v9a1 1 0 01-1 1h-2M8 9h5M8 12h5M8 15h3" />,
    spark: <path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5L18 18M6 18l2.5-2.5M15.5 8.5L18 6" />,
    archive: <path d="M4 5h16v4H4zM5 9v10h14V9M10 13h4" />,
    edit: <path d="M4 20h4L19 9l-4-4L4 16zM13.5 6.5l4 4" />,
    eye: (
        <>
            <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z" />
            <circle cx="12" cy="12" r="3" />
        </>
    ),
    restore: <path d="M4 12a8 8 0 108-8 8 8 0 00-6 2.7M4 4v4h4" />,
    plus: <path d="M12 5v14M5 12h14" />,
    lock: (
        <>
            <rect x="5" y="11" width="14" height="9" rx="2" />
            <path d="M8 11V8a4 4 0 018 0v3" />
        </>
    ),
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

// Google's four-color "G", for the "Continue with Google" buttons.
export function GoogleIcon({ size = 20, className = '' }) {
    return (
        <svg width={size} height={size} viewBox="0 0 48 48" aria-hidden="true" focusable="false" className={className}>
            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.3-.1-2.4-.4-3.5z" />
            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z" />
            <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z" />
            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C37 39.2 44 34 44 24c0-1.3-.1-2.4-.4-3.5z" />
        </svg>
    );
}
