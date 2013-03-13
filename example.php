<?PHP
/**
 * This file holds an example on how to use the EventfulApi class.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   Eventful
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2013 EventfulApi
 * @license   https://github.com/jesseforrest/EventfulApi License 1.0
 * @link      https://github.com/jesseforrest/EventfulApi/wiki
 */

require_once 'EventfulApi.php';

// Define your Eventful API key (Eventful must provide you with a key)
$apiKey = 'nZXRXNPDLjQMxb7w';

// Create an instance
$eventfulApi = new EventfulApi($apiKey);

// Attempt to search for events in Los Angeles, CA
$args = array(
   'location' => 'Los Angeles, CA'
);
$response = $eventfulApi->call('events/search', $args);

// Output the response 
var_dump($response);