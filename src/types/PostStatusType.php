<?php

namespace src\types;


enum PostStatusType: string
{
    case PUBLIC = 'public';
    case DRAFT  = 'draft';
    case SCHEDULED = 'scheduled';
}
