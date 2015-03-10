<?php
/**
 * GollosRestApi
 *
 * Copyright (c) 2015, Dmitry Mamontov <d.slonyara@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Dmitry Mamontov nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   gollos-restapi
 * @author    Dmitry Mamontov <d.slonyara@gmail.com>
 * @copyright 2015 Dmitry Mamontov <d.slonyara@gmail.com>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since     File available since Release 1.0.0
 */
/**
 * GollosRestApi - The main class
 *
 * @author    Dmitry Mamontov <d.slonyara@gmail.com>
 * @copyright 2015 Dmitry Mamontov <d.slonyara@gmail.com>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version   Release: 1.0.0
 * @link      https://github.com/dmamontov/gollos-restapi/
 * @since     Class available since Release 1.0.0
 */

class GollosRestApi
{
    /*
     * URL fro RestAPI
     */
    const URL = 'http://gollosapi.com/api/';

    /*
     * Methods
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    /**
     * Key access to API
     * @var string
     * @access protected
     */
    protected $key;

    /**
     * Hash
     * @var string
     * @access protected
     */
    protected $hmac;

    /**
     * Acceptable methods
     * @var array
     * @access private
     */
    private $allowable = array(
        'getProducts', 'addProducts', 'updateProducts', 'removeProducts',
        'getGroups', 'addGroups', 'updateGroups', 'removeGroups',
        'getVendors', 'addVendors', 'updateVendors', 'removeVendors',
        'getCustomers', 'addCustomers', 'updateCustomers', 'removeCustomers',
        'getOrders', 'removeOrders'
    );

    /**
     * List of all possible actions and methods
     * @var array
     * @access private
     */
    private $actions = array(
        'get' => self::METHOD_GET,
        'add' => self::METHOD_POST,
        'update' => self::METHOD_PUT,
        'remove' => self::METHOD_DELETE
    );

    /**
     * Class constructor
     * @param string $apiKey
     * @param string $secretKey
     * @return void
     * @access public
     * @final
     */
    final public function __construct($apiKey, $secretKey)
    {
        $this->key = $apiKey;
        $this->hmac = hash_hmac('sha1', $apiKey, $secretKey);
    }

    /**
     * Search method and execute the request
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @access public
     * @final
     */
    final public function __call($name, $arguments)
    {
        if (in_array($name, $this->allowable) === false) {
            throw new BadMethodCallException('Invalid method');
        }

        $url = self::URL;
        $method = self::METHOD_GET;

        foreach ($this->actions as $action => $actionMethod) {
            if (stripos($name, $action) !== false) {
                if (
                    (in_array($action, array('add', 'update')) && (count($arguments) < 2 || is_numeric($arguments[0]) === false)) ||
                    ($action == 'remove' && is_numeric($arguments[0]) === false)
                ) {
                    throw new InvalidArgumentException('Invalid argument');
                }
                $url .= strtolower(end(explode($action, $name))) . (is_numeric($arguments[0]) ? "/{$arguments[0]}/" : '');
                $method = $actionMethod;
                break;
            }
        }
        if (is_numeric($arguments[0])) {
            unset($arguments[0]);
        }

        return $this->curlRequest($url, $method, count($arguments) > 0 && is_array(reset($arguments)) ? reset($arguments) : null);
    }

    /**
     * Execution of the request
     * @param string $url
     * @param string $method
     * @param array $parameters
     * @param integer $timeout
     * @param string $format
     * @return mixed
     * @access protected
     */
    protected function curlRequest($url, $method = 'GET', $parameters, $timeout = 30, $format = 'json')
    {
         if ($method == self::METHOD_GET && is_null($parameters) == false) {
             $url .= '?'.http_build_query($parameters);
         }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/$format",
            "Content-Type: application/$format",
            "x-go-apiKey: $this->key",
            "x-go-hmac: $this->hmac",
        ));
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if ($method == self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif (in_array($method, array(self::METHOD_PUT, self::METHOD_DELETE))) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        
        if (is_null($parameters) === false && in_array($method, array(self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE))) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            throw new Exception($error, $errno);
        }

        $result = json_decode($response, true);

        if ($statusCode >= 400) {
            throw new Exception($result['message'], $statusCode);
        }

        return count($result) == 0 ? true : $result;
    }
}
