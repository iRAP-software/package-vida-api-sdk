<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Road Classifications resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class RoadClassificationController extends AbstractResourceController
{
    protected function getResourcePath()
    {
        return 'road_classifications';
    }

    public function getResource($id = null, $args = null)
    {        
        $request = new \iRAP\VidaSDK\Models\APIRequest($this->m_auth);
        $request->setUrl($this->getResourcePath(), $args[0], $id);
        $request->send();
        return $this->response($request);
    }
}
