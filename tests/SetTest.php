<?php

/**
 * This file is part of the GetSet package.
 *
 * @author  Margus Kaidja <margusk@gmail.com>
 * @link    https://github.com/marguskaidja/php-getset
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

declare(strict_types=1);

namespace margusk\GetSet\Tests;

use margusk\GetSet\Attributes\Set;
use margusk\GetSet\Exceptions\InvalidArgumentException;
use margusk\GetSet\GetSetTrait;
use PHPUnit\Framework\TestCase;

class SetTest extends TestCase
{
    public function test_set_should_update_value_with_property_attribute()
    {
        $obj = new class {
            use GetSetTrait;

            #[Set]
            protected string $p1;

            public function getP1Value()
            {
                return $this->p1;
            }
        };

        $value = 'this is updated value';
        $obj->p1 = $value;
        $this->assertEquals($value, $obj->getP1Value());

        $value = 'this is updated value2';
        $obj->p1($value);
        $this->assertEquals($value, $obj->getP1Value());

        $value = 'this is updated value3';
        $obj->setP1($value);
        $this->assertEquals($value, $obj->getP1Value());
    }

    public function test_set_should_update_value_with_class_attribute()
    {
        $obj = new #[Set] class {
            use GetSetTrait;

            protected string $p1;

            public function getP1Value()
            {
                return $this->p1;
            }
        };

        $value = 'this is updated value';
        $obj->p1 = $value;
        $this->assertEquals($value, $obj->getP1Value());

        $value = 'this is updated value2';
        $obj->p1($value);
        $this->assertEquals($value, $obj->getP1Value());

        $value = 'this is updated value3';
        $obj->setP1($value);
        $this->assertEquals($value, $obj->getP1Value());
    }

    public function test_set_should_update_value_with_property_attribute_override()
    {
        $obj = new #[Set(false)] class {
            use GetSetTrait;

            #[Set(true)]
            protected string $p1;

            public function getP1Value()
            {
                return $this->p1;
            }
        };

        $value = 'this is updated value';
        $obj->p1 = $value;
        $this->assertEquals($value, $obj->getP1Value());

        $value = 'this is updated value2';
        $obj->p1($value);
        $this->assertEquals($value, $obj->getP1Value());

        $value = 'this is updated value3';
        $obj->setP1($value);
        $this->assertEquals($value, $obj->getP1Value());
    }

    public function test_mutator_in_class_attribute()
    {
        $obj = new #[Set(true, "htmlspecialchars")] class {
            use GetSetTrait;

            protected string $p1;

            public function getP1Value()
            {
                return $this->p1;
            }
        };

        $value = '<b>GetSet</b>';
        $obj->p1 = $value;
        $this->assertEquals(htmlspecialchars($value), $obj->getP1Value());
    }

    public function test_mutator_in_class_attribute_and_propertyname_substitution()
    {
        $obj = new #[Set(true, "self::mutate%property%")] class {
            use GetSetTrait;

            protected string $p1;

            public function mutateP1($value)
            {
                return htmlspecialchars($value);
            }

            public function getP1Value()
            {
                return $this->p1;
            }
        };

        $value = '<b>GetSet</b>';
        $obj->p1 = $value;
        $this->assertEquals($obj->mutateP1($value), $obj->getP1Value());
    }

    public function test_disable_mutator_with_property_attribute_override()
    {
        $obj = new #[Set(true, "htmlspecialchars")] class {
            use GetSetTrait;

            #[Set(true, "")]
            protected string $p1;

            public function getP1Value()
            {
                return $this->p1;
            }
        };

        $value = '<b>GetSet</b>';
        $obj->p1 = $value;
        $this->assertEquals($value, $obj->getP1Value());
    }

    public function test_set_should_fail_with_protected_value()
    {
        $this->expectException(InvalidArgumentException::class);

        $obj = new #[Set(true)] class {
            use GetSetTrait;

            protected string $p1;

            #[Set(false)]
            protected string $p2;
        };

        $obj->p2 = 'this must fail';
    }

    public function test_attributes_must_be_inherited_from_parent_class()
    {
        $obj = new class extends ParentTestClass {
            protected string $p1;

            public function getP1Value()
            {
                return $this->p1;
            }
        };

        $value = 'this is protected value';
        $obj->p1 = $value;
        $this->assertEquals($value, $obj->getP1Value());
    }

    public function test_honour_existing_setter_method()
    {
        $obj = new #[Set] class {
            use GetSetTrait;

            protected string $p1;

            public function setP1($value)
            {
                $this->p1 = 'mutated value';
            }

            public function getP1value()
            {
                return $this->p1;
            }
        };

        $obj->p1 = 'updated value';

        $this->assertEquals('mutated value', $obj->getP1value());
    }
}