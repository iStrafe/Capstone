<?php

namespace App\Enums;

enum AdoptionStatus: string
{
    case Pending = 'Pending';
    case Approved = 'Approved';
    case Rejected = 'Rejected';
    case Released = 'Released';
}
