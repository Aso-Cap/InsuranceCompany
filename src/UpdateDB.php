<?php
//namespace ImportDb;
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(dirname(__FILE__)) . '/../../..');
}

class UpdateDB
{
    private static $infoCnx = [
        'db_host' => "" ,
        'db_port' => "",
        'dp_prefix' => "",
        'db_name' => "",
        'db_user' => "",
        'db_pass' => "",
        'db_charset' => ""
    ];

    private static $initialized = array();

    /**
     * Permet d'executer un scrpit sql sur une bd sql
     *
     * @param string $sqlScript
     *            chemin du script a executer
     *            
     * @return void
     * @author Ayoub SOUID AHMED <a.souidahmed@gmail.com>
     */
    public static function runUpdate($sqlScript)
    {
        self::$infoCnx['db_host'] = getenv("DB_HOST");
        self::$infoCnx['db_name'] = getenv("DB_NAME");
        self::$infoCnx['db_user'] = getenv("DB_USER");
        self::$infoCnx['db_pass'] = getenv("DB_PASS");
        echo "\n------------------------------\nDebut Execution du script '$sqlScript' sur la bd : " . self::$infoCnx['db_name'] . " .\n------------------------------\n";
        preg_match_all("/^((UPDATE|ALTER|CREATE|INSERT|REPLACE|DROP|DELETE) .*;)\s*(?=$)$/Usm", file_get_contents($sqlScript), $sql_COMMON);
        $log = false;
        if (! is_file("$sqlScript.md5") || md5_file("$sqlScript") != file_get_contents("$sqlScript.md5")) {
            foreach ($sql_COMMON[1] as $query) {
                
                try {
                    if (! self::init(self::$infoCnx['db_name'])->query(self::toUtf8($query))) {
                        echo PHP_EOL . "ERROR when executing query in  : " . self::$infoCnx['db_name'] . substr($query, 0, 77) . "...\n";
                    } else {
                        // echo "\n" . $query . "==>OK \n";
                        echo '.';
                    }
                } catch (Exception $e) {
                    $msgError = PHP_EOL . "Erreur UpdateDB.php" . $e->getMessage() . $e->getTraceAsString();
                    $msgError .= PHP_EOL . "query => " . $query . PHP_EOL;
                    $fichierLog = dirname($sqlScript) . "/" . basename($sqlScript, ".sql") . ".log";
                    self::logErreur($msgError, $fichierLog);
                    $log = true;
                }
            }
            if (is_writable(dirname("$sqlScript.md5"))) {
                file_put_contents("$sqlScript.md5", md5_file($sqlScript));
            }
            if ($log) {
                echo "\n des erreurs ont ete recontré lors de l'exection, voir fichier des logs => " . $fichierLog . "\n";
            }
        } else {
            echo "Le script SQL n'a pas bougé depuis la dernière exécution, aucune modification\n";
        }
        echo "\n------------------------------\nFin Execution du script '$sqlScript' sur la bd : " . self::$infoCnx['db_name'] . " .\n------------------------------\n";
    }

    /**
     * Permet d'executer un scrpit sql par environnement sur une bd sql
     *
     * @param string $file
     *            chemin du script a executer
     *
     * @return void
     * @author Ayoub SOUID AHMED <a.souidahmed@gmail.com>
     */
    public static function UpdateParEnv($file)
    {
        $env = '_' . getenv("ENVIRONMENT") . '_';
        $sql = str_replace("@@_ENV_@@", $env, file_get_contents($file));
        $sqlEnv = "/tmp/" . basename($file, ".sql") . "_tmp.sql";
        self::writeFile($sqlEnv, $sql, 'w');
        self::runUpdate($sqlEnv);
        self::deleteLocalfile($sqlEnv);
    }

    /**
     * Initialise le lien vers une base de données
     *
     * @param string $database
     *            nom de la bd
     * @return PDO
     */
    private static function init($database, $kill = false)
    {
        $link = false;
        if ($kill === true) {
            unset(self::$initialized[$database]);
            return;
        }
        if (! isset(self::$initialized[$database])) {
            $user = self::$infoCnx["db_user"];
            $pass = self::$infoCnx["db_pass"];
            try {
                $port = "port=".self::$infoCnx["db_port"].";";
            } catch (Exception $e) {
                $port = "";
            }
            if (self::$infoCnx["dp_prefix"]) {
                $prefix = self::$infoCnx["dp_prefix"] . '_';
            } else {
                $prefix = '';
            }
            $dsn = 'mysql:host=' . self::$infoCnx["db_host"] . ';' . $port . 'dbname=' . $prefix . $database;
            $link = new PDO($dsn, $user, $pass);
            if ($link) {
                $link->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $link->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
                if (version_compare(PHP_VERSION, '5.2.1', '<')) {
                    $link->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                    $link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                }
                if(!empty(self::$infoCnx["db_charset"])){
                    $link->query("SET NAMES " . self::$infoCnx["db_charset"] . ";");
                }
                self::$initialized[$database] = $link;
                return $link;
            }
            return false;
        } else {
            $link = self::$initialized[$database];
        }
        
        return $link;
    }
    /**
     * Permet de supprimer un fichier local
     *
     * @param string $pathFile
     *            chemin du fichier a supprimer
     *
     * @return boolean
     * @author Ayoub SOUID AHMED <a.souidahmed@gmail.com>
     */
    public static function deleteLocalfile($pathFile)
    {
        system("rm " . escapeshellarg($pathFile), $ret);
        if ($ret) {
            return false;
        }
        return true;
    }
    
    /**
     * Permet d'ecrire des donnees sur un fichier
     *
     * @param string $fileName
     *            chemin du fichier
     * @param string $data
     * @param string $mode
     *            mode d'ecriture
     * @return boolean
     * @author Ayoub SOUID AHMED <a.souidahmed@gmail.com>
     */
    public static function writeFile($fileName, $data, $mode = 'a')
    {
        $fp = fopen($fileName, $mode);
        if (! fwrite($fp, $data) && ! empty($data) && ! is_file($fileName)) {
            return null;
        }
        fclose($fp);
        return true;
    }
    
    public static function logErreur($msgError, $filePath)
    {
        self::writeFile($filePath, $msgError);
    }
    
    public static function toUtf8($text)
    {
        // echo "#".$text." ".mb_detect_encoding($text)."#";
        if (self::isUTF8($text)) {
            return $text;
        }
        return utf8_encode($text);
    }
    
    public static function isUTF8($str)
    {
        if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
            return true;
        }
        return false;
    }
}
if (!empty($argv)) {
    if (count($argv) == 2) {
        $file = $argv[1];
        if (is_file($file)) {
            if (pathinfo($file, PATHINFO_EXTENSION) == "sql") {
                UpdateDb::runUpdate($file);
            } else {
                echo "le script à executer doit etre en format '.sql'\n";
            }
        } else {
            echo "le fichier $file n'existe pas\n";
        }
    } else {
        echo "merci de fournir le path du script 'sql' a executer\n";
    }
}
