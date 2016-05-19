<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\StepEngine\Process;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Cart\CartClientInterface;
use Spryker\Yves\StepEngine\Dependency\DataContainer\DataContainerInterface;
use Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface;
use Spryker\Yves\StepEngine\Process\StepEngine;
use Spryker\Yves\StepEngine\Dependency\Step\StepInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StepEngineTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testStepEnginePreCheckShouldReturnRedirectResponseWhenPreConditionReturnsFalse()
    {
        $escapeRoute = 'escape_route';
        $stepMock = $this->createStepMock();
        $stepMock->expects($this->once())->method('preCondition')->willReturn(false);
        $stepMock->expects($this->once())->method('getEscapeRoute')->willReturn($escapeRoute);

        $stepEngine = $this->createStepEngine([$stepMock], new QuoteTransfer());
        $response = $stepEngine->process($this->createRequest());

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($escapeRoute, $response->getTargetUrl());
    }

    /**
     * @return void
     */
    public function testStepEngineWhenRequireInputIsFalseShouldCallExecuteWithoutTriggeringFormHandling()
    {
        $stepRoute = 'test';
        $nextStepRoute = 'next_step_route';
        $quoteTransfer = new QuoteTransfer();

        $stepMock = $this->createStepMock();
        $stepMock->expects($this->once())->method('preCondition')->willReturn(true);
        $stepMock->expects($this->exactly(2))->method('postCondition')->willReturn(true);
        $stepMock->expects($this->exactly(4))->method('getStepRoute')->willReturn($stepRoute);
        $stepMock->expects($this->once())->method('requireInput')->willReturn(false);
        $stepMock->expects($this->once())->method('execute');

        $nextStepMock = $this->createStepMock();
        $nextStepMock->expects($this->exactly(1))->method('getStepRoute')->willReturn($nextStepRoute);

        $stepEngine = $this->createStepEngine([$stepMock, $nextStepMock], $quoteTransfer);

        $request = $this->createRequest();
        $request->attributes->set('_route', $stepRoute);
        $response = $stepEngine->process($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($nextStepRoute, $response->getTargetUrl());
    }

    /**
     * @return void
     */
    public function testStepEngineWhenFormUsedAndValidFormSubmitedShouldCallExecuteWithInput()
    {
        $stepRoute = 'test';
        $nextStepRoute = 'next_step_route';
        $quoteTransfer = new QuoteTransfer();

        $stepMock = $this->createStepMock();
        $stepMock->expects($this->once())->method('preCondition')->willReturn(true);
        $stepMock->expects($this->exactly(2))->method('postCondition')->willReturn(true);
        $stepMock->expects($this->exactly(4))->method('getStepRoute')->willReturn($stepRoute);
        $stepMock->expects($this->once())->method('requireInput')->willReturn(true);
        $stepMock->expects($this->once())->method('execute')->willReturn($quoteTransfer);

        $nextStepMock = $this->createStepMock();
        $nextStepMock->expects($this->exactly(1))->method('getStepRoute')->willReturn($nextStepRoute);

        $formMock = $this->createFormMock();
        $formMock->expects($this->once())->method('isValid')->willReturn(true);
        $formMock->expects($this->once())->method('getData')->willReturn($quoteTransfer);

        $formCollectionHandlerMock = $this->getFormCollectionHandlerMock();
        $formCollectionHandlerMock->expects($this->once())->method('hasSubmittedForm')->willReturn(true);
        $formCollectionHandlerMock->expects($this->once())->method('handleRequest')->willReturn($formMock);

        $stepEngine = $this->createStepEngine([$stepMock, $nextStepMock], $quoteTransfer);

        $request = $this->createRequest();
        $request->attributes->set('_route', $stepRoute);
        $response = $stepEngine->process($request, $formCollectionHandlerMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($nextStepRoute, $response->getTargetUrl());
    }

    /**
     * @return void
     */
    public function testStepEngineWhenFormUsedButNotSubmitedShoulSetDefaultDataToForm()
    {
        $stepRoute = 'test';
        $quoteTransfer = new QuoteTransfer();

        $stepMock = $this->createStepMock();
        $stepMock->expects($this->once())->method('preCondition')->willReturn(true);
        $stepMock->expects($this->exactly(1))->method('postCondition')->willReturn(true);
        $stepMock->expects($this->exactly(2))->method('getStepRoute')->willReturn($stepRoute);
        $stepMock->expects($this->once())->method('requireInput')->willReturn(true);

        $formCollectionHandlerMock = $this->getFormCollectionHandlerMock();
        $formCollectionHandlerMock->expects($this->once())->method('hasSubmittedForm')->willReturn(false);
        $formCollectionHandlerMock->expects($this->once())->method('provideDefaultFormData');
        $formCollectionHandlerMock->expects($this->once())->method('getForms')->willReturn([]);

        $stepEngine = $this->createStepEngine([$stepMock], $quoteTransfer);

        $request = $this->createRequest();
        $request->attributes->set('_route', $stepRoute);
        $response = $stepEngine->process($request, $formCollectionHandlerMock);

        $this->assertArrayHasKey('previousStepUrl', $response);
    }

    /**
     * @return void
     */
    public function testStepEngineWhenFormUsedAndSubmittedAndInvalidShouldRenderView()
    {
        $stepRoute = 'test';
        $quoteTransfer = new QuoteTransfer();

        $stepMock = $this->createStepMock();
        $stepMock->expects($this->once())->method('preCondition')->willReturn(true);
        $stepMock->expects($this->exactly(1))->method('postCondition')->willReturn(true);
        $stepMock->expects($this->exactly(2))->method('getStepRoute')->willReturn($stepRoute);
        $stepMock->expects($this->once())->method('requireInput')->willReturn(true);

        $formMock = $this->createFormMock();
        $formMock->expects($this->once())->method('isValid')->willReturn(false);

        $formCollectionHandlerMock = $this->getFormCollectionHandlerMock();
        $formCollectionHandlerMock->expects($this->once())->method('hasSubmittedForm')->willReturn(true);
        $formCollectionHandlerMock->expects($this->once())->method('getForms')->willReturn([]);

        $formCollectionHandlerMock->expects($this->once())->method('handleRequest')->willReturn($formMock);

        $stepEngine = $this->createStepEngine([$stepMock], $quoteTransfer);

        $request = $this->createRequest();
        $request->attributes->set('_route', $stepRoute);
        $response = $stepEngine->process($request, $formCollectionHandlerMock);

        $this->assertArrayHasKey('previousStepUrl', $response);
    }

    /**
     * @param array $steps
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Yves\StepEngine\Process\StepEngine
     */
    protected function createStepEngine(array $steps, QuoteTransfer $quoteTransfer)
    {
        $dataContainerMock = $this->getDataContainerMock();
        $dataContainerMock->method('get')->willReturn($quoteTransfer);

        return new StepEngine(
            $steps,
            $dataContainerMock,
            $this->createUrlGeneratorMock(),
            'error_route'
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Dependency\Step\StepInterface
     */
    protected function createStepMock()
    {
        $stepMock = $this->getMock(StepInterface::class);
        $stepMock->method('getTemplateVariables')->willReturn([]);

        return $stepMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected function createUrlGeneratorMock()
    {
        $urlGeneratorMock = $this->getMock(UrlGeneratorInterface::class);

        $urlGeneratorMock->method('generate')->willReturnCallback(
            function ($escapeRoute) {
                return $escapeRoute;
            }
        );

        return $urlGeneratorMock;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Client\Cart\CartClientInterface
     */
    protected function createCartClientMock(QuoteTransfer $quoteTransfer)
    {
        $cartClientMock = $this->getMock(CartClientInterface::class);
        $cartClientMock->method('getQuote')->willReturn($quoteTransfer);

        return $cartClientMock;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    protected function createRequest()
    {
        return Request::createFromGlobals();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Form\FormInterface
     */
    protected function createFormMock()
    {
        return $this->getMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Form\FormCollectionHandlerInterface
     */
    protected function getFormCollectionHandlerMock()
    {
        return $this->getMock(FormCollectionHandlerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Dependency\DataContainer\DataContainerInterface
     */
    private function getDataContainerMock()
    {
        $dataContainerMock = $this->getMockBuilder(DataContainerInterface::class);

        return $dataContainerMock->getMock();
    }

}