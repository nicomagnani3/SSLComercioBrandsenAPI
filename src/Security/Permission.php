<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Permission {





    /************ GRUPOS  *************/

    public const GENERAL = [
        'VER_PRODUCTOS',
        'CREAR_PRODUCTOS',
     
    ];
    public const EMPRESA = [
        'CREAR_PRODUCTO',      
    ];
    public const COMERCIO = [
        'CREAR_PRODUCTO',      
    ];
    public const EMPRENDEDOR = [
        'CREAR_PRODUCTO',      
    ];
    public const PROFESIONAL = [
        'CREAR_PRODUCTO',      
    ];
 
    public const ADMINISTRADOR  = [
        'ADMINISTRADOR'
    ];
    public const GRUPOS = [
        'GENERAL' => self::GENERAL,
        'EMPRESA' => self::EMPRESA,     
        'COMERCIO' => self::EMPRESA,       
        'EMPRENDEDOR' => self::EMPRESA,       
        'PROFESIONAL' => self::EMPRESA,
        'ADMINISTRADOR' => self::ADMINISTRADOR
    ];
    /****************************** */


    public function __construct () {
        /*** en caso de querer levantar los grupos y permisos desde tabla, 
         * lo deberia hacer aqui desde el constructor 
         * */
    }

    public function getPermisos ($user) {

        $gruposUser = $user->getGrupos();

        $permisos = [];
        $permisos_aux= [];

        foreach ($gruposUser as $grupo) {
            $permisos_aux = array_merge($permisos_aux,self::GRUPOS[$grupo]);
        }
        $permisos_aux = array_unique($permisos_aux, SORT_REGULAR);

        foreach ($permisos_aux as $permiso) {
            $permisos[] = $permiso;
        }

        return $permisos;
    }

    public function hasPermission ($target , $permissions) {
        

        
        if ( in_array ($target,$permissions['permission']) || in_array ('ADMINISTRADOR',$permissions['grupos'])) {
            return true;
        }else {
            return false;
        }
    }

    public function permissionDenied () {    
        return new JsonResponse(['Message' => 'Usted no Cuenta con los permisos necesarios para acceder aqui']
        , Response::HTTP_UNAUTHORIZED);
    }
}