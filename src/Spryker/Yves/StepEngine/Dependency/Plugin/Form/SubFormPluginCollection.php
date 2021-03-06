<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\StepEngine\Dependency\Plugin\Form;

class SubFormPluginCollection implements \Iterator, \Countable
{

    /**
     * @var \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface[]
     */
    private $subForms = [];

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface $subForm
     *
     * @return $this
     */
    public function add(SubFormPluginInterface $subForm)
    {
        $this->subForms[] = $subForm;

        return $this;
    }

    /**
     * @return \Spryker\Yves\StepEngine\Dependency\Plugin\Form\SubFormPluginInterface
     */
    public function current()
    {
        return $this->subForms[$this->position];
    }

    /**
     * @return void
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->subForms[$this->position]);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->subForms);
    }

}
