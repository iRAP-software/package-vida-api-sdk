<?php
/* * ************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 * *************************************************************************************************
 *
 * Controller for User Invites
 *
 */

namespace iRAP\VidaSDK\Controllers;

class InviteController extends AbstractResourceController
{

    protected function getResourcePath()
    {
        return 'invites';
    }

    public function inviteUser(string $email, string $first_name = null,
                               string $last_name = null,
                               string $permissions = null)
    {
        $data = ['email' => $email];

        if (!is_null($first_name)) {
            $data['first_name'] = $first_name;
        }

        if (!is_null($last_name)) {
            $data['last_name'] = $last_name;
        }

        if (!is_null($permissions)) {
            $data['permissions'] = $permissions;
        }

        return $this->postResource($data);
    }

    public function getInviteDetails($value)
    {
        return $this->getResource($value);
    }

    public function acceptInvitation(string $token)
    {
        return $this->patchResource('',
                [
                'token' => $token
        ]);
    }
}