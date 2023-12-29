<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Star Ratings resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

use iRAP\VidaSDK\Filter;

class StarRatingsController extends AbstractResourceController
{
    protected function getResourcePath()
    {
        return 'star-ratings';
    }
    
    
    /**
     * Fetches a before countermeasures star rating for a specified location. You must specify the 
     * location ID, and the ID of the dataset it belongs to.
     * @param int $id - the ID of the location
     * @param int $dataset_id - the ID of the dataset the location relates to
     * @param Filter $filter
     * @return \iRAP\VidaSDK\Models\APIRequest
     */
    public function getBeforeStarRatingsRequest($id, $dataset_id)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest($this->m_auth);
        $args = array('before', $dataset_id);
        $request->setUrl($this->getResourcePath(), $id, $args);
        return $request;
    }
    
    
    /**
     * Get a list of star ratings for a dataset, using the dataset's ID.
     * @param int $datasetID
     * @return \iRAP\VidaSDK\Models\APIRequest
     */
    public function getBeforeStarRatingsForDatasetRequest($datasetID, \iRAP\VidaSDK\FilterInterface $filter = null)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest($this->m_auth);
        $args = array('dataset', $datasetID, 'before');
        $request->setUrl($this->getResourcePath(), 'for', $args, $filter);
        return $request;
    }
    
    
    /**
     * Get a list of star ratings for a dataset, using the dataset's ID.
     * @param int $datasetID
     * @return \iRAP\VidaSDK\Models\APIRequest
     */
    public function getAfterStarRatingsForDatasetRequest($datasetID, \iRAP\VidaSDK\FilterInterface $filter = null)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest($this->m_auth);
        $args = array('dataset', $datasetID, 'after');
        $request->setUrl($this->getResourcePath(), 'for', $args, $filter);
        return $request;
    }
}