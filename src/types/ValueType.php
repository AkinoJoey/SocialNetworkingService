<?php

namespace src\types;

enum ValueType: string
{
    case STRING = 'string';
    case INT = 'int';
    case FLOAT = 'float';
    case DATE = 'date'; // YYYY-MM-DD string
    case EMAIL = 'email';
    case PASSWORD = 'password';
}
