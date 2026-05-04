<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    // send email
    $app->post('/send', function ($request, $response) {
        $params = (array)$request->getParsedBody();

        // 1. Récupérer Twig depuis le container
        $twig = $this->get('view');

// 2. Générer le HTML à partir du template Twig
        $htmlBody = $twig->fetch('emails/notification.html.twig', [
            'nom' => $params['nom'],
            'prenom' => $params['prenom'],
            'sujet' => $params['sujet'],
            'message' => $params['message']
        ]);

        $mailer = $this->get('mailer');
        $mailer->isHTML(true);
        $mailer->setFrom('infos@raftras.com');

        $mailer->addAddress('infos@raftras.com');
        $mailer->Subject = "Contact - demande de renseignement";
        $mailer->Body = $htmlBody;


        $mailer->send();

        $response->getBody()->write("Email Sent");
        return $response;
    });
    $app->post('/import-request', function ($request, $response) {
        $params = (array)$request->getParsedBody();

        // 1. Récupérer Twig depuis le container
        $twig = $this->get('view');


        $htmlBody = $twig->fetch('emails/import_request.html.twig', [
            'nom_entreprise' => $params['nom_entreprise'],
            'email' => $params['email'],
            'telephone' => $params['telephone'],
            'localisation_entreprise' => $params['localisation_entreprise'],
            'pays_importation' => $params['pays_importation'],
            'pays_destination' => $params['pays_destination'],
            'description_commande' => $params['description_commande']
        ]);

        $mailer = $this->get('mailer');
        $mailer->isHTML(true);
        $mailer->setFrom('infos@raftras.com');

        $mailer->addAddress('infos@raftras.com');
        $mailer->Subject = "Nouvelle demande d'importation";
        $mailer->Body = $htmlBody;


        $mailer->send();

        $response->getBody()->write("Email Sent");
        return $response;
    });
    $app->post('/export-request', function ($request, $response) {
        $params = (array)$request->getParsedBody();

        // 1. Récupérer Twig depuis le container
        $twig = $this->get('view');


        $htmlBody = $twig->fetch('emails/export_request.html.twig', [
            'nom_entreprise' => $params['nom_entreprise'],
            'email' => $params['email'],
            'telephone' => $params['telephone'],
            'localisation_entreprise' => $params['localisation_entreprise'],
            'pays_exportation' => $params['pays_exportation'],
            'pays_destination' => $params['pays_destination'],
            'description_commande' => $params['description_commande']
        ]);

        $mailer = $this->get('mailer');
        $mailer->isHTML(true);
        $mailer->setFrom('infos@raftras.com');

        $mailer->addAddress('infos@raftras.com');
        $mailer->Subject = "Nouvelle demande d'exportation";
        $mailer->Body = $htmlBody;


        $mailer->send();

        $response->getBody()->write("Email Sent");
        return $response;
    });


};
