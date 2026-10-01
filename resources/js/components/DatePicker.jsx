import { useState } from 'react';
import Icon from './Icon';

const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

// Dates travel as 'YYYY-MM-DD' strings. They are read as local calendar days, never through
// new Date('YYYY-MM-DD'), which would treat them as UTC midnight and can shift the day.
export function parseDate(iso) {
    const [year, month, day] = iso.split('-').map(Number);

    return new Date(year, month - 1, day);
}

export function toIso(date) {
    const pad = (number) => String(number).padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

export function formatLong(iso) {
    return parseDate(iso).toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
}

/**
 * A month calendar for picking one day. Days before `min` are crossed out and can't be chosen;
 * `min` itself (today) is outlined.
 */
export default function DatePicker({ value, onChange, min, labelledBy, describedBy, invalid = false }) {
    const minDate = parseDate(min);
    const start = value ? parseDate(value) : minDate;
    const [month, setMonth] = useState(new Date(start.getFullYear(), start.getMonth(), 1));

    const atFirstMonth = month.getFullYear() === minDate.getFullYear() && month.getMonth() === minDate.getMonth();
    const daysInMonth = new Date(month.getFullYear(), month.getMonth() + 1, 0).getDate();
    const blanks = month.getDay();
    const days = Array.from({ length: daysInMonth }, (_, index) => new Date(month.getFullYear(), month.getMonth(), index + 1));
    const title = month.toLocaleDateString('en-PH', { month: 'long', year: 'numeric' });

    const shift = (by) => setMonth(new Date(month.getFullYear(), month.getMonth() + by, 1));

    return (
        <div
            role="group"
            aria-labelledby={labelledBy}
            aria-describedby={describedBy}
            className={`flex w-full max-w-md flex-col gap-3.5 rounded-[18px] border bg-white p-4 sm:p-5 ${invalid ? 'border-rejected' : 'border-mist'}`}
        >
            <div className="flex items-center justify-between">
                <button
                    type="button"
                    onClick={() => shift(-1)}
                    disabled={atFirstMonth}
                    aria-label="Previous month"
                    className="flex size-10 items-center justify-center rounded-full border border-mist-strong bg-white hover:border-azure-500 disabled:cursor-not-allowed disabled:opacity-40"
                >
                    <Icon name="chevronLeft" size={16} />
                </button>
                <span className="font-bold" aria-live="polite">{title}</span>
                <button
                    type="button"
                    onClick={() => shift(1)}
                    aria-label="Next month"
                    className="flex size-10 items-center justify-center rounded-full border border-mist-strong bg-white hover:border-azure-500"
                >
                    <Icon name="chevronRight" size={16} />
                </button>
            </div>

            <div className="grid grid-cols-7 gap-1.5">
                {weekdays.map((day) => (
                    <span key={day} className="text-center text-[13px] font-semibold text-muted" aria-hidden="true">
                        {day}
                    </span>
                ))}
                {Array.from({ length: blanks }, (_, index) => (
                    <span key={`blank-${index}`} />
                ))}
                {days.map((day) => {
                    const iso = toIso(day);
                    const past = day < minDate;
                    const label = day.toLocaleDateString('en-PH', { weekday: 'long', month: 'long', day: 'numeric' });

                    if (past) {
                        return (
                            <span
                                key={iso}
                                className="flex h-11 items-center justify-center rounded-xl text-[15px] text-[#a9b3c3] line-through"
                                aria-label={`${label}, already past`}
                                role="img"
                            >
                                {day.getDate()}
                            </span>
                        );
                    }

                    const selected = iso === value;
                    const today = iso === min;

                    return (
                        <button
                            key={iso}
                            type="button"
                            onClick={() => onChange(iso)}
                            aria-pressed={selected}
                            aria-current={today ? 'date' : undefined}
                            aria-label={today ? `${label}, today` : label}
                            className={`h-11 rounded-xl text-[15px] font-medium transition-colors ${
                                selected ? 'bg-azure-800 font-bold text-white' : today ? 'border-2 border-azure-500 bg-white hover:bg-azure-50' : 'bg-sky hover:bg-azure-200'
                            }`}
                        >
                            {day.getDate()}
                        </button>
                    );
                })}
            </div>
        </div>
    );
}
