import { usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import Button from '../../components/Button';
import CatCard from '../../components/CatCard';
import Icon from '../../components/Icon';
import PageHeader from '../../components/PageHeader';
import SiteLayout from '../../layouts/SiteLayout';

const sexes = [
    { key: 'any', label: 'Any' },
    { key: 'Female', label: 'Female' },
    { key: 'Male', label: 'Male' },
];

// Ages are whole years; cats without an age only show under "Any".
const ages = [
    { key: 'any', label: 'Any', test: () => true },
    { key: 'kitten', label: 'Under 1', test: (age) => age !== null && age < 1 },
    { key: 'young', label: '1 to 3 years', test: (age) => age >= 1 && age <= 3 },
    { key: 'adult', label: '4 years +', test: (age) => age >= 4 },
];

const sorts = {
    newest: { label: 'Newest first', compare: () => 0 },
    name: { label: 'Name, A to Z', compare: (a, b) => a.name.localeCompare(b.name) },
};

// The public adoption list. `cats` arrive newest first, each with its requestState for this visitor.
export default function Index({ cats }) {
    const { auth } = usePage().props;
    const [query, setQuery] = useState('');
    const [sex, setSex] = useState('any');
    const [age, setAge] = useState('any');
    const [sort, setSort] = useState('newest');

    const shown = useMemo(() => {
        const terms = query.toLowerCase().split(/\s+/).filter(Boolean);
        const ageTest = ages.find((item) => item.key === age).test;

        return cats
            .filter((cat) => {
                const text = [cat.name, cat.color, cat.breed].join(' ').toLowerCase();

                return terms.every((term) => text.includes(term)) && (sex === 'any' || cat.sex === sex) && ageTest(cat.age);
            })
            .sort(sorts[sort].compare);
    }, [cats, query, sex, age, sort]);

    const filtered = query !== '' || sex !== 'any' || age !== 'any';
    const clear = () => {
        setQuery('');
        setSex('any');
        setAge('any');
    };

    return (
        <SiteLayout title="Adopt a cat" active="adopt">
            <PageHeader
                eyebrow="Adopt"
                title="Cats looking for a home"
                actions={
                    cats.length > 0 && (
                        <div className="relative w-full md:w-96">
                            <label htmlFor="cat-search" className="sr-only">Search cats</label>
                            <Icon name="search" className="pointer-events-none absolute left-4 top-3.5 text-muted" />
                            <input
                                id="cat-search"
                                type="search"
                                value={query}
                                onChange={(event) => setQuery(event.target.value)}
                                placeholder="Search by name, color or breed"
                                className="h-12 w-full rounded-full border-[1.5px] border-mist-strong bg-white pl-12 pr-4 placeholder:text-muted focus:border-azure-500 focus:outline-3 focus:outline-azure-300"
                            />
                        </div>
                    )
                }
            >
                Every cat here lives on campus and is looked after by our volunteers.
                {!auth.user && ' You’ll need an account to send a request.'}
            </PageHeader>

            <div className="mx-auto flex max-w-7xl flex-col gap-7 px-4 pb-20 sm:px-8">
                {cats.length > 0 && (
                    <div className="flex flex-col justify-between gap-5 lg:flex-row lg:items-center">
                        <div className="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:gap-7">
                            <ChipGroup label="Sex" options={sexes} value={sex} onChange={setSex} />
                            <ChipGroup label="Age" options={ages} value={age} onChange={setAge} />
                        </div>
                        <div className="flex items-center gap-4">
                            <p className="text-[15px] text-muted" role="status">
                                {shown.length === 1 ? '1 cat' : `${shown.length} cats`}
                            </p>
                            <label htmlFor="cat-sort" className="sr-only">Sort cats</label>
                            <select
                                id="cat-sort"
                                value={sort}
                                onChange={(event) => setSort(event.target.value)}
                                className="h-10 rounded-full border border-mist-strong bg-white pl-4 pr-9 text-sm font-medium focus:border-azure-500 focus:outline-3 focus:outline-azure-300"
                            >
                                {Object.entries(sorts).map(([key, item]) => (
                                    <option key={key} value={key}>{item.label}</option>
                                ))}
                            </select>
                        </div>
                    </div>
                )}

                {shown.length > 0 ? (
                    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {shown.map((cat) => (
                            <CatCard key={cat.id} cat={cat} />
                        ))}
                    </div>
                ) : (
                    <div className="flex flex-col items-center gap-3 rounded-[20px] border border-dashed border-mist-strong px-6 py-14 text-center">
                        <span className="flex size-13 items-center justify-center rounded-2xl bg-azure-50 text-azure-700">
                            <Icon name="heart" size={24} />
                        </span>
                        <p className="font-bold">{cats.length === 0 ? 'No cats are up for adoption right now' : 'No cats match these filters'}</p>
                        <p className="text-body">{cats.length === 0 ? 'Check back soon, or follow AduCats on Facebook for news.' : 'Try another search or clear the filters.'}</p>
                        {filtered && cats.length > 0 && (
                            <Button variant="outline" size="sm" onClick={clear}>
                                Clear filters
                            </Button>
                        )}
                    </div>
                )}
            </div>
        </SiteLayout>
    );
}

function ChipGroup({ label, options, value, onChange }) {
    return (
        <div role="group" aria-label={label} className="flex flex-wrap items-center gap-2">
            <span className="mr-1 text-sm font-semibold text-muted" aria-hidden="true">{label}</span>
            {options.map((option) => (
                <button
                    key={option.key}
                    type="button"
                    onClick={() => onChange(option.key)}
                    aria-pressed={value === option.key}
                    className="h-10 rounded-full border border-mist-strong bg-white px-4 text-sm font-medium hover:border-azure-500 aria-pressed:border-ink aria-pressed:bg-ink aria-pressed:text-white"
                >
                    {option.label}
                </button>
            ))}
        </div>
    );
}
