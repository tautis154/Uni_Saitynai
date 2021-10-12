<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Medicine;
use App\Entity\Review;

use App\Entity\Visit;
use App\Form\DoctorType;
use App\Form\MedicineType;
use App\Form\ReviewType;
use App\Form\VisitType;
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

class VisitController extends AbstractApiController
{
    public function newAction(Request $request)
    {
        $form = $this->buildForm(VisitType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $visit = $form->getData();

        try {
            $this->getDoctrine()->getManager()->persist($visit);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($visit, Response::HTTP_CREATED);
    }

    public function listAction(Request $request): Response
    {
        $visits = $this->getDoctrine()->getRepository(Visit::class)->findAll();

        if (!$visits) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($visits);
    }

    public function findAction(Request $request)
    {
        $visitId = $request->get('id');

        $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy([
            'id' => $visitId,
        ]);

        if (!$visit) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($visit);
    }

    public function deleteAction(Request $request): Response
    {
        $visitId = $request->get('id');

        $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy([
            'id' => $visitId,
        ]);

        if (!$visit) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $this->getDoctrine()->getManager()->remove($visit);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed', Response::HTTP_NO_CONTENT);
    }

    public function editAction(Request $request)
    {
        $visitId = $request->get('id');

        $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy([
            'id' => $visitId
        ]);

        if (!$visit) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $form = $this->buildForm(VisitType::class, $visit, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $visit = $form->getData();

        try {
            $this->getDoctrine()->getManager()->persist($visit);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($visit);
    }
}
