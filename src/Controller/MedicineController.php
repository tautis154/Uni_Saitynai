<?php

namespace App\Controller;

use App\Entity\Medicine;

use App\Form\MedicineType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicineController extends AbstractApiController
{
    public function newAction(Request $request)
    {
        $form = $this->buildForm(MedicineType::class);

        $form->handleRequest($request);

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

        return $this->respond($medicine, Response::HTTP_CREATED);
    }

    public function listAction(Request $request): Response
    {
        $medicines = $this->getDoctrine()->getRepository(Medicine::class)->findAll();

        if (!$medicines) {
            //Jei erroras padaryt, kad grazintu jsona
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($medicines);
    }

    public function findAction(Request $request)
    {
        $medicineId = $request->get('id');

        $medicine = $this->getDoctrine()->getRepository(Medicine::class)->findOneBy([
            'id' => $medicineId,
        ]);

        if (!$medicine) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        return $this->json($medicine);
    }

    public function deleteAction(Request $request): Response
    {
        $medicineId = $request->get('id');

        $medicine = $this->getDoctrine()->getRepository(Medicine::class)->findOneBy([
            'id' => $medicineId,
        ]);

        if (!$medicine) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $this->getDoctrine()->getManager()->remove($medicine);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed', Response::HTTP_NO_CONTENT);
    }

    public function editAction(Request $request)
    {
        $medicineId = $request->get('id');


        $medicine = $this->getDoctrine()->getRepository(Medicine::class)->findOneBy([
            'id' => $medicineId
        ]);

        if (!$medicine) {
            return $this->respond('404 Not Found', Response::HTTP_NOT_FOUND);
        }

        $form = $this->buildForm(MedicineType::class, $medicine, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

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

        return $this->respond($medicine);
    }
}
