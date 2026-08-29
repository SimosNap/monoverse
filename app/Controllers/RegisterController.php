<?php
declare(strict_types=1);

namespace Monoverse\Controllers;

final class RegisterController extends BaseController
{
    public function index(): void
    {
        $this->render('register');
    }
}
