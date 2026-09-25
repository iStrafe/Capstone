<?php

namespace App\Enums;

enum AdoptionStatus: string
{
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
    case Released = 'Released';

    /**
     * A request with one of these statuses takes its cat off the adoption list.
     *
     * @return list<self>
     */
    public static function reservingCat(): array
    {
        return [self::Approved, self::Released];
    }

    /**
     * Requests the applicant is still waiting on; one per user and cat at a time.
     *
     * @return list<self>
     */
    public static function open(): array
    {
        return [self::Pending, self::Approved];
    }
}
