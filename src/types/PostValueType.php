<?php

namespace src\types;


enum PostValueType
{
    case CONTENT;
    case MEDIA_PATH;
    case SCHEDULED_AT;
    case URL;
}
