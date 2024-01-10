<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Bounds resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class BoundsController extends AbstractResourceController
{
    protected function getResourcePath(): string
    {
        return 'bounds';
    }
}
