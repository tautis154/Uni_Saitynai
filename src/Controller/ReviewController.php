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

        if (!$doctor) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $form = $this->buildForm(ReviewType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $review = $form->getData();

        try {
            $review->setDoctor($doctor);
            $this->getDoctrine()->getManager()->persist($review);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($review, Response::HTTP_CREATED);
    }

    public function listAction(Request $request): Response
    {
        $doctorId = $request->get('doctor_id');


        $reviews = $this->getDoctrine()->getRepository(Review::class)->findBy(['doctor' => $doctorId]);

        if (!$reviews) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($reviews);
    }

    public function findAction(Request $request)
    {
        $doctorId = $request->get('doctor_id');

        $reviewId = $request->get('review_id');

        $review = $this->getDoctrine()->getRepository(Review::class)->findOneBy([
            'id' => $reviewId,
            'doctor' => $doctorId
        ]);

        if (!$review) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($review);
    }

    public function deleteAction(Request $request): Response
    {
        $doctorId = $request->get('doctor_id');
        $reviewId = $request->get('review_id');

        $review = $this->getDoctrine()->getRepository(Review::class)->findOneBy([
            'id' => $reviewId,
            'doctor' => $doctorId
        ]);

        if (!$review) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $this->getDoctrine()->getManager()->remove($review);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed', Response::HTTP_NO_CONTENT);
    }

    public function editAction(Request $request)
    {
        $reviewId = $request->get('review_id');

        $doctorId = $request->get('doctor_id');


        $review = $this->getDoctrine()->getRepository(Review::class)->findOneBy([
            'id' => $reviewId,
            'doctor' => $doctorId
        ]);

        if (!$review) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $form = $this->buildForm(ReviewType::class, $review, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

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

        return $this->respond($review);
    }
}
