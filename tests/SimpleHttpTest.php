<?php
/**
 * Created by PhpStorm.
 * User: ec
 * Date: 29.06.15
 * Time: 15:44
 * Project: php-simple-http
 * @author: Evgeny Pynykh bpteam22@gmail.com
 */

namespace bpteam\SimpleHttp;

use \PHPUnit_Framework_TestCase;
use \ReflectionClass;

class SimpleHttpTest extends PHPUnit_Framework_TestCase {
    /**
     * @param        $name
     * @param string $className
     * @return \ReflectionMethod
     */
    protected static function getMethod($name, $className = 'bpteam\SimpleHttp\SimpleHttp')
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @param        $name
     * @param string $className
     * @return \ReflectionProperty
     */
    protected static function getProperty($name, $className = 'bpteam\SimpleHttp\SimpleHttp')
    {
        $class = new ReflectionClass($className);
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property;
    }

    public function testLoad()
    {
        $shttp = new SimpleHttp();
        $url = 'http://ya.ru';
        $text = $shttp->load($url);
        $this->assertRegExp('%yandex%ims', $text);
    }

    public function testSendPost()
    {
        $shttp = new SimpleHttp();
        $url = 'http://bpteam.net/post_test.php';
        $post = ['url' => 'vk.com', 'test' => 'test_post'];
        $shttp->setMethod('POST');
        $shttp->setPost($post);
        $text = $shttp->load($url);
        $this->assertRegExp('%test_post%ims', $text);
    }

    public function testSetShame()
    {
        $shttp = new SimpleHttp();
        $url = 'https://vk.com';
        $shttp->setShame('https');
        $text = $shttp->load($url);
        $this->assertRegExp('%vk\.com%ims', $text);
    }
}