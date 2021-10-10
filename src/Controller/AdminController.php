<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Review;

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

class AdminController extends AbstractApiController
{
    public function newAction(Request $request)
    {
       $form = $this->buildForm(AdminType::class);

       $form->handleRequest($request);

       if (!$form->isSubmitted() || !$form->isValid()) {
           return $this->respond($form, Response::HTTP_BAD_REQUEST);
       }

       $admin = $form->getData();

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

     //   $this->getDoctrine()->getManager()->remove($doctor);
      //  $this->getDoctrine()->getManager()->flush();

        return $this->respond('Successfully removed admin');
    }

    public function editAction(Request $request)
    {
        $adminId = $request->get('id');

        if (!$adminId) {
            throw new NotFoundHttpException('Admin not found');
        }

        $admin = $this->getDoctrine()->getRepository(Admin::class)->findOneBy([
            'id' => $adminId,
        ]);

        $form = $this->buildForm(AdminType::class, $admin, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $admin = $form->getData();

        try {
            $this->getDoctrine()->getManager()->persist($admin);
            $this->getDoctrine()->getManager()->flush();
        } catch (\Exception $e) {
            return $this->respond('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respond($admin);
    }
}
