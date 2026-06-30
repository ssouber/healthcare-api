<?php

namespace Lightit\Authentication\App\Controllers;

use Dedoc\Scramble\Attributes\Endpoint;
use Illuminate\Http\Response;
use Lightit\Authentication\Domain\Actions\LogoutAction;

class LogoutController
{
    #[Endpoint(
        operationId: 'logoutPatient',
        title: 'Logout Patient',
        description: 'Logs patient out.',
    )]
    public function __invoke(LogoutAction $logoutAction): Response
    {
        $logoutAction->execute();

        return response()->noContent();
    }
}
