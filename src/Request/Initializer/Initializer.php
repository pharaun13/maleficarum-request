<?php
/**
 * This class carries ioc initialization functionality used by this component.
 */
declare (strict_types=1);

namespace Maleficarum\Request\Initializer;

class Initializer {
    /* ------------------------------------ Class Methods START ---------------------------------------- */

    /**
     * This method will initialize the entire package.
     *
     * @param array $opts
     *
     * @return string
     */
    static public function initialize(array $opts = []): string {
        // load default builder if skip not requested
        $builders = $opts['builders'] ?? [];
        is_array($builders) or $builders = [];
        if (!isset($builders['request']['skip'])) {
            \Maleficarum\Ioc\Container::register('Maleficarum\Request\Request', function () {
                return (new \Maleficarum\Request\Request(new \Phalcon\Http\Request, \Maleficarum\Request\Request::PARSER_JSON));
            });
        }

        // load request object
        \Maleficarum\Ioc\Container::registerDependency('Maleficarum\Request', \Maleficarum\Ioc\Container::get('Maleficarum\Request\Request'));

        return __METHOD__;
    }

    /* ------------------------------------ Class Methods END ------------------------------------------ */
}
