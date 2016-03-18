<?php

/*
 * This file is part of the OverblogGraphQLBundle package.
 *
 * (c) Overblog <http://github.com/overblog/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Overblog\GraphQLBundle\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Resolver
{
    /**
     * @var PropertyAccessor
     */
    private static $accessor;

    public static function defaultResolveFn($source, $args, ResolveInfo $info)
    {
        $fieldName = $info->fieldName;
        $value = null;

        $index = sprintf('[%s]', $fieldName);

        if (self::getAccessor()->isReadable($source, $index)) {
            $value = self::getAccessor()->getValue($source, $index);
        } elseif (is_object($source)) {
            $value = self::propertyValueFromObject($source, $fieldName);
        }

        return $value instanceof \Closure ? $value($source, $args, $info) : $value;
    }

    private static function propertyValueFromObject($object, $fieldName)
    {
        $value = null;

        if (self::getAccessor()->isReadable($object, $fieldName)) {
            $value = self::getAccessor()->getValue($object, $fieldName);
        }

        return $value;
    }

    private static function getAccessor()
    {
        if (null === self::$accessor) {
            self::$accessor = PropertyAccess::createPropertyAccessor();
        }

        return self::$accessor;
    }
}
