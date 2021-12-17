<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 04/11/2020
 * Time: 11:19
 */

namespace App\Controller\Admin;

use App\Entity\File;
use App\Entity\Cliente;
use App\Service\UploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class AdminUploadClienteController extends AbstractController
{
    /**
     * @Route("/admin/files-cliente", name="controller_upload_cliente_files")
     */
    public function uploadFilesAction(Request $request, UploadService $uploadService) {
        $em = $this->getDoctrine()->getManager();
        if(is_null($request->request->get('user_id_photo'))){
            $user = $em->getRepository(Cliente::class)->findOneById($request->get('user_id_photo'));
        }else{
            $user = $em->getRepository(Cliente::class)->findOneById($request->request->get('user_id_photo'));
        }
        $files = $request->files->get("files");
        $descriptions = $request->request->get('description');
        $fileRepository = $em->getRepository(File::class)->findBy(array('image' => $user));
        $uploadService->setParam($fileRepository, 'upload/files/'.$user->getId().'-'.$user->getSlug().'/');
        $file = new File();
        $file->setImage($user);
        $json = $uploadService->request($files, $descriptions, $file, 'controller_upload_cliente_delete',$user);
        //dump($json);die;
        return new JsonResponse($json);
    }

    /**
     * @Route("/admin/files-cliente-carteles", name="controller_upload_carteles_files")
     */
    public function uploadFilesCartelesAction(Request $request, UploadService $uploadService) {
        $em = $this->getDoctrine()->getManager();
        if(is_null($request->request->get('user_id_photo_cartel'))){
            $user = $em->getRepository(Cliente::class)->findOneById($request->get('user_id_photo_cartel'));
        }else{
            $user = $em->getRepository(Cliente::class)->findOneById($request->request->get('user_id_photo_cartel'));
        }
        $files = $request->files->get("files_carteles");
        $descriptions = $request->request->get('description');
        $fileRepository = $em->getRepository(File::class)->findBy(array('carteles' => $user));
        $uploadService->setParam($fileRepository, 'upload/files1/'.$user->getId().'-'.$user->getSlug().'/');
        $file = new File();
        $file->setCarteles($user);
        $json = $uploadService->request($files, $descriptions, $file, 'controller_upload_cartel_delete',$user);
        //dump($json);die;
        return new JsonResponse($json);
    }
    /**
     * @Route("/admin/delete-cliente-file/{id}/{user}", name="controller_upload_cliente_delete")
     */
    public function deleteFilesAction(UploadService $uploadService, $id,$user){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Cliente::class)->findOneById(array('id' => $user));

        $file = $em->find(File::class, $id);
        $json = $uploadService->delete('upload/files/'.$user->getId().'-'.$user->getSlug().'/', $file->getFilename());
        $uploadService->delete('upload/files/'.$user->getId().'-'.$user->getSlug().'/', 'thumb-'.$file->getFilename());
        $em->remove($file);
        $em->flush();
        return new JsonResponse($json);
    }

    /**
     * @Route("/admin/delete-cartel-file/{id}/{user}", name="controller_upload_cartel_delete")
     */
    public function deleteCartelFilesAction(UploadService $uploadService, $id,$user){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Cliente::class)->findOneById(array('id' => $user));

        $file = $em->find(File::class, $id);
        $json = $uploadService->delete('upload/files1/'.$user->getId().'-'.$user->getSlug().'/', $file->getFilename());
        $uploadService->delete('upload/files1/'.$user->getId().'-'.$user->getSlug().'/', 'thumb-'.$file->getFilename());
        $em->remove($file);
        $em->flush();
        return new JsonResponse($json);
    }
}