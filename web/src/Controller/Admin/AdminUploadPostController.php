<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 04/11/2020
 * Time: 11:19
 */

namespace App\Controller\Admin;

use App\Entity\File;
use App\Entity\Post;
use App\Service\UploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class AdminUploadPostController extends AbstractController
{
    /**
     * @Route("/admin/files-post", name="controller_upload_post_files")
     */
    public function uploadFilesAction(Request $request, UploadService $uploadService) {
        $em = $this->getDoctrine()->getManager();
        if(is_null($request->request->get('user_id_photo'))){
            $user = $em->getRepository(Post::class)->findOneById($request->get('user_id_photo'));
        }else{
            $user = $em->getRepository(Post::class)->findOneById($request->request->get('user_id_photo'));
        }
        $files = $request->files->get("files");
        $descriptions = $request->request->get('description');
        $fileRepository = $em->getRepository(File::class)->findBy(array('post' => $user));
        $uploadService->setParam($fileRepository, 'upload/files/'.$user->getId().'-'.$user->getSlug().'/');
        $file = new File();
        $file->setPost($user);

        $json = $uploadService->request($files, $descriptions, $file,  'controller_upload_post_delete',$user);
        return new JsonResponse($json);
    }

    /**
     * @Route("/admin/text-image-post", name="controller_upload_text_image")
     */
    public function uploadTextImageAction(Request $request) {
        $files = $request->files->get("upload");
        if ($files instanceof UploadedFile){
            $files->move('upload/files/text',$files->getClientOriginalName());
            $json = array(
                'uploaded' => 1,
                'fileName' => $files->getClientOriginalName(),
                'url' => $this->container->get('request_stack')->getCurrentRequest()->getBasePath().'/upload/files/text/'.$files->getClientOriginalName(),
                );
        }else{
            $json = array(
                'uploaded' => 0,
                'error' => array (
                    'message' => "Archivo no soportado"
                )
            );
        }
        return new JsonResponse($json);
    }

    /**
     * @Route("/admin/delete-post-file/{id}/{user}", name="controller_upload_post_delete")
     */
    public function deleteFilesAction(UploadService $uploadService, $id,$user){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Post::class)->findOneById(array('id' => $user));

        $file = $em->find(File::class, $id);
        $json = $uploadService->delete('upload/files/'.$user->getId().'-'.$user->getSlug().'/', $file->getFilename());
        $uploadService->delete('upload/files/'.$user->getId().'-'.$user->getSlug().'/', 'thumb-'.$file->getFilename());
        $em->remove($file);
        $em->flush();
        return new JsonResponse($json);
    }
}