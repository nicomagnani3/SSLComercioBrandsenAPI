<?php


namespace App\AbstractClass;
abstract class ImagenesPublicacionAbstract
{
    public function getArray()
    {
        return [
            'id'=>$this->getId(),
            'idpublicacion' => $this->getIdpublicacion(),
            'tipoarchivo'=>$this->getTipoarchivo(),
            'archivo' => base64_encode($this->getArchivo()),
            'contenttype' => $this->getContenttype(),
            'filename' => $this->getFilename(),
        ];  
    }
}