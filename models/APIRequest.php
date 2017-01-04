<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iRAP\VidaSDK\Models;

class APIRequest
{
    private static $s_version;
    private static $s_url = \iRAP\VidaSDK\API_URL;
    private $m_ch;
    private $m_headers;
    public $m_result;
    public $m_httpCode;
    public $m_status;
    public $m_error;
    
    public function __construct()
    {
        if (defined('\iRAP\VidaSDK\USER_AUTH_ID'))
        {
            $auth = new authentication(\iRAP\VidaSDK\APP_AUTH_ID, \iRAP\VidaSDK\APP_API_KEY, \iRAP\VidaSDK\APP_PRIVATE_KEY, \iRAP\VidaSDK\USER_AUTH_ID, \iRAP\VidaSDK\USER_API_KEY, \iRAP\VidaSDK\USER_PRIVATE_KEY);
        }
        else
        {
            $auth = new authentication(\iRAP\VidaSDK\APP_AUTH_ID, \iRAP\VidaSDK\APP_API_KEY, \iRAP\VidaSDK\APP_PRIVATE_KEY);
        }
        $this->m_headers = $auth->m_authentication;
    }

    public function send()
    {
        curl_setopt($this->m_ch, CURLOPT_HEADER, true);
        curl_setopt($this->m_ch, CURLOPT_RETURNTRANSFER, true);
        $this->m_headers = $this->formatHeaders();
        curl_setopt($this->m_ch, CURLOPT_HTTPHEADER, $this->m_headers);
        $response = curl_exec($this->m_ch);
        $this->processResponse($response);
        curl_close($this->m_ch);
        if (defined('IRAPDIAGNOSTICS'))
        {
            echo $this->m_result;
        }
    } 
            
    public function setUrl($resource, $id = null, $arg = null)
    {
        $url = self::$s_url;
        if (!empty(self::$s_version))
        {
            $url .= '/'.self::$s_version;
        }
        $url .= '/'.$resource;
        if (!empty($id))
        {
            $url .= '/'.$id;
        }
        if (!empty($arg))
        {
            $url .= '/'.$arg;
        }
        $this->m_ch = curl_init($url);
    }
    
    public function setHeaders($headers)
    {
        $this->m_headers = array_merge($this->m_headers, $headers);
    }
    
    private function formatHeaders() {
        $headers = array();
        foreach ($this->m_headers as $key=>$value)
        {
            $headers[] = $key.': '.$value;
        }
        return $headers;
    }
    
    public function setPostData($data)
    {
        curl_setopt($this->m_ch, CURLOPT_POST, true);
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, $data);
    }
    
    public function setPutData($data)
    {
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, $data);
    }
    
    public function setDeleteRequest()
    {
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
    
    private function processResponse($response)
    {
        $info = curl_getinfo($this->m_ch);
        $header = substr($response, 0, ($info['header_size']-1));
        $this->m_result = substr($response, $info['header_size']-1);
        $this->m_httpCode = $info['http_code'];
        foreach (explode(PHP_EOL, $header) as $line)
        {
            $line = explode(': ', $line);
            $key = $line[0];
            $value = $line[1];
            if ($key == 'Status')
            {
                $this->m_status = $value;
            }
            elseif ($key == 'Error')
            {
                $this->m_error = $value;
            }
        }
    }
}

