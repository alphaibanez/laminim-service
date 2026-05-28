<?php

namespace Lkt\Http;

use Lkt\Debug\VarDumper;
use Lkt\Factory\Instantiator\Instances\AbstractInstance;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Schema;
use Lkt\Http\DTO\GrantedPermsAttempt;
use Lkt\Http\DTO\TargetAccessPolicy;
use Lkt\Http\Enums\AccessLevel;
use Lkt\Http\Enums\HttpEvent;
use Lkt\Http\Enums\HttpStatus;
use Lkt\Http\Routes\AbstractRoute;
use Lkt\Users\Interfaces\SessionUserInterface;
use Lkt\WebItems\Enums\WebItemAction;
use \Lkt\WebItems\WebItem;
use function Lkt\Tools\Arrays\digArray;

class Request
{
    readonly public AccessLevel $accessLevel;
    readonly public string $targetComponent;
    readonly public TargetAccessPolicy $targetAccessPolicy;

    /** @var TargetAccessPolicy[] */
    protected array $targetAccessPolicyAttempts;
    readonly public GrantedPermsAttempt $attemptToGrantPerms;
    readonly public string $extractedTargetInstanceIdFromParamsKey;
    readonly public WebItem|null $targetWebItem;
    readonly public AbstractInstance|null $targetInstance;
    readonly public SessionUserInterface|null $loggedUser;

    /** @var HttpEventHandler[] */
    readonly public array $httpEventHandlers;

    readonly public bool $hasValidAccess;
    readonly public HttpStatus|null $hasValidAccessStatus;
    readonly public int $page;
    readonly public array $payload;


    public function __construct(
        readonly public array       $params = [],
        AbstractRoute $route,
        bool $ensureLoggedUser = true,
    )
    {
        $this->accessLevel = $route->getAccessLevel();

        $this->loggedUser = Router::getRouteLoggedUser($route);

        if ($this->accessLevel === AccessLevel::OnlyNotLoggedUsers && $this->loggedUser) {
            $this->hasValidAccess = false;
            return;
        }

        if ($ensureLoggedUser && !$this->loggedUser && ($this->accessLevel === AccessLevel::OnlyLoggedUsers || $this->accessLevel === AccessLevel::OnlyAdminUsers)) {
            $this->hasValidAccess = false;
            $this->hasValidAccessStatus = HttpStatus::Unauthorized;
            return;
        }

        if ($this->accessLevel === AccessLevel::OnlyAdminUsers && !$this->loggedUser?->hasAdminAccess()) {
            $this->hasValidAccess = false;
            return;
        }
        $this->hasValidAccessStatus = null;

        $this->httpEventHandlers = $route->getHttpEventHandlers();

        // Page
        $extractPageKey = $route->getPageValueParamsExtractionKey();
        if ($extractPageKey) $this->page = (int)$this->params[$extractPageKey];

        $payload = digArray($this->params, $route->getPayloadValueParamsExtractionKey());
        if (!$payload) $payload = [];
        if (!is_array($payload)) $payload = [$payload];
        $this->payload = $payload;

        // Access Level: Component
        $extractWebItemKey = $route->getWebItemValueParamsExtractionKey();
        if ($extractWebItemKey) {
            $this->targetWebItem = WebItem::detectWebItem($this->params[$extractWebItemKey]);
            if ($this->targetWebItem) $this->targetComponent = $this->targetWebItem->component;
            else $this->targetComponent = '';

        } else {
            $this->targetComponent = $route->getTargetComponent();
            $this->targetWebItem = null;
        }

        $this->targetAccessPolicyAttempts = $route->getTargetAccessPolicyAttempts();
        $this->targetAccessPolicy = $route->getTargetAccessPolicy();
        $this->attemptToGrantPerms = $route->getGrantedPermsAttempt();

        $targetIsLoggedUser = $route->getTargetIsLoggedUser();

        if ($targetIsLoggedUser) {
            $this->targetInstance = $this->loggedUser;
        } else {

            // Access Level: Component Instance
            $extractIdKey = $route->getIdColumnValueParamsExtractionKey();

            if ($this->targetComponent && $extractIdKey) {
                $schema = Schema::get($this->targetComponent);
                $idValue = digArray($this->params, $extractIdKey);
                $identifiers = array_values($schema->getIdentifiers());

                $tmp = [];

                if (!$idValue) {
                    if ($this->payload) {
                        $idValues = $this->payload;
                    } else {
                        $idValues = $this->params;
                    }

                    foreach ($identifiers  as $i => $identifier) {
                        $name = $identifier->getName();
                        if ($identifier instanceof ForeignKeyField && isset($idValues[$name. 'Id'])) {
                            $tmp[$name] = $idValues[$name. 'Id'];

                        } else {
                            $tmp[$name] = $idValues[$name];
                        }
                    }

                } else {
                    if (strpos($idValue, ',') !== false) {
                        $idValues = explode(',', $idValue);

                    } else {
                        $idValues = [$idValue];
                    }

                    foreach ($identifiers  as $i => $identifier) {
                        $tmp[$identifier->getName()] = $idValues[$i];
                    }
                }
                $idValue = $tmp;
                $instance =  $schema->getItemInstance($idValue);
                $this->extractedTargetInstanceIdFromParamsKey = $extractIdKey;
                $targetInstance = $instance;
                if (!$instance) {
                    $this->hasValidAccess = false;
                    return;
                }
            } elseif ($this->targetComponent && $route->isAnonymousTarget()) {
                $schema = Schema::get($this->targetComponent);
                $instance = $schema->getItemInstance();
                $targetInstance = $instance;

            } else {
                $targetInstance = null;
            }
            $this->targetInstance = $targetInstance;

            if ($this->targetComponent){
                if ($this->accessLevel === AccessLevel::OnlyAdminUsers) {
                    $isValid = true;
                    foreach ($route->getRequiredPermissions() as $permission) {
                        $isValid = $isValid && $this->loggedUser->hasAdminPermission($this->targetComponent, $permission, $this->targetInstance);
                    }
                    if (!$isValid) {
                        $this->hasValidAccess = $isValid;
                        return;
                    }

                } else if ($this->accessLevel === AccessLevel::OnlyLoggedUsers) {
                    $isValid = true;
                    foreach ($route->getRequiredPermissions() as $permission) {
                        $isValid = $isValid && $this->loggedUser->hasAppPermission($this->targetComponent, $permission, $this->targetInstance);
                    }

                    if (!$isValid) {
                        $this->hasValidAccess = $isValid;
                        return;
                    }
                }
            }
        }

        $this->hasValidAccess = true;
    }

    public function getTargetAccessPolicy(WebItemAction $webItemAction): string|Response
    {
        $policy = null;

        if (count($this->targetAccessPolicyAttempts) > 0 && $this->targetComponent) {
            $schema = Schema::get($this->targetComponent);
            foreach ($this->targetAccessPolicyAttempts as $policyAttempt) {
                $key = '';
                switch ($policyAttempt->type) {
                    case 'simple':
                        $key = $policyAttempt->public;
                        break;

                    case 'per-access-level':
                        if ($this->accessLevel === AccessLevel::OnlyAdminUsers) {
                            $key = $policyAttempt->admin;
                        } else if ($this->accessLevel === AccessLevel::OnlyLoggedUsers) {
                            $key = $policyAttempt->logged;
                        } else {
                            $key = $policyAttempt->public;
                        }
                        break;
                }

                if ($schema->hasAccessPolicy($key)) {
                    $policy = $policyAttempt;
                    break;
                }
            }
        }

        if (!$policy) $policy = $this->targetAccessPolicy;

        $accessPolicy = '';

        switch ($policy->type) {
            case 'simple':
                $accessPolicy = $policy->public;
                break;

            case 'per-access-level':
                if ($this->accessLevel === AccessLevel::OnlyAdminUsers) {
                    $accessPolicy = $policy->admin;
                } else if ($this->accessLevel === AccessLevel::OnlyLoggedUsers) {
                    $accessPolicy = $policy->logged;
                } else {
                    $accessPolicy = $policy->public;
                }
                break;
        }

        if ($this->targetWebItem) {
            if ($this->accessLevel === AccessLevel::OnlyAdminUsers) {
                if (!in_array($webItemAction, $this->targetWebItem->getEnabledAdminActions())) {
                    if ($this->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::NotEnoughPerms, $this->httpEventHandlers, []);
                    return Response::badRequest();
                }

                if (!$accessPolicy) {
                    $defaultAccessPolicy = $this->targetWebItem->getAdminActionAccessPolicy($webItemAction);
                    if ($defaultAccessPolicy) $accessPolicy = $defaultAccessPolicy;
                }
            }
            else {
                if (!in_array($webItemAction, $this->targetWebItem->getEnabledAppActions())) {
                    if ($this->httpEventHandlers) HttpEventHandler::triggerEvent(HttpEvent::NotEnoughPerms, $this->httpEventHandlers, []);
                    return Response::badRequest();
                }

                if (!$accessPolicy) {
                    $defaultAccessPolicy = $this->targetWebItem->getAppActionAccessPolicy($webItemAction);
                    if ($defaultAccessPolicy) $accessPolicy = $defaultAccessPolicy;
                }
            }
        }

        return $accessPolicy;
    }

    public static function getCurrent(): static|null
    {
        return Router::getRequest();
    }
}