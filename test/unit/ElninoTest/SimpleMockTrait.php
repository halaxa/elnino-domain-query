<?php
/**
 * Created by PhpStorm.
 * User: Filip
 * Date: 7.3.14
 * Time: 8:01
 */

namespace ElninoTest;

use PHPUnit_Framework_MockObject_Matcher_Invocation as Invocation;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

trait SimpleMockTrait
{
    /**
     * Creates mock with methods returning values specified in the map.
     * If return value is Exception, the mock will be configured to throw it instead of returning it.
     * If return value is Closure, the mock will be configured to use it as the method itself.
     * Optional parameter $invocations is array of PHPUnit_Framework_MockObject_Matcher_Invocation ($this->once() etc.)
     * to specify invocation count with the same order as methods. Defaults to $this->atLeastOnce() for every method
     * if ommited.
     *
     * <code>
     * $mock1 = $this->getMockSimply('SomeClass');
     *
     * $mock2 = $this->getMockSimply('MyClass', [
     *      'isReady'       => false
     *      'makeDinner'    => new Exception('Not enough ingredients')  // makeDinner will throw it not return it
     *      'createMouse'   => function ($eyes, $legs) {  // createMouse will be this Closure
     *                             // ...
     *                             return $mouse;
     *                         }
     * ],[
     *      $this->any(), // isReady
     *      null, // makeDinner will default to $this->atLeastOnce
     *      $this->exactly(2) // createMouse
     * ]);
     * </code>
     *
     * @param string       $className   FQCN of a class to mock -- will be passed to getMock()
     * @param array        $methods     Map of method names and their respective return values.
     * @param Invocation[] $invocations Array of invocations with order of $methods
     * @return MockObject
     */
    public function getMockSimply($className, array $methods = [], array $invocations = [])
    {
        /** @var \PHPUnit_Framework_TestCase $this */
        $mock = $this->getMockBuilder($className)->disableOriginalConstructor()->getMock();
        $idx = 0;
        foreach ($methods as $name => $returnValue) {

            if (is_object($returnValue) && $returnValue instanceof \Exception) {
                $mockReturn = $this->throwException($returnValue);

            } elseif (is_object($returnValue) && $returnValue instanceof \Closure) {
                $mockReturn = $this->returnCallback($returnValue);

            } else {
                $mockReturn = $this->returnValue($returnValue);
            }

            if (isset($invocations[$idx]) && $invocations[$idx] instanceof Invocation) {
                $invocation = $invocations[$idx];
            } else {
                $invocation = $this->atLeastOnce();
            }
            ++$idx;

            $mock->expects($invocation)
                ->method($name)
                ->will($mockReturn);
        }
        return $mock;
    }
} 
