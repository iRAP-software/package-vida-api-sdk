<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Applied Countermeasures resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class AppliedCountermeasuresController extends AbstractResourceController
{
    protected function getResourcePath()
    {
        return 'applied-countermeasures';
    }
}
