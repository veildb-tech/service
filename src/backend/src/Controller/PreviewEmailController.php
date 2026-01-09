<?php

namespace App\Controller;

use App\Repository\Workspace\WorkspaceRepository;
use App\Service\Workspace\GetSelectedWorkspace;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bundle\SecurityBundle\Security;

class PreviewEmailController extends AbstractController
{

    public function __construct(
        private readonly Environment $twig,
        private Security $security,
        private GetSelectedWorkspace $getSelectedWorkspace,
        private WorkspaceRepository $workspaceRepository
    ) {
    }

    #[Route('/preview/email', name: 'app_preview_email')]
    public function index()
    {
        $user = $this->security->getUser();

        $workspace = $this->workspaceRepository->findOneBy(['code' => 'bridge-digital']);



        $email = (new TemplatedEmail())
            ->htmlTemplate('emails/user/restore.html.twig')
            ->subject(sprintf('%s invited you to join DB Manager service', $user->getFirstname()))
            ->context([
                'workspace' => $workspace,
                'user' => $user,
                'expiredPeriod' => '123123123',
                'url' => 'https://gooogle.com'
            ]);


        $renderer = new BodyRenderer($this->twig);
        $renderer->render($email);

        echo $email->getHtmlBody();
        exit;
    }
}
