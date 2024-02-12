<?php
/* * ************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 * *************************************************************************************************
 *
 * Controller for Permission
 *
 */

namespace iRAP\VidaSDK\Controllers;

use Exception;
use iRAP\VidaSDK\Models\Response;

class PermissionController extends AbstractResourceController
{

    /*
     * This array will hold list of users that have permission to access
     * access identifier will be key and value will be array of user ids
     */
    private static array $permission = [];
    private static array $manager = [];
    private static ?Response $response = null;

    protected function getResourcePath(): string
    {
        return 'permission';
    }

    /**
     * @throws Exception
     */
    public function __construct($auth, $filter = null)
    {
        parent::__construct($auth, $filter);

        if (count(self::$permission) === 0 && count(self::$manager) === 0) {
            $this->getResource();
        }
    }

    public function getResource($id = null, $args = null): Response
    {
        self::$response = parent::getResource($id, $args);

        if (self::$response->getCode() === 200) {
            $permissions = self::$response->getResponse();

            if (!is_array($permissions)) {
                $permissions = [$permissions];
            }

            foreach ($permissions as $permission) {
                if (!array_key_exists($permission->access_identifier, self::$permission)) {
                    self::$permission[$permission->access_identifier] = [];
                }
                if (!array_key_exists($permission->access_identifier, self::$manager)) {
                    self::$manager[$permission->access_identifier] = [];
                }
                if ($permission->read && !in_array($permission->user_id, self::$permission[$permission->access_identifier])) {
                    self::$permission[$permission->access_identifier][] = $permission->user_id;
                }
                if (!$permission->read && in_array($permission->user_id, self::$permission[$permission->access_identifier])) {
                    self::$permission[$permission->access_identifier] = array_filter(self::$permission[$permission->access_identifier], fn($v) => $v !== $permission->user_id);
                }
                if ($permission->manager && !in_array($permission->user_id, self::$manager[$permission->access_identifier])) {
                    self::$manager[$permission->access_identifier][] = $permission->user_id;
                }
                if (!$permission->manager && in_array($permission->user_id, self::$manager[$permission->access_identifier])) {
                    self::$manager[$permission->access_identifier] = array_filter(self::$manager[$permission->access_identifier], fn($v) => $v !== $permission->user_id);
                }
            }
        }

        return self::$response;
    }

    /**
     * @throws Exception
     */
    public function hasPermission(int $userId, string $identifier = null): Response
    {
        if (self::$response->getCode() === 200) {
            $hasPermission = false;

            if (!is_null($identifier)) {
                $hasPermission = $this->checkPermission($userId, $identifier);
            } else {
                foreach (self::$permission as $i => $u) {
                    if ($this->checkPermission($userId, $i)) {
                        $hasPermission = true;
                        break;
                    }
                }
            }

            return new Response(200, $hasPermission ? 'true' : 'false', self::$response->getStatus(), self::$response->getError());
        } else {
            return self::$response;
        }
    }

    /**
     * @throws Exception
     */
    public function isManager(int $userId, string $identifier = null): Response
    {
        if (self::$response->getCode() === 200) {
            $isManager = false;

            if (!is_null($identifier)) {
                $isManager = $this->checkManager($userId, $identifier);
            } else {
                foreach (self::$manager as $i => $u) {
                    if ($this->checkManager($userId, $i)) {
                        $isManager = true;
                        break;
                    }
                }
            }

            return new Response(200, $isManager ? 'true' : 'false', self::$response->getStatus(), self::$response->getError());
        } else {
            return self::$response;
        }
    }

    /**
     * @param string $identifier
     * @param int|int[] $userId
     * @param bool|null $hasPermission
     * @param bool|null $isManager
     * @return Response
     * @throws Exception
     */
    public function setPermission(string $identifier, $userId, ?bool $hasPermission = null, ?bool $isManager = null): Response
    {
        $response = $this->postResource([
            'identifier' => $identifier,
            'user_id' => $userId,
            'permission' => $hasPermission,
            'manager' => $isManager
        ]);
        $this->setValues(self::$permission, $identifier, $userId, $hasPermission);
        $this->setValues(self::$manager, $identifier, $userId, $isManager);
        return $response;
    }

    private function checkPermission(int $userId, string $identifier): bool
    {
        return array_key_exists($identifier, self::$permission) && in_array($userId, self::$permission[$identifier]);
    }

    private function checkManager(int $userId, string $identifier): bool
    {
        return array_key_exists($identifier, self::$manager) && in_array($userId, self::$manager[$identifier]);
    }

    /**
     * Set values locally, so won't have to send multiple requests to the API
     * @param array $variableToAlter Private attributes of this class that holds permission details
     * @param string $identifier Access identifier
     * @param int|int[] $userId
     * @param ?bool $valueToCheck Based on this, the values will alter for this identifier in the private attributes
     * @return void
     */
    private function setValues(array &$variableToAlter, string $identifier, $userId, ?bool $valueToCheck = null): void
    {
        if (!is_null($valueToCheck)) {
            if (!array_key_exists($identifier, $variableToAlter)) {
                $variableToAlter[$identifier] = [];
            }

            if (!is_array($userId)) {
                $userId = [$userId];
            }

            foreach ($userId as $uId) {
                if ($valueToCheck && !in_array($uId, $variableToAlter[$identifier])) {
                    $variableToAlter[$identifier][] = $uId;
                }

                if (!$valueToCheck && in_array($uId, $variableToAlter[$identifier])) {
                    $variableToAlter[$identifier] = array_filter($variableToAlter[$identifier], fn($v) => $v !== $uId);
                }
            }
        }
    }
}