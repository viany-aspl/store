<?php
/**
 * Main Script of phpWebSockets
 *
 * Run this file in a shell or windows cmd to start the socket server.
 * Sorry for calling this daemon but the goal is that this server run
 * as daemon in near future.
 *
 * @author Moritz Wutz <moritzwutz@gmail.com>
 * @version 0.1
 * @package phpWebSockets
 */

ob_implicit_flush(true);

require 'ws/socket.class.php';
require 'ws/socketWebSocket.class.php';
require 'mcrypt.php';
$WebSocket = new socketWebSocket();

?>