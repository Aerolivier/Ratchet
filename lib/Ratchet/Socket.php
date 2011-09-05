<?php
namespace Ratchet;

/**
 * A wrapper for the PHP socket_ functions
 * @author Chris Boden <shout at chrisboden dot ca>
 */
class Socket {
    /**
     * @type resource
     */
    protected $_socket;

    public static $_defaults = Array(
        'domain'   => AF_INET
      , 'type'     => SOCK_STREAM
      , 'protocol' => SOL_TCP
    );

    /**
     * @param int Specifies the protocol family to be used by the socket.
     * @param int The type of communication to be used by the socket
     * @param int Sets the specific protocol within the specified domain to be used when communicating on the returned socket
     * @throws Ratchet\Exception
     */
    public function __construct($domain = null, $type = null, $protocol = null) {
        list($domain, $type, $protocol) = static::getConfig($domain, $type, $protocol);

        $this->_socket = @socket_create($domain, $type, $protocol);

        if (!is_resource($this->_socket)) {
            throw new Exception();
        }
    }

    /**
     * @internal
     */
    protected static function getConfig($domain = null, $type = null, $protocol = null) {
        foreach (static::$_defaults as $key => $val) {
            if (null === $$key) {
                $$key = $val;
            }
        }

        return Array($domain, $type, $protocol);
    }

    /**
     * @internal
     */
    public function __call($method, $arguments) {
        if (function_exists('socket_' . $method)) {
            array_unshift($arguments, $this->_socket);
            return call_user_func_array('socket_' . $method, $arguments);
        }
    }
}