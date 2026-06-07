<?php

namespace App\EventListener;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
readonly class LoginSuccessListener
{
    public function __construct(
        private RouterInterface $router
    ) {}

    /**
     * Redirige l'utilisateur vers la bonne espace après succès de l'authentification.
     *
     * @param LoginSuccessEvent $event
     * @return void
     */
    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        $roleRouteMap = [
            'ROLE_PERSONNEL'  => 'app_espace_personnel_index',
            'ROLE_ENSEIGNANT' => 'app_espace_enseignant_index',
            'ROLE_ENTREPRISE' => 'app_espace_entreprise_index',
            'ROLE_ETUDIANT'   => 'app_espace_etudiant_index',
        ];

        if (!$user instanceof UserInterface)
            return;

        foreach ($roleRouteMap as $role => $route) {
            if (in_array($role, $user->getRoles(), true)) {
                $event->setResponse(new RedirectResponse($this->router->generate($route)));

                return;
            }
        }
    }
}
