<?php

namespace src\types;


enum NotificationType: string
{
    case POST_LIKE = 'post_like';
    case COMMENT = 'comment';
    case FOLLOW = 'follow';
    case DM = 'dm';
}
