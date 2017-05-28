<?php
namespace BaseBundle\HttpFoundation;
use Symfony\Component\HttpFoundation\Request;

class SpecialRequest extends Request
{
    public function getBasePath() {
        return '';
    }

    public function getBaseUrl() {
        return '';
    }

    public static function createSpecialRequest(array $query = [], array $request = [], array $attributes = [], array $cookies = [],
                                                array $files = [], array $server = [], $content = null) {
        return new SpecialRequest($query, $request, $attributes, $cookies, $files, $server, $content);
    }
}