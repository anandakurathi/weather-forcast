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

    private $db = null;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * @return null
     */
    public function getToken()
    {
        try {
            $query = "
                SELECT
                       token
                FROM "
                    .$this->table.
                "WHERE
                    DATE(expiry) >= :expiryDate
                ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':expiryDate', NOW());
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
