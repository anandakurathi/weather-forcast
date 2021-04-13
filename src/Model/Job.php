<?php


namespace Src\Model;


class Job
{
    /**
     * @var string DB Table name
     */
    protected $table = 'jobs';

    /**
     * @var null|DatabaseConnector
     */
    private $db = null;

    const STATUS = [
        'NEW' => 'NEW',
        'PROCESSING' => 'PROCESSING',
        'DONE' => 'DONE',
        'ERROR' => 'ERROR'
    ];

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * create Job
     * @param $wento
     * @return null
     */
    public function createJob($wento)
    {
        $data = [
            'status' => self::STATUS['NEW'],
            'whento' => $wento
        ];

        try {
            $query = "INSERT INTO ".$this->table.
                "(status, whento, created_at, updated_at)
                VALUES (:status, :whento, now(), now())";
            $stmt = $this->db->prepare($query);
            $stmt->execute($data);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * get jobs to be executed
     * @return null|mixed
     */
    public function getJob()
    {
        try {
            $query = "
                SELECT
                       id, status
                FROM "
                .$this->table.
                " WHERE
                    DATE(whento) <= NOW()
                  AND
                    status <> :status
                ORDER BY id DESC LIMIT 1
                ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':status' => self::STATUS['DONE']]);
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * update Job based on the id
     * @param $id
     * @param $status
     * @return null|int
     */
    public function updateJobById($id, $status)
    {
        if (!$id || !is_int($id)) {
            return null;
        }

        try {
            $query = "
                UPDATE ".$this->table."
                SET status = :status
                WHERE
                    id = :id
                ORDER BY id DESC LIMIT 1
                ";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                'status' => self::STATUS[$status],
                'id' => $id
            ]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
