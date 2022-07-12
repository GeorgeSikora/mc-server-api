<?php header('Content-type: text/html; charset=utf-8');
require_once realpath(__DIR__ . '/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$API_NAME   = $_ENV['API_NAME'];
$host       = $_ENV['RCON_HOST'];       // Server host name or IP
$port       = $_ENV['RCON_PORT'];       // Port rcon is listening on
$password   = $_ENV['RCON_PASSWORD'];   // rcon.password setting set in server.properties
$timeout    = 3;                        // How long to timeout.

use Thedudeguy\Rcon;
$rcon = new Rcon($host, $port, $password, $timeout);

// Get client uri request
$uri = $_SERVER['REQUEST_URI'];

// Remove api name from uri, make lowercase
$reqName = strtolower(str_replace('/'.$API_NAME, '', $uri));

if ($rcon->connect())
{
    if ($reqName == '/serverinfo')
    {
        $rcon->sendCommand('list');
    
        $response = trim($rcon->getResponse());
        $splitted = explode(' ', $response);
    
        $playersOnline  = $splitted[2];
        $playersTotal   = $splitted[7];
        $playersList    = explode(', ', explode(': ', $response)[1] ?? '');
    
        echo json_encode([
            'status'    => 'online',
            'online'    => $playersOnline,
            'slots'     => $playersTotal,
            'players'   => $playersList,
        ]); exit();
    }
    else 
    {
        echo json_encode([
            'error' => 'bad request name',
        ]); exit();
    }
}
else
{
    echo json_encode([
        'status' => 'offline',
    ]); exit();
}

