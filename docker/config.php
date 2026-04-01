<?php  // Moodle configuration file — Docker version

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'moodle-db';           // Docker service name
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodle';
$CFG->dbpass    = getenv('MOODLE_DB_PASS') ?: 'SecurePassword123!';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => 3306,
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);

// ⚠️ FOR LOCAL TESTING — change to 'https://lms.lpec.lk' when deploying to VPS
$CFG->wwwroot   = 'https://lms.lpec.lk';
$CFG->reverseproxy = true; // Fixes redirect loop on port mismatched local setups
$CFG->dataroot  = '/var/www/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0770;

// ── Redis Session Handling (DISABLED FOR LOCAL TESTING) ──
// $CFG->session_handler_class = '\core\session\redis';
// $CFG->session_redis_host = 'moodle-redis';
// $CFG->session_redis_port = 6379;
// $CFG->session_redis_database = 0;
// $CFG->session_redis_prefix = 'moodle_sess_';
// $CFG->session_redis_acquire_lock_timeout = 120;
// $CFG->session_redis_lock_expire = 7200;

// ── Performance ──
$CFG->cachedir = '/var/www/moodledata/cache';
$CFG->localcachedir = '/tmp/moodle-local-cache';

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
