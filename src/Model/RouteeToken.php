<?php

namespace Src\Model;

/**
 * Class RouteeToken
 * @package Src\Services
 */
class RouteeToken
{
    /**
     * @var string DB Table name
     */
    protected $table = 'routee_tokens';
    /**
     * @var DatabaseConnector|null
     */
    private $db = null;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Get the token if the expiry time is greater than the current time.
     * @return mixed|null
     */
    public function getToken()
    {
        try {
            $query = "
                SELECT
                       token
                FROM "
                .$this->table.
                " WHERE
                    DATE(expiry) >= NOW()
                ORDER BY id DESC LIMIT 1
                ";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * record latest token in DB for future use
     * @param $token
     * @param $expiry
     * @return null
     */
    public function insertToken($token, $expiry)
    {
        $data = [
            'token' => $token,
            'expiry' => $this->calculateTokenExpiry($expiry),
        ];

        try {
            $query = "INSERT INTO ".$this->table.
                "(token, expiry, created_at, updated_at)
                VALUES (:token, :expiry, now(), now())";
            $stmt = $this->db->prepare($query);
            $stmt->execute($data);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * calculate the expiry time of  generated token
     * @param  int  $expirySeconds
     * @return false|string
     */
    public function calculateTokenExpiry(int $expirySeconds)
    {
        return date("Y-m-d h:i:s", time() + $expirySeconds);
    }
}
