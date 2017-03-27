<?php

namespace UserBundle\Controller;

use AppBundle\Manager\BattleManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email;
use UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints\NotBlank;


class ApiRegistrationController extends FOSRestController
{
    /**
     * @Rest\Post("/player/create")
     *
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $validate_result = $this->validateUserCreation($request);

        if($validate_result) {

            $user = new User();

            //encode pw
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $validate_result->get('password'));
            $user->setPassword($password);

            //username
            $user->setUsername($validate_result->get('username'));

            $user->setEmail($validate_result->get('email'));

            //save user
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $player_profile = [
                'name' => $user->getUsername(),
                'attack' => $user->getAttack(),
                'life' => $user->getLife(),
                'x_auth_token' => $user->getApiKey()
            ];

            $result = [
                'player_profile' => $player_profile,
            ];

            $status_code = Response::HTTP_OK;
        } else {

            $result = [
                'message' => 'Parameters invalid!',
            ];

            $status_code = Response::HTTP_BAD_REQUEST;
        }

        return View::create($result, $status_code);
    }

    /**
     * Validates the user creation
     *
     * @param Request $request
     * @return bool|\Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function validateUserCreation(Request $request)
    {
        $request_values = $request->request;
        $validator = $this->container->get('validator');

        $username_errors = $validator->validate($request_values->get('username'), new NotBlank());
        $email_errors = $validator->validate($request_values->get('email'), [new Email(), new NotBlank()]);
        $password_errors = $validator->validate($request_values->get('password'), new NotBlank());

        //user exists with username or email
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')
            ->findOneBy(array('username' => $request_values->get('username')));

        if($user || count($username_errors) || count($email_errors) || count($password_errors)) {
            return false;
        } 
        
        return $request_values;
    }
}