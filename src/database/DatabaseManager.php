<?php

namespace src\database;

class DatabaseManager
{
    protected static array $mysqliConnections = [];
    protected static array $memcachedConnections = [];

    public static function getMysqliConnection(string $connectionName = 'default'): MySQLWrapper
    {
        if (!isset(static::$mysqliConnections[$connectionName])) {
            static::$mysqliConnections[$connectionName] = new MySQLWrapper();
        }

        return static::$mysqliConnections[$connectionName];
    }

    public static function reconnect(string $connectionName = 'default'): MySQLWrapper
    {
        $current = static::$mysqliConnections[$connectionName];
        $current->close();
        unset(static::$mysqliConnections[$connectionName]);
        return static::getMysqliConnection();
    }
}
