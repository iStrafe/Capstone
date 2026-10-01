import { usePage } from '@inertiajs/react';
import Button, { ButtonLink } from '../components/Button';
import CatPhoto from '../components/CatPhoto';
import Icon from '../components/Icon';
import SiteLayout, { useDonate } from '../layouts/SiteLayout';
import { FACEBOOK_URL, MAPS_URL } from '../lib/places';


const reasons = [
    { icon: 'heart', title: 'Less stress', text: 'Time with cats lowers anxiety and lifts students’ mood.' },
    { icon: 'shield', title: 'Better health', text: 'Being around cats supports the immune system and overall well-being.' },
    { icon: 'users', title: 'Run by volunteers', text: 'Every cat we list is cared for by volunteers until it finds a home.' },
];

// About us: who AduCats is, why campus cats matter, the "not a shelter" notice and where to find us.
export default function About({ photos }) {
    const { links } = usePage().props;
    const [first, ...others] = photos;

    return (
        <SiteLayout title="About us" active="about">
            <section className="mx-auto grid max-w-7xl items-center gap-12 px-4 pb-14 pt-10 sm:px-8 lg:grid-cols-2 lg:gap-16 lg:pt-16">
                <div className="flex flex-col gap-5">
                    <p className="text-xs font-bold uppercase tracking-[0.14em] text-azure-700">About AduCats</p>
                    <h1 className="font-display text-5xl font-semibold leading-[1.02] sm:text-6xl">Humans and campus cats, living well together.</h1>
                    <p className="max-w-xl text-lg leading-relaxed text-body sm:text-xl">
                        AduCats is a non-profit group of Adamson University volunteers. We look after the cats around campus and connect each one with a loving adopter.
                    </p>
                    <div className="flex flex-col gap-3 sm:flex-row">
                        <ButtonLink href={links.adopt} inertia size="lg">
                            Meet the cats <Icon name="arrowRight" size={18} />
                        </ButtonLink>
                        <DonateButton />
                    </div>
                </div>

                {first ? (
                    <div className="grid h-80 grid-cols-2 grid-rows-2 gap-4 sm:h-[440px]">
                        <CatPhoto cat={first} className="row-span-2 h-full w-full rounded-[28px]" />
                        {others.map((cat) => (
                            <CatPhoto key={cat.id} cat={cat} className="h-full min-h-0 w-full rounded-[24px]" />
                        ))}
                        {others.length < 2 && <div className="rounded-[24px] bg-azure-100" aria-hidden="true" />}
                        {others.length < 1 && <div className="rounded-[24px] bg-azure-200" aria-hidden="true" />}
                    </div>
                ) : (
                    <div className="flex h-80 items-center justify-center rounded-[28px] bg-azure-100 text-azure-600" aria-hidden="true">
                        <Icon name="heart" size={64} />
                    </div>
                )}
            </section>

            <section aria-labelledby="why-title" className="mx-auto flex max-w-7xl flex-col gap-6 px-4 pb-14 sm:px-8">
                <h2 id="why-title" className="font-display text-3xl font-semibold sm:text-4xl">Why cats matter on campus</h2>
                <ul className="grid gap-5 md:grid-cols-3">
                    {reasons.map((reason) => (
                        <li key={reason.title} className="flex flex-col gap-2.5 rounded-[20px] border border-mist bg-white p-6">
                            <span className="flex size-12 items-center justify-center rounded-2xl bg-azure-50 text-azure-800">
                                <Icon name={reason.icon} size={22} />
                            </span>
                            <span className="text-lg font-bold">{reason.title}</span>
                            <span className="leading-relaxed text-body">{reason.text}</span>
                        </li>
                    ))}
                </ul>
            </section>

            <section className="mx-auto grid max-w-7xl gap-6 px-4 pb-20 sm:px-8 lg:grid-cols-[1fr_420px]">
                <div className="flex flex-col gap-4 rounded-[28px] bg-azure-800 p-8 text-white sm:p-10">
                    <p className="text-xs font-bold uppercase tracking-[0.14em] text-azure-200">Please read</p>
                    <h2 className="font-display text-3xl font-semibold sm:text-[34px]">We’re not a shelter</h2>
                    <p className="text-[17px] leading-relaxed text-azure-50">
                        Please don’t leave pets or cats on campus. The university is not an animal shelter, and RA 10631, Section 7 of the Animal Welfare Act makes it
                        unlawful for anyone who has custody of an animal to abandon it.
                    </p>
                    <p className="text-[17px] leading-relaxed text-azure-50">
                        We care for the cats already on campus and help them get adopted. Thank you for supporting responsible pet adoption and animal welfare!
                    </p>
                </div>
                <div className="flex flex-col gap-4 rounded-[28px] border border-mist bg-white p-7">
                    <h2 className="font-display text-2xl font-semibold">Come visit us</h2>
                    <p className="flex gap-2.5 leading-relaxed text-body">
                        <Icon name="pin" className="mt-0.5 shrink-0 text-azure-700" />
                        900 San Marcelino St., Ermita, Manila 1000
                    </p>
                    <a href={MAPS_URL} target="_blank" rel="noreferrer" className="flex items-center gap-2 font-semibold text-azure-700 hover:text-azure-900">
                        <Icon name="external" size={18} /> Open in Google Maps
                    </a>
                    <a href={FACEBOOK_URL} target="_blank" rel="noreferrer" className="flex items-center gap-2 font-semibold text-azure-700 hover:text-azure-900">
                        <Icon name="external" size={18} /> AduCats on Facebook
                    </a>
                    <ButtonLink href={links.contact} inertia variant="outline" className="mt-auto self-start">
                        <Icon name="mail" size={18} /> Send us a message
                    </ButtonLink>
                </div>
            </section>
        </SiteLayout>
    );
}

// Lives inside SiteLayout, so it can open the layout's donate dialog.
function DonateButton() {
    const openDonate = useDonate();

    return (
        <Button onClick={openDonate} variant="outline" size="lg">
            <Icon name="heart" size={18} /> Donate
        </Button>
    );
}
