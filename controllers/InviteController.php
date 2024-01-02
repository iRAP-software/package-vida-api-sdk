<?php
/* * ************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 * *************************************************************************************************
 *
 * Controller for User Invites
 *
 */

namespace iRAP\VidaSDK\Controllers;

use Exception;
use iRAP\VidaSDK\Models\Response;

class InviteController extends AbstractResourceController
{

    protected function getResourcePath(): string
    {
        return 'invites';
    }

    /**
     * @throws Exception
     */
    public function invite(string $email, string $first_name = null,
                           string $last_name = null,
                           array  $permissions = []): Response
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

    /**
     * @throws Exception
     */
    public function details($value): Response
    {
        return $this->getResource($value);
    }

    /**
     * @throws Exception
     */
    public function accept(string $token): Response
    {
        return $this->patchResource('',
                [
                'token' => $token
        ]);
    }
}