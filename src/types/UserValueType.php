<?php

namespace src\types;

enum UserValueType
{
    case ACCOUNT_NAME;
    case USERNAME;
    case EMAIL;
    case PASSWORD;
    case AGE;
    case LOCATION;
    case DESCRIPTION;
}
