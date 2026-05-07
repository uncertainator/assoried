<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
