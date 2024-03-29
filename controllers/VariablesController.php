<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Variables resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class VariablesController extends AbstractResourceController
{
    protected function getResourcePath(): string
    {
        return 'variables';
    }
}
