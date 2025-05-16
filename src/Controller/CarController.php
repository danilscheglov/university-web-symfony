<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CarController extends AbstractController
{
    public function __construct(
        private readonly CarRepository $carRepository,
    )
    {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('car/list.html.twig', [
            'cars' => $this->carRepository->findCarsByOwner($this->getUser()),
        ]);
    }

    #[Route('/car/create', name: 'car.create', methods: ['GET'])]
    public function create(SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $errors = $session->getFlashBag()->get('errors') ?? [];
        $errors_messages = [];
        if(count($errors) > 0) {
            $errors = array_shift($errors);
            foreach ($errors as $item) {
                $errors_messages[$item->getPropertyPath()] = $item->getMessage();
            }
        }

        $old_data = $session->getFlashBag()->get('formData') ?? [];

        return $this->render('car/form.html.twig', [
            'colorGroups' => Car::getColorGroups(),
            'errors' => $errors_messages,
            'formData' => count($old_data) > 0 ? $old_data[0] : [],
        ]);
    }

    #[Route('/car', name: 'car.store', methods: ['POST'])]
    public function store(Request $request, ValidatorInterface $validator, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $car = new Car();

        $car->setColor($request->get('color', ''))
            ->setModel($request->get('model', ''))
            ->setYear(intval($request->get('year',  0)))
            ->setBrand($request->get('brand', ''))
            ->setOwner($this->getUser());

        $errors = $validator->validate($car);
        if (count($errors) > 0) {
            $session->getFlashBag()->add('errors', $errors);
            $session->getFlashBag()->add('formData', $request->request->all());

            return $this->redirectToRoute('car.create');
        }

        $this->carRepository->save($car);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/car/{id}', name: 'car.destroy', methods: ['DELETE'])]
    public function destroy(Car $car): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->carRepository->remove($car);

        return $this->redirectToRoute('app_home');
    }
}