<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Medicine;
use App\Entity\Review;

use App\Form\DoctorType;
use App\Form\ReviewType;
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

class ReviewController extends AbstractApiController
{
    public function newAction(Request $request)
    {
        $doctorId = $request->get('doctor_id');

        $doctor = $this->getDoctrine()->getRepository(Doctor::class)->findOneBy([
            'id' => $doctorId,
        ]);

        $form = $this->buildForm(ReviewType::class);

        $form->handleRequest($request);

        return $this->respond('Veikia');

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $review = $form->getData();



        try {
            $this->getDoctrine()->getManager()->persist($review);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond('Veikia');
    }

    public function listAction(Request $request): Response
    {
        $reviews = $this->getDoctrine()->getRepository(Review::class)->findAll();

        return $this->json($reviews);
    }

    public function findAction(Request $request)
    {
        $reviewId = $request->get('review_id');

        if (!$reviewId) {
            throw new NotFoundHttpException();
        }

        $review = $this->getDoctrine()->getRepository(Review::class)->findOneBy([
            'id' => $reviewId,
        ]);

        return $this->respond('Veikia');
        return $this->json($review);
    }

    public function deleteAction(Request $request): Response
    {
        $reviewId = $request->get('review_id');

        return $this->respond('Successfully removed review');

        if (!$reviewId) {
            throw new NotFoundHttpException();
        }

        $review = $this->getDoctrine()->getRepository(Medicine::class)->findOneBy([
            'id' => $reviewId,
        ]);

        //   $this->getDoctrine()->getManager()->remove($doctor);
        //  $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed review');
    }

    public function editAction(Request $request)
    {
        $reviewId = $request->get('review_id');

        $doctorId = $request->get('doctor_id');

        $doctor = $this->getDoctrine()->getRepository(Doctor::class)->findOneBy([
            'id' => $doctorId,
        ]);

        if (!$reviewId) {
            throw new NotFoundHttpException('Review not found');
        }

        $review = $this->getDoctrine()->getRepository(Review::class)->findOneBy([
            'id' => $reviewId,
        ]);

        $form = $this->buildForm(ReviewType::class, $review, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        return $this->respond('Veikia');

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $review = $form->getData();



        try {
            $this->getDoctrine()->getManager()->persist($review);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($review);
    }
}
