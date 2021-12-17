<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 04/11/2020
 * Time: 11:46
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UploadService
{
    protected $repository;
    protected $path;
    protected $container;
    protected $em;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container) {
        $this->em = $entityManager;
        $this->container = $container;
    }

    public function setParam($repository, $path) {
        $this->path = $path;
        $this->repository = $repository;
    }

    public function request($files, $descriptions, $object, $container_delete,$user) {
        if ($files) {
            return $this->save($files, $descriptions, $object, $container_delete,$user);
        } else {
            return $this->getFiles($container_delete,$user);
        }
    }

    public function save($files, $descriptions, $object, $container_delete,$user) {

        $response['files'] = array();
        if (is_array($files)) {
            foreach ($files as $index => $file) {
                $filename = $this->processImage($file, $this->path);
                $real_file =$file; /// add
                $file = $object;
                $this->setObject($file, $filename, $descriptions[$index]);
                $this->em->persist($file);
                $this->em->flush();

                $photo = $this->ImagesThumnb($real_file,$file->getFilename(),$this->path,300,194);
                $f = array(
                    'name' => $filename,
                    'description' => $object->getDescription(),
                    'url' => $this->container->get('request_stack')->getCurrentRequest()->getBasePath().'/'.  $this->path . $filename,
                    'thumbnailUrl' =>$this->container->get('request_stack')->getCurrentRequest()->getBasePath().  '/' .$photo,
                    'size' => $file->getSize(),
                    'deleteUrl' => $this->deleteLink($file->getId(), $container_delete,$user),
                    'deleteType' => "GET",
                    'id' => $file->getId()
                );
                array_push($response['files'], $f);
            }
        }
        return $response;
    }

    protected function setObject($file, $filename, $description){
        $file->setFilename($filename);
        $file->setSize(filesize($this->path . $filename));
        $file->setDescription($description);
    }

    public function getFiles($container_delete,$user) {
        $files = $this->repository;
        $response['files'] = array();

        foreach ($files as $file) {
            $f = array(
                'name' => $file->getFilename(),
                'description' => $file->getDescription(),
                'url' => $this->container->get('request_stack')->getCurrentRequest()->getBasePath().'/' . $this->path . $file->getFilename(),
                'thumbnailUrl' => $this->container->get('request_stack')->getCurrentRequest()->getBasePath().'/' . $this->path .'thumb-'. $file->getFilename(),
                'size' => $file->getSize(),
                'deleteUrl' => $this->deleteLink($file->getId(), $container_delete,$user),
                'deleteType' => "GET"
            );
            array_push($response['files'], $f);
        }
        return $response;
    }

    public function delete($path, $file) {
        $this->deleteFile($path . $file);
        $response = array(
            $file => true
        );

        return $response;
    }

    protected function deleteLink($id, $name,$user) {
        return $this->container->get('router')->generate($name, array('id' => $id,'user' => $user->getId()));
    }

    /**
     * Eliminar archivo
     * @param type $file
     */
    protected function deleteFile($file) {
        $file_path = $file;

        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    /**
     * Generar miniatura
     * @param type $filename
     * @param type $path
     * @param type $width
     * @param type $height
     */
    protected function ImagesThumnb($file, $filename, $path, $width, $height) {

        $a = getimagesize($path . $filename);
        $image_type = $a[2];
        if (in_array($image_type, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP))) {

            $rutaImagenOriginal= $path.$filename;
            $rutaSaveImagenOriginal= $path.'thumb-'.$filename;

            $uploaded_file_info = pathinfo($file->getClientOriginalName());
            $extensionFoto = $uploaded_file_info['extension'];

            if($extensionFoto == 'jpg' || $extensionFoto == 'JPG' || $extensionFoto == 'jpeg'){
                //Creamos una variable imagen a partir de la imagen original
                $img_original = imagecreatefromjpeg($rutaImagenOriginal);
            }else if($extensionFoto == 'png' || $extensionFoto == 'PNG'){
                //Creamos una variable imagen a partir de la imagen original
                $img_original = imagecreatefrompng($rutaImagenOriginal);
            }elseif($extensionFoto == 'gif' || $extensionFoto == 'GIF'){
                //Creamos una variable imagen a partir de la imagen original
                $img_original = imagecreatefromgif($rutaImagenOriginal);
            }

            //Se define el maximo ancho o alto que tendra la imagen final
            $max_ancho = $width;
            $max_alto = $height;

            //Ancho y alto de la imagen original
            list($ancho,$alto)=getimagesize($rutaImagenOriginal);

            //Se calcula ancho y alto de la imagen final
            $x_ratio = $max_ancho / $ancho;
            $y_ratio = $max_alto / $alto;

            //Si el ancho y el alto de la imagen no superan los maximos,
            //ancho final y alto final son los que tiene actualmente
            if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho
                $ancho_final = $ancho;
                $alto_final = $alto;
            }
            /*
             * si proporcion horizontal*alto mayor que el alto maximo,
             * alto final es alto por la proporcion horizontal
             * es decir, le quitamos al alto, la misma proporcion que
             * le quitamos al alto
             *
            */
            elseif (($x_ratio * $alto) < $max_alto){
                $alto_final = ceil($x_ratio * $alto);
                $ancho_final = $max_ancho;
            }
            /*
             * Igual que antes pero a la inversa
            */
            else{
                $ancho_final = ceil($y_ratio * $ancho);
                $alto_final = $max_alto;
            }
            //Creamos una imagen en blanco de tamaño $ancho_final  por $alto_final .
            $tmp=imagecreatetruecolor($ancho_final,$alto_final);
            //Copiamos $img_original sobre la imagen que acabamos de crear en blanco ($tmp)
            imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);

            //Definimos la calidad de la imagen final
            $calidad=95;

            if($extensionFoto == 'jpg' || $extensionFoto == 'JPG' || $extensionFoto == 'jpeg'){
                //Se crea la imagen final en el directorio indicado
                imagejpeg($tmp,$rutaSaveImagenOriginal,$calidad);
            }else if($extensionFoto == 'png' || $extensionFoto == 'PNG' ){
                //Se crea la imagen final en el directorio indicado
                imagepng($tmp,$rutaSaveImagenOriginal,8);
            }elseif($extensionFoto == 'gif' || $extensionFoto == 'GIF' ){
                //Se crea la imagen final en el directorio indicado
                imagegif($tmp,$rutaSaveImagenOriginal);
            }
            return $rutaSaveImagenOriginal;
        }
    }

    /**
     * Guardado de fotos
     * @param type $uploaded_file
     * @param type $path
     * @return string
     */
    protected static function processImage($uploaded_file, $path) {

        $uploaded_file_info = pathinfo($uploaded_file->getClientOriginalName());
        $filename = $uploaded_file_info['filename'] . '_' . uniqid() . "." . $uploaded_file_info['extension'];
        $uploaded_file->move($path, $filename);

        return $filename;
    }
}