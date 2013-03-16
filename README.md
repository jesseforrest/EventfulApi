EventfulApi
===========

A PHP class to help communicate with the Eventful REST API.


Example - Basic Usage
---------------------

This example will show how to connect to make a basic event search request
from the Eventful REST API using the EventfulApi class.

```php 
require_once 'EventfulApi.php';

// Define your Eventful API key (Eventful must provide you with a key)
$apiKey = 'nZXRXNPDLjQMxb7w';

// Create an instance
$eventfulApi = new EventfulApi($apiKey);

// Attempt to search for events in Los Angeles, CA
$args = array(
   'q' => 'music',
   'l' => 'Los Angeles, CA'
);
$isSuccessful = $eventfulApi->call('events/search', $args);
if ($isSuccessful)
{
   // Output the response as a string
   echo $eventfulApi->getResponseAsString();
   
   // Output the response as an array
   var_dump($eventfulApi->getResponseAsArray());
}
```