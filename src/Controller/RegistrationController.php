<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractApiController
{
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $form = $this->buildForm(RegistrationFormType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $user = $form->getData();


        try {
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($user);
    }

    public function listAction(Request $request): Response
    {
        //Ok tai su daktaru padaryt taip, kad gettint pirma useri ir
        // jei userio id lygu id daktaroUserio id is requesto tai tada vaziuojam ir viskas gerai veikia
        //return $this->json($this->getUser());
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        if (!$users) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($users);
    }

    public function findAction(Request $request)
    {
        $userId = $request->get('id');

        if (!$userId) {
            throw new NotFoundHttpException();
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'id' => $userId,
        ]);

        if (!$user) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->getUser();

        if ($user === $currentUser || in_array("ROLE_ADMIN", $currentUser->getRoles(), true)) {
            return $this->json($user);
        } else {
            return $this->respond('403 Access Denied', Response::HTTP_FORBIDDEN);
        }
    }

    public function deleteAction(Request $request): Response
    {
        $userId = $request->get('id');

        if (!$userId) {
            throw new NotFoundHttpException();
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'id' => $userId,
        ]);

        if (!$user) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->getUser();

        if ($user === $currentUser || in_array("ROLE_ADMIN", $currentUser->getRoles(), true)) {
            $this->getDoctrine()->getManager()->remove($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->respond('Successfully removed user');
        } else {
            return $this->respond('403 Access Denied', Response::HTTP_FORBIDDEN);
        }
    }

    public function editAction(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $userId = $request->get('id');

        if (!$userId) {
            throw new NotFoundHttpException('User not found');
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'id' => $userId,
        ]);

        if (!$user) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $form = $this->buildForm(RegistrationFormType::class, $user, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $user = $form->getData();

        $currentUser = $this->getUser();

        if ($user === $currentUser || in_array("ROLE_ADMIN", $currentUser->getRoles(), true)) {
            try {
                $user->setPassword(
                    $userPasswordHasherInterface->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->respond($user);
        } else {
            return $this->respond('403 Access Denied', Response::HTTP_FORBIDDEN);
        }
    }
}
