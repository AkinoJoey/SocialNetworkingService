<?php

namespace src\database\data_access;

use src\database\data_access\implementations\PostDAOImpl;
use src\database\data_access\implementations\ProfileDAOImpl;
use src\database\data_access\implementations\UserDAOImpl;
use src\database\data_access\interfaces\PostDAO;
use src\database\data_access\interfaces\ProfileDAO;
use src\database\data_access\interfaces\UserDAO;
use src\database\data_access\interfaces\CommentDAO;
use src\database\data_access\implementations\CommentDAOImpl;
use src\database\data_access\implementations\PostLikeDAOImpl;
use src\database\data_access\interfaces\CommentLikeDAO;
use src\database\data_access\interfaces\PostLikeDAO;
use src\database\data_access\implementations\CommentLikeDAOImpl;
use src\database\data_access\implementations\DmMessageDAOImpl;
use src\database\data_access\implementations\DmThreadDAOImpl;
use src\database\data_access\implementations\FollowDAOImpl;
use src\database\data_access\implementations\NotificationDAOImpl;
use src\database\data_access\implementations\PasswordResetTokenDAOImpl;
use src\database\data_access\interfaces\DmMessageDAO;
use src\database\data_access\interfaces\DmThreadDAO;
use src\database\data_access\interfaces\FollowDAO;
use src\database\data_access\interfaces\NotificationDAO;
use src\database\data_access\interfaces\PasswordResetTokenDAO;

class DAOFactory
{
    public static function getUserDAO(): UserDAO
    {
        return new UserDAOImpl();
    }

    public static function getProfileDAO(): ProfileDAO
    {
        return new ProfileDAOImpl();
    }

    public static function getPostDAO(): PostDAO
    {
        return new PostDAOImpl();
    }

    public static function getCommentDAO(): CommentDAO
    {
        return new CommentDAOImpl();
    }

    public static function getPostLikeDAO(): PostLikeDAO
    {
        return new PostLikeDAOImpl();
    }

    public static function getCommentLikeDAO(): CommentLikeDAO
    {
        return new CommentLikeDAOImpl();
    }

    public static function getFollowDAO(): FollowDAO
    {
        return new FollowDAOImpl();
    }

    public static function getDmThreadDAO(): DmThreadDAO
    {
        return new DmThreadDAOImpl();
    }

    public static function getDmMessageDAO(): DmMessageDAO
    {
        return new DmMessageDAOImpl();
    }

    public static function getNotificationDAO(): NotificationDAO
    {
        return new NotificationDAOImpl();
    }

    public static function getPasswordResetTokenDAO(): PasswordResetTokenDAO
    {
        return new PasswordResetTokenDAOImpl();
    }
}
