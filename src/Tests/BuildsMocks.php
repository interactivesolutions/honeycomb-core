<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombCore\Tests;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Trait BuildsMocks
 * @package InteractiveSolutions\HoneycombCore\Tests
 */
trait BuildsMocks
{
    /**
     * @param string $mockClass
     * @param string|null $injectorClass
     * @param bool $method
     * @param array|null $constructorArgs
     * @param bool $onlyForInjector
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws \ReflectionException
     */
    public function initPHPUnitMock(
        string $mockClass,
        string $injectorClass = null,
        $method = false,
        array $constructorArgs = null,
        bool $onlyForInjector = false
    ): PHPUnit_Framework_MockObject_MockObject {
        $this->forgetInstances($mockClass, $injectorClass);
        $mock = $this->createPHPUnitMockBuilder($mockClass, $constructorArgs, $method);
        $this->injectMockToLaravel($mockClass, $mock, $onlyForInjector, $injectorClass);

        return $mock;
    }

    /**
     * @param string $mockClass
     * @param string|null $injectorClass
     * @param bool $onlyForInjector
     * @return MockInterface
     */
    public function initMockeryMock(
        string $mockClass,
        string $injectorClass = null,
        bool $onlyForInjector = false
    ): MockInterface {
        $this->forgetInstances($mockClass, $injectorClass);
        $mock = Mockery::mock($mockClass);
        $this->injectMockToLaravel($mockClass, $mock, $onlyForInjector, $injectorClass);

        return $mock;
    }

    /**
     * @param string $mockClass
     * @param string|null $injectorClass
     */
    private function forgetInstances(string $mockClass, string $injectorClass = null): void
    {
        $this->app->forgetInstance($mockClass);

        if (isset($injectorClass)) {
            $this->app->forgetInstance($injectorClass);
        }
    }

    /**
     * @param string $mockClass
     * @param array|null $constructorArgs
     * @param array|null|bool $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws \ReflectionException
     */
    private function createPHPUnitMockBuilder(
        string $mockClass,
        array $constructorArgs = null,
        $methods = false
    ): PHPUnit_Framework_MockObject_MockObject {
        /** @var MockBuilder $builder */
        $builder = $this->getMockBuilder($mockClass);

        if (isset($constructorArgs)) {
            $builder->setConstructorArgs($constructorArgs);
        } else {
            $builder->disableOriginalConstructor();
        }

        if ($methods !== false) {
            $builder->setMethods($methods);
        }

        return $builder->getMock();
    }

    /**
     * @param string $mockClass
     * @param PHPUnit_Framework_MockObject_MockObject|MockInterface $mock
     * @param bool $onlyForInjector
     * @param string|null $injectorClass
     */
    private function injectMockToLaravel(
        string $mockClass,
        $mock,
        bool $onlyForInjector = false,
        string $injectorClass = null
    ): void {
        if ($onlyForInjector) {
            if (!isset($injectorClass)) {
                throw new \RuntimeException('Injector class must be specified when using "onlyForInjector" bind');
            }

            $this->app->when($injectorClass)->needs($mockClass)->give(function () use ($mock) {
                return $mock;
            });
        } else {
            $this->app->instance($mockClass, $mock);
        }
    }
}
