<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\RecipeStep;
use App\Repository\RecipeIngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\RecipeStepRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\JwtUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;

class RecipeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private JwtUserService $jwtUserService,
        private RecipeRepository $recipeRepository,
        private RecipeStepRepository $recipeStepRepository,
        private RecipeIngredientRepository $recipeIngredientRepository,

    ) {
    }

    #[Route('/api/recipies', name: 'recipies', methods: ['GET'])]
    public function recipies(): Response
    {
        $recipies = $this->recipeRepository->findAll();

        // Convert recipes to an array
        $data = [];

        foreach ($recipies as $recipe) {
            $recipeData = [
                'id' => $recipe->getId(),
                'title' => $recipe->getTitle(),
                'description' => $recipe->getDescription(),
                'portions' => $recipe->getPortions(),
                'time_prepa' => $recipe->getTimePrepa(),
                'time_cooking' => $recipe->getTimeCooking(),
            ];

            // Add steps
            $steps = [];

            foreach ($recipe->getRecipeSteps() as $step) {
                $steps[] = [
                    'name' => $step->getName(),
                ];
            }
            $recipeData['steps'] = $steps;

            // Add ingredients
            $ingredients = [];

            foreach ($recipe->getRecipeIngredients() as $ingredient) {
                $ingredients[] = [
                    'name' => $ingredient->getName(),
                ];
            }
            $recipeData['ingredients'] = $ingredients;

            // Add image
            $imageFileName = $recipe->getImage();
            $imageUrl = '/assets/images/' . $imageFileName;
            $recipeData['image'] = $imageUrl;
            $data[] = $recipeData;
        }

        // Add user
        $user = $recipe->getUser();

        if ($user) {
            $userData = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'pseudo' => $user->getPseudo(),
            ];
            $recipeData['user'] = $userData;
        }

        return $this->json($data);
    }

    #[Route('/api/recipe/{id}', name: 'app_recipe_get', methods: ['GET'])]
    public function getRecipe($id): Response
    {
        // Retrieve the recipe by its ID
        $recipe = $this->recipeRepository->find($id);

        if (!$recipe) {
            return $this->json(['error' => 'Recipe not found'], Response::HTTP_NOT_FOUND);
        }

        // image
        $imageFileName = $recipe->getImage();
        $imageUrl = '/assets/images/' . $imageFileName;

        // Retrieve the user
        $user = $recipe->getUser();

        // Convert the recipe object to an array to return as JSON
        $recipeData = [
            'id'           => $recipe->getId(),
            'title'        => $recipe->getTitle(),
            'description'  => $recipe->getDescription(),
            'portions'     => $recipe->getPortions(),
            'time_prepa'   => $recipe->getTimePrepa(),
            'time_cooking' => $recipe->getTimeCooking(),
            'image'        => $imageUrl,
            'steps'        => [],
            'ingredients'  => [],
            'username'     => $user->getEmail(),
        ];


        foreach ($recipe->getRecipeSteps() as $step) {
            $recipeData['steps'][] = [
                'id' => $step->getId(),
                'name' => $step->getName()
            ];
        }

        foreach ($recipe->getRecipeIngredients() as $ingredient) {
            $recipeData['ingredients'][] = [
                'id' => $ingredient->getId(),
                'name' => $ingredient->getName()
            ];
        }

        return $this->json($recipeData);
    }

    #[Route('/api/recipe/new', name: 'app_recipe_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        // handle user
        try {
            $username = $this->jwtUserService->getUserFromToken($request);
            $user = $this->jwtUserService->getUser($username);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // get data
        $data = json_decode($request->getContent(), true);

        $recipe = new Recipe();
        $recipe->setUser($user);
        $recipe->setTitle($data['title'] ?? '');
        $recipe->setDescription($data['description'] ?? '');
        $recipe->setPortions($data['portions'] ?? '');
        $recipe->setTimePrepa($data['time_prepa'] ?? '');
        $recipe->setTimeCooking($data['time_cooking'] ?? '');

        // Handle steps
        if (isset($data['steps'])) {
            foreach ($data['steps'] as $stepData) {
                $step = new RecipeStep();
                $step->setName($stepData['name'] ?? '');
                $recipe->addRecipeStep($step);
                $this->em->persist($step);
            }
        }

        // Handle ingredients
        if (isset($data['ingredients'])) {
            foreach ($data['ingredients'] as $ingredientData) {
                $ingredient = new RecipeIngredient();
                $ingredient->setName($ingredientData['name'] ?? '');
                $recipe->addRecipeIngredient($ingredient);
                $this->em->persist($ingredient);
            }
        }

        // handle default image
        $imagePath = 'plat.jpg';
        $recipe->setImage($imagePath);

        $this->em->persist($recipe);
        $this->em->flush();

        return $this->json($recipe->getId());
    }

    #[Route('/api/recipe/new/image/{id}', name: 'app_recipe_new_img', methods: ['POST'])]
    public function newImage(
        Request $request,
        Recipe $id
    ): Response {
        // handle user
        try {
            $username = $this->jwtUserService->getUserFromToken($request);
            $user = $this->jwtUserService->getUser($username);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Get Recipe
        $recipe = $this->recipeRepository->find($id);
        if (!$recipe) {
            return new JsonResponse(['error' => 'Recipe not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if an image file is uploaded
        if ($request->files->has('image')) {
            /** @var UploadedFile $imageFile */
            $imageFile = $request->files->get('image');

            // Remove old image file if exists
            $oldFilename = $recipe->getImage();
            if ($oldFilename) {
                $filesystem = new Filesystem();
                $oldFilePath = $this->getParameter('images_directory') . '/' . $oldFilename;
                if ($filesystem->exists($oldFilePath)) {
                    $filesystem->remove($oldFilePath);
                }
            }

            // Generate unique filename
            $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();

            // Move the file to the desired directory
            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Handle file upload error, if any
                return $this->json(['error' => 'Error uploading image'], Response::HTTP_BAD_REQUEST);
            }

            // Set the filename in the recipe entity
            $recipe->setImage($newFilename);

            $this->em->persist($recipe);
            $this->em->flush();

            return $this->json('ok');
        }

        return $this->json(['error' => 'No image uploaded'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/recipe/edit/{id}', name: 'app_recipe_update', methods: ['PUT'])]
    public function updateRecipe(int $id, Request $request): Response
    {
        // handle user
        try {
            $username = $this->jwtUserService->getUserFromToken($request);
            $user = $this->jwtUserService->getUser($username);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $recipe = $this->recipeRepository->find($id);

        if (!$recipe) {
            return $this->json(['error' => 'Recipe not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $recipe->setTitle($data['title'] ?? $recipe->getTitle());
        $recipe->setDescription($data['description'] ?? $recipe->getDescription());
        $recipe->setPortions($data['portions'] ?? $recipe->getPortions());
        $recipe->setTimePrepa($data['time_prepa'] ?? $recipe->getTimePrepa());
        $recipe->setTimeCooking($data['time_cooking'] ?? $recipe->getTimeCooking());

        // Remove existing steps not present in the request
        foreach ($recipe->getRecipeSteps() as $step) {
            if (!in_array($step->getId(), array_column($data['steps'] ?? [], 'id'))) {
                $this->em->remove($step);
            }
        }

        // Remove existing ingredients not present in the request
        foreach ($recipe->getRecipeIngredients() as $ingredient) {
            if (!in_array($ingredient->getId(), array_column($data['ingredients'] ?? [], 'id'))) {
                $this->em->remove($ingredient);
            }
        }

        // Handle steps
        if (isset($data['steps'])) {
            foreach ($data['steps'] as $stepData) {
                $step = $this->recipeStepRepository->find($stepData['id']);
                if (!$step) {
                    $step = new RecipeStep();
                    $step->setRecipe($recipe);
                }
                $step->setName($stepData['name']);
                $this->em->persist($step);
            }
        }

        // Handle ingredients
        if (isset($data['ingredients'])) {
            foreach ($data['ingredients'] as $ingredientData) {
                $ingredient = $this->recipeIngredientRepository->find($ingredientData['id']);
                if (!$ingredient) {
                    $ingredient = new RecipeIngredient();
                    $ingredient->setRecipe($recipe);
                }
                $ingredient->setName($ingredientData['name']);
                $this->em->persist($ingredient);
            }
        }

        // handle default image
        $imagePath = 'plat.jpg';
        $recipe->setImage($imagePath);

        $this->em->persist($recipe);
        $this->em->flush();

        return $this->json(['success' => true, 'id' => $recipe->getId()]);
    }
}
