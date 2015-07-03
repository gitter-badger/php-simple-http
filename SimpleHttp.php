<?php
/**
 * Created by PhpStorm.
 * User: ec
 * Date: 29.06.15
 * Time: 15:39
 * Project: php-simple-http
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

namespace bpteam\SimpleHttp;

use bpteam\DryText\DryPath;

class SimpleHttp
{
    protected $options = [];
    protected $shame = 'http';
    protected $url;
    protected $context;
    protected $useProxy;

    public function setOption($name, $value)
    {
        $methodName = 'set' . ucfirst($name);
        if (method_exists(__CLASS__, $methodName)) {
            $this->$methodName($value);
        } else {
            $this->options[$name] = $value;
        }
    }

    public function unSetOption($name)
    {
        unset($this->options[$name]);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->options['method'];
    }

    /**
     * @param string $method GET or POST
     */
    public function setMethod($method)
    {
        $this->unSetOption('header');
        switch ($method) {
            case 'POST' :
                $this->options['method'] = 'POST';
                $this->onPost();
                break;
            default:
                $this->options['method'] = 'GET';
                $this->offPost();
        }
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->options['header'];
    }

    /**
     * @param string|array $header
     */
    public function setHeader($header)
    {
        $this->options['header'] = $this->makeHeader($header);
    }

    public function addHeader($header)
    {
        $this->options['header'] .= $this->makeHeader($header);
    }

    protected function makeHeader($header)
    {
        return is_array($header) ? implode("\n", $header) : ($header . "\n");
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->options['content'];
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->options['content'] = is_array($content) ? http_build_query($content) : $content;
    }

    public function getPost()
    {
        return $this->getContent();
    }

    public function setPost($post)
    {
        $this->setContent($post);
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->options['timeout'];
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->options['timeout'] = $timeout;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getShame()
    {
        return $this->shame;
    }

    /**
     * @param string $shame
     */
    public function setShame($shame)
    {
        switch ($shame) {
            case 'https' || 'ssl':
                $this->shame = 'ssl';
                $this->onHTTPS();
                break;
            default:
                $this->shame = 'http';
                $this->offHTTPS();
        }
        $this->shame = $shame;
    }

    /**
     * @param mixed $useProxy
     */
    public function setUseProxy($useProxy)
    {
        $this->useProxy = $this->setProxy($useProxy);
    }

    /**
     * @param bool|string $proxy
     * @return bool
     */
    protected function setProxy($proxy)
    {
        switch ((bool)$proxy) {
            case true:
                if (is_string($proxy)) {
                    if (DryPath::isIp($proxy)) {
                        $this->options['proxy'] = 'tcp://' . $proxy;
                        //$this->setOption('request_fulluri', true);
                    }
                }
                break;
            default:
                $this->unSetOption('proxy');
            //$this->unSetOption('request_fulluri');
        }
        return (bool)$proxy;
    }

    /**
     * @return mixed
     */
    public function getUseProxy()
    {
        return $this->useProxy;
    }

    protected function onHTTPS()
    {
        $this->setOption('verify_peer', false);
    }

    protected function offHTTPS()
    {
        $this->unSetOption('verify_peer');
    }

    protected function onPost()
    {
        $this->setHeader('Content-Type: application/x-www-form-urlencoded');
        $this->setOption('content', '');
    }

    protected function offPost()
    {
        $this->unSetOption('content');
    }

    protected function init()
    {
        $this->context = stream_context_create([$this->getShame() => $this->options]);
    }


    public function load($url)
    {
        $this->init();
        return file_get_contents($url, null, $this->context);
    }

}