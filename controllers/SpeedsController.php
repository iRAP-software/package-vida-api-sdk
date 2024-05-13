<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 *
 * Controller for the Speeds resource. Overrides the Abstract Resource Controller.
 *
 */

namespace iRAP\VidaSDK\Controllers;

use iRAP\VidaSDK\FilterInterface;
use iRAP\VidaSDK\Models\APIRequest;

class SpeedsController extends AbstractResourceController
{
    protected function getResourcePath(): string
    {
        return 'speeds';
    }
}