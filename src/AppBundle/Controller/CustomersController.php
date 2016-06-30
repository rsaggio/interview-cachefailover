<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Dao\CustomersDao;

/**
 * @Route(service="customers_controller")
 */
class CustomersController extends Controller
{

    private $customersDao;
    
    public function __construct(CustomersDao $customersDao) {
        $this->customersDao = $customersDao;    
    }
    /**
     * @Route("/customers/")
     * @Method("GET")
     */
    public function getAction()
    {

        $customers = $this->customersDao->getAll();

        return new JsonResponse($customers);

    }

    /**
     * @Route("/customers/")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {

        $customers = json_decode($request->getContent());

        if (empty($customers)) {
            return new JsonResponse(['status' => 'No donuts for you'], 400);
        }

        foreach ($customers as $customer) {
            $this->customersDao->insert($customer);
        }

        return new JsonResponse(['status' => 'Customers successfully created']);
    }

    /**
     * @Route("/customers/")
     * @Method("DELETE")
     */
    public function deleteAction()
    {
        $this->customersDao->drop();
        return new JsonResponse(['status' => 'Customers successfully deleted']);
    }
}
