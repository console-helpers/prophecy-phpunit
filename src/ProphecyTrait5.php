<?php

namespace Prophecy\PhpUnit;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Doubler\DoubleException;
use Prophecy\Exception\Doubler\InterfaceNotFoundException;
use Prophecy\Exception\Prediction\PredictionException;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;

/**
 * @mixin TestCase
 */
trait ProphecyTrait
{
    /**
     * @var Prophet|null
     *
     * @internal
     */
    private $prophet;

    /**
     * @throws DoubleException
     * @throws InterfaceNotFoundException
     *
     * @template T of object
     * @phpstan-param class-string<T>|null $classOrInterface
     * @phpstan-return ($classOrInterface is null ? ObjectProphecy<object> : ObjectProphecy<T>)
     *
     * @not-deprecated
     */
    protected function prophesize($classOrInterface = null)
    {
        static $hasFailureTypes;

        // PHPUnit 10.1.0+.
        if ($hasFailureTypes === null) {
            $hasFailureTypes = method_exists($this, 'registerFailureType');
        }

        if ($hasFailureTypes) {
            $this->registerFailureType(PredictionException::class);
        }

        return $this->getProphet()->prophesize($classOrInterface);
    }

    /**
     * @after
     */
    #[After]
    protected function tearDownProphecy()
    {
        if (null !== $this->prophet) {
            $this->verifyProphecyDoubles();
        }

        $this->prophet = null;
    }

    protected function verifyProphecyDoubles()
    {
        if ($this->prophet === null) {
            return;
        }

        try {
            $this->prophet->checkPredictions();
        } catch (PredictionException $e) {
            throw new AssertionFailedError($e->getMessage());
        } finally {
            // Some Prophecy assertions may have been done in tests themselves even when a failure happened before checking mock objects.
            $this->countProphecyAssertions();
        }
    }

    /**
     * @internal
     */
    private function countProphecyAssertions()
    {
        \assert($this instanceof TestCase);

        foreach ($this->prophet->getProphecies() as $objectProphecy) {
            foreach ($objectProphecy->getMethodProphecies() as $methodProphecies) {
                foreach ($methodProphecies as $methodProphecy) {
                    \assert($methodProphecy instanceof MethodProphecy);

                    $this->addToAssertionCount(\count($methodProphecy->getCheckedPredictions()));
                }
            }
        }
    }

    /**
     * @internal
     */
    private function getProphet()
    {
        if ($this->prophet === null) {
            $this->prophet = new Prophet;
        }

        return $this->prophet;
    }
}
