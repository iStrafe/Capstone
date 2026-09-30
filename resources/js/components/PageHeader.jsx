// The heading block at the top of a public page: small eyebrow, big title, optional intro and actions.
export default function PageHeader({ eyebrow, title, children, actions }) {
    return (
        <section className="mx-auto flex max-w-7xl flex-col justify-between gap-6 px-4 pb-8 pt-10 sm:px-8 md:flex-row md:items-end lg:pt-14">
            <div className="flex max-w-3xl flex-col gap-3">
                {eyebrow && <p className="text-xs font-bold uppercase tracking-[0.14em] text-azure-700">{eyebrow}</p>}
                <h1 className="font-display text-4xl font-semibold leading-[1.05] sm:text-5xl lg:text-[52px]">{title}</h1>
                {children && <div className="text-lg leading-relaxed text-body">{children}</div>}
            </div>
            {actions}
        </section>
    );
}
