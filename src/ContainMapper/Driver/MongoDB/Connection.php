<?php
/**
 * Contain Project
 *
 * This source file is subject to the BSD license bundled with
 * this package in the LICENSE.txt file. It is also available
 * on the world-wide-web at http://www.opensource.org/licenses/bsd-license.php.
 * If you are unable to receive a copy of the license or have
 * questions concerning the terms, please send an email to
 * me@andrewkandels.com.
 *
 * @category    akandels
 * @package     contain
 * @author      Andrew Kandels (me@andrewkandels.com)
 * @copyright   Copyright (c) 2012 Andrew P. Kandels (http://andrewkandels.com)
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link        http://andrewkandels.com/contain
 */

namespace ContainMapper\Driver\MongoDB;

use Traversable;
use ContainMapper\Exception;
use Mongo;
use ContainMapper\Driver\ConnectionInterface;

/**
 * MongoDB Connection
 *
 * @category    akandels
 * @package     contain
 * @copyright   Copyright (c) 2012 Andrew P. Kandels (http://andrewkandels.com)
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Connection implements ConnectionInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Mongo
     */
    protected $connection;

    /**
     * @var MongoCollection
     */
    protected $collection;

    /**
     * Constructor
     *
     * @param   array|Traversable           Configuration
     * @param   string                      Name of the MongoDB collection
     * @return  $this
     */
    public function __construct($config, $collection)
    {
        if ($config instanceof Traversable) {
            $config = iterator_to_array($config);
        }
        if (!is_array($config)) {
            throw new Exception\InvalidArgumentException('$config should be an array or an '
                . 'instance of Traversable.'
            );
        }

        $this->config = $config;
    }

    /**
     * Builds a connection to MongoDB and returns the Mongo object.
     *
     * @return  Mongo
     */
    public function getConnection()
    {
        // already established?
        if ($this->connection) {
            return $this->connection;
        }

        $dsn = 'mongodb://';

        if (!empty($this->config['username']) && !empty($this->config['password'])) {
            $dsn .= sprintf('%s:%s@',
                $this->config['username'],
                $this->config['password']
            );
        }

        if (empty($this->config['host'])) {
            throw new InvalidArgumentException('$config must include a \'host\' key.');
        }

        $dsn .= $this->config['host'];

        if (!empty($this->config['port'])) {
            $dsn .= ':' . $this->config['port'];
        }

        if (!empty($this->config['database'])) {
            $dsn .= '/' . $this->config['database'];
        }

        $this->connection = new Mongo($dsn);

        return $this->connection;
    }
}
