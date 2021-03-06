<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Unit\Spryker\Yves\StepEngine\Dependency\Step\Fixtures;

use Spryker\Shared\Transfer\AbstractTransfer;
use Symfony\Component\HttpFoundation\Request;
use Spryker\Yves\StepEngine\Dependency\Step\AbstractBaseStep as AbstractBaseStep;

class BaseStep extends AbstractBaseStep
{

    /**
     * @param \Spryker\Shared\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function preCondition(AbstractTransfer $dataTransfer)
    {
        return true;
    }

    /**
     * @param \Spryker\Shared\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function requireInput(AbstractTransfer $dataTransfer)
    {
        return true;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Spryker\Shared\Transfer\AbstractTransfer $dataTransfer
     *
     * @return void
     */
    public function execute(Request $request, AbstractTransfer $dataTransfer)
    {
    }

    /**
     * @param \Spryker\Shared\Transfer\AbstractTransfer $dataTransfer
     *
     * @return bool
     */
    public function postCondition(AbstractTransfer $dataTransfer)
    {
        return true;
    }

}
