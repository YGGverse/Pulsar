<?php

declare(strict_types=1);

namespace Yggverse\Pulsar\Model;

class Database
{
    public \PDO $_database;

    public function __construct(
        string  $database,
        ?string $username = null,
        ?string $password = null
    ) {
        $this->_database = new \PDO(
            sprintf(
                'sqlite:%s',
                $database
            ),
            $username,
            $password
        );

        $this->_database->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );

        $this->_database->setAttribute(
            \PDO::ATTR_DEFAULT_FETCH_MODE,
            \PDO::FETCH_OBJ
        );

        $this->_database->query('
            CREATE TABLE IF NOT EXISTS "channel"
            (
                "id"          INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                "time"        INTEGER NOT NULL,
                "source"      TEXT NOT NULL,
                "link"        TEXT,
                "title"       TEXT,
                "description" TEXT
            )
        ');

        $this->_database->query('
            CREATE TABLE IF NOT EXISTS "channelItem"
            (
                "id"          INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                "channelId"   INTEGER NOT NULL,
                "time"        INTEGER NOT NULL,
                "pubTime"     INTEGER,
                "guid"        TEXT NOT NULL,
                "link"        TEXT,
                "title"       TEXT,
                "description" TEXT,
                "content"     TEXT
            )
        ');
    }

    public function getChannelIdBySource(
        string $source
    ): ?int
    {
        $query = $this->_database->prepare(
            'SELECT `id` FROM `channel` WHERE `source` LIKE :source LIMIT 1'
        );

        $query->execute(
            [
                ':source' => $source
            ]
        );

        if ($result = $query->fetch())
        {
            return $result->id;
        }

        return null;
    }

    public function addChannel(
        string $source,
        ?string $link,
        ?string $title,
        ?string $description,
        ?int $time = null
    ): ?int
    {
        $query = $this->_database->prepare(
            'INSERT INTO `channel` (`source`, `link`, `title`, `description`, `time`)
                            VALUES (:source,  :link,  :title,  :description,  :time)'
        );

        $query->execute(
            [
                ':source'      => $source,
                ':link'        => $link,
                ':title'       => $title,
                ':description' => $description,
                ':time'        => $time ? $time : time()
            ]
        );

        if ($id = $this->_database->lastInsertId())
        {
            return (int) $id;
        }

        return null;
    }

    public function isChannelItemExist(
        int $channelId,
        string $guid
    ): bool
    {
        $query = $this->_database->prepare(
            'SELECT NULL FROM `channelItem` WHERE `channelId` = :channelId AND `guid` LIKE :guid LIMIT 1'
        );

        $query->execute(
            [
                ':channelId' => $channelId,
                ':guid'      => $guid
            ]
        );

        return (bool) $query->fetch();
    }

    public function addChannelItem(
        int $channelId,
        string $guid,
        ?string $link,
        ?string $title,
        ?string $description,
        ?string $content,
        ?int $pubTime,
        ?int $time = null
    ): ?int
    {
        $query = $this->_database->prepare(
            'INSERT INTO `channelItem` (`channelId`, `guid`, `link`, `title`, `description`, `content`, `pubTime`, `time`)
                                VALUES (:channelId,  :guid,  :link,  :title,  :description,  :content,  :pubTime,  :time)'
        );

        $query->execute(
            [
                ':channelId'   => $channelId,
                ':guid'        => $guid,
                ':link'        => $link,
                ':title'       => $title,
                ':description' => $description,
                ':content'     => $content,
                ':pubTime'     => $pubTime,
                ':time'        => $time ? $time : time()
            ]
        );

        if ($id = $this->_database->lastInsertId())
        {
            return (int) $id;
        }

        return null;
    }
}