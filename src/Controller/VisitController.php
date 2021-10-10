<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Review;

use App\Entity\Visit;
use App\Form\AdminType;
use App\Form\DoctorType;
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

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $visit = $form->getData();

        return $this->respond('veikia');
        try {
            $this->getDoctrine()->getManager()->persist($visit);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($visit);
    }

    public function listAction(Request $request): Response
    {
        $visits = $this->getDoctrine()->getRepository(Admin::class)->findAll();

        return $this->json('works');
        return $this->json($visits);
    }

    public function findAction(Request $request)
    {
        $visitId = $request->get('id');

        if (!$visitId) {
            throw new NotFoundHttpException();
        }

        return $this->json('works');

        $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy([
            'id' => $visitId,
        ]);

        return $this->json($visit);
    }

    public function deleteAction(Request $request): Response
    {
        $visit = $request->get('id');

        if (!$visit) {
            throw new NotFoundHttpException();
        }

        return $this->json('works');

        $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy([
            'id' => $visit,
        ]);

        //   $this->getDoctrine()->getManager()->remove($doctor);
        //  $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed visit');
    }

    public function editAction(Request $request)
    {
        $visitId = $request->get('id');

        if (!$visitId) {
            throw new NotFoundHttpException('Doctor not found');
        }

        return $this->json('works');
        $visit = $this->getDoctrine()->getRepository(Visit::class)->findOneBy([
            'id' => $visitId,
        ]);

        $form = $this->buildForm(AdminType::class, $visit, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
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
