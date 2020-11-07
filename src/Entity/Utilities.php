<?php

namespace App\Entity;

class Utilities
{
    public function getIsValueInstance(&$value,&$msj = "",$type = null,$leng = null){
        $ret = false;
        if (is_null($value)) {
            $msj = "Error el atributo es obligatorio.";
        }else{
            $ret = true;
        }
        $value = trim($value);
        if (!is_null($type) && $ret == true) {
            switch ($type) {
                case 'i':
                $ret = ctype_digit($value);
                break;
                case 'f':
                $ret = is_numeric($value);
                break;
                case 's':
                $ret = true;
                break;
            }
            if ($ret == false) {
                $msj = "Error en el tipo de dato esperado.";
            }
        }      
        if(!is_null($leng) && $ret == true){
            if (strlen($value) == 0 || strlen($value) > $leng) {
                $ret = false;
                $msj = "Error de longitud esperada.";
            }else{
                $ret = true;
            }
        }
        return $ret;
    }

    public function getIsValueInstanceColect(&$value,&$msj = "",$type = null,$leng = null){
        $ret = false;
        if (is_null($value)) {
            $msj = "Error el atributo es obligatorio.";
        }else{
            $ret = true;
        }
        $value = trim($value);
        if (!is_null($type) && $ret == true) {
            try {
                    switch ($type) {
                        case 'j':
                            $value = json_decode($value);
                            $ret = true;
                        break;
                        case 'a':
                            $value = json_decode($value,true);
                            $ret = true;
                        break;
                    }
                } catch (\Throwable $th) {
                    $ret = false;
                }
            }
        if ($ret == false) {
            $msj = "Error en el tipo de dato esperado.";
        }
        if(!is_null($leng) && $ret == true){
            if (strlen($value) == 0 || strlen($value) > $leng) {
                $ret = false;
                $msj = "Error de longitud esperada.";
            }else{
                $ret = true;
            }
        }
        return $ret;
    }

    public function getIsValueInstanceInArray(&$value,&$msj = "",$type = null,$leng = null,$typeExplode = null){
        try {
           if (!is_null($typeExplode)) {
               $value = explode($typeExplode, $value);
           }
           foreach ($value as $data) {
               if (!$this->getIsValueInstance($data,$msj,$type,$leng)) {
                   return false;
               }
           }
           return true;
       } catch (\Throwable $th) {
           return false;
       }
    }

    public static function getIsValue($value,&$msj = "",$type = null,$leng = null){
        return self::getIsValueInstance($value,$msj,$type,$leng);
    }

    public static function array_to_json_persist_class($strPath,$arrayParam){
        return self::array_to_json_persist($strPath,$arrayParam);
    }

    public function array_to_xml($array, &$xml_user_info) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml_user_info->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                }else{
                    $subnode = $xml_user_info->addChild("item$key");
                    $this->array_to_xml($value, $subnode);
                }
            }else {
                $xml_user_info->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }

    public function array_to_json_persist($strPath,$arrayParam){
        try {
            $fp = fopen($strPath.'.json', 'w');
            fwrite($fp, json_encode($arrayParam,JSON_PRETTY_PRINT));
            fclose($fp);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
    
    public function array_change_key_case_recursive($arr)
    {
        return array_map(function($item){
            if(is_array($item))
                $item = $this->array_change_key_case_recursive($item);
            return $item;
        },array_change_key_case($arr));
    }
}