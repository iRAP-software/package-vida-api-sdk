<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 *
 * Controller for the Star Ratings resource. Overrides the Abstract Resource Controller.
 *
 */

namespace iRAP\VidaSDK\Controllers;

use iRAP\VidaSDK\FilterInterface;
use iRAP\VidaSDK\Models\APIRequest;

class RoadClassificationsController extends AbstractResourceController
{
    protected function getResourcePath(): string
    {
        return 'road-classifications';
    }
}