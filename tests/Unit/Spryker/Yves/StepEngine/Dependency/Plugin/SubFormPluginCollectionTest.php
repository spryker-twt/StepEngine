<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\StepEngine\Dependency\Plugin;

use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginCollection;
use Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface;

/**
 * @group Unit
 * @group Spryker
 * @group Yves
 * @group StepEngine
 * @group Dependency
 * @group Plugin
 * @group SubFormPluginCollectionTest
 */
class SubFormPluginCollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return void
     */
    public function testAdd()
    {
        $subFormPluginCollection = new SubFormPluginCollection();

        $this->assertInstanceOf(
            SubFormPluginCollection::class,
            $subFormPluginCollection->add($this->getSubFormPlugin())
        );
    }

    /**
     * @return void
     */
    public function testKey()
    {
        $subFormPluginCollection = new SubFormPluginCollection();

        $this->assertSame(0, $subFormPluginCollection->key());
    }

    /**
     * @return void
     */
    public function testCollectionMustIterateable()
    {
        $subFormPluginCollection = new SubFormPluginCollection();
        $subFormPluginCollection->add($this->getSubFormPlugin());

        foreach ($subFormPluginCollection as $subFormPlugin) {
            $this->assertInstanceOf(SubFormPluginInterface::class, $subFormPlugin);
        }
    }

    /**
     * @return void
     */
    public function testCollectionIsCountable()
    {
        $subFormPluginCollection = new SubFormPluginCollection();
        $subFormPluginCollection->add($this->getSubFormPlugin());

        $this->assertCount(1, $subFormPluginCollection);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface
     */
    private function getSubFormPlugin()
    {
        return $this->getMock(SubFormPluginInterface::class);
    }

}
