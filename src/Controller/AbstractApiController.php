<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractApiController extends AbstractFOSRestController
{
   protected function buildForm(string $type, $data = null, $options = []): FormInterface
   {
       $options = array_merge($options, [
           'csrf_protection' => false
       ]);

       return $this->container->get('form.factory')->createNamed('', $type, $data, $options);
   }

   protected function respond($data, int $statusCode = Response::HTTP_OK)
   {
       return $this->handleView($this->view($data, $statusCode));
   }

}
