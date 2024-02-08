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

    public static function getPostDAO() : PostDAO {
        return new PostDAOImpl();
    }

    public static function getCommentDAO(): CommentDAO
    {
        return new CommentDAOImpl();
    }

    public static function getPostLikeDAO() : PostLikeDAO {
        return new PostLikeDAOImpl();
    }

    public static function getCommentLikeDAO() : CommentLikeDAO {
        return new CommentLikeDAOImpl();
    }
}
