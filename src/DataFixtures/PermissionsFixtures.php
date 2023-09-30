<?php

declare(strict_types=1);

namespace Groshy\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Groshy\Entity\Permission;
use Groshy\Entity\Role;

class PermissionsFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function load(ObjectManager $manager)
    {
		foreach (['GALLERY_', 'IMAGE_'] as $prefix) {

		}

	    $galleryCreatePermission   = $this->findOrCreate('GALLERY_CREATE');
	    $galleryReadPermission   = $this->findOrCreate('GALLERY_READ');
	    $galleryUpdatePermission   = $this->findOrCreate('GALLERY_UPDATE');
	    $galleryDeletePermission   = $this->findOrCreate('GALLERY_DELETE');
	    $galleryListPermission   = $this->findOrCreate('GALLERY_LIST');

	    $imageCreatePermission   = $this->findOrCreate('IMAGE_CREATE');
	    $imageReadPermission   = $this->findOrCreate('IMAGE_READ');
	    $imageUpdatePermission   = $this->findOrCreate('IMAGE_UPDATE');
	    $imageDeletePermission   = $this->findOrCreate('IMAGE_DELETE');
	    $imageListPermission   = $this->findOrCreate('IMAGE_LIST');

	    /** @var Role $userRole */
	    $userRole = $this->getReference(RolesFixtures::User_ROLE_REFERENCE);
	    $userRole->sync('permissions', new ArrayCollection([
		    $galleryCreatePermission, $galleryReadPermission, $galleryUpdatePermission, $galleryDeletePermission, $galleryListPermission,
		    $imageCreatePermission, $imageReadPermission, $imageUpdatePermission, $imageDeletePermission, $imageListPermission
	    ]));

	    $manageUsersPermission   = $this->findOrCreate('manageUsers');
        $viewUsersPermission     = $this->findOrCreate('viewUsers');
        $manageStoriesPermission = $this->findOrCreate('manageStories');
        $manageCategoriesPermission = $this->findOrCreate('manageCategories');
        $canBeAuthor = $this->findOrCreate('canBeAuthor');
        $unblockPost = $this->findOrCreate('unblockPost');

        /** @var Role $superAdministratorRole */
        $superAdministratorRole = $this->getReference(RolesFixtures::SuperAdministrator_ROLE_REFERENCE);
        $superAdministratorRole->sync('permissions', new ArrayCollection([
            $manageUsersPermission, $viewUsersPermission, $manageStoriesPermission, $manageCategoriesPermission,
            $canBeAuthor, $unblockPost
        ]));

        $this->entityManager->persist($superAdministratorRole);
        $this->entityManager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RolesFixtures::class
        ];
    }

    private function findOrCreate(string $permissionName): Permission
    {
        $permission = $this->entityManager
            ->getRepository(Permission::class)
            ->findOneBy(['name' => $permissionName]);

        if (!$permission) {
            $permission = (new Permission())->setName($permissionName);
            $this->entityManager->persist($permission);
            $this->entityManager->flush();
        }

        return $permission;
    }

}