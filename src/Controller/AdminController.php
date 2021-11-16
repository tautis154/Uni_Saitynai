<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\User;
use App\Form\AdminType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminController extends AbstractApiController
{
    public function newAction(Request $request)
    {
       $form = $this->buildForm(AdminType::class);

       $form->handleRequest($request);

       if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
           return $this->respond($form, Response::HTTP_BAD_REQUEST);
       }

       $admin = $form->getData();

       $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'id' => $admin->getFkUser()->getId(),
       ]);

        $doctor = $this->getDoctrine()->getRepository(Doctor::class)->findOneBy([
            'fk_user' => $admin->getFkUser(),
        ]);

        if ($doctor)
        {
            return $this->respond('400 Bad Request (Cannnot set user_id to admin because doctor already has same user_id)', Response::HTTP_BAD_REQUEST);
        }

       $user->setRoles(["ROLE_ADMIN"]);

        try {
            $this->getDoctrine()->getManager()->persist($admin);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

       return $this->respond($admin);
    }

    public function listAction(Request $request): Response
    {
        $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();

        if (!$admins) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($admins);
    }

    public function findAction(Request $request)
    {
        $adminId = $request->get('id');

        if (!$adminId) {
            throw new NotFoundHttpException();
        }

        $admin = $this->getDoctrine()->getRepository(Admin::class)->findOneBy([
            'id' => $adminId,
        ]);

        if (!$admin) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($admin);
    }

    public function deleteAction(Request $request): Response
    {
        $adminId = $request->get('id');

        if (!$adminId) {
            throw new NotFoundHttpException();
        }

        $admin = $this->getDoctrine()->getRepository(Admin::class)->findOneBy([
            'id' => $adminId,
        ]);

        if (!$admin) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $this->getDoctrine()->getManager()->remove($admin);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed admin');
    }

    public function editAction(Request $request)
    {
        $adminId = $request->get('id');

        if (!$adminId) {
            throw new NotFoundHttpException('Admin not found');
        }

        $originalAdmin = $this->getDoctrine()->getRepository(Admin::class)->findOneBy([
            'id' => $adminId,
        ]);


        if (!$originalAdmin) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $originalAdminUserId = $originalAdmin->getFkUser()->getId();

        $form = $this->buildForm(AdminType::class, $originalAdmin, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $admin = $form->getData();

        if ($admin->getFkUser()->getId() === $originalAdminUserId) {
            try {
                $this->getDoctrine()->getManager()->persist($admin);
                $this->getDoctrine()->getManager()->flush();
            } catch (\Exception $e) {
                return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->respond($admin);
        } else {
            return $this->respond('400 Bad Request (Cannnot set new user_id to existing doctor)', Response::HTTP_BAD_REQUEST);
        }
    }
}
