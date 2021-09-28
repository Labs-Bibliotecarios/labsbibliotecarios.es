<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DynamicOOOS\Symfony\Component\Cache\Exception;

use DynamicOOOS\Psr\Cache\CacheException as Psr6CacheInterface;
use DynamicOOOS\Psr\SimpleCache\CacheException as SimpleCacheInterface;
if (\interface_exists(SimpleCacheInterface::class)) {
    class LogicException extends \LogicException implements Psr6CacheInterface, SimpleCacheInterface
    {
    }
} else {
    class LogicException extends \LogicException implements Psr6CacheInterface
    {
    }
}
