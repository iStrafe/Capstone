import { Link } from '@inertiajs/react';

const variants = {
    // Deep azure: the main action on a page (adopt, donate).
    primary: 'bg-azure-600 text-white hover:bg-azure-700',
    // Darker azure for secondary solid buttons (log in, submit).
    dark: 'bg-azure-800 text-white hover:bg-azure-900',
    outline: 'border-[1.5px] border-mist-strong bg-white text-azure-800 hover:border-azure-500 hover:bg-azure-50',
    quiet: 'bg-azure-100 text-ink hover:bg-azure-200',
    onDark: 'bg-white text-azure-900 hover:bg-azure-100',
    // Destructive actions such as deleting an account.
    danger: 'bg-rejected text-white hover:bg-[#7f241d]',
    disabled: 'cursor-not-allowed bg-neutral-bg text-muted',
};

const sizes = {
    sm: 'h-10 px-4 text-sm',
    md: 'h-12 px-5 text-base',
    lg: 'h-14 px-7 text-[17px]',
};

export function buttonClasses({ variant = 'primary', size = 'md', className = '' } = {}) {
    return [
        'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-full font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-60',
        variants[variant],
        sizes[size],
        className,
    ].join(' ');
}

export default function Button({ variant, size, className, type = 'button', ...props }) {
    return <button type={type} className={buttonClasses({ variant, size, className })} {...props} />;
}

/**
 * A link styled as a button. Pass `inertia` for links to other React pages; links to
 * Blade pages must stay plain anchors so the browser does a full page load.
 */
export function ButtonLink({ variant, size, className, inertia = false, ...props }) {
    const Component = inertia ? Link : 'a';

    return <Component className={buttonClasses({ variant, size, className })} {...props} />;
}
