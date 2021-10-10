<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Medicine;
use App\Entity\Review;

use App\Form\DoctorType;
use App\Form\MedicineType;
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

class MedicineController extends AbstractApiController
{
    public function newAction(Request $request)
    {
        $form = $this->buildForm(MedicineType::class);

        $form->handleRequest($request);

        return $this->respond('Veikia');

        if (!$form->isSubmitted() || !$form->isValid() || $form->isEmpty()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $medicine = $form->getData();



        try {
            $this->getDoctrine()->getManager()->persist($medicine);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond('Veikia');
    }

    public function listAction(Request $request): Response
    {
        $medicines = $this->getDoctrine()->getRepository(Medicine::class)->findAll();

        return $this->json($medicines);
    }

    public function findAction(Request $request)
    {
        $medicineId = $request->get('medicine_id');

        if (!$medicineId) {
            throw new NotFoundHttpException();
        }

        $medicine = $this->getDoctrine()->getRepository(Medicine::class)->findOneBy([
            'id' => $medicineId,
        ]);

        return $this->respond('Veikia');
        return $this->json($medicine);
    }

    public function deleteAction(Request $request): Response
    {
        $medicineId = $request->get('id');

        if (!$medicineId) {
            throw new NotFoundHttpException();
        }

        $medicine = $this->getDoctrine()->getRepository(Medicine::class)->findOneBy([
            'id' => $medicineId,
        ]);

        //   $this->getDoctrine()->getManager()->remove($doctor);
        //  $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed review');
    }

    public function editAction(Request $request)
    {
        $medicineId = $request->get('review_id');

        if (!$medicineId) {
            throw new NotFoundHttpException('Medicine not found');
        }

        $medicine = $this->getDoctrine()->getRepository(Medicine::class)->findOneBy([
            'id' => $medicineId,
        ]);

        $form = $this->buildForm(ReviewType::class, $medicine, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        return $this->respond('Veikia');

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $medicine = $form->getData();



        try {
            $this->getDoctrine()->getManager()->persist($medicine);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($medicine);
    }
}
