// A cat's photo, or the "No photo yet" placeholder when the admin hasn't uploaded one.
export default function CatPhoto({ cat, className = '' }) {
    if (cat.image) {
        return <img src={cat.image} alt={`Photo of ${cat.name}`} loading="lazy" className={`object-cover ${className}`} />;
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
