<?php

namespace Celtic34fr\ContactCore\Trait;

use Exception;
use mysqli;

class QUERY_CONSTANTS
{
    const mode_to_string = [
        "S" => "%%%s",
        "T" => "%s%%",
        "L" => "%%%s%%",
    ];
}

trait MySQLiTrait
{
    protected mysqli $conn;

    public function __construct()
    {
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $name = $_ENV['DB_NAME'];
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $charset = $_ENV['DB_CHARSET'];

        // Create connection
        $conn = mysqli_connect($host, $user, $password, $name, $port);

        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $conn->set_charset($charset);
        $this->conn = $conn;
    }

    /**
     * Mysqli findBy : génération chaîne SQL et recherche dans une table via mysqli avec Critères de sélection,
     * de tri, limite (nombre de résultats) et offset de recherche (résultats sautés)
     *
     * @param array $selections // ensemble des critères de sqelection
     * @param array $orders // critères de tri
     * @param int $limits // nombre maximum de résultats (défaut : 0 pour tous)
     * @param int $offset // nombre de résultats à suater (défault : 0 pour aucun)
     * @param string $alias // alias préfix des nom de champ (défaut : vide)
     * @return array|false      // tableau contenant l'ensemble des entités trouvées ou faux
     *
     * Critères :
     *  nom_champ.E => valeur       : nom_champ == valeur
     *  nom_champ.N => valeur       : nom_champ != valeur
     *  nom_champ.S => valeur       : nom_champ LIKE 'valeur%'
     *  nom_champ.T => valeur       : nom_champ LIKE '%valeur'
     *  nom_champ.L => valeur       : nom_champ LIKE '%valeur%'
     */
    public function findBy(array  $selections = [], array $orders = [], int $limits = 0, int $offset = 0,
                           string $alias = ""): bool|array
    {
        $query = "SELECT * FROM " . $this->dbName . (strlen($alias) > 0 ? ' ' . $alias : '');
        $alias .= (strlen($alias) > 0 ? '.' : '');

        /** Traitement des critères de sélection */
        if (!empty($criteres)) {
            $query .= " WHERE ";
            foreach ($selections as $field => $value) {
                $real_field = $field;
                $mode = "E";
                if (strpos($field, '.') !== false) {
                    list($real_field, $mode) = explode(".", $field, 2);
                    $mode = strtoupper($mode);
                }
                $query .= $alias . $real_field;
                switch ($mode) {
                    case "E": // EQUAL : égalité stricte
                        if (is_null($value)) {
                            $query .= " IS NULL ";
                        } else {
                            $query .= " = ?:" . strtoupper($real_field) . " ";
                        }
                        break;
                    case "N": // EQUAL : égalité stricte
                        if (is_null($value)) {
                            $query .= " IS NOT NULL ";
                        } else {
                            $query .= " != ?:" . strtoupper($real_field) . " ";
                        }
                        break;
                    case "S": // START WITH : débute par que pour les chaines de caractères
                    case "T": // TAIL WITH : fini par que pour les chaines de caractères
                    case "L": // LIKE CONTAINS : contient par que pour les chaines de caractères
                        $query .= " LIKE ?:" . strtoupper($real_field) . " ";
                        $params[] = sprintf(QUERY_CONSTANTS::mode_to_string[$mode], addcslashes($value, "%_"));
                        break;
                }
            }
        }

        /** Traitement des critères de tri */

        /** Traitement de limite et offset */

        /** Exécution de la requête et traitement des retours */
    }

    public function queryBuilder1(array  $selections = [], array $orders = [], int $limits = 0, int $offset = 0,
                                  string $alias = ""): array|false
    {
        $query = "SELECT * FROM " . $this->dbName . (strlen($alias) > 0 ? ' ' . $alias : '');
        $alias .= (strlen($alias) > 0 ? '.' : '');
        if (!empty($criteres)) {
            $query .= " WHERE ";
            foreach ($selections as $field => $value) {
                $real_field = $field;
                $mode = "E";
                if (strpos($field, '.') !== false) {
                    list($real_field, $mode) = explode(".", $field, 2);
                    $mode = strtoupper($mode);
                }
                $query .= $alias . $real_field;
                switch ($mode) {
                    case "E": // EQUAL : égalité stricte
                        if (is_null($value)) {
                            $query .= " IS NULL";
                        } else {
                            $query .= " = ?";
                        }
                        break;
                    case "N": // EQUAL : égalité stricte
                        if (is_null($value)) {
                            $query .= " IS NOT NULL";
                        } else {
                            $query .= " != ?";
                        }
                        break;
                    case "S": // START WITH : débute par que pour les chaines de caractères
                    case "T": // TAIL WITH : fini par que pour les chaines de caractères
                    case "L": // LIKE CONTAINS : contient par que pour les chaines de caractères
                        $query .= " LIKE ? ";
                        $params[] = sprintf(QUERY_CONSTANTS::mode_to_string[$mode], addcslashes($value, "%_"));
                        break;
                }
            }
        }
        if (!empty($orders)) {
            $query .= " ORDER BY ";
            foreach ($orders as $field => $order) {
                $order = strtoupper($order);
                assert(in_array($order, ["ASC", "DESC"]));
                $query .= "$alias.$field $order, ";
            }
            $query = substr($query, 0, strlen($query) - 2);
        }
        $query .= ($limits > 0) ? " LIMIT " . $limits : "";
        $query .= ($offset > 0) ? " OFFSET " . $offset : "";
//        return [$query, $params];

        $stmt = $this->conn->query($query);
        if (mysqli_num_rows($stmt) > 0) {
            while ($row = mysqli_fetch_object($stmt, $this->className)) {
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }

    /**
     * @param int $table_id
     * @return bool|object|null
     * @throws Exception
     */
    public function find(int $table_id): bool|null|object
    {
        $query = "SELECT * FROM " . $this->dbName . " WHERE id = $table_id";
        $stmt = $this->conn->query($query);
        if (mysqli_num_rows($stmt) > 0) {
            if (mysqli_num_rows($stmt) > 1) {
                return throw new Exception("MySQL Error : more than one result");
            }
            return mysqli_fetch_object($stmt, $this->className);
        }
        return false;
    }

    /**
     * @return array|false
     */
    public function findAll(): bool|array
    {
        $query = "SELECT * FROM " . $this->dbName;
        $stmt = $this->conn->query($query);
        $rows = [];
        if (mysqli_num_rows($stmt) > 0) {
            while ($row = mysqli_fetch_object($stmt, $this->className)) {
                $rows[] = $row;
            }
            return $rows;
        }
        return false;
    }

    /**
     * @return array
     */
    private function getTableColumns(): array
    {
        $sql = "SHOW COLUMNS FROM " . $this->dbName;
        $res = $this->conn->query($sql);
        $structure = [];

        while ($row = $res->fetch_assoc()) {
            $name = $row['Field'];
            switch (true) {
                case (strpos($row['Type'], 'varchar') !== false):
                    $type = "string";
                    break;
                case ($row['Type'] === 'tinyint(1)'):
                    $type = "bool";
                    break;
                case (strpos($row['Type'], 'int') !== false):
                    $type = 'int';
                    break;
                case ($row['Type'] === 'longtext'):
                    $type = 'textarea';
                    break;
                default:
                    $type = $row['Type'];
                    break;
            }
            $structure[$name] = $type;
        }
        return $structure;
    }

    private function formatValueQuery($fieldName, $value)
    {
        if (!array_key_exists($fieldName, $this->structure)) {
            throw new Exception("Champ $fieldName est inconnu dans ".$this->dbName);
        }
        switch ($this->structure[$fieldName]) {
            case "string":
                return '"'.$value.'"';
                break;
            case "bool":
                return ((bool) $value ? ' TRUE ' : ' FALSE ');
                break;
            case "int":
                return (int) $value;
                break;
            case "textarea":
                /** à faire */
                break;
            case "datetime":
                /** à coder */
                break;
            default:
                return $value;
                break;
        }
    }
}
