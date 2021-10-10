<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Review;

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

       if (!$form->isSubmitted() || !$form->isValid()) {
           return $this->respond($form, Response::HTTP_BAD_REQUEST);
       }

       $doctor = $form->getData();

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

        return $this->json($doctor);
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

     //   $this->getDoctrine()->getManager()->remove($doctor);
      //  $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed doctor');
    }

    public function editAction(Request $request)
    {
        $doctorId = $request->get('id');

        if (!$doctorId) {
            throw new NotFoundHttpException('Doctor not found');
        }

        $doctor = $this->getDoctrine()->getRepository(Doctor::class)->findOneBy([
            'id' => $doctorId,
        ]);

        $form = $this->buildForm(DoctorType::class, $doctor, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $doctor = $form->getData();

        try {
            $this->getDoctrine()->getManager()->persist($doctor);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($doctor);
    }
}
