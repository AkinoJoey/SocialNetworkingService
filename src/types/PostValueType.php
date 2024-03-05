<?php

namespace src\types;


enum PostValueType
{
    case CONTENT;
    case MEDIA;
    case TYPE_REPLY_TO;
}
