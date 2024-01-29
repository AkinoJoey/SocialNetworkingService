<?php

namespace src\database\data_access;

use src\database\data_access\implementations\UserDAOImpl;
use src\database\data_access\interfaces\UserDAO;

class DAOFactory
{
    public static function getUserDAO(): UserDAO
    {
        return new UserDAOImpl();
    }
}
