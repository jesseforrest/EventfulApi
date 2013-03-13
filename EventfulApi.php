<?PHP
/**
 * This file holds the EventfulApi class.
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

/**
 * This class is a PHP client for the REST-based Eventful API web service.
 *
 * @category  PHP
 * @package   Eventful
 * @author    Jesse Forrest <jesseforrest@gmail.com>
 * @copyright 2013 EventfulApi
 * @license   https://github.com/jesseforrest/EventfulApi License 1.0
 * @link      https://github.com/jesseforrest/EventfulApi/wiki
 */
class EventfulApi
{
   /**
    * The URI of the API
    * 
    * @var string
    */
   const API_URL = 'http://api.eventful.com';

   /**
    * The application key. This will be provided by http://api.eventful.com
    *
    * @var string|null
    */
   public $appKey = null;

   /**
    * The username to login to the API
    *
    * @var string|null
    */
   protected $user = null;

   /**
    * The password to login to the API
    *
    * @var string|null
    */
   protected $password = null;

   /**
    * The user authentication key
    *
    * @var string|null
    */
   protected $userKey = null;

   /**
    * The latest request URI
    *
    * @var string|null
    */
   protected $requestUri = null;

   /**
    * The latest response as unserialized data
    *
    * @var string|null
    */
   public $response = null;

   /**
    * The class constructor which is used to create a new EventfulApi client.
    *
    * @param string $appKey The Eventful application key that was provided by
    * Eventful to you.
    * 
    * @return void
    */
   public function __construct($appKey)
   {
      $this->appKey = $appKey;
   }

   /**
    * Log in and verify the user.
    *
    * @param string $user     The Eventful username
    * @param string $password The Eventful password
    * 
    * @return boolean Returns <var>true</var> on successful login or 
    * <var>false</var> otherwise.
    */
   public function login($user, $password)
   {
      $this->user = $user;

      // Call login to receive a nonce (an arbitrary number used only one time).
      // The nonce is stored in an error structure.
      $this->call('users/login', array());
      $data = $this->response;
      $nonce = $data['nonce'];

      // Generate the digested password response.
      $response = md5($nonce . ':' . md5($password));

      // Send back the nonce and response.
      $args = array(
         'nonce' => $nonce,
         'response' => $response,
      );
      $r = $this->call('users/login', $args);

      if (!$r)
      {
         $this->password = $response . ':' . $nonce;
         return false;
      }

      // Store the provided userKey.
      $this->userKey = (string) $r->userKey;

      return true;
   }

   /**
    * Call a method on the Eventful API.
    *
    * @param string $method The API method (e.g. "events/search")
    * @param array  $args   An optional associative array of arguments to pass
    *                       to the API.
    * 
    * @return mixed
    */
   public function call($method, $args = array())
   {
      // Methods may or may not have a leading slash.
      $method = trim($method, '/ ');

      // Construct the URL that corresponds to the method.
      $url = self::API_URL . '/rest/' . $method;
      $this->requestUri = $url;
      $req = new HTTP_Request($url);
      $req->setMethod(HTTP_REQUEST_METHOD_POST);

      // Add each argument to the POST body.
      $req->addPostData('app_key', $this->appKey);
      $req->addPostData('user', $this->user);
      $req->addPostData('user_key', $this->userKey);
      
      foreach ($args as $key => $value)
      {
         if (preg_match('/_file$/', $key))
         {
            // Treat file parameters differently.
            $req->addFile($key, $value);
         }
         else if (is_array($value))
         {
            foreach ($value as $instance)
            {
               $req->addPostData($key, $instance);
            }
         }
         else
         {
            $req->addPostData($key, $value);
         }
      }

      // Send the request and handle basic HTTP errors.
      $req->sendRequest();
      
      // Invalid response code
      if ($req->getResponseCode() !== 200)
      {
         return PEAR::raiseError('Invalid Response Code: ' 
            . $req->getResponseCode(), $req->getResponseCode());
      }

      // Process the response XML through SimpleXML
      $response = $req->getResponseBody();
      $this->response = $response;
      $data = new SimpleXMLElement($response);

      // Check for call-specific error messages
      if ($data->getName() === 'error')
      {
         $error = $data['string'] . ': ' . $data->description;
         $code = $data['string'];
         return PEAR::raiseError($error, $code);
      }

      return($data);
   }
   
   /**
    * Todo
    * 
    * @return void
    */
   protected function curl()
   {
      
   }
}
