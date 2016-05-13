<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\StepEngine\Process\Steps;

class StepCollection implements StepCollectionInterface
{

    /**
     * @var array
     */
    private $steps = [];

    /**
     * @param \Spryker\Yves\StepEngine\Process\Steps\StepInterface $step
     *
     * @return $this
     */
    public function addStep(StepInterface $step)
    {
        $this->steps[] = $step;

        return $this;
    }
    
}
