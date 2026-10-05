import { useState } from 'react';

// A cat's photo, or the "No photo yet" placeholder when the admin hasn't uploaded one or the
// file can't be loaded (deleted from storage, or a link that has expired).
export default function CatPhoto({ cat, className = '' }) {
    const [failed, setFailed] = useState(null);

    if (cat.image && failed !== cat.image) {
        return <img src={cat.image} alt={`Photo of ${cat.name}`} loading="lazy" onError={() => setFailed(cat.image)} className={`object-cover ${className}`} />;
    }

    return (
        <img
            src={cat.placeholder}
            alt={`No photo yet of ${cat.name}`}
            loading="lazy"
            className={`bg-azure-50 object-cover ${className}`}
        />
    );
}
