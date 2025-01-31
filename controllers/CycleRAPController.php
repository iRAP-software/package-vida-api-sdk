<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 *
 * Controller for the CycleRAP resource. Overrides the Abstract Resource Controller.
 *
 */

namespace iRAP\VidaSDK\Controllers;

class CycleRAPController extends AbstractResourceController
{
    protected function getResourcePath(): string
    {
        return 'cyclerap';
    }
}
