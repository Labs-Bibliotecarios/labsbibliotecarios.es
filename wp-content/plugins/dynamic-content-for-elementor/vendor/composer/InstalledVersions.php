<?php

namespace DynamicOOOS\Composer;

use DynamicOOOS\Composer\Autoload\ClassLoader;
use DynamicOOOS\Composer\Semver\VersionParser;
class InstalledVersions
{
    private static $installed = array('root' => array('pretty_version' => 'dev-develop', 'version' => 'dev-develop', 'aliases' => array(), 'reference' => 'cea435f42b6b461d45e4e0d7273c72a23c8eda88', 'name' => '__root__'), 'versions' => array('__root__' => array('pretty_version' => 'dev-develop', 'version' => 'dev-develop', 'aliases' => array(), 'reference' => 'cea435f42b6b461d45e4e0d7273c72a23c8eda88'), 'dompdf/dompdf' => array('pretty_version' => 'v0.8.3', 'version' => '0.8.3.0', 'aliases' => array(), 'reference' => '75f13c700009be21a1965dc2c5b68a8708c22ba2'), 'guzzlehttp/guzzle' => array('pretty_version' => '7.3.0', 'version' => '7.3.0.0', 'aliases' => array(), 'reference' => '7008573787b430c1c1f650e3722d9bba59967628'), 'guzzlehttp/promises' => array('pretty_version' => '1.4.1', 'version' => '1.4.1.0', 'aliases' => array(), 'reference' => '8e7d04f1f6450fef59366c399cfad4b9383aa30d'), 'guzzlehttp/psr7' => array('pretty_version' => '1.8.2', 'version' => '1.8.2.0', 'aliases' => array(), 'reference' => 'dc960a912984efb74d0a90222870c72c87f10c91'), 'jlawrence/eos' => array('pretty_version' => 'v3.2.2', 'version' => '3.2.2.0', 'aliases' => array(), 'reference' => '25e3d0f2316cb4636000f452a8e7dcc83725a32a'), 'matthiasmullie/path-converter' => array('pretty_version' => '1.1.3', 'version' => '1.1.3.0', 'aliases' => array(), 'reference' => 'e7d13b2c7e2f2268e1424aaed02085518afa02d9'), 'mcaskill/composer-exclude-files' => array('pretty_version' => 'v2.0.0', 'version' => '2.0.0.0', 'aliases' => array(), 'reference' => '2bf916ab1ec9959b3a58ba3652bad6ffedf0e10e'), 'myclabs/php-enum' => array('pretty_version' => '1.8.3', 'version' => '1.8.3.0', 'aliases' => array(), 'reference' => 'b942d263c641ddb5190929ff840c68f78713e937'), 'paquettg/php-html-parser' => array('pretty_version' => '3.1.1', 'version' => '3.1.1.0', 'aliases' => array(), 'reference' => '4e01a438ad5961cc2d7427eb9798d213c8a12629'), 'paquettg/string-encode' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => 'a8708e9fac9d5ddfc8fc2aac6004e2cd05d80fee'), 'paypal/paypal-checkout-sdk' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => 'ed6a55075448308b87a8b59dcb7fedf04a048cb1'), 'paypal/paypalhttp' => array('pretty_version' => '1.0.0', 'version' => '1.0.0.0', 'aliases' => array(), 'reference' => '1ad9b846a046f09d6135cbf2cbaa7701bbc630a3'), 'payum/iso4217' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => '6a45480e2818350dea58b7a076d0115aa7ff5789'), 'phenx/php-font-lib' => array('pretty_version' => '0.5.2', 'version' => '0.5.2.0', 'aliases' => array(), 'reference' => 'ca6ad461f032145fff5971b5985e5af9e7fa88d8'), 'phenx/php-svg-lib' => array('pretty_version' => 'v0.3.3', 'version' => '0.3.3.0', 'aliases' => array(), 'reference' => '5fa61b65e612ce1ae15f69b3d223cb14ecc60e32'), 'php-http/httplug' => array('pretty_version' => '2.2.0', 'version' => '2.2.0.0', 'aliases' => array(), 'reference' => '191a0a1b41ed026b717421931f8d3bd2514ffbf9'), 'php-http/promise' => array('pretty_version' => '1.1.0', 'version' => '1.1.0.0', 'aliases' => array(), 'reference' => '4c4c1f9b7289a2ec57cde7f1e9762a5789506f88'), 'psr/cache' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => 'd11b50ad223250cf17b86e38383413f5a6764bf8'), 'psr/cache-implementation' => array('provided' => array(0 => '1.0|2.0')), 'psr/container' => array('pretty_version' => '1.1.1', 'version' => '1.1.1.0', 'aliases' => array(), 'reference' => '8622567409010282b7aeebe4bb841fe98b58dcaf'), 'psr/http-client' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => '2dfb5f6c5eff0e91e20e913f8c5452ed95b86621'), 'psr/http-client-implementation' => array('provided' => array(0 => '1.0')), 'psr/http-message' => array('pretty_version' => '1.0.1', 'version' => '1.0.1.0', 'aliases' => array(), 'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363'), 'psr/http-message-implementation' => array('provided' => array(0 => '1.0')), 'psr/log' => array('pretty_version' => '1.1.4', 'version' => '1.1.4.0', 'aliases' => array(), 'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11'), 'psr/simple-cache-implementation' => array('provided' => array(0 => '1.0')), 'ralouphie/getallheaders' => array('pretty_version' => '3.0.3', 'version' => '3.0.3.0', 'aliases' => array(), 'reference' => '120b605dfeb996808c31b6477290a714d356e822'), 'sabberworm/php-css-parser' => array('pretty_version' => '8.3.1', 'version' => '8.3.1.0', 'aliases' => array(), 'reference' => 'd217848e1396ef962fb1997cf3e2421acba7f796'), 'stripe/stripe-php' => array('pretty_version' => 'v7.95.0', 'version' => '7.95.0.0', 'aliases' => array(), 'reference' => 'ed372a1f6430b06dda408bb33b27d2be35ceaf07'), 'sunra/php-simple-html-dom-parser' => array('pretty_version' => 'v1.5.2', 'version' => '1.5.2.0', 'aliases' => array(), 'reference' => '75b9b1cb64502d8f8c04dc11b5906b969af247c6'), 'symfony/cache' => array('pretty_version' => 'v4.4.30', 'version' => '4.4.30.0', 'aliases' => array(), 'reference' => 'f1c33520a5a439dfd7bd4d5e9cec26c6b79054cc'), 'symfony/cache-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => 'c0446463729b89dd4fa62e9aeecc80287323615d'), 'symfony/cache-implementation' => array('provided' => array(0 => '1.0|2.0')), 'symfony/css-selector' => array('pretty_version' => 'v3.4.47', 'version' => '3.4.47.0', 'aliases' => array(), 'reference' => 'da3d9da2ce0026771f5fe64cb332158f1bd2bc33'), 'symfony/dom-crawler' => array('pretty_version' => 'v3.4.47', 'version' => '3.4.47.0', 'aliases' => array(), 'reference' => 'ef97bcfbae5b384b4ca6c8d57b617722f15241a6'), 'symfony/expression-language' => array('pretty_version' => 'v3.4.47', 'version' => '3.4.47.0', 'aliases' => array(), 'reference' => 'de38e66398fca1fcb9c48e80279910e6889cb28f'), 'symfony/polyfill-ctype' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => '46cd95797e9df938fdd2b03693b5fca5e64b01ce'), 'symfony/polyfill-mbstring' => array('pretty_version' => 'v1.23.1', 'version' => '1.23.1.0', 'aliases' => array(), 'reference' => '9174a3d80210dca8daa7f31fec659150bbeabfc6'), 'symfony/polyfill-php70' => array('pretty_version' => 'v1.20.0', 'version' => '1.20.0.0', 'aliases' => array(), 'reference' => '5f03a781d984aae42cebd18e7912fa80f02ee644'), 'symfony/polyfill-php73' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => 'fba8933c384d6476ab14fb7b8526e5287ca7e010'), 'symfony/polyfill-php80' => array('pretty_version' => 'v1.23.1', 'version' => '1.23.1.0', 'aliases' => array(), 'reference' => '1100343ed1a92e3a38f9ae122fc0eb21602547be'), 'symfony/service-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => 'f040a30e04b57fbcc9c6cbcf4dbaa96bd318b9bb'), 'symfony/var-exporter' => array('pretty_version' => 'v5.3.7', 'version' => '5.3.7.0', 'aliases' => array(), 'reference' => '2ded877ab0574d8b646f4eb3f716f8ed7ee7f392'), 'tecnickcom/tcpdf' => array('pretty_version' => '6.4.2', 'version' => '6.4.2.0', 'aliases' => array(), 'reference' => '172540dcbfdf8dc983bc2fe78feff48ff7ec1c76'), 'telegram-bot/api' => array('pretty_version' => 'v2.3.21', 'version' => '2.3.21.0', 'aliases' => array(), 'reference' => 'dafad7c9d5468c07f9bbb06c3f358b4b4d842848'), 'tijsverkoyen/css-to-inline-styles' => array('pretty_version' => '2.2.3', 'version' => '2.2.3.0', 'aliases' => array(), 'reference' => 'b43b05cf43c1b6d849478965062b6ef73e223bb5'), 'yahnis-elsts/plugin-update-checker' => array('pretty_version' => 'v4.11', 'version' => '4.11.0.0', 'aliases' => array(), 'reference' => '3155f2d3f1ca5e7ed3f25b256f020e370515af43')));
    private static $canGetVendors;
    private static $installedByVendor = array();
    public static function getInstalledPackages()
    {
        $packages = array();
        foreach (self::getInstalled() as $installed) {
            $packages[] = \array_keys($installed['versions']);
        }
        if (1 === \count($packages)) {
            return $packages[0];
        }
        return \array_keys(\array_flip(\call_user_func_array('array_merge', $packages)));
    }
    public static function isInstalled($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (isset($installed['versions'][$packageName])) {
                return \true;
            }
        }
        return \false;
    }
    public static function satisfies(VersionParser $parser, $packageName, $constraint)
    {
        $constraint = $parser->parseConstraints($constraint);
        $provided = $parser->parseConstraints(self::getVersionRanges($packageName));
        return $provided->matches($constraint);
    }
    public static function getVersionRanges($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            $ranges = array();
            if (isset($installed['versions'][$packageName]['pretty_version'])) {
                $ranges[] = $installed['versions'][$packageName]['pretty_version'];
            }
            if (\array_key_exists('aliases', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['aliases']);
            }
            if (\array_key_exists('replaced', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['replaced']);
            }
            if (\array_key_exists('provided', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['provided']);
            }
            return \implode(' || ', $ranges);
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['version'])) {
                return null;
            }
            return $installed['versions'][$packageName]['version'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getPrettyVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['pretty_version'])) {
                return null;
            }
            return $installed['versions'][$packageName]['pretty_version'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getReference($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['reference'])) {
                return null;
            }
            return $installed['versions'][$packageName]['reference'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getRootPackage()
    {
        $installed = self::getInstalled();
        return $installed[0]['root'];
    }
    public static function getRawData()
    {
        return self::$installed;
    }
    public static function reload($data)
    {
        self::$installed = $data;
        self::$installedByVendor = array();
    }
    private static function getInstalled()
    {
        if (null === self::$canGetVendors) {
            self::$canGetVendors = \method_exists('DynamicOOOS\\Composer\\Autoload\\ClassLoader', 'getRegisteredLoaders');
        }
        $installed = array();
        if (self::$canGetVendors) {
            foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
                if (isset(self::$installedByVendor[$vendorDir])) {
                    $installed[] = self::$installedByVendor[$vendorDir];
                } elseif (\is_file($vendorDir . '/composer/installed.php')) {
                    $installed[] = self::$installedByVendor[$vendorDir] = (require $vendorDir . '/composer/installed.php');
                }
            }
        }
        $installed[] = self::$installed;
        return $installed;
    }
}
