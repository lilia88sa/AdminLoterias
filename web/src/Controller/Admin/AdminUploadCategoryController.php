<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 04/11/2020
 * Time: 11:19
 */

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\File;
use App\Service\UploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class AdminUploadCategoryController extends AbstractController
{
    /**
     * @Route("/admin/files-category", name="controller_upload_category_files")
     */
    public function uploadFilesAction(Request $request, UploadService $uploadService) {
        $em = $this->getDoctrine()->getManager();
        if(is_null($request->request->get('user_id_photo'))){
            $user = $em->getRepository(Category::class)->findOneById($request->get('user_id_photo'));
        }else{
            $user = $em->getRepository(Category::class)->findOneById($request->request->get('user_id_photo'));
        }
        $files = $request->files->get("files");
        $descriptions = $request->request->get('description');
        $fileRepository = $em->getRepository(File::class)->findBy(array('category' => $user));
        $uploadService->setParam($fileRepository, 'upload/files/'.$user->getId().'-'.$user->getSlug().'/');
        $file = new File();
        $file->setCategory($user);
        $json = $uploadService->request($files, $descriptions, $file, 'controller_upload_category_delete',$user);
        //dump($json);die;
        return new JsonResponse($json);
    }

    /**
     * @Route("/admin/delete-category-files/{id}/{user}", name="controller_upload_category_delete")
     */
    public function deleteFilesAction(UploadService $uploadService, $id,$user){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Category::class)->findOneById(array('id' => $user));

        $file = $em->find(File::class, $id);
        $json = $uploadService->delete('upload/files/'.$user->getId().'-'.$user->getSlug().'/', $file->getFilename());
        $uploadService->delete('upload/files/'.$user->getId().'-'.$user->getSlug().'/', 'thumb-'.$file->getFilename());
        $em->remove($file);
        $em->flush();
        return new JsonResponse($json);
    }
}