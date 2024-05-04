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
        // Init connection
        $this->_database = new \PDO(
            sprintf(
                'sqlite:%s',
                str_starts_with(
                    $database,
                    DIRECTORY_SEPARATOR
                ) ? $database
                    : __DIR__ .
                    DIRECTORY_SEPARATOR . '..'.
                    DIRECTORY_SEPARATOR . '..'.
                    DIRECTORY_SEPARATOR . 'config'.
                    DIRECTORY_SEPARATOR . $database
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

        // Init structure
        $this->_database->query('
            CREATE TABLE IF NOT EXISTS "channel"
            (
                "id"          INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                "time"        INTEGER NOT NULL,
                "order"       INTEGER NOT NULL,
                "alias"       VARCHAR NOT NULL,
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

    public function getChannel(
        int $id
    ): ?object
    {
        $query = $this->_database->prepare(
            'SELECT * FROM `channel` WHERE `id` = ?'
        );

        $query->execute([$id]);

        if ($result = $query->fetch())
        {
            return $result;
        }

        return null;
    }

    public function getChannels(): ?array
    {
        $query = $this->_database->query(
            'SELECT * FROM `channel` ORDER BY `order` ASC, `title` ASC, `id` ASC'
        );

        if ($result = $query->fetchAll())
        {
            return $result;
        }

        return null;
    }

    public function getChannelByAlias(
        string $alias
    ): ?object
    {
        $query = $this->_database->prepare(
            'SELECT * FROM `channel` WHERE `alias` LIKE ? LIMIT 1'
        );

        $query->execute([$alias]);

        if ($result = $query->fetch())
        {
            return $result;
        }

        return null;
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
        string  $alias,
        string  $source,
        ?string $link        = null,
        ?string $title       = null,
        ?string $description = null,
        ?int    $time        = null,
        ?int    $order       = null
    ): ?int
    {
        $query = $this->_database->prepare(
            'INSERT INTO `channel` (`alias`, `source`, `link`, `title`, `description`, `time`, `order`)
                            VALUES (:alias,  :source,  :link,  :title,  :description,  :time,  :order)'
        );

        $query->execute(
            [
                ':alias'       => $alias,
                ':source'      => $source,
                ':link'        => $link,
                ':title'       => $title,
                ':description' => $description,
                ':time'        => $time  ? $time  : time(),
                ':order'       => $order ? $order : 0
            ]
        );

        if ($id = $this->_database->lastInsertId())
        {
            return (int) $id;
        }

        return null;
    }

    public function getChannelItem(
        int $id
    ): ?object
    {
        $query = $this->_database->prepare(
            'SELECT * FROM `channelItem` WHERE `id` = ? LIMIT 1'
        );

        $query->execute([$id]);

        if ($result = $query->fetch())
        {
            return $result;
        }

        return null;
    }

    public function getChannelItems(
        int $start = 0,
        int $limit = 20
    ): ?array
    {
        $query = $this->_database->query(
            sprintf(
                'SELECT * FROM `channelItem` ORDER BY `pubTime` DESC, `time` DESC, `id` DESC LIMIT %d,%d',
                $start,
                $limit
            )
        );

        if ($result = $query->fetchAll())
        {
            return $result;
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