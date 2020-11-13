<?php

namespace Tinify;

const VERSION = "1.5.2";

class Exception extends \Exception {
    public $status;

    public static function create($message, $type, $status) {
        if ($status == 401 || $status == 429) {
            $klass = "Tinify\AccountException";
        } else if($status >= 400 && $status <= 499) {
            $klass = "Tinify\ClientException";
        } else if($status >= 500 && $status <= 599) {
            $klass = "Tinify\ServerException";
        } else {
            $klass = "Tinify\Exception";
        }

        if (empty($message)) $message = "No message was provided";
        return new $klass($message, $type, $status);
    }

    function __construct($message, $type = NULL, $status = NULL) {
        $this->status = $status;
        if ($status) {
            parent::__construct($message . " (HTTP " . $status . "/" . $type . ")");
        } else {
            parent::__construct($message);
        }
    }
}

class AccountException extends Exception {}
class ClientException extends Exception {}
class ServerException extends Exception {}
class ConnectionException extends Exception {}

class ResultMeta {
    protected $meta;

    public function __construct($meta) {
        $this->meta = $meta;
    }

    public function width() {
        return intval($this->meta["image-width"]);
    }

    public function height() {
        return intval($this->meta["image-height"]);
    }

    public function location() {
        return isset($this->meta["location"]) ? $this->meta["location"] : null;
    }
}

class Result extends ResultMeta {
    protected $data;

    public function __construct($meta, $data) {
        $this->meta = $meta;
        $this->data = $data;
    }

    public function data() {
        return $this->data;
    }

    public function toBuffer() {
        return $this->data;
    }

    public function toFile($path) {
        return file_put_contents($path, $this->toBuffer());
    }

    public function size() {
        return intval($this->meta["content-length"]);
    }

    public function mediaType() {
        return $this->meta["content-type"];
    }

    public function contentType() {
        return $this->mediaType();
    }
}

class Source {
    private $url, $commands;

    public static function fromFile($path) {
        return self::fromBuffer(file_get_contents($path));
    }

    public static function fromBuffer($string) {
        $response = Tinify::getClient()->request("post", "/shrink", $string);
        return new self($response->headers["location"]);
    }

    public static function fromUrl($url) {
        $body = array("source" => array("url" => $url));
        $response = Tinify::getClient()->request("post", "/shrink", $body);
        return new self($response->headers["location"]);
    }

    public function __construct($url, $commands = array()) {
        $this->url = $url;
        $this->commands = $commands;
    }

    public function preserve() {
        $options = $this->flatten(func_get_args());
        $commands = array_merge($this->commands, array("preserve" => $options));
        return new self($this->url, $commands);
    }

    public function resize($options) {
        $commands = array_merge($this->commands, array("resize" => $options));
        return new self($this->url, $commands);
    }

    public function store($options) {
        $response = Tinify::getClient()->request("post", $this->url,
            array_merge($this->commands, array("store" => $options)));
        return new Result($response->headers, $response->body);
    }

    public function result() {
        $response = Tinify::getClient()->request("get", $this->url, $this->commands);
        return new Result($response->headers, $response->body);
    }

    public function toFile($path) {
        return $this->result()->toFile($path);
    }

    public function toBuffer() {
        return $this->result()->toBuffer();
    }

    private static function flatten($options) {
        $flattened = array();
        foreach ($options as $option) {
            if (is_array($option)) {
                $flattened = array_merge($flattened, $option);
            } else {
                array_push($flattened, $option);
            }
        }
        return $flattened;
    }
}

class Client {
    const API_ENDPOINT = "https://api.tinify.com";

    const RETRY_COUNT = 1;
    const RETRY_DELAY = 500;

    private $options;

    public static function userAgent() {
        $curl = curl_version();
        return "Tinify/" . VERSION . " PHP/" . PHP_VERSION . " curl/" . $curl["version"];
    }

    function __construct($key, $app_identifier = NULL, $proxy = NULL) {
        $curl = curl_version();

        if (!($curl["features"] & CURL_VERSION_SSL)) {
            throw new ClientException("Your curl version does not support secure connections");
        }

        if ($curl["version_number"] < 0x071201) {
            $version = $curl["version"];
            throw new ClientException("Your curl version ${version} is outdated; please upgrade to 7.18.1 or higher");
        }

        $this->options = array(
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_USERPWD => "api:" . $key,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => join(" ", array_filter(array(self::userAgent(), $app_identifier))),
        );

        if ($proxy) {
            $parts = parse_url($proxy);
            if (isset($parts["host"])) {
                $this->options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
                $this->options[CURLOPT_PROXY] = $parts["host"];
            } else {
                throw new ConnectionException("Invalid proxy");
            }

            if (isset($parts["port"])) {
                $this->options[CURLOPT_PROXYPORT] = $parts["port"];
            }

            $creds = "";
            if (isset($parts["user"])) $creds .= $parts["user"];
            if (isset($parts["pass"])) $creds .= ":" . $parts["pass"];

            if ($creds) {
                $this->options[CURLOPT_PROXYAUTH] = CURLAUTH_ANY;
                $this->options[CURLOPT_PROXYUSERPWD] = $creds;
            }
        }
    }

    function request($method, $url, $body = NULL) {
        $header = array();
        if (is_array($body)) {
            if (!empty($body)) {
                $body = json_encode($body);
                array_push($header, "Content-Type: application/json");
            } else {
                $body = NULL;
            }
        }

        for ($retries = self::RETRY_COUNT; $retries >= 0; $retries--) {
            if ($retries < self::RETRY_COUNT) {
                usleep(self::RETRY_DELAY * 1000);
            }

            $request = curl_init();
            if ($request === false || $request === null) {
                throw new ConnectionException(
                    "Error while connecting: curl extension is not functional or disabled."
                );
            }

            curl_setopt_array($request, $this->options);

            $url = strtolower(substr($url, 0, 6)) == "https:" ? $url : self::API_ENDPOINT . $url;
            curl_setopt($request, CURLOPT_URL, $url);
            curl_setopt($request, CURLOPT_CUSTOMREQUEST, strtoupper($method));

            if (count($header) > 0) {
                curl_setopt($request, CURLOPT_HTTPHEADER, $header);
            }

            if ($body) {
                curl_setopt($request, CURLOPT_POSTFIELDS, $body);
            }

            $response = curl_exec($request);

            if (is_string($response)) {
                $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
                $headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
                curl_close($request);

                $headers = self::parseHeaders(substr($response, 0, $headerSize));
                $body = substr($response, $headerSize);

                if (isset($headers["compression-count"])) {
                    Tinify::setCompressionCount(intval($headers["compression-count"]));
                }

                if ($status >= 200 && $status <= 299) {
                    return (object) array("body" => $body, "headers" => $headers);
                }

                $details = json_decode($body);
                if (!$details) {
                    $message = sprintf("Error while parsing response: %s (#%d)",
                        PHP_VERSION_ID >= 50500 ? json_last_error_msg() : "Error",
                        json_last_error());
                    $details = (object) array(
                        "message" => $message,
                        "error" => "ParseError"
                    );
                }

                if ($retries > 0 && $status >= 500) continue;
                throw Exception::create($details->message, $details->error, $status);
            } else {
                $message = sprintf("%s (#%d)", curl_error($request), curl_errno($request));
                curl_close($request);
                if ($retries > 0) continue;
                throw new ConnectionException("Error while connecting: " . $message);
            }
        }
    }

    protected static function parseHeaders($headers) {
        if (!is_array($headers)) {
            $headers = explode("\r\n", $headers);
        }

        $res = array();
        foreach ($headers as $header) {
            if (empty($header)) continue;
            $split = explode(":", $header, 2);
            if (count($split) === 2) {
                $res[strtolower($split[0])] = trim($split[1]);
            }
        }
        return $res;
    }
}

class Tinify {
    private static $key = NULL;
    private static $appIdentifier = NULL;
    private static $proxy = NULL;

    private static $compressionCount = NULL;
    private static $client = NULL;

    public static function setKey($key) {
        self::$key = $key;
        self::$client = NULL;
    }

    public static function setAppIdentifier($appIdentifier) {
        self::$appIdentifier = $appIdentifier;
        self::$client = NULL;
    }

    public static function setProxy($proxy) {
        self::$proxy = $proxy;
        self::$client = NULL;
    }

    public static function getCompressionCount() {
        return self::$compressionCount;
    }

    public static function setCompressionCount($compressionCount) {
        self::$compressionCount = $compressionCount;
    }

    public static function getClient() {
        if (!self::$key) {
            throw new AccountException("Provide an API key with Tinify\setKey(...)");
        }

        if (!self::$client) {
            self::$client = new Client(self::$key, self::$appIdentifier, self::$proxy);
        }

        return self::$client;
    }

    public static function setClient($client) {
        self::$client = $client;
    }
}

function setKey($key) {
    return Tinify::setKey($key);
}

function setAppIdentifier($appIdentifier) {
    return Tinify::setAppIdentifier($appIdentifier);
}

function setProxy($proxy) {
    return Tinify::setProxy($proxy);
}

function getCompressionCount() {
    return Tinify::getCompressionCount();
}

function compressionCount() {
    return Tinify::getCompressionCount();
}

function fromFile($path) {
    return Source::fromFile($path);
}

function fromBuffer($string) {
    return Source::fromBuffer($string);
}

function fromUrl($string) {
    return Source::fromUrl($string);
}

function validate() {
    try {
        Tinify::getClient()->request("post", "/shrink");
    } catch (AccountException $err) {
        if ($err->status == 429) return true;
        throw $err;
    } catch (ClientException $err) {
        return true;
    }
}
