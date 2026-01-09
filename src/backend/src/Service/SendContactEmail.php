<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;

class SendContactEmail
{


    public function __construct(private Security $security)
    {

    }

    public function send()
    {
        $user = $this->security->getUser();
        $token = $this->security->getToken();


        $qwe = 123;

    }
}
