<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Programmes resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class ProgrammesController extends AbstractResourceController
{
    protected function getResourcePath()
    {
        return 'programmes';
    }
}
