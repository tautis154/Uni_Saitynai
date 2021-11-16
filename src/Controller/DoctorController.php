<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Review;

use App\Entity\User;
use App\Form\AdminType;
use App\Form\DoctorType;
use App\Repository\ReviewRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class DoctorController extends AbstractApiController
{
    public function newAction(Request $request)
    {
        $form = $this->buildForm(DoctorType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $doctor = $form->getData();

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'id' => $doctor->getFkUser()->getId(),
        ]);

        $admin = $this->getDoctrine()->getRepository(Admin::class)->findOneBy([
            'fk_user' => $doctor->getFkUser(),
        ]);

        if ($admin)
        {
            return $this->respond('400 Bad Request (Cannnot set user_id to doctor because admin already has same user_id)', Response::HTTP_BAD_REQUEST);
        }

        //Padaryt, kad jei prie daktaro jau yra tai turi neleisti to pacio userId lecrating
        $user->setRoles(["ROLE_DOCTOR"]);

        try {
            $this->getDoctrine()->getManager()->persist($doctor);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($doctor);
    }

    public function listAction(Request $request): Response
    {
       $doctors = $this->getDoctrine()->getRepository(Doctor::class)->findAll();

       if (!$doctors) {
           return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
       }

       return $this->json($doctors);
    }

    public function findAction(Request $request)
    {
        $doctorId = $request->get('id');

        if (!$doctorId) {
            throw new NotFoundHttpException();
        }

        $doctor = $this->getDoctrine()->getRepository(Doctor::class)->findOneBy([
            'id' => $doctorId,
        ]);

        if (!$doctor) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->getUser();

        if ($doctor->getFkUser() === $currentUser || in_array("ROLE_ADMIN", $currentUser->getRoles(), true)) {
            return $this->json($doctor);
        } else {
            return $this->respond('403 Access Denied', Response::HTTP_FORBIDDEN);
        }
    }

    public function deleteAction(Request $request): Response
    {
        $doctorId = $request->get('id');

        if (!$doctorId) {
            throw new NotFoundHttpException();
        }

        $doctor = $this->getDoctrine()->getRepository(Doctor::class)->findOneBy([
            'id' => $doctorId,
        ]);

        if (!$doctor) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->getUser();

        if ($doctor->getFkUser() === $currentUser || in_array("ROLE_ADMIN", $currentUser->getRoles(), true)) {
            $this->getDoctrine()->getManager()->remove($doctor);
            $this->getDoctrine()->getManager()->flush();

            return $this->respond('Successfully removed doctor');
        } else {
            return $this->respond('403 Access Denied', Response::HTTP_FORBIDDEN);
        }
    }

    public function editAction(Request $request)
    {
        $doctorId = $request->get('id');

        if (!$doctorId) {
            throw new NotFoundHttpException('Doctor not found');
        }

        $originalDoctor = $this->getDoctrine()->getRepository(Doctor::class)->findOneBy([
            'id' => $doctorId,
        ]);


        if (!$originalDoctor) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $originalDoctorUserId = $originalDoctor->getFkUser()->getId();

        $form = $this->buildForm(DoctorType::class, $originalDoctor, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $doctor = $form->getData();

        $currentUser = $this->getUser();

        if ($doctor->getFkUser()->getId() === $originalDoctorUserId) {
            if ($doctor->getFkUser() === $currentUser || in_array("ROLE_ADMIN", $currentUser->getRoles(), true)) {
                try {
                    $this->getDoctrine()->getManager()->persist($doctor);
                    $this->getDoctrine()->getManager()->flush();
                } catch (\Exception $e) {
                    return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                return $this->respond($doctor);
            } else {
                return $this->respond('403 Access Denied', Response::HTTP_FORBIDDEN);
            }
        } else {
            return $this->respond('400 Bad Request (Cannnot set new user_id to existing doctor)', Response::HTTP_BAD_REQUEST);
        }

    }
}
