<?php

declare(strict_types=1);

namespace Constellix\Client\Managers\ContactList;

use Constellix\Client\Models\ContactLists\TeamsWebhook;

/**
 * Manages contact list Teams Webhook resources
 * @package Constellix\Client\Managers
 */
class TeamsWebhookManager extends AbstractContactListItemManager
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/contactlists/:contactlist_id/teams';
    protected string $modelClassName = TeamsWebhook::class;

    /**
     * Fetch a specific Teams webhook
     * @param int $id
     * @return TeamsWebhook
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): TeamsWebhook
    {
        return $this->getObject($id);
    }

    /**
     * Instantiate a new Teams Webhook object.
     * @param string|null $className
     * @return TeamsWebhook
     */
    protected function createObject(?string $className = null): TeamsWebhook
    {
        $object = parent::createObject($className);
        $object->setContactList($this->contactList);
        return $object;
    }
}
